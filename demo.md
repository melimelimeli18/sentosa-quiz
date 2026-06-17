# Demo Account System

## Goal

Allow external visitors (recruiters, unknown users) to try SentosaQuiz as a teacher or student without registration, via dedicated demo routes. Demo data is isolated, randomized per session to avoid collision, and auto-cleaned.

## Stack constraints

Laravel, Filament v3, Livewire v3, PostgreSQL (Neon), Spatie Laravel Permission. No Vue/Axios.

---

## 1. Entry points

Two buttons/links on the public landing page:

- `GET /demo-login/teacher`
- `GET /demo-login/student`

Both routes:

- Must be disabled outside a `demo`-flagged environment: guard with `if (!config('app.demo_enabled')) abort(404);` (add `DEMO_ENABLED` to `.env`, read into `config/app.php`).
- On hit, create a fresh randomized demo user, log them in via `Auth::login()`, redirect to the appropriate dashboard.
- No registration form, no password entry. Fully automatic.

---

## 2. Database changes

Add to `users` table (migration):

```php
$table->boolean('is_demo')->default(false)->index();
```

Add to `quiz_attempts` table (migration):

```php
$table->boolean('is_demo')->default(false)->index();
```

Add to `quizzes` table (migration):

```php
$table->boolean('is_demo')->default(false)->index();
```

Rationale: cleanup and analytics filtering must operate on these flags directly, not by joining through `users.is_demo`, so each relevant table needs its own flag. This avoids missed exclusions in `ChapterAnalysisService` and admin demographics widgets.

**Critical:** every existing analytics query (`ChapterAnalysisService`, admin demographics widgets, per-class/per-subject averages) must add `where('is_demo', false)` (or equivalent) to exclude demo activity from real school statistics. Audit all aggregate queries as part of this implementation.

---

## 3. Fixed demo class

One real `classes` row, created once via seeder, never deleted, never used by real students:

- Name: e.g. `"Demo Class"`.
- Flag: add `is_demo` boolean to `classes` table too, default `false`, set `true` only for this row. Exclude from any real class listing/dropdown in `SubjectResource` and class selection UIs.
- All demo students join this class only. No dashboard exposes other students in this class (already true per current design — student dashboards show only own data, not classmates).

---

## 4. Demo teacher flow

### 4.1 Account creation (on `/demo-login/teacher` hit)

- Generate random unique identifier, e.g. `Str::random(8)`.
- Create user: `email = "guru-demo-{random}@demo.local"`, `name = "Guru Demo"`, random password (irrelevant, login is automatic), `is_demo = true`.
- Assign `teacher` role via Spatie (`assignRole('teacher')`).
- Log in immediately, redirect to Filament admin panel teacher dashboard.

### 4.2 Quiz creation (demo-scoped)

- Teacher demo can create a new quiz via the existing Excel import flow (`maatwebsite/excel`), using the prepared template.
- New quiz is saved with `is_demo = true` and `created_by = {demo_teacher_id}`.
- **Quiz visibility for demo teachers must be scoped to `created_by = auth()->id()` when `auth()->user()->is_demo` is true.** Filament Resource query for quizzes must add this filter conditionally — demo teachers never see quizzes created by other demo teachers (past or concurrent sessions).
- Demo teacher can assign the quiz only to the fixed Demo Class (restrict class dropdown to that one class when `is_demo`).
- This quiz is NOT shown to demo students. Demo students always see the pre-prepared fixed demo quiz (see section 5). The teacher-created quiz is a self-contained preview only, viewed by the teacher who created it (e.g., "quiz created successfully, here's the question list/preview").

---

## 5. Demo student flow

### 5.1 Account creation (on `/demo-login/student` hit)

- Generate random unique identifier.
- Create user: `email = "siswa-demo-{random}@demo.local"`, `name = "Siswa Demo"`, random password, `is_demo = true`.
- Assign `student` role via Spatie.
- Auto-join the fixed Demo Class (insert into class-student pivot).
- Log in immediately, redirect to `/student/dashboard`.

### 5.2 Quiz access

