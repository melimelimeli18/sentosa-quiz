<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\McqOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or get Demo Class
        $demoClass = SchoolClass::firstOrCreate(
            ['name' => 'Demo Class', 'is_demo' => true],
            ['grade' => 'XII']
        );

        // 2. Create or get Subject
        $subject = Subject::firstOrCreate(
            ['name' => 'Biologi']
        );

        // 3. Create or get Chapter
        $chapter = Chapter::firstOrCreate(
            ['subject_id' => $subject->id, 'order' => 1, 'name' => 'Fotosintesis']
        );

        // 4. Create fixed non-demo teacher to own the permanent demo quiz
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $fixedTeacher = User::firstOrCreate(
            ['email' => 'teacher-fixed@sentosa.com'],
            [
                'name' => 'Guru Utama',
                'password' => bcrypt('password123'),
                'is_demo' => false
            ]
        );
        $fixedTeacher->assignRole($teacherRole);

        // 5. Create fixed demo quiz for students (is_demo = false, permanent)
        $demoQuiz = Quiz::firstOrCreate(
            ['title' => 'Demo Quiz - Sample Subject', 'is_demo' => false],
            [
                'subject_id' => $subject->id,
                'teacher_id' => $fixedTeacher->id,
                'type' => 'mid_term',
                'duration_minutes' => 30,
                'is_published' => true,
                'total_points' => 100,
                'allowed_attempts' => 1,
            ]
        );

        // 6. Assign demoQuiz to demoClass
        $demoQuiz->classes()->syncWithoutDetaching([$demoClass->id => [
            'assigned_by' => $fixedTeacher->id,
            'assigned_at' => now()
        ]]);

        // 7. Create sample questions if not exists
        if ($demoQuiz->questions()->count() === 0) {
            // Question 1
            $q1 = Question::create([
                'body' => 'Di manakah fotosintesis terjadi pada sel tumbuhan?',
                'type' => 'mcq',
                'subject_id' => $subject->id,
                'chapter_id' => $chapter->id,
                'created_by' => $fixedTeacher->id,
                'source' => 'manual',
                'is_public' => true,
            ]);

            $options1 = [
                ['label' => 'A', 'body' => 'Mitokondria', 'is_correct' => false],
                ['label' => 'B', 'body' => 'Kloroplas', 'is_correct' => true],
                ['label' => 'C', 'body' => 'Ribosom', 'is_correct' => false],
                ['label' => 'D', 'body' => 'Nukleus', 'is_correct' => false],
            ];

            foreach ($options1 as $opt) {
                McqOption::create(array_merge($opt, ['question_id' => $q1->id]));
            }

            // Question 2
            $q2 = Question::create([
                'body' => 'Faktor apa yang tidak mempengaruhi laju fotosintesis?',
                'type' => 'mcq',
                'subject_id' => $subject->id,
                'chapter_id' => $chapter->id,
                'created_by' => $fixedTeacher->id,
                'source' => 'manual',
                'is_public' => true,
            ]);

            $options2 = [
                ['label' => 'A', 'body' => 'Cahaya matahari', 'is_correct' => false],
                ['label' => 'B', 'body' => 'Karbondioksida', 'is_correct' => false],
                ['label' => 'C', 'body' => 'Air', 'is_correct' => false],
                ['label' => 'D', 'body' => 'Oksigen', 'is_correct' => true],
            ];

            foreach ($options2 as $opt) {
                McqOption::create(array_merge($opt, ['question_id' => $q2->id]));
            }

            // Attach to Quiz
            $demoQuiz->questions()->attach([
                $q1->id => ['order' => 1],
                $q2->id => ['order' => 2],
            ]);
        }
    }
}
