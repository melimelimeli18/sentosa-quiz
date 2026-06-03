<?php

use App\Http\Controllers\StudentController;
use App\Livewire\QuizRunner;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        if (auth()->user()->hasRole(['admin', 'teacher'])) {
            return redirect('/admin');
        }

        return redirect()->route('student.dashboard');
    })->name('dashboard');
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/quiz/{quiz}', QuizRunner::class)->name('quiz.take');
    Route::post('/quiz/{quiz}/submit', [StudentController::class, 'submitQuiz'])->name('quiz.submit');
    Route::get('/quiz/{attempt}/result', [StudentController::class, 'result'])->name('quiz.result');
    Route::get('/quiz/{attempt}/stats', [StudentController::class, 'stats'])->name('quiz.stats');
});

require __DIR__.'/settings.php';
