@php
$tab = request()->query('tab', 'overview');
$tabs = [
    'overview' => 'Overview',
    'tasks' => 'Tasks',
    'team' => 'Team',
    'files' => 'Files',
    'activity' => 'Activity',
];
$priorityColor = ['high' => 'red', 'medium' => 'amber', 'low' => 'gray'];
$nextStatus = ['todo' => 'doing', 'doing' => 'done'];
$nextLabel = ['todo' => 'Start', 'doing' => 'Complete'];
@endphp

<x-layouts.app title="{{ $project->name }} — FlowSphere">
    <div class="mb-6">
        <a href="{{ route('projects.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Projects</a>
        <div class="mt-1 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">{{ $project->name }}</h1>
            <x-badge color="brand">{{ ucfirst($project->status) }}</x-badge>
        </div>
    </div>

    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('projects.show', [$project, 'tab' => $key]) }}"
                    class="pb-3 text-sm font-medium border-b-2 {{ $tab === $key ? 'border-brand-600 text-brand-700' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    @if ($tab === 'overview')
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-card class="p-5">
                <p class="text-sm text-gray-500">Progress</p>
                <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $project->progress }}%</p>
            </x-card>

            <x-card class="p-5">
                <p class="text-sm text-gray-500">Health Score</p>
                <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $healthScore }}%</p>
                <p class="text-xs text-gray-500 mt-1">{{ $healthLabel }}</p>
            </x-card>

            <x-card class="p-5">
                <p class="text-sm text-gray-500">Due Date</p>
                <p class="mt-1 text-lg font-semibold text-gray-900">
                    {{ $project->due_date?->toFormattedDateString() ?? 'Not set' }}
                </p>
            </x-card>
        </div>

        @if ($project->description)
            <x-card class="mt-4">
                <h2 class="text-base font-semibold text-gray-900 mb-2">Description</h2>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $project->description }}</p>
            </x-card>
        @endif
    @endif

    @if ($tab === 'tasks')
        <x-card class="mb-4">
            <form method="POST" action="{{ url('/api/projects/' . $project->id . '/tasks') }}" class="flex items-end gap-3">
                @csrf
                <div class="flex-1">
                    <x-input name="title" label="Add a task to To Do" type="text" placeholder="Task title" required />
                </div>
                <x-button type="submit">Add Task</x-button>
            </form>
        </x-card>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach (['todo' => 'To Do', 'doing' => 'Doing', 'done' => 'Done'] as $status => $label)
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ $label }} ({{ $tasksByStatus[$status]->count() }})</h3>

                    <div class="space-y-3">
                        @forelse ($tasksByStatus[$status] as $task)
                            <x-card class="p-4">
                                <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>

                                <div class="mt-2 flex items-center gap-2 flex-wrap">
                                    @if ($task->priority)
                                        <x-badge :color="$priorityColor[$task->priority] ?? 'gray'">{{ ucfirst($task->priority) }}</x-badge>
                                    @endif
                                    @if ($task->assignee)
                                        <span class="text-xs text-gray-500">{{ $task->assignee->name }}</span>
                                    @endif
                                </div>

                                @if ($task->due_date)
                                    <p class="mt-1 text-xs text-gray-400">Due {{ $task->due_date->toFormattedDateString() }}</p>
                                @endif

                                @if (isset($nextStatus[$status]))
                                    <form method="POST" action="{{ url('/api/tasks/' . $task->id) }}" class="mt-3">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $nextStatus[$status] }}">
                                        <x-button type="submit" variant="secondary" class="w-full text-xs py-1">{{ $nextLabel[$status] }} &rarr;</x-button>
                                    </form>
                                @endif
                            </x-card>
                        @empty
                            <p class="text-sm text-gray-400">Nothing here.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($tab === 'team')
        <x-card>
            <div class="divide-y divide-gray-100">
                @foreach ($members as $member)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->pivot->title ?? $member->job_title ?? 'Member' }}</p>
                        </div>
                        <x-badge>{{ ucfirst($member->pivot->role) }}</x-badge>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    @if ($tab === 'files')
        <x-card>
            @forelse ($attachments as $file)
                <div class="py-3 flex items-center justify-between {{ ! $loop->last ? 'border-b border-gray-100' : '' }}">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $file->name }}</p>
                        <p class="text-xs text-gray-500">{{ $file->uploader?->name ?? 'Unknown' }} &middot; {{ $file->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-8">No files uploaded yet.</p>
            @endforelse
        </x-card>
    @endif

    @if ($tab === 'activity')
        <x-card>
            <p class="text-sm text-gray-500 text-center py-8">No activity recorded yet.</p>
        </x-card>
    @endif
</x-layouts.app>
