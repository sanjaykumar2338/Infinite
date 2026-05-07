<?php

namespace App\Models;

use App\Support\ForgeSundayWeeklyBrief;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'report_type',
        'summary',
        'report_data',
        'file_path',
        'period_start',
        'period_end',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'published_at' => 'datetime',
            'report_data' => 'array',
        ];
    }

    public function isForgeSundayWeeklyBrief(): bool
    {
        return $this->report_type === ForgeSundayWeeklyBrief::REPORT_TYPE;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function charts()
    {
        return $this->hasMany(ReportChart::class);
    }
}
