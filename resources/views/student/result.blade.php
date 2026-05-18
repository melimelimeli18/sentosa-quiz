@extends('layouts.student')

@section('title', 'Hasil Quiz')

@section('content')
<div class="space-y-6">

    {{-- Score Card --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center shadow-sm">
        <p class="text-xs uppercase tracking-widest text-[#C50303] font-semibold mb-2">
            {{ $attempt->quiz->subject->name }} &middot; {{ $attempt->quiz->title }}
        </p>
        <div class="text-7xl font-extrabold my-4 {{ $attempt->score >= 70 ? 'text-green-600' : 'text-[#C50303]' }}">
            {{ number_format($attempt->score, 0) }}
        </div>
        <div class="h-px bg-gray-200 my-4"></div>
        <p class="text-gray-400 text-base">dari {{ $attempt->quiz->total_points }} poin</p>
        <p class="text-gray-400 text-sm mt-1">Dikumpulkan: {{ $attempt->submitted_at?->format('d M Y, H:i') }}</p>

        <div class="mt-6">
            <a href="{{ route('student.dashboard') }}"
               class="inline-block px-6 py-2.5 bg-[#C50303] hover:bg-[#a50202] text-white rounded-xl font-semibold transition">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Gray separator --}}
    <div class="h-px bg-gray-200"></div>

    {{-- Answer Review --}}
    <h2 class="text-base font-bold text-gray-700 uppercase tracking-widest">Pembahasan Jawaban</h2>

    @foreach($attempt->answers as $i => $answer)
        @php $question = $answer->question; @endphp
        <div class="bg-white border {{ $answer->is_correct ? 'border-green-400' : 'border-red-300' }} rounded-2xl p-5 space-y-3 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 w-7 h-7 rounded-full {{ $answer->is_correct ? 'bg-green-100 text-green-600' : 'bg-red-100 text-[#C50303]' }} flex items-center justify-center text-sm font-bold">
                    {{ $i + 1 }}
                </span>
                <p class="text-gray-800">{{ $question->body }}</p>
            </div>

            @if($question->type === 'mcq')
                <div class="pl-10 space-y-2">
                    @foreach($question->options as $option)
                        <div class="flex items-center gap-3 text-sm px-4 py-2 rounded-lg
                            {{ $option->is_correct ? 'bg-green-50 text-green-700 border border-green-300' : '' }}
                            {{ !$option->is_correct && $answer->selected_option_id === $option->id ? 'bg-red-50 text-red-700 border border-red-300' : '' }}
                            {{ !$option->is_correct && $answer->selected_option_id !== $option->id ? 'text-gray-600' : '' }}
                        ">
                            <span class="font-bold w-5">{{ $option->label }}</span>
                            <span>{{ $option->body }}</span>
                            @if($option->is_correct) <span class="ml-auto text-green-600 font-semibold">✓ Benar</span> @endif
                            @if(!$option->is_correct && $answer->selected_option_id === $option->id) <span class="ml-auto text-[#C50303] font-semibold">✗ Jawabanmu</span> @endif
                        </div>
                    @endforeach
                </div>
            @elseif($question->type === 'short_answer')
                <div class="pl-10 space-y-2 text-sm">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                        <p class="text-gray-400 text-xs mb-1">Jawabanmu:</p>
                        <p class="text-gray-800">{{ $answer->short_answer_text ?: '—' }}</p>
                    </div>
                    @if($question->keywords)
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="text-gray-400 text-xs">Kata kunci:</span>
                        @foreach($question->keywords as $kw)
                            @php $found = str_contains(strtolower($answer->short_answer_text ?? ''), strtolower($kw)); @endphp
                            <span class="px-2 py-0.5 rounded text-xs {{ $found ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                                {{ $kw }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            @endif

            <div class="pl-10 text-sm font-semibold {{ $answer->is_correct ? 'text-green-600' : 'text-[#C50303]' }}">
                +{{ number_format($answer->points_earned, 0) }} poin
            </div>
        </div>
    @endforeach

</div>
@endsection

@if(session('clear_quiz_storage_key'))
    @push('scripts')
        <script>
            localStorage.removeItem(@json(session('clear_quiz_storage_key')));
        </script>
    @endpush
@endif
