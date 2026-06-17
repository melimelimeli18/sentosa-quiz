<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Collection;
use Livewire\Component;

class QuizRunner extends Component
{
    public QuizAttempt $attempt;

    public Quiz $quiz;

    public Collection $questions;

    public array $answers = [];

    public ?int $remainingSeconds = null;

    public ?string $startedAtIso = null;

    public function mount(Quiz $quiz): void
    {
        // Prevent students without a class or access
        abort_unless(auth()->check(), 403);
        abort_unless(
            $quiz->is_published
            && $quiz->classes()->where('class_id', auth()->user()->class_id)->exists(),
            403
        );

        // Prevent re-taking if already completed
        $completed = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', auth()->id())
            ->where('is_completed', true)
            ->first();

        if ($completed) {
            $this->redirect(route('student.quiz.result', $completed));

            return;
        }

        // Resume existing incomplete attempt OR create new
        $existing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', auth()->id())
            ->where('is_completed', false)
            ->with('answers')
            ->first();

        $this->attempt = $existing ?? QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => auth()->id(),
            'is_demo' => auth()->check() && auth()->user()->is_demo,
        ]);

        $this->quiz = $quiz->load('subject');
        $this->questions = $quiz->questions()
            ->with(['options' => fn ($query) => $query->orderBy('label')])
            ->orderByPivot('order')
            ->get();

        // Pre-fill answers from database (resume support)
        foreach ($this->attempt->answers()->get() as $answer) {
            $this->answers[$answer->question_id] = $answer->selected_option_id
                ?? $answer->short_answer_text;
        }

        // Init timer if quiz has duration
        if ($quiz->duration_minutes) {
            $elapsed = now()->diffInSeconds($this->attempt->started_at);
            $this->remainingSeconds = max(0, ($quiz->duration_minutes * 60) - $elapsed);
        }

        $this->startedAtIso = $this->attempt->started_at?->toIso8601String();
    }

    public function render()
    {
        return view('livewire.quiz-runner')->layout('layouts.student');
    }
}
