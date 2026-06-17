<?php

namespace App\Filament\Widgets;

use App\Models\Chapter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

class WeakestChaptersWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'half';
    
    protected static ?string $heading = 'Weakest Chapters (School-wide)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Chapter::query()
                    ->join('questions', 'chapters.id', '=', 'questions.chapter_id')
                    ->join('student_answers', 'questions.id', '=', 'student_answers.question_id')
                    ->join('quiz_attempts', 'quiz_attempts.id', '=', 'student_answers.attempt_id')
                    ->where('quiz_attempts.is_completed', true)
                    ->where('quiz_attempts.is_demo', false)
                    ->groupBy('chapters.id', 'chapters.name')
                    ->select(
                        'chapters.id',
                        'chapters.name as chapter_name',
                        DB::raw('COUNT(student_answers.id) as total_answers'),
                        DB::raw('ROUND(SUM(CASE WHEN student_answers.is_correct THEN 1 ELSE 0 END)::numeric / COUNT(student_answers.id) * 100, 1) as correct_pct')
                    )
            )
            ->columns([
                TextColumn::make('chapter_name')
                    ->label('Chapter')
                    ->sortable(),
                TextColumn::make('correct_pct')
                    ->label('Correct %')
                    ->numeric(1)
                    ->suffix('%')
                    ->sortable()
                    ->color('danger'),
            ])
            ->paginated(false)
            ->defaultSort('correct_pct', 'asc');
    }
}
