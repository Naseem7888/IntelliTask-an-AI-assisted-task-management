<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskReorderRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::forUser(Auth::id());

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        $tasks = $query->orderedByPriority()->paginate(15);

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * Toggle the status of the specified task.
     */
    public function toggleStatus(Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        if ($task->isCompleted()) {
            $task->markAsPending();
        } else {
            $task->markAsCompleted();
        }

        return response()->json($task);
    }

    /**
     * Reorder the tasks for the authenticated user.
     *
     * @param TaskReorderRequest $request
     * @param TaskService $taskService
     * @return JsonResponse
     */
    public function reorder(TaskReorderRequest $request, TaskService $taskService): JsonResponse
    {
        $taskService->reorderTasks(Auth::id(), $request->validated()['task_ids']);

        return response()->json(['message' => 'Tasks reordered successfully.']);
    }
}