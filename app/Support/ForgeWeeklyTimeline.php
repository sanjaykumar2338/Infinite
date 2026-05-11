<?php

namespace App\Support;

class ForgeWeeklyTimeline
{
    public const REPORT_TYPE = 'forge_weekly_timeline';

    /**
     * @return list<string>
     */
    public static function requiredPaths(): array
    {
        return [
            'report_type',
            'meta.prepared_time',
            'meta.system',
            'executive_summary.headline',
            'timeline.days',
            'timeline.progression_curve',
            'pattern_insights',
            'directional_takeaway.text',
        ];
    }

    /**
     * @return array{valid: bool, missing: list<string>, payload: array<string, mixed>|null}
     */
    public static function inspect(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [
                'valid' => false,
                'missing' => self::requiredPaths(),
                'payload' => null,
            ];
        }

        $normalized = self::normalize($payload);
        $missing = [];

        foreach (['meta.prepared_time', 'meta.system', 'executive_summary.headline', 'directional_takeaway.text'] as $path) {
            $value = data_get($normalized, $path);

            if (! is_string($value) || trim($value) === '') {
                $missing[] = $path;
            }
        }

        if (($normalized['report_type'] ?? null) !== self::REPORT_TYPE) {
            $missing[] = 'report_type';
        }

        $days = data_get($normalized, 'timeline.days');
        $curve = data_get($normalized, 'timeline.progression_curve');
        $insights = data_get($normalized, 'pattern_insights');

        if (! is_array($days) || $days === [] || count(array_filter($days, fn ($day) => is_string($day) && trim($day) !== '')) !== count($days)) {
            $missing[] = 'timeline.days';
        }

        if (! is_array($curve) || $curve === [] || count(array_filter($curve, fn ($value) => is_numeric($value))) !== count($curve)) {
            $missing[] = 'timeline.progression_curve';
        }

        if (is_array($days) && is_array($curve) && count($days) !== count($curve)) {
            $missing[] = 'timeline.progression_curve';
        }

        if (! is_array($insights) || $insights === [] || count(array_filter($insights, fn ($insight) => is_string($insight) && trim($insight) !== '')) !== count($insights)) {
            $missing[] = 'pattern_insights';
        }

        return [
            'valid' => $missing === [],
            'missing' => array_values(array_unique($missing)),
            'payload' => $normalized,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public static function normalize(array $payload): array
    {
        $days = array_values(array_map(
            fn ($day) => is_string($day) ? trim($day) : '',
            is_array(data_get($payload, 'timeline.days')) ? data_get($payload, 'timeline.days') : [],
        ));

        $curve = array_values(array_map(
            fn ($value) => is_numeric($value) ? max(0, min(100, (float) $value)) : null,
            is_array(data_get($payload, 'timeline.progression_curve')) ? data_get($payload, 'timeline.progression_curve') : [],
        ));

        $insights = array_values(array_map(
            fn ($insight) => is_string($insight) ? trim($insight) : '',
            is_array($payload['pattern_insights'] ?? null) ? $payload['pattern_insights'] : [],
        ));

        data_set($payload, 'timeline.days', $days);
        data_set($payload, 'timeline.progression_curve', $curve);
        $payload['pattern_insights'] = $insights;

        return $payload;
    }

    public static function sample(): array
    {
        return ForgeReportFixture::load('forge-weekly-timeline.json');
    }
}
