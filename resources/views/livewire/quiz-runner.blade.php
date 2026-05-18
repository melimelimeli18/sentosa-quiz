<div
    class="space-y-6"
    data-quiz-runner
    data-storage-key="sentosa_quiz_attempt_{{ $attempt->id }}"
    data-started-at="{{ $startedAtIso }}"
    data-duration-seconds="{{ $quiz->duration_minutes ? $quiz->duration_minutes * 60 : '' }}"
    data-total-questions="{{ $questions->count() }}"
    data-initial-answers='@json($answers)'
    oncontextmenu="return false;"
    oncopy="return false;"
    oncut="return false;"
    onpaste="return false;"
>
    <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 shadow-sm">
        <div>
            <p class="text-xs uppercase tracking-widest text-[#C50303] font-semibold mb-1">
                {{ $quiz->subject->name }} &middot; {{ $quiz->type === 'mid_term' ? 'UTS' : 'UAS' }}
            </p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->title }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $questions->count() }} soal &middot; Total {{ $quiz->total_points }} poin</p>
        </div>

        @if($remainingSeconds !== null)
            <div
                data-timer
                class="flex-shrink-0 text-center bg-white rounded-xl px-5 py-3 border border-gray-300 shadow-sm"
            >
                <p class="text-xs text-gray-400 mb-1">Sisa Waktu</p>
                <p data-timer-text class="text-2xl font-mono font-bold text-gray-800">
                    {{ gmdate('i:s', $remainingSeconds) }}
                </p>
            </div>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm sticky top-0 z-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
            <div>
                <p class="text-xs uppercase tracking-widest text-gray-400 font-semibold">Progress</p>
                <p class="text-sm font-semibold text-gray-700">
                    <span data-answered-count>0</span> dari {{ $questions->count() }} soal terjawab
                </p>
            </div>
            <button
                type="button"
                data-scroll-first-unanswered
                class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:border-[#C50303] hover:text-[#C50303] transition"
            >
                Ke soal kosong
            </button>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-1.5 mb-4">
            <div data-progress-bar class="bg-[#C50303] h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach($questions as $i => $q)
                <a
                    href="#question-{{ $q->id }}"
                    data-nav-question="{{ $q->id }}"
                    class="w-9 h-9 rounded-lg text-sm font-semibold transition flex items-center justify-center bg-white text-gray-500 border border-gray-200 hover:border-[#C50303] hover:text-[#C50303]"
                    aria-label="Soal {{ $i + 1 }}"
                >
                    {{ $i + 1 }}
                </a>
            @endforeach
        </div>
    </div>

    <form method="POST" action="{{ route('student.quiz.submit', $quiz) }}" data-quiz-form class="space-y-5">
        @csrf

        @foreach($questions as $i => $question)
            <section
                id="question-{{ $question->id }}"
                data-question-card
                data-question-id="{{ $question->id }}"
                class="bg-white border border-gray-200 rounded-xl p-6 space-y-5 shadow-sm scroll-mt-36"
            >
                <input type="hidden" name="answers[{{ $question->id }}]" data-answer-input="{{ $question->id }}" value="{{ $answers[$question->id] ?? '' }}">

                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-[#C50303] text-sm font-bold ring-1 ring-red-300">
                            {{ $i + 1 }}
                        </span>
                        <span class="text-xs uppercase tracking-widest text-gray-400">
                            {{ $question->type === 'mcq' ? 'Pilihan Ganda' : 'Jawaban Singkat' }}
                        </span>
                    </div>
                    <span data-question-status class="text-xs font-semibold text-gray-400">Belum dijawab</span>
                </div>

                <p class="text-lg text-gray-800 leading-relaxed">{{ $question->body }}</p>

                @if($question->type === 'mcq')
                    <div class="space-y-3">
                        @foreach($question->options as $option)
                            <label
                                data-option-label
                                class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition bg-gray-50 border-gray-200 text-gray-700 hover:border-[#C50303] hover:bg-red-50/40"
                            >
                                <input
                                    type="radio"
                                    name="display_q{{ $question->id }}"
                                    value="{{ $option->id }}"
                                    data-answer-control
                                    data-question-id="{{ $question->id }}"
                                    class="hidden"
                                    {{ ($answers[$question->id] ?? null) == $option->id ? 'checked' : '' }}
                                />
                                <span
                                    data-option-badge
                                    class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 font-bold text-sm"
                                >
                                    {{ $option->label }}
                                </span>
                                <span>{{ $option->body }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($question->type === 'short_answer')
                    <textarea
                        rows="4"
                        data-answer-control
                        data-question-id="{{ $question->id }}"
                        placeholder="Tulis jawaban kamu di sini..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#C50303] focus:border-[#C50303] transition resize-none"
                    >{{ $answers[$question->id] ?? '' }}</textarea>
                @endif
            </section>
        @endforeach

        <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
            <p class="text-sm text-gray-500">
                Jawaban disimpan di browser ini sampai kamu menekan kumpulkan.
            </p>
            <button
                type="submit"
                data-submit-button
                class="px-6 py-2.5 rounded-xl bg-green-600 text-white hover:bg-green-500 transition font-bold shadow-sm"
            >
                Kumpulkan
            </button>
        </div>
    </form>

    @script
    <script>
        (() => {
            const root = document.querySelector('[data-quiz-runner]');
            if (!root || root.dataset.initialized === 'true') return;
            root.dataset.initialized = 'true';

            const storageKey = root.dataset.storageKey;
            const form = root.querySelector('[data-quiz-form]');
            const totalQuestions = Number(root.dataset.totalQuestions || 0);
            const initialAnswers = JSON.parse(root.dataset.initialAnswers || '{}');
            const answers = { ...initialAnswers, ...readStoredAnswers() };
            const questionIds = [...root.querySelectorAll('[data-question-card]')]
                .map((card) => card.dataset.questionId);
            let submitting = false;

            function readStoredAnswers() {
                try {
                    return JSON.parse(localStorage.getItem(storageKey) || '{}');
                } catch {
                    return {};
                }
            }

            function normalizeValue(value) {
                return value === null || value === undefined ? '' : String(value);
            }

            function isAnswered(value) {
                return normalizeValue(value).trim() !== '';
            }

            function persist() {
                localStorage.setItem(storageKey, JSON.stringify(answers));
            }

            function syncControls() {
                root.querySelectorAll('[data-answer-input]').forEach((input) => {
                    input.value = normalizeValue(answers[input.dataset.answerInput]);
                });

                root.querySelectorAll('[data-answer-control]').forEach((control) => {
                    const value = normalizeValue(answers[control.dataset.questionId]);

                    if (control.type === 'radio') {
                        control.checked = value !== '' && control.value === value;
                    } else {
                        control.value = value;
                    }
                });

                root.querySelectorAll('[data-option-label]').forEach((label) => {
                    const radio = label.querySelector('input[type="radio"]');
                    const badge = label.querySelector('[data-option-badge]');
                    const checked = radio.checked;

                    label.classList.toggle('bg-red-50', checked);
                    label.classList.toggle('border-[#C50303]', checked);
                    label.classList.toggle('text-gray-900', checked);
                    label.classList.toggle('shadow-sm', checked);
                    label.classList.toggle('bg-gray-50', !checked);
                    label.classList.toggle('border-gray-200', !checked);
                    label.classList.toggle('text-gray-700', !checked);

                    badge.classList.toggle('bg-[#C50303]', checked);
                    badge.classList.toggle('text-white', checked);
                    badge.classList.toggle('bg-gray-200', !checked);
                    badge.classList.toggle('text-gray-600', !checked);
                });

                updateProgress();
            }

            function updateProgress() {
                const answeredIds = questionIds.filter((id) => isAnswered(answers[id])).length;
                const percent = totalQuestions > 0 ? Math.round((answeredIds / totalQuestions) * 100) : 0;

                root.querySelector('[data-answered-count]').textContent = answeredIds;
                root.querySelector('[data-progress-bar]').style.width = `${percent}%`;

                root.querySelectorAll('[data-question-card]').forEach((card) => {
                    const id = card.dataset.questionId;
                    const answered = isAnswered(answers[id]);
                    const status = card.querySelector('[data-question-status]');
                    const nav = root.querySelector(`[data-nav-question="${id}"]`);

                    status.textContent = answered ? 'Terjawab' : 'Belum dijawab';
                    status.classList.toggle('text-green-600', answered);
                    status.classList.toggle('text-gray-400', !answered);

                    nav.classList.toggle('bg-green-100', answered);
                    nav.classList.toggle('text-green-700', answered);
                    nav.classList.toggle('border-green-400', answered);
                    nav.classList.toggle('bg-white', !answered);
                    nav.classList.toggle('text-gray-500', !answered);
                    nav.classList.toggle('border-gray-200', !answered);
                });
            }

            root.querySelectorAll('[data-answer-control]').forEach((control) => {
                const eventName = control.type === 'radio' ? 'change' : 'input';

                control.addEventListener(eventName, () => {
                    answers[control.dataset.questionId] = control.value;
                    persist();
                    syncControls();
                });
            });

            root.querySelector('[data-scroll-first-unanswered]')?.addEventListener('click', () => {
                const card = [...root.querySelectorAll('[data-question-card]')]
                    .find((candidate) => !isAnswered(answers[candidate.dataset.questionId]));

                (card || root.querySelector('[data-question-card]'))?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start',
                });
            });

            form.addEventListener('submit', (event) => {
                if (submitting) return;

                const unanswered = totalQuestions - questionIds.filter((id) => isAnswered(answers[id])).length;
                const message = unanswered > 0
                    ? `Masih ada ${unanswered} soal yang belum dijawab. Kumpulkan sekarang?`
                    : 'Apakah kamu yakin ingin mengumpulkan jawaban? Kamu tidak bisa mengubah jawaban setelah submit.';

                if (!window.confirm(message)) {
                    event.preventDefault();
                    return;
                }

                submitting = true;
                root.querySelector('[data-submit-button]').disabled = true;
            });

            document.addEventListener('keydown', (event) => {
                if (event.ctrlKey && ['c', 'v', 'x'].includes(event.key.toLowerCase())) {
                    event.preventDefault();
                }
            });

            function startTimer() {
                const durationSeconds = Number(root.dataset.durationSeconds || 0);
                const startedAt = Date.parse(root.dataset.startedAt || '');
                const timer = root.querySelector('[data-timer]');
                const timerText = root.querySelector('[data-timer-text]');

                if (!durationSeconds || !startedAt || !timer || !timerText) return;

                const render = () => {
                    const elapsed = Math.floor((Date.now() - startedAt) / 1000);
                    const remaining = Math.max(0, durationSeconds - elapsed);
                    const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
                    const seconds = String(remaining % 60).padStart(2, '0');

                    timerText.textContent = `${minutes}:${seconds}`;
                    timer.classList.toggle('border-[#C50303]', remaining < 60);
                    timer.classList.toggle('border-gray-300', remaining >= 60);
                    timer.classList.toggle('animate-pulse', remaining < 60);
                    timerText.classList.toggle('text-[#C50303]', remaining < 60);
                    timerText.classList.toggle('text-gray-800', remaining >= 60);

                    if (remaining <= 0 && !submitting) {
                        submitting = true;
                        form.submit();
                    }
                };

                render();
                setInterval(render, 1000);
            }

            syncControls();
            persist();
            startTimer();
        })();
    </script>
    @endscript
</div>
