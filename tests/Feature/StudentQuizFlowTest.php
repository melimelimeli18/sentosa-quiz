<?php

namespace Tests\Feature;

use App\Models\McqOption;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentQuizFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_open_quiz_and_see_all_questions_on_one_page(): void
    {
        [$student, $quiz, $questions] = $this->makeQuiz();

        $response = $this->actingAs($student)->get(route('student.quiz.take', $quiz));

        $response->assertOk();
        $response->assertSee('Question one');
        $response->assertSee('Question two');
        $response->assertSee('Kumpulkan');
        $this->assertDatabaseHas('quiz_attempts', [
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'is_completed' => false,
        ]);
        $this->assertCount(2, $questions);
    }

    public function test_final_submit_saves_answers_grades_attempt_and_redirects_to_result(): void
    {
        [$student, $quiz, $questions] = $this->makeQuiz();
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
        ]);
        $correctOption = $questions[0]->options()->where('is_correct', true)->first();

        $response = $this->actingAs($student)->post(route('student.quiz.submit', $quiz), [
            'answers' => [
                $questions[0]->id => $correctOption->id,
                $questions[1]->id => 'photosynthesis chlorophyll',
            ],
        ]);

        $response->assertRedirect(route('student.quiz.result', $attempt));
        $attempt->refresh();

        $this->assertTrue($attempt->is_completed);
        $this->assertNotNull($attempt->submitted_at);
        $this->assertEquals(100, (float) $attempt->score);
        $this->assertDatabaseHas('student_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $questions[0]->id,
            'selected_option_id' => $correctOption->id,
            'is_correct' => true,
        ]);
        $this->assertDatabaseHas('student_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $questions[1]->id,
            'short_answer_text' => 'photosynthesis chlorophyll',
            'is_correct' => true,
        ]);
    }

    public function test_completed_attempts_are_not_resubmitted(): void
    {
        [$student, $quiz, $questions] = $this->makeQuiz();
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'is_completed' => true,
            'submitted_at' => now(),
            'score' => 50,
        ]);

        $response = $this->actingAs($student)->post(route('student.quiz.submit', $quiz), [
            'answers' => [
                $questions[0]->id => $questions[0]->options()->where('is_correct', true)->value('id'),
            ],
        ]);

        $response->assertRedirect(route('student.quiz.result', $attempt));
        $this->assertDatabaseMissing('student_answers', [
            'attempt_id' => $attempt->id,
        ]);
    }

    public function test_submit_rejects_option_ids_that_do_not_belong_to_the_question(): void
    {
        [$student, $quiz, $questions] = $this->makeQuiz();
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
        ]);
        $wrongQuestionOption = $questions[1]->options()->create([
            'label' => 'A',
            'body' => 'Wrong question option',
            'is_correct' => true,
        ]);

        $response = $this->actingAs($student)->post(route('student.quiz.submit', $quiz), [
            'answers' => [
                $questions[0]->id => $wrongQuestionOption->id,
            ],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('student_answers', 0);
    }

    private function makeQuiz(): array
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

        $class = SchoolClass::create([
            'name' => 'XII IPA 1',
            'grade' => 'XII',
        ]);
        $student = User::factory()->create(['class_id' => $class->id]);
        $student->assignRole('student');
        $teacher = User::factory()->create();
        $teacher->assignRole('teacher');
        $subject = Subject::create([
            'name' => 'Biology',
        ]);
        $quiz = Quiz::create([
            'title' => 'Fast Quiz',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'type' => 'mid_term',
            'is_published' => true,
            'total_points' => 100,
        ]);
        $quiz->classes()->attach($class->id, [
            'assigned_by' => $teacher->id,
            'assigned_at' => now(),
        ]);

        $firstQuestion = Question::create([
            'body' => 'Question one',
            'type' => 'mcq',
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
        ]);
        McqOption::create([
            'question_id' => $firstQuestion->id,
            'label' => 'A',
            'body' => 'Correct',
            'is_correct' => true,
        ]);
        McqOption::create([
            'question_id' => $firstQuestion->id,
            'label' => 'B',
            'body' => 'Incorrect',
            'is_correct' => false,
        ]);

        $secondQuestion = Question::create([
            'body' => 'Question two',
            'type' => 'short_answer',
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'keywords' => ['photosynthesis', 'chlorophyll'],
            'keyword_threshold' => 2,
        ]);

        $quiz->questions()->attach($firstQuestion->id, ['order' => 1]);
        $quiz->questions()->attach($secondQuestion->id, ['order' => 2]);

        return [$student, $quiz, collect([$firstQuestion, $secondQuestion])];
    }
}
