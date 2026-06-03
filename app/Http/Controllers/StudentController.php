<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use App\Services\ChapterAnalysisService;
use App\Services\GradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();

        $quizzes = Quiz::whereHas('classes', fn ($q) => $q->where('class_id', $user->class_id))
            ->where('is_published', true)
            ->whereDoesntHave('attempts', fn ($q) => $q->where('student_id', $user->id)->where('is_completed', true))
            ->with('subject')
            ->get();

        $completedAttempts = QuizAttempt::where('student_id', $user->id)
            ->where('is_completed', true)
            ->with('quiz.subject')
            ->latest('submitted_at')
            ->limit(10)
            ->get();

        return view('student.dashboard', compact('quizzes', 'completedAttempts'));
    }

    public function submitQuiz(Request $request, Quiz $quiz): RedirectResponse
    {
        $user = auth()->user();

        abort_unless($user && $user->hasRole('student'), 403);
        abort_unless(
            $quiz->is_published
            && $quiz->classes()->where('class_id', $user->class_id)->exists(),
            403
        );

        $completed = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->where('is_completed', true)
            ->first();

        if ($completed) {
            return redirect()->route('student.quiz.result', $completed);
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->id)
            ->where('is_completed', false)
            ->firstOrFail();

        $answers = collect($request->input('answers', []));
        $quiz->load(['questions.options']);
        $questionIds = $quiz->questions->pluck('id')->all();

        $answers = $answers->only($questionIds);

        DB::transaction(function () use ($attempt, $quiz, $answers): void {
            foreach ($quiz->questions as $question) {
                $rawValue = $answers->get($question->id);
                $payload = ['answered_at' => now()];

                if ($question->type === 'mcq') {
                    $selectedOptionId = filled($rawValue) ? (int) $rawValue : null;

                    if (
                        $selectedOptionId
                        && ! $question->options->contains('id', $selectedOptionId)
                    ) {
                        abort(422, 'Selected option does not belong to the question.');
                    }

                    $payload['selected_option_id'] = $selectedOptionId;
                    $payload['short_answer_text'] = null;
                } else {
                    $payload['selected_option_id'] = null;
                    $payload['short_answer_text'] = filled($rawValue) ? trim((string) $rawValue) : null;
                }

                StudentAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                    $payload
                );
            }

            GradingService::gradeAttempt($attempt->refresh()->load('answers.selectedOption'));
        });

        // Bust the dashboard cache so it reflects the newly completed quiz
        Cache::forget("student_dashboard:{$user->id}");

        return redirect()
            ->route('student.quiz.result', $attempt)
            ->with('clear_quiz_storage_key', "sentosa_quiz_attempt_{$attempt->id}");
    }

    public function result(QuizAttempt $attempt): View
    {
        abort_unless($attempt->student_id === auth()->id(), 403);

        $attempt->load('quiz.subject', 'answers.question.options', 'answers.selectedOption');

        return view('student.result', compact('attempt'));
    }

    public function stats(QuizAttempt $attempt): View
    {
        abort_unless($attempt->student_id === auth()->id(), 403);
        abort_unless($attempt->is_completed, 404);

        $analysis = ChapterAnalysisService::analyse($attempt);

        return view('student.quiz.stats', [
            'attempt' => $attempt->load('quiz.subject'),
            'analysis' => $analysis,
        ]);
    }
}
