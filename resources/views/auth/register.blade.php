<x-layouts.guest title="Register — FlowSphere">
    <h1 class="text-xl font-semibold text-gray-900 mb-6">Create your FlowSphere account</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <x-input name="name" label="Name" type="text" required autofocus />
        <x-input name="email" label="Email" type="email" required />
        <x-input name="password" label="Password" type="password" required />
        <x-input name="password_confirmation" label="Confirm password" type="password" required />

        <x-button type="submit" class="w-full">Register</x-button>
    </form>

    <p class="mt-6 text-sm text-gray-600">Already have an account? <a href="{{ route('login') }}" class="text-brand-600 font-medium hover:underline">Log in</a></p>
</x-layouts.guest>
