<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority_order',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
        'priority_order' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The available task statuses.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';

    /**
     * Get all available task statuses.
     *
     * @return array
     */
    public static function getAvailableStatuses(): array
    {
        return array_keys(config('tasks.statuses', []));
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to order tasks by priority.
     */
    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('priority_order');
    }

    /**
     * Scope a query to filter tasks by status.
     */
    public function scopeByStatus($query, string $status)
    {
        if (in_array($status, [self::STATUS_PENDING, self::STATUS_COMPLETED])) {
            return $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Scope a query to get tasks for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the title attribute with proper formatting.
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst(trim($value)),
            set: fn ($value) => trim($value),
        );
    }


    /**
     * Get the description attribute with proper formatting.
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? trim($value) : null,
            set: fn ($value) => $value ? trim($value) : null,
        );
    }

    /**
     * Check if the task is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

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
     * Get validation rules for creating a task.
     */
    public static function getCreateValidationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:' . config('tasks.limits.title.max', 255)],
            'description' => ['nullable', 'string', 'max:' . config('tasks.limits.description.max', 5000)],
            'status' => ['nullable', 'string', Rule::in(self::getAvailableStatuses())],
            'priority_order' => ['nullable', 'integer', 'min:0'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Get validation rules for updating a task.
     */
    public static function getUpdateValidationRules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:' . config('tasks.limits.title.max', 255)],
            'description' => ['sometimes', 'nullable', 'string', 'max:' . config('tasks.limits.description.max', 5000)],
            'status' => ['sometimes', 'required', 'string', Rule::in(self::getAvailableStatuses())],
            'priority_order' => ['sometimes', 'required', 'integer', 'min:0'],
            'user_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Get validation rules for bulk priority update.
     */
    public static function getPriorityUpdateValidationRules(): array
    {
        return [
            'tasks' => ['required', 'array'],
            'tasks.*.id' => ['required', 'integer', 'exists:tasks,id'],
            'tasks.*.priority_order' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get the next available priority order for a user.
     */
    public static function getNextPriorityOrder(int $userId): int
    {
        $maxPriority = self::where('user_id', $userId)
            ->where('status', self::STATUS_PENDING)
            ->max('priority_order');

        return $maxPriority ? $maxPriority + 1 : 0;
    }

    /**
     * Reorder tasks for a user after a task is deleted or status changed.
     */
    public static function reorderTasksForUser(int $userId): void
    {
        $tasks = self::where('user_id', $userId)
            ->where('status', self::STATUS_PENDING)
            ->orderBy('priority_order')
            ->get();

        foreach ($tasks as $index => $task) {
            $task->update(['priority_order' => $index]);
        }
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Set default priority order when creating a new task
        static::creating(function ($task) {
            if (is_null($task->priority_order)) {
                $task->priority_order = self::getNextPriorityOrder($task->user_id);
            }
            
            if (is_null($task->status)) {
                $task->status = self::STATUS_PENDING;
            }
        });

        // Reorder tasks when a task is deleted
        static::deleted(function ($task) {
            if ($task->status === self::STATUS_PENDING) {
                self::reorderTasksForUser($task->user_id);
            }
        });

        // Reorder tasks when status changes from pending to completed
        static::updated(function ($task) {
            if ($task->wasChanged('status') &&
                $task->getOriginal('status') === self::STATUS_PENDING &&
                $task->status === self::STATUS_COMPLETED) {
                self::reorderTasksForUser($task->user_id);
            }
            if ($task->wasChanged('status') &&
                $task->getOriginal('status') === self::STATUS_COMPLETED &&
                $task->status === self::STATUS_PENDING) {
                $task->priority_order = self::getNextPriorityOrder($task->user_id);
                $task->saveQuietly();
            }
        });
    }
}