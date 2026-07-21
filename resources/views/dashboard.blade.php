<x-layouts.app title="Dashboard — FlowSphere">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $workspace->name }}</h1>
            <p class="text-sm text-gray-500">Here's what's happening in your workspace today.</p>
        </div>

        @if ($unreadNotifications > 0)
            <span class="inline-flex items-center rounded-full bg-brand-100 text-brand-700 text-sm font-medium px-3 py-1">
                {{ $unreadNotifications }} unread {{ Str::plural('notification', $unreadNotifications) }}
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-card class="p-5">
            <p class="text-sm text-gray-500">Active Projects</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['active_projects'] }}</p>
        </x-card>

        <x-card class="p-5">
            <p class="text-sm text-gray-500">Completed Tasks</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['completed_tasks'] }}</p>
        </x-card>

        <x-card class="p-5">
            <p class="text-sm text-gray-500">Team Members</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['members'] }}</p>
        </x-card>

        <x-card class="p-5">
            <p class="text-sm text-gray-500">Deadlines This Week</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['deadlines_this_week'] }}</p>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card>
            <h2 class="text-base font-semibold text-gray-900 mb-4">Recent Activity</h2>

            @forelse ($recentActivity as $activity)
                <div class="flex items-start gap-3 py-2 {{ ! $loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="h-2 w-2 mt-2 rounded-full bg-brand-500 shrink-0"></div>
                    <div>
                        <p class="text-sm text-gray-800">
                            <span class="font-medium">{{ $activity->user?->name ?? 'Someone' }}</span>
                            {{ $activity->description }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No activity yet.</p>
            @endforelse
        </x-card>

        <x-card>
            <h2 class="text-base font-semibold text-gray-900 mb-4">Team Productivity</h2>

            @forelse ($productivity as $member)
                <div class="py-2 {{ ! $loop->last ? 'border-b border-gray-100' : '' }}">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="font-medium text-gray-800">{{ $member['name'] }}</span>
                        <span class="text-gray-500">{{ $member['completed'] }}/{{ $member['assigned'] }} tasks</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-gray-100">
                        <div class="h-1.5 rounded-full bg-brand-500" style="width: {{ $member['productivity'] }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No team members yet.</p>
            @endforelse
        </x-card>
    </div>
</x-layouts.app>
