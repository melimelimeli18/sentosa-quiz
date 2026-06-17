<?php

namespace App\Filament\Resources\Quizzes\Pages;

use App\Filament\Resources\Quizzes\QuizResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuiz extends CreateRecord
{
    protected static string $resource = QuizResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->check() && auth()->user()->is_demo) {
            $data['is_demo'] = true;
            $data['teacher_id'] = auth()->id();
        } else {
            $data['is_demo'] = false;
        }

        return $data;
    }
}
