<?php

namespace App\Filament\Widgets;

use App\Models\SchoolClass;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

class ClassPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Class Performance';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SchoolClass::query()
                    ->join('users', 'classes.id', '=', 'users.class_id')
                    ->join('quiz_attempts', 'users.id', '=', 'quiz_attempts.student_id')
                    ->where('quiz_attempts.is_completed', true)
                    ->where('classes.is_demo', false)
                    ->where('users.is_demo', false)
                    ->where('quiz_attempts.is_demo', false)
                    ->groupBy('classes.id', 'classes.name')
                    ->select(
                        'classes.id',
                        'classes.name as class_name',
                        DB::raw('ROUND(AVG(quiz_attempts.score)::numeric, 1) as avg_score'),
                        DB::raw('COUNT(quiz_attempts.id) as total_attempts')
                    )
            )
            ->columns([
                TextColumn::make('class_name')
                    ->label('Class')
                    ->sortable(),
                TextColumn::make('avg_score')
                    ->label('Average Score')
                    ->numeric(1)
                    ->suffix('%')
                    ->sortable()
                    ->color(fn ($state) => $state < 50 ? 'danger' : ($state >= 80 ? 'success' : 'warning')),
                TextColumn::make('total_attempts')
                    ->label('Total Attempts')
                    ->sortable(),
            ])
            ->defaultSort('avg_score', 'desc');
    }
}