- Student demo dashboard shows exactly one fixed, pre-prepared quiz (`is_demo = false`, permanent, never deleted — created once via main `DemoSeeder`, not per-session). This guarantees a consistent demo experience independent of whether any teacher demo session is active.
- Student demo can attempt this quiz. The resulting `quiz_attempts` row is created with `is_demo = true`.
- Student-facing result/analytics views (profile card, per-quiz stats, `ChapterAnsalysisService` output) must filter `where('user_id', auth()->id())` — already implied by current design, but verify no query aggregates across all attempts on the fixed demo quiz (which would mix concurrent demo students' results).

---

## 6. Cleanup strategy (two layers)

### 6.1 Primary layer — scheduled, time-based (authoritative)

Artisan command `demo:cleanup`, registered in `routes/console.php` (or `Console/Kernel.php` for older Laravel) to run hourly via Laravel Scheduler:

```php
Schedule::command('demo:cleanup')->hourly();
```

Command logic:

```php
class DemoCleanup extends Command
{
    protected $signature = 'demo:cleanup';

    public function handle(): void
    {
        $cutoff = now()->subHours(24);

        // Delete demo quiz attempts first (children before parents)
        QuizAttempt::where('is_demo', true)
            ->where('created_at', '<', $cutoff)
            ->delete();

        // Delete demo quizzes created by teachers (cascades to their questions if FK cascade is set)
        Quiz::where('is_demo', true)
            ->where('created_at', '<', $cutoff)
            ->delete();

        // Delete demo users (cascades to class-pivot rows if FK cascade is set; verify manually otherwise)
        User::where('is_demo', true)
            ->where('created_at', '<', $cutoff)
            ->each(function (User $user) {
                $user->classes()->detach(); // explicit detach if no cascade
                $user->delete();
            });
    }
}
```

**Verify FK cascade behavior** on `quiz_attempts.user_id`, `quiz_attempts.quiz_id`, and the class-student pivot before relying on implicit cascade. If not cascading, delete explicitly in the order: attempts → quiz questions (if demo-quiz-specific) → quizzes → pivot rows → users.

This layer is independent of logout behavior and guarantees no permanent data accumulation, since closing a browser tab triggers no application event.

### 6.2 Secondary layer — event-based (optimization only, not authoritative)

Hook into Laravel's `Logout` event:

```php
Event::listen(Logout::class, function (Logout $event) {
    if ($event->user?->is_demo) {
        // immediate cleanup for this user's attempts/quizzes, then the user itself
        QuizAttempt::where('user_id', $event->user->id)->where('is_demo', true)->delete();
        Quiz::where('created_by', $event->user->id)->where('is_demo', true)->delete();
        $event->user->classes()->detach();
        $event->user->delete();
    }
});
```

This reduces database clutter for users who explicitly log out, but must never be treated as the cleanup guarantee — most demo users will close the tab without logging out, so the scheduled job (6.1) remains mandatory.

---

## 7. Seeder structure

`DemoSeeder` (separate from main `DatabaseSeeder`, run once at deploy time, idempotent):

```php
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed demo class (only if not already present)
        $demoClass = ClassModel::firstOrCreate(
            ['name' => 'Demo Class', 'is_demo' => true],
            [/* other required fields */]
        );

        // Fixed demo quiz for students (only if not already present)
        $demoQuiz = Quiz::firstOrCreate(
            ['title' => 'Demo Quiz - Sample Subject', 'is_demo' => false],
            [/* questions, chapter, type, etc. — must satisfy (subject_id, type) unique constraint */]
        );
        // Assign $demoQuiz to $demoClass if not already assigned.
    }
}
```

This seeder does NOT create teacher/student demo accounts — those are generated per-request by the `/demo-login/*` routes, not pre-seeded. Run via `php artisan db:seed --class=DemoSeeder` once after migration, idempotent on repeat runs (safe to re-run without duplicating the fixed class/quiz).

---

## 8. Routes & Controller

```php
// routes/web.php
Route::middleware('demo.enabled')->group(function () {
    Route::get('/demo-login/teacher', [DemoController::class, 'loginAsTeacher'])->name('demo.teacher');
    Route::get('/demo-login/student', [DemoController::class, 'loginAsStudent'])->name('demo.student');
});
```

Create middleware `demo.enabled` to abort 404 if `!config('app.demo_enabled')`, rather than inlining the check in every method.

```php
class DemoController extends Controller
{
    public function loginAsTeacher()
    {
        $user = $this->createDemoUser('teacher');
        Auth::login($user);
        return redirect('/admin');
    }

    public function loginAsStudent()
    {
        $user = $this->createDemoUser('student');
        $this->joinFixedDemoClass($user);
        Auth::login($user);
        return redirect('/student/dashboard');
    }

    private function createDemoUser(string $role): User
    {
        $suffix = Str::random(8);
        $user = User::create([
            'name' => $role === 'teacher' ? 'Guru Demo' : 'Siswa Demo',
            'email' => ($role === 'teacher' ? 'guru-demo-' : 'siswa-demo-') . $suffix . '@demo.local',
            'password' => bcrypt(Str::random(16)),
            'is_demo' => true,
        ]);
        $user->assignRole($role);
        return $user;
    }

    private function joinFixedDemoClass(User $user): void
    {
        $demoClass = ClassModel::where('is_demo', true)->first();
        $user->classes()->attach($demoClass->id);
    }
}
```

---

## 9. Required query filters (audit checklist)

Every item below MUST exclude `is_demo = true` records, except where the demo flow itself depends on them:

- [ ] `ChapterAnalysisService` — exclude `is_demo` attempts from real chapter analysis.
- [ ] Admin demographics widgets (school-wide stats, per-class/per-subject averages, bottom-5 weakest chapters) — exclude `is_demo` users/attempts.
- [ ] Class listing dropdowns (anywhere a teacher/admin selects a class for non-demo purposes) — exclude the fixed Demo Class.
- [ ] Quiz listing for teachers — when `auth()->user()->is_demo`, scope to `created_by = auth()->id()`; when not demo, exclude `is_demo = true` quizzes entirely.
- [ ] Student dashboard quiz list — demo students see only the fixed demo quiz, not any teacher-demo-created quiz.

---

## 10. Out of scope / explicitly rejected designs

- Per-class demo isolation (separate class per student session) — rejected; single fixed Demo Class is sufficient since students cannot see classmates.
- Cleanup triggered solely by logout — rejected as sole mechanism; tab-close produces no event. Scheduled job is mandatory.
- Teacher-created demo quizzes visible to demo students — rejected; breaks session independence and risks showing stale/concurrent teachers' quizzes to unrelated student sessions.
- `migrate:fresh` style resets — rejected; would also wipe non-demo production data if sharing a database.
