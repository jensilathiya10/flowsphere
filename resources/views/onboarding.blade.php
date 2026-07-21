<x-layouts.guest title="Set up your workspace — FlowSphere">
    <h1 class="text-xl font-semibold text-gray-900 mb-1">Set up your workspace</h1>
    <p class="text-sm text-gray-600 mb-6">One more step before your dashboard is ready.</p>

    @if ($errors->any())
        <ul class="mb-4 text-sm text-red-600 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ url('/api/workspaces') }}" class="space-y-5">
        @csrf

        <x-input name="name" label="Workspace name" type="text" placeholder="Acme Inc." required autofocus />

        <div>
            <span class="block text-sm font-medium text-gray-700 mb-2">Workspace type</span>

            <div class="grid grid-cols-2 gap-3">
                @foreach (['company' => 'Company', 'startup' => 'Startup', 'personal' => 'Personal', 'freelance' => 'Freelance'] as $value => $label)
                    <label class="relative flex items-center justify-center rounded-md border border-gray-300 px-3 py-3 text-sm font-medium text-gray-700 cursor-pointer has-checked:border-brand-500 has-checked:bg-brand-50 has-checked:text-brand-700">
                        <input type="radio" name="type" value="{{ $value }}" class="sr-only" {{ old('type') === $value ? 'checked' : '' }} required>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <x-button type="submit" class="w-full">Create workspace</x-button>
    </form>
</x-layouts.guest>
