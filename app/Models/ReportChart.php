<?php

namespace App\Models;

use App\Support\ForgeWeeklyHeatmap;
use App\Support\ForgeWeeklyTimeline;
use Illuminate\Database\Eloquent\Model;

class ReportChart extends Model
{
    protected $fillable = [
        'user_id',
        'report_id',
        'title',
        'report_type',
        'chart_type',
        'data',
        'file_path',
        'period_start',
        'period_end',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'period_start' => 'date',
            'period_end' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function isForgeWeeklyTimeline(): bool
    {
        return $this->report_type === ForgeWeeklyTimeline::REPORT_TYPE;
    }

    public function isForgeWeeklyHeatmap(): bool
    {
        return $this->report_type === ForgeWeeklyHeatmap::REPORT_TYPE;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
