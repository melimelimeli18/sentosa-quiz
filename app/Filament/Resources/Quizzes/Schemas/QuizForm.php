<?php

namespace App\Filament\Resources\Quizzes\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->required(),
                Select::make('teacher_id')
                    ->label('Teacher')
                    ->options(fn () => auth()->check() && auth()->user()->is_demo
                        ? [auth()->id() => auth()->user()->name]
                        : User::role(['admin', 'teacher'])->where('is_demo', false)->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->default(fn () => auth()->id())
                    ->required(),
                Select::make('type')
                    ->options([
                        'mid_term'   => 'Persiapan UTS (Ujian Tengah Semester)',
                        'final_term' => 'Persiapan UAS (Ujian Akhir Semester)',
                    ])
                    ->default('mid_term')
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (\Illuminate\Validation\Rules\Unique $rule, callable $get) => 
                        $rule->where('subject_id', $get('subject_id'))
                    )
                    ->validationMessages([
                        'unique' => 'A quiz of this type already exists for this subject.',
                    ]),
                TextInput::make('duration_minutes')
                    ->numeric(),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('total_points')
                    ->required()
                    ->numeric()
                    ->default(100),
                TextInput::make('allowed_attempts')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('classes')
                    ->multiple()
                    ->relationship(
                        name: 'classes',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => auth()->check() && auth()->user()->is_demo
                            ? $query->where('is_demo', true)
                            : $query->where('is_demo', false)
                    )
                    ->preload()
            ]);
    }
}
