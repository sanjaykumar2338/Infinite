<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeReport extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'summary',
        'badge_name',
        'file_path',
        'month',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
