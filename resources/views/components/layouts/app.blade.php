<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'FlowSphere' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-6 h-14 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-brand-600">FlowSphere</a>

                <div class="flex items-center gap-1">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-brand-700 bg-brand-50' : 'text-gray-600 hover:text-gray-900' }}">Dashboard</a>
                    <a href="{{ route('projects.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('projects.*') ? 'text-brand-700 bg-brand-50' : 'text-gray-600 hover:text-gray-900' }}">Projects</a>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-button variant="secondary" type="submit">Log out</x-button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-8">
        {{ $slot }}
    </main>
</body>
</html>
