<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'owner_id', 'name', 'description', 'status',
        'start_date', 'due_date', 'progress', 'health_score',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function aiReports(): HasMany
    {
        return $this->hasMany(AiReport::class)->latest();
    }

    public function daysRemaining(): ?int
    {
        return $this->due_date ? now()->diffInDays($this->due_date, false) : null;
    }

    /** Recalculate and persist progress (%) from task completion. */
    public function recalculateProgress(): int
    {
        $total = $this->tasks()->count();
        $done = $this->tasks()->where('status', 'done')->count();
        $progress = $total > 0 ? (int) round(($done / $total) * 100) : 0;
        $this->update(['progress' => $progress]);

        return $progress;
    }
}
