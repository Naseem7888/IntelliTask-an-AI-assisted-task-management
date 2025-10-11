<?php

namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function creating(Task $task): void
    {
        if (is_null($task->priority_order)) {
            $task->priority_order = Task::getNextPriorityOrder($task->user_id);
        }
        if (is_null($task->status)) {
            $task->status = Task::STATUS_PENDING;
        }
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged('status')) {
            if ($task->getOriginal('status') === Task::STATUS_PENDING && $task->isCompleted()) {
                Task::reorderTasksForUser($task->user_id);
            }
            if ($task->getOriginal('status') === Task::STATUS_COMPLETED && $task->isPending()) {
                $task->priority_order = Task::getNextPriorityOrder($task->user_id);
                $task->saveQuietly();
            }
        }
    }

    public function deleted(Task $task): void
    {
        if ($task->isPending()) {
            Task::reorderTasksForUser($task->user_id);
        }
    }
}
