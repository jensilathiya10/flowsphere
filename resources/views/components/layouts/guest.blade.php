<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'FlowSphere' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 font-sans antialiased">
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <span class="text-xl font-semibold text-brand-600">FlowSphere</span>
        </div>

        <x-card>
            {{ $slot }}
        </x-card>
    </div>
</body>
</html>
