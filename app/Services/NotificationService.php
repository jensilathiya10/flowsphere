<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Thin wrapper around the `notifications` table used by task assignment,
 * deadline reminders, and comment mentions. Fire these calls from model
 * events or queued jobs (see README) rather than inline in controllers.
 */
class NotificationService
{
    public function notify(User $user, string $type, string $title, ?string $body = null, ?Model $subject = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
        ]);
    }

    public function taskAssigned(User $assignee, \App\Models\Task $task): Notification
    {
        return $this->notify(
            $assignee,
            'task_assigned',
            "You were assigned: {$task->title}",
            "Due " . ($task->due_date?->toFormattedDateString() ?? 'no date set'),
            $task
        );
    }

    public function deadlineApproaching(User $assignee, \App\Models\Task $task): Notification
    {
        return $this->notify(
            $assignee,
            'deadline_due',
            "Deadline approaching: {$task->title}",
            "Due " . $task->due_date?->toFormattedDateString(),
            $task
        );
    }

    public function unreadCount(User $user): int
    {
        return $user->notifications()->whereNull('read_at')->count();
    }
}
