<?php

namespace App\Support;

class ForgeSundayWeeklyBrief
{
    public const REPORT_TYPE = 'forge_sunday_weekly_brief';

    /**
     * @return list<string>
     */
    public static function requiredPaths(): array
    {
        return [
            'meta.prepared_time',
            'meta.system',
            'executive_verdict.headline',
            'identity_evolution.text',
            'before_after_shift.text',
            'executive_summary.text',
            'key_insights.firing',
            'key_insights.string',
            'business_translation_layer.objection_handling',
            'business_translation_layer.positioning',
            'business_translation_layer.conversion_signal',
            'habit_reinforcement.text',
            'next_week_focus.text',
        ];
    }

    /**
     * @return array{valid: bool, missing: list<string>}
     */
    public static function inspect(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [
                'valid' => false,
                'missing' => self::requiredPaths(),
            ];
        }

        $missing = [];

        foreach (self::requiredPaths() as $path) {
            $value = data_get($payload, $path);

            if (! is_string($value) || trim($value) === '') {
                $missing[] = $path;
            }
        }

        return [
            'valid' => $missing === [],
            'missing' => $missing,
        ];
    }

    public static function sample(): array
    {
        $path = database_path('seeders/data/forge-sunday-weekly-brief.json');
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new \RuntimeException('Unable to load Forge Sunday weekly brief sample JSON.');
        }

        $payload = json_decode($contents, true);

        if (! is_array($payload)) {
            throw new \RuntimeException('Forge Sunday weekly brief sample JSON is invalid.');
        }

        return $payload;
    }
}
