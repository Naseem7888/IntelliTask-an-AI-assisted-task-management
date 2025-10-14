<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the tasks for the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Scope a query to include users with pending tasks.
     */
    public function scopeWithPendingTasks($query)
    {
        return $query->whereHas('tasks', function ($query) {
            $query->where('status', Task::STATUS_PENDING);
        });
    }

    /**
     * Scope a query to include users with completed tasks.
     */
    public function scopeWithCompletedTasks($query)
    {
        return $query->whereHas('tasks', function ($query) {
            $query->where('status', Task::STATUS_COMPLETED);
        });
    }

    /**
     * Get the count of pending tasks for the user.
     */
    public function getPendingTasksCountAttribute()
    {
        return $this->tasks()->where('status', Task::STATUS_PENDING)->count();
    }

    /**
     * Get the count of completed tasks for the user.
     */
    public function getCompletedTasksCountAttribute()
    {
        return $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
    }

    /**
     * Get the total count of tasks for the user.
     */
    public function getTotalTasksCountAttribute()
    {
        return $this->tasks()->count();
    }

    /**
     * Get pending tasks for the user ordered by priority.
     */
    public function getPendingTasksAttribute()
    {
        return $this->tasks()
            ->where('status', Task::STATUS_PENDING)
            ->orderBy('priority_order')
            ->get();
    }

    /**
     * Get completed tasks for the user ordered by completion date.
     */
    public function getCompletedTasksAttribute()
    {
        return $this->tasks()
            ->where('status', Task::STATUS_COMPLETED)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Check if the user has any pending tasks.
     */
    public function hasPendingTasks()
    {
        return $this->tasks()->where('status', Task::STATUS_PENDING)->exists();
    }

    /**
     * Check if the user has any completed tasks.
     */
    public function hasCompletedTasks()
    {
        return $this->tasks()->where('status', Task::STATUS_COMPLETED)->exists();
    }
}
