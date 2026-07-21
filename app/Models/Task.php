<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'assignee_id', 'created_by', 'title', 'description',
        'status', 'priority', 'due_date', 'checklist', 'position', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'checklist' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->oldest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->status !== 'done' && $this->due_date->isPast();
    }

    public function checklistProgress(): int
    {
        $items = collect($this->checklist ?? []);
        if ($items->isEmpty()) {
            return 0;
        }

        return (int) round(($items->where('done', true)->count() / $items->count()) * 100);
    }
}
