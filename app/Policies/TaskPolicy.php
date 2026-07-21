<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    protected function role(User $user, Task $task): ?string
    {
        return $task->project->workspace->members()
            ->where('user_id', $user->id)
            ->first()?->pivot->role;
    }

    public function view(User $user, Task $task): bool
    {
        return $this->role($user, $task) !== null;
    }

    public function update(User $user, Task $task): bool
    {
        $role = $this->role($user, $task);

        return in_array($role, ['owner', 'manager']) || $task->assignee_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return in_array($this->role($user, $task), ['owner', 'manager']);
    }

    public function comment(User $user, Task $task): bool
    {
        return $this->role($user, $task) !== null;
    }
}
