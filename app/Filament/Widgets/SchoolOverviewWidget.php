<?php

namespace App\Filament\Widgets;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SchoolOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', User::role('student')->where('is_demo', false)->count())
                ->description('Registered students')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            
            Stat::make('Published Quizzes', Quiz::where('is_published', true)->where('is_demo', false)->count())
                ->description('Active assessments')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
            
            Stat::make('Completed Attempts', QuizAttempt::where('is_completed', true)->where('is_demo', false)->count())
                ->description('Total submissions')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('info'),
                
            Stat::make('School Average Score', number_format(QuizAttempt::where('is_completed', true)->where('is_demo', false)->avg('score') ?? 0, 1) . '%')
                ->description('Across all subjects')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
