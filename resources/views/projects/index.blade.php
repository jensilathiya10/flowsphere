<x-layouts.app title="Projects — FlowSphere">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Projects</h1>
            <p class="text-sm text-gray-500">{{ $workspace->name }}</p>
        </div>

        <a href="{{ route('projects.create') }}">
            <x-button>New Project</x-button>
        </a>
    </div>

    @if ($projects->isEmpty())
        <x-card class="text-center py-12">
            <p class="text-gray-500 mb-4">No projects yet.</p>
            <a href="{{ route('projects.create') }}">
                <x-button>Create your first project</x-button>
            </a>
        </x-card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($projects as $project)
                <a href="{{ route('projects.show', $project['id']) }}" class="block">
                    <x-card class="p-5 h-full hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <h2 class="font-semibold text-gray-900 mb-3">{{ $project['name'] }}</h2>

                        <div class="mb-4">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                <span>Progress</span>
                                <span>{{ $project['progress'] }}%</span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-gray-100">
                                <div class="h-1.5 rounded-full bg-brand-500" style="width: {{ $project['progress'] }}%"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>{{ $project['members'] }} {{ Str::plural('member', $project['members']) }}</span>
                            <span>
                                @if ($project['days_remaining'] === null)
                                    No due date
                                @elseif ($project['days_remaining'] < 0)
                                    Overdue
                                @else
                                    {{ $project['days_remaining'] }} days left
                                @endif
                            </span>
                        </div>
                    </x-card>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.app>
