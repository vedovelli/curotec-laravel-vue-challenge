<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Task Model
 * 
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property Carbon|null $due_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * // Computed properties (accessors)
 * @property-read string $status_text
 * @property-read bool $is_overdue
 * @property-read bool $is_completed
 * @property-read int $days_until_due
 */
final class Task extends Model
{
    use HasFactory;

    // Status constants for validation and consistency
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Scope a query to only include pending tasks.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include completed tasks.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include overdue tasks.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING)
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', now());
    }

    /**
     * Scope a query to only include tasks due today.
     * 
     * Note: Ready for future dashboard features (e.g., "Due Today" widget)
     */
    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', today());
    }

    /**
     * Scope a query to only include tasks due within the next X days.
     * 
     * Note: Ready for future dashboard features (e.g., "Due This Week" widget)
     */
    public function scopeDueWithin(Builder $query, int $days): Builder
    {
        return $query->whereNotNull('due_date')
                    ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    // ==========================================
    // CUSTOM METHODS
    // ==========================================

    /**
     * Mark the task as completed.
     */
    public function markAsCompleted(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        return $this->save();
    }

    /**
     * Mark the task as pending.
     */
    public function markAsPending(): bool
    {
        $this->status = self::STATUS_PENDING;
        return $this->save();
    }

    /**
     * Check if the task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if the task is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the task is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->isPending() 
               && $this->due_date !== null 
               && $this->due_date->isPast();
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the user that owns the task.
     * 
     * Note: This relationship is prepared for future implementation
     * when user association is added to the tasks table.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ==========================================
    // ACCESSORS
    // ==========================================

    /**
     * Get the human-readable status text.
     */
    protected function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_COMPLETED => 'Completed',
            default => 'Unknown',
        };
    }

    /**
     * Get whether the task is overdue (accessor version).
     */
    protected function getIsOverdueAttribute(): bool
    {
        return $this->isOverdue();
    }

    /**
     * Get whether the task is completed (accessor version).
     */
    protected function getIsCompletedAttribute(): bool
    {
        return $this->isCompleted();
    }

    /**
     * Get the number of days until the task is due.
     * Returns negative number if overdue, null if no due date.
     */
    protected function getDaysUntilDueAttribute(): ?int
    {
        if ($this->due_date === null) {
            return null;
        }

        return (int) now()->diffInDays($this->due_date, false);
    }

    /**
     * Get a formatted due date string.
     */
    protected function getFormattedDueDateAttribute(): ?string
    {
        if ($this->due_date === null) {
            return null;
        }

        return $this->due_date->format('M j, Y');
    }

    /**
     * Get the task priority based on due date proximity.
     */
    protected function getPriorityLevelAttribute(): string
    {
        if ($this->isCompleted()) {
            return 'completed';
        }

        if ($this->isOverdue()) {
            return 'overdue';
        }

        if ($this->due_date === null) {
            return 'normal';
        }

        $daysUntilDue = $this->days_until_due;

        return match (true) {
            $daysUntilDue <= 1 => 'urgent',
            $daysUntilDue <= 3 => 'high',
            $daysUntilDue <= 7 => 'medium',
            default => 'normal',
        };
    }
}
