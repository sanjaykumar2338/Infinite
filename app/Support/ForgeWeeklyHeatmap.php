<?php

namespace App\Support;

class ForgeWeeklyHeatmap
{
    public const REPORT_TYPE = 'forge_weekly_heatmap';

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
            'heatmap.days',
            'heatmap.rows',
            'strategic_interpretation.text',
            'reinforcement.text',
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

        foreach (['meta.prepared_time', 'meta.system', 'executive_summary.headline', 'strategic_interpretation.text', 'reinforcement.text'] as $path) {
            $value = data_get($normalized, $path);

            if (! is_string($value) || trim($value) === '') {
                $missing[] = $path;
            }
        }

        if (($normalized['report_type'] ?? null) !== self::REPORT_TYPE) {
            $missing[] = 'report_type';
        }

        $days = data_get($normalized, 'heatmap.days');
        $rows = data_get($normalized, 'heatmap.rows');

        if (! is_array($days) || $days === [] || count(array_filter($days, fn ($day) => is_string($day) && trim($day) !== '')) !== count($days)) {
            $missing[] = 'heatmap.days';
        }

        if (! is_array($rows) || $rows === []) {
            $missing[] = 'heatmap.rows';
        } else {
            foreach ($rows as $index => $row) {
                if (! is_array($row) || ! is_string($row['label'] ?? null) || trim($row['label']) === '') {
                    $missing[] = "heatmap.rows.{$index}.label";
                }

                $values = $row['values'] ?? null;
                if (! is_array($values) || ! is_array($days) || count($values) !== count($days)) {
                    $missing[] = "heatmap.rows.{$index}.values";
                }
            }
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
            is_array(data_get($payload, 'heatmap.days')) ? data_get($payload, 'heatmap.days') : [],
        ));

        $rows = array_values(array_map(function ($row) {
            if (! is_array($row)) {
                return ['label' => '', 'values' => []];
            }

            return [
                'label' => is_string($row['label'] ?? null) ? trim($row['label']) : '',
                'values' => array_values(array_map(
                    fn ($value) => is_string($value) ? trim(strtolower($value)) : 'steady',
                    is_array($row['values'] ?? null) ? $row['values'] : [],
                )),
            ];
        }, is_array(data_get($payload, 'heatmap.rows')) ? data_get($payload, 'heatmap.rows') : []));

        data_set($payload, 'heatmap.days', $days);
        data_set($payload, 'heatmap.rows', $rows);

        return $payload;
    }

    public static function sample(): array
    {
        return ForgeReportFixture::load('forge-weekly-heatmap.json');
    }
}
