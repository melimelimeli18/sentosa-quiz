<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SentosaQuiz — @yield('title', 'Student Portal')</title>
    <meta name="description" content="Platform kuis evaluasi mandiri siswa SMA Sentosa" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen font-sans antialiased">

    {{-- Top Nav --}}
    <nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between shadow-sm">
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3">
            <img
                src="{{ asset('images/logo.webp') }}"
                alt="Sekolah Sentosa Jakarta"
                class="h-9 w-9 object-contain"
            />
            <span class="font-bold text-base text-gray-900">SentosaQuiz</span>
        </a>
        <div class="flex items-center gap-4 text-sm text-gray-500">
            <span class="hidden sm:inline">{{ auth()->user()?->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition"
                >
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    {{-- Gray separator --}}
    <div class="h-px bg-gray-200"></div>

    <main class="max-w-4xl mx-auto px-4 py-8">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
