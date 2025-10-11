<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TaskManager extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public bool $showCreateForm = false;
    public bool $showAISuggestions = false;
    public ?int $editingTaskId = null;

    public array $newTask = [
        'title' => '',
        'description' => '',
    ];

    public array $editingTask = [
        'title' => '',
        'description' => '',
    ];

    protected $listeners = ['tasks-created' => 'refreshTasks'];

    public int $countAll = 0;
    public int $countPending = 0;
    public int $countCompleted = 0;

    public function mount()
    {
        $this->refreshTasks();
        $this->computeCounts();
    }

    public function refreshTasks()
    {
        $this->resetPage();
        $this->computeCounts();
    }

    protected function computeCounts(): void
    {
        $userId = Auth::id();
        $this->countAll = Task::where('user_id', $userId)->count();
        $this->countPending = Task::where('user_id', $userId)->where('status', Task::STATUS_PENDING)->count();
        $this->countCompleted = Task::where('user_id', $userId)->where('status', Task::STATUS_COMPLETED)->count();
    }

    public function createTask()
    {
        $this->validate([
            'newTask.title' => 'required|string|max:255',
            'newTask.description' => 'nullable|string|max:5000',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->tasks()->create([
            'title' => $this->newTask['title'],
            'description' => $this->newTask['description'],
            'status' => Task::STATUS_PENDING,
        ]);

        $this->reset('newTask', 'showCreateForm');
        $this->refreshTasks();
    }

    public function editTask(int $taskId)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        $this->editingTaskId = $taskId;
        $this->editingTask = [
            'title' => $task->title,
            'description' => $task->description ?? '',
        ];
    }

    public function updateTask(int $taskId)
    {
        $this->validate([
            'editingTask.title' => 'required|string|max:255',
            'editingTask.description' => 'nullable|string|max:5000',
        ]);

        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        $task->update([
            'title' => $this->editingTask['title'],
            'description' => $this->editingTask['description'],
        ]);

        $this->reset('editingTaskId', 'editingTask');
        $this->refreshTasks();
    }

    public function cancelEditing()
    {
        $this->reset('editingTaskId', 'editingTask');
    }

    public function deleteTask(int $taskId)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        $task->delete();
        $this->refreshTasks();
    }

    public function toggleTaskStatus(int $taskId)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        $task->status = $task->isCompleted() ? Task::STATUS_PENDING : Task::STATUS_COMPLETED;
        $task->save();
        $this->refreshTasks();
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        $query = $user->tasks();
        
        if ($this->filter === 'pending') {
            $query->where('status', Task::STATUS_PENDING);
        } elseif ($this->filter === 'completed') {
            $query->where('status', Task::STATUS_COMPLETED);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.task-manager', ['tasks' => $tasks]);
    }
}