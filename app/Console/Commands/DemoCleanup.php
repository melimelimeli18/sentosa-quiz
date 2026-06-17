<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DemoCleanup extends Command
{
    protected $signature = 'demo:cleanup';
    protected $description = 'Clean up old demo data (users, quizzes, attempts, questions) older than 24 hours';

    public function handle(): void
    {
        $cutoff = now()->subHours(24);

        $this->info("Cleaning up demo data created before {$cutoff}...");

        // 1. Find demo user IDs to clean up
        $demoUserIds = User::where('is_demo', true)
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        // 2. Find demo quiz IDs
        $demoQuizIds = Quiz::where('is_demo', true)
            ->where('created_at', '<', $cutoff)
            ->pluck('id');

        // 3. Delete demo quiz attempts (student answers will cascade delete)
        $deletedAttemptsCount = QuizAttempt::where('is_demo', true)
            ->where('created_at', '<', $cutoff)
            ->delete();

        // Also delete attempts by the demo users we are deleting
        $deletedUserAttemptsCount = QuizAttempt::whereIn('student_id', $demoUserIds)->delete();

        $this->info("Deleted " . ($deletedAttemptsCount + $deletedUserAttemptsCount) . " quiz attempts.");

        // 4. Detach classes for quizzes created by demo teachers
        DB::table('quiz_class')->whereIn('quiz_id', $demoQuizIds)->delete();

        // 5. Delete demo quizzes (cascades to quiz_questions and quiz_class because of DB constraint)
        $deletedQuizzesCount = Quiz::where('is_demo', true)
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$deletedQuizzesCount} quizzes.");

        // 6. Delete questions created by demo users (cascades to mcq_options)
        $deletedQuestionsCount = Question::whereIn('created_by', $demoUserIds)->delete();
        $this->info("Deleted {$deletedQuestionsCount} questions.");

        // 7. Delete demo users
        $deletedUsersCount = User::where('is_demo', true)
            ->where('created_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted {$deletedUsersCount} users.");

        $this->info("Demo cleanup complete.");
    }
}
