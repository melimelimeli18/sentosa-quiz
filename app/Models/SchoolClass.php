<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'classes';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'class_id');
    }

    protected static function booted()
    {
        static::creating(function ($schoolClass) {
            if (empty($schoolClass->join_code)) {
                do {
                    $code = strtoupper(\Illuminate\Support\Str::random(6));
                } while (static::where('join_code', $code)->exists());
                
                $schoolClass->join_code = $code;
            }
        });
    }
}
