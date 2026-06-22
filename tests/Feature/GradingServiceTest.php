<?php

namespace Tests\Feature;

use App\Models\McqOption;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use App\Models\Subject;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingServiceTest extends TestCase
{
    use RefreshDatabase;

    private Quiz $quiz;
    private User $student;
    private QuizAttempt $attempt;

    protected function setUp(): void
    {
        parent::setUp();

        $subject = Subject::create(['name' => 'Biologi']);
        $teacher = User::create([
            'name'     => 'Teacher',
            'email'    => 'teacher@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->quiz = Quiz::create([
            'title'            => 'Test Quiz',
            'subject_id'       => $subject->id,
            'teacher_id'       => $teacher->id,
            'type'             => 'mid_term',
            'total_points'     => 100,
        ]);

        $this->student = User::create([
            'name'     => 'Student',
            'email'    => 'student@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->attempt = QuizAttempt::create([
            'quiz_id'    => $this->quiz->id,
            'student_id' => $this->student->id,
        ]);
    }

    private function makeQuestion(array $attrs = []): Question
    {
        return Question::create(array_merge([
            'body'       => 'Sample question?',
            'type'       => 'mcq',
            'subject_id' => $this->quiz->subject_id,
            'created_by' => $this->quiz->teacher_id,
        ], $attrs));
    }

    public function test_grades_perfect_mcq_attempt(): void
    {
        $question = $this->makeQuestion();
        $correct  = McqOption::create(['question_id' => $question->id, 'label' => 'A', 'body' => '4', 'is_correct' => true]);
        McqOption::create(['question_id' => $question->id, 'label' => 'B', 'body' => '5', 'is_correct' => false]);
        $this->quiz->questions()->attach($question->id, ['order' => 1]);

        StudentAnswer::create([
            'attempt_id'         => $this->attempt->id,
            'question_id'        => $question->id,
            'selected_option_id' => $correct->id,
        ]);

        GradingService::gradeAttempt($this->attempt->fresh());

        $this->attempt->refresh();
        $this->assertEquals(100.0, $this->attempt->score);
        $this->assertTrue($this->attempt->is_completed);
    }

    public function test_grades_wrong_mcq_as_zero(): void
    {
        $question = $this->makeQuestion();
        McqOption::create(['question_id' => $question->id, 'label' => 'A', 'body' => '4', 'is_correct' => true]);
        $wrong = McqOption::create(['question_id' => $question->id, 'label' => 'B', 'body' => '5', 'is_correct' => false]);
        $this->quiz->questions()->attach($question->id, ['order' => 1]);

        StudentAnswer::create([
            'attempt_id'         => $this->attempt->id,
            'question_id'        => $question->id,
            'selected_option_id' => $wrong->id,
        ]);

        GradingService::gradeAttempt($this->attempt->fresh());

        $this->attempt->refresh();
        $this->assertEquals(0.0, $this->attempt->score);
        $this->assertTrue($this->attempt->is_completed);
    }

    public function test_grades_short_answer_by_keyword_threshold(): void
    {
        $question = $this->makeQuestion([
            'type'              => 'short_answer',
            'keywords'          => ['fotosintesis', 'klorofil', 'cahaya'],
            'keyword_threshold' => 2,
        ]);
        $this->quiz->questions()->attach($question->id, ['order' => 1]);

        StudentAnswer::create([
            'attempt_id'        => $this->attempt->id,
            'question_id'       => $question->id,
            'short_answer_text' => 'Fotosintesis adalah proses dengan klorofil.',
        ]);

        GradingService::gradeAttempt($this->attempt->fresh());

        $this->attempt->refresh();
        $this->assertEquals(100.0, $this->attempt->score);
    }

    public function test_short_answer_fails_below_threshold(): void
    {
        $question = $this->makeQuestion([
            'type'              => 'short_answer',
            'keywords'          => ['fotosintesis', 'klorofil', 'cahaya'],
            'keyword_threshold' => 2,
        ]);
        $this->quiz->questions()->attach($question->id, ['order' => 1]);

        StudentAnswer::create([
            'attempt_id'        => $this->attempt->id,
            'question_id'       => $question->id,
            'short_answer_text' => 'Tidak tahu.',
        ]);

        GradingService::gradeAttempt($this->attempt->fresh());

        $this->attempt->refresh();
        $this->assertEquals(0.0, $this->attempt->score);
    }

    public function test_quiz_with_no_questions_scores_zero(): void
    {
        GradingService::gradeAttempt($this->attempt->fresh());

        $this->attempt->refresh();
        $this->assertEquals(0.0, $this->attempt->score);
        $this->assertTrue($this->attempt->is_completed);
    }
}
