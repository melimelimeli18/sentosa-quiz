<?php

namespace App\Filament\Widgets;

use App\Models\Subject;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

class SubjectPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'half';
    
    protected static ?string $heading = 'Subject Performance';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Subject::query()
                    ->join('quizzes', 'subjects.id', '=', 'quizzes.subject_id')
                    ->join('quiz_attempts', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
                    ->where('quiz_attempts.is_completed', true)
                    ->where('quizzes.is_demo', false)
                    ->where('quiz_attempts.is_demo', false)
                    ->groupBy('subjects.id', 'subjects.name')
                    ->select(
                        'subjects.id',
                        'subjects.name as subject_name',
                        DB::raw('ROUND(AVG(quiz_attempts.score)::numeric, 1) as avg_score'),
                        DB::raw('COUNT(quiz_attempts.id) as total_attempts')
                    )
            )
            ->columns([
                TextColumn::make('subject_name')
                    ->label('Subject')
                    ->sortable(),
                TextColumn::make('avg_score')
                    ->label('Avg Score')
                    ->numeric(1)
                    ->suffix('%')
                    ->sortable()
                    ->color(fn ($state) => $state < 50 ? 'danger' : ($state >= 80 ? 'success' : 'warning')),
            ])
            ->paginated(false)
            ->defaultSort('avg_score', 'desc');
    }
}
