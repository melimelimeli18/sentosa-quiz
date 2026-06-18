<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'SentosaQuiz') }}</title>

        <link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-[#1b1b18] flex items-center justify-center min-h-screen p-6 font-sans">
        <main class="w-full max-w-sm flex flex-col items-center">
            <img src="{{ asset('images/logo_2.webp') }}" alt="Sentosa Quiz Logo" class="w-32 h-32 object-contain mb-4">
            
            <h1 class="text-3xl font-bold text-[#C50303] mb-1">Sentosa Quiz</h1>
            <p class="text-gray-500 mb-8 text-center text-sm font-medium">Kenali Hambatan Belajar, Maksimalkan Potensimu.</p>
            
            <div class="flex flex-col w-full gap-4">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="w-full py-3 px-4 text-center border border-[#C50303] text-[#1b1b18] hover:bg-gray-50 rounded-lg transition-colors">
                        Login
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="w-full py-3 px-4 text-center bg-[#C50303] text-white hover:bg-[#a50202] rounded-lg font-bold transition-colors shadow-sm shadow-[#C50303]/20">
                        Sign Up
                    </a>
                @endif

                @if (config('app.demo_enabled'))
                    <div class="relative flex py-2 items-center">
                        <div class="flex-grow border-t border-gray-200"></div>
                        <span class="flex-shrink mx-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Demo</span>
                        <div class="flex-grow border-t border-gray-200"></div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <a href="{{ route('demo.student') }}" class="w-full py-3 px-4 text-center bg-[#C50303] text-white hover:bg-[#a50202] rounded-lg font-bold transition-colors shadow-sm shadow-[#C50303]/20">
                            Login as Student
                        </a>
                    </a>
                        <a href="{{ route('demo.teacher') }}" class="w-full py-3 px-4 text-center bg-[#C50303] text-white hover:bg-[#a50202] rounded-lg font-bold transition-colors shadow-sm shadow-[#C50303]/20">
                            Login as Teacher
                        </a>
                    </div>
                @endif
            </div>
        </main>
    </body>
</html>
