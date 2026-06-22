<?php

namespace App\Services;

use App\Models\QuizAttempt;
use App\Models\StudentAnswer;

class GradingService
{
    public static function gradeAttempt(QuizAttempt $attempt): void
    {
        // ponytail: eager load relations to prevent N+1 queries
        $attempt->load(['answers.selectedOption', 'quiz.questions.options']);
        $quiz = $attempt->quiz;
        $questionCount = $quiz->questions->count();

        if ($questionCount === 0) {
            $attempt->update([
                'score'        => 0,
                'is_completed' => true,
                'submitted_at' => now(),
            ]);
            return;
        }

        $pointsPerQuestion = $quiz->total_points / $questionCount;
        $totalEarned = 0;
        $answersToUpdate = [];

        foreach ($attempt->answers as $answer) {
            $question  = $quiz->questions->find($answer->question_id);
            $isCorrect = false;

            if (! $question) {
                continue;
            }

            if ($question->type === 'mcq') {
                $isCorrect = optional($answer->selectedOption)->is_correct ?? false;

            } elseif ($question->type === 'short_answer') {
                $keywords  = $question->keywords ?? [];
                $threshold = $question->keyword_threshold ?? 1;
                $text      = strtolower($answer->short_answer_text ?? '');

                $matched = collect($keywords)
                    ->filter(fn ($kw) => str_contains($text, strtolower($kw)))
                    ->count();

                $isCorrect = $matched >= $threshold;
            }

            $earned       = $isCorrect ? $pointsPerQuestion : 0;
            $totalEarned += $earned;

            // ponytail: collect updates for batching
            $answersToUpdate[] = [
                'id'                 => $answer->id,
                'attempt_id'         => $answer->attempt_id,
                'question_id'        => $answer->question_id,
                'selected_option_id' => $answer->selected_option_id,
                'short_answer_text'  => $answer->short_answer_text,
                'is_correct'         => $isCorrect,
                'points_earned'      => $earned,
                'answered_at'        => $answer->answered_at,
            ];
        }

        // ponytail: batch update student answers to avoid N+1 update queries
        if (!empty($answersToUpdate)) {
            StudentAnswer::upsert($answersToUpdate, ['id'], ['is_correct', 'points_earned']);
        }

        $attempt->update([
            'score'        => round($totalEarned, 2),
            'is_completed' => true,
            'submitted_at' => now(),
        ]);
    }
}