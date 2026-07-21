<x-layouts.app title="New Project — FlowSphere">
    <div class="max-w-lg">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">New Project</h1>

        <x-card>
            <form method="POST" action="{{ url('/api/workspaces/' . $workspace->id . '/projects') }}" class="space-y-4">
                @csrf

                <x-input name="name" label="Project name" type="text" required autofocus />

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-input name="start_date" label="Start date" type="date" />
                    <x-input name="due_date" label="Due date" type="date" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-button type="submit">Create Project</x-button>
                    <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
