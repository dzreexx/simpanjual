<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My App')</title>
    {{-- <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> --}}
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    @include('partials.sidebar')

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        {{-- Navbar --}}
        @include('partials.navbar')

        {{-- Konten Utama --}}
        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</body>
</html>