<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskStatusRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filters = [
            'status' => $request->string('status')->toString(),
            'priority' => $request->string('priority')->toString(),
            'search' => $request->string('search')->toString(),
        ];

        $tasksQuery = Task::query()
            ->where('user_id', $user->id)
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['priority'], fn ($query, $priority) => $query->where('priority', $priority))
            ->when($filters['search'], function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });

        $tasks = $tasksQuery
            ->orderByRaw("CASE status WHEN ? THEN 0 WHEN ? THEN 1 ELSE 2 END", [
                Task::STATUS_PENDING,
                Task::STATUS_IN_PROGRESS,
            ])
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        $today = now()->toDateString();
        $stats = [
            'total' => $user->tasks()->count(),
            'completed' => $user->tasks()->where('status', Task::STATUS_DONE)->count(),
            'overdue' => $user->tasks()
                ->where('status', '!=', Task::STATUS_DONE)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->count(),
            'today' => $user->tasks()
                ->whereNotNull('due_date')
                ->whereDate('due_date', $today)
                ->count(),
        ];

        return view('tasks.index', [
            'tasks' => $tasks,
            'filters' => $filters,
            'stats' => $stats,
            'statuses' => Task::statuses(),
            'priorities' => Task::priorities(),
        ]);
    }

    public function store(TaskRequest $request)
    {
        $request->user()->tasks()->create($request->payload());

        return redirect()->route('tasks.index')
            ->with('status', 'Task created.');
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->payload($task));

        return redirect()->route('tasks.index')
            ->with('status', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return back()->with('status', 'Task removed.');
    }

    public function updateStatus(TaskStatusRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->payload());

        return back()->with('status', 'Task status updated.');
    }
}
