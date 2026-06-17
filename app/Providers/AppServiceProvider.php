<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            function (\Illuminate\Auth\Events\Logout $event) {
                $user = $event->user;
                if ($user && $user->is_demo) {
                    \App\Models\QuizAttempt::where('student_id', $user->id)->delete();
                    
                    $demoQuizIds = \App\Models\Quiz::where('teacher_id', $user->id)->pluck('id');
                    \Illuminate\Support\Facades\DB::table('quiz_class')->whereIn('quiz_id', $demoQuizIds)->delete();
                    \App\Models\Quiz::where('teacher_id', $user->id)->delete();
                    
                    \App\Models\Question::where('created_by', $user->id)->delete();
                    
                    $user->delete();
                }
            }
        );
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
