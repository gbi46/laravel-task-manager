@php
    $statusLabels = [
        \App\Models\Task::STATUS_PENDING => 'Pending',
        \App\Models\Task::STATUS_IN_PROGRESS => 'In Progress',
        \App\Models\Task::STATUS_DONE => 'Completed',
    ];

    $priorityLabels = [
        \App\Models\Task::PRIORITY_LOW => 'Low',
        \App\Models\Task::PRIORITY_MEDIUM => 'Medium',
        \App\Models\Task::PRIORITY_HIGH => 'High',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-500">Stay on top of every deliverable</p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Task Manager') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 mb-1">Total tasks</p>
                    <p class="text-3xl font-semibold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 mb-1">Completed</p>
                    <p class="text-3xl font-semibold text-emerald-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 mb-1">Due today</p>
                    <p class="text-3xl font-semibold text-indigo-600">{{ $stats['today'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 mb-1">Overdue</p>
                    <p class="text-3xl font-semibold text-rose-600">{{ $stats['overdue'] }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <form method="POST" action="{{ route('tasks.store') }}" class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 space-y-4">
                    @csrf
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Create a task</h3>
                            <p class="text-sm text-gray-500">Capture the work that needs your attention.</p>
                        </div>
                        <x-primary-button>{{ __('Save Task') }}</x-primary-button>
                    </div>

                    <div class="grid gap-4">
                        <div>
                            <x-input-label value="Title" for="title" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
                            @error('title')
                                <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <x-input-label value="Description" for="description" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid sm:grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Due date" for="due_date" />
                                <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" value="{{ old('due_date') }}" />
                                @error('due_date')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <x-input-label value="Priority" for="priority" />
                                <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority }}" @selected(old('priority', \App\Models\Task::PRIORITY_MEDIUM) === $priority)>
                                            {{ $priorityLabels[$priority] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <x-input-label value="Status" for="status" />
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" @selected(old('status', \App\Models\Task::STATUS_PENDING) === $status)>
                                            {{ $statusLabels[$status] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="text-sm text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Filters & quick links</h3>
                            <p class="text-sm text-gray-500">Drill into specific slices of your work.</p>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Clear filters</a>
                    </div>
                    <form method="GET" class="space-y-4">
                        <div>
                            <x-input-label value="Search" for="search" />
                            <x-text-input id="search" type="search" name="search" value="{{ $filters['search'] }}" class="mt-1 block w-full" placeholder="Find tasks by name or note..." />
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Status" for="filter-status" />
                                <select id="filter-status" name="status" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Any status</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" @selected($filters['status'] === $status)>
                                            {{ $statusLabels[$status] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Priority" for="filter-priority" />
                                <select id="filter-priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Any priority</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority }}" @selected($filters['priority'] === $priority)>
                                            {{ $priorityLabels[$priority] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ url()->current() }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
                            <x-secondary-button type="submit">{{ __('Apply') }}</x-secondary-button>
                        </div>
                    </form>

                    <div class="mt-6 divide-y divide-gray-100">
                        <a href="{{ route('tasks.index', ['status' => \App\Models\Task::STATUS_DONE]) }}" class="flex items-center justify-between py-2 text-sm text-gray-600 hover:text-indigo-600">
                            View completed work
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="{{ route('tasks.index', ['priority' => \App\Models\Task::PRIORITY_HIGH]) }}" class="flex items-center justify-between py-2 text-sm text-gray-600 hover:text-indigo-600">
                            Jump to high priority
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="{{ route('tasks.index', ['status' => \App\Models\Task::STATUS_PENDING, 'priority' => \App\Models\Task::PRIORITY_MEDIUM]) }}" class="flex items-center justify-between py-2 text-sm text-gray-600 hover:text-indigo-600">
                            Focus on upcoming work
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tasks</h3>
                        <p class="text-sm text-gray-500">Update, re-prioritize, or complete work from here.</p>
                    </div>
                    <p class="text-sm text-gray-400">{{ $tasks->total() }} results</p>
                </div>

                <ul class="divide-y divide-gray-100">
                    @forelse ($tasks as $task)
                        <li class="p-6">
                            <div class="flex flex-col gap-4">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">#{{ $task->id }}</p>
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $task->title }}</h4>
                                        @if ($task->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                                        @endif
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs font-medium">
                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full {{ $task->status === \App\Models\Task::STATUS_DONE ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700' }}">
                                                {{ $statusLabels[$task->status] }}
                                            </span>
                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-700">
                                                {{ $priorityLabels[$task->priority] }} priority
                                            </span>
                                            @if ($task->due_date)
                                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700">
                                                    Due {{ $task->due_date->format('M j, Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <form action="{{ route('tasks.status', $task) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <label for="status-{{ $task->id }}" class="text-sm text-gray-500 hidden md:block">Status</label>
                                            <select id="status-{{ $task->id }}" name="status" class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}" @selected($task->status === $status)>{{ $statusLabels[$status] }}</option>
                                                @endforeach
                                            </select>
                                            <x-secondary-button type="submit" class="text-xs">{{ __('Update') }}</x-secondary-button>
                                        </form>
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button>
                                                {{ __('Delete') }}
                                            </x-danger-button>
                                        </form>
                                    </div>
                                </div>

                                <details class="bg-gray-50 rounded-md border border-dashed border-gray-200">
                                    <summary class="cursor-pointer select-none px-4 py-2 text-sm font-medium text-gray-700">
                                        Edit details
                                    </summary>
                                    <div class="px-4 pb-4 pt-2">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-3">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid md:grid-cols-2 gap-3">
                                                <div>
                                                    <x-input-label value="Title" for="title-{{ $task->id }}" />
                                                    <x-text-input id="title-{{ $task->id }}" name="title" type="text" value="{{ old('title', $task->title) }}" class="mt-1 block w-full" required />
                                                </div>
                                                <div>
                                                    <x-input-label value="Due date" for="due-{{ $task->id }}" />
                                                    <x-text-input id="due-{{ $task->id }}" name="due_date" type="date" value="{{ old('due_date', optional($task->due_date)->format('Y-m-d')) }}" class="mt-1 block w-full" />
                                                </div>
                                            </div>
                                            <div>
                                                <x-input-label value="Description" for="description-{{ $task->id }}" />
                                                <textarea id="description-{{ $task->id }}" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $task->description) }}</textarea>
                                            </div>
                                            <div class="grid md:grid-cols-2 gap-3">
                                                <div>
                                                    <x-input-label value="Priority" for="priority-{{ $task->id }}" />
                                                    <select id="priority-{{ $task->id }}" name="priority" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                                        @foreach ($priorities as $priority)
                                                            <option value="{{ $priority }}" @selected($task->priority === $priority)>
                                                                {{ $priorityLabels[$priority] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <x-input-label value="Status" for="edit-status-{{ $task->id }}" />
                                                    <select id="edit-status-{{ $task->id }}" name="status" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                                        @foreach ($statuses as $status)
                                                            <option value="{{ $status }}" @selected($task->status === $status)>
                                                                {{ $statusLabels[$status] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-end gap-3">
                                                <x-secondary-button type="submit">{{ __('Save changes') }}</x-secondary-button>
                                            </div>
                                        </form>
                                    </div>
                                </details>
                            </div>
                        </li>
                    @empty
                        <li class="p-8 text-center text-gray-500">
                            <p class="text-lg font-semibold mb-2">No tasks match your filters.</p>
                            <p class="text-sm">Create a new task or adjust the filters to see more results.</p>
                        </li>
                    @endforelse
                </ul>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
