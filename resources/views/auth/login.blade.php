<x-layouts.guest title="Log in — FlowSphere">
    <h1 class="text-xl font-semibold text-gray-900 mb-6">Log in to FlowSphere</h1>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <x-input name="email" label="Email" type="email" required autofocus />
        <x-input name="password" label="Password" type="password" required />

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            Remember me
        </label>

        <x-button type="submit" class="w-full">Log in</x-button>
    </form>

    <p class="mt-6 text-sm text-gray-600">No account? <a href="{{ route('register') }}" class="text-brand-600 font-medium hover:underline">Register</a></p>
</x-layouts.guest>
