<?php

namespace App\Support;

class ForgeMonthlyBadgeReport
{
    public const REPORT_TYPE = 'forge_monthly_badge';

    public const BADGE_MOMENTUM_ARCHITECT = 'Momentum Architect';

    public const BADGE_INFLUENCE_COMMANDER = 'Influence Commander';

    public const BADGE_PRESENCE_DOMINATOR = 'Presence Dominator';

    /**
     * @return list<string>
     */
    public static function allowedBadges(): array
    {
        return [
            self::BADGE_MOMENTUM_ARCHITECT,
            self::BADGE_INFLUENCE_COMMANDER,
            self::BADGE_PRESENCE_DOMINATOR,
        ];
    }

    /**
     * @return list<string>
     */
    public static function requiredPaths(): array
    {
        return [
            'badge_name',
            'meta.prepared_time',
            'meta.system',
            'executive_summary.headline',
            'strategic_interpretation.text',
            'identity_evolution.text',
            'next_month_focus.text',
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

        $missing = [];

        foreach (self::requiredPaths() as $path) {
            $value = data_get($payload, $path);

            if (! is_string($value) || trim($value) === '') {
                $missing[] = $path;
            }
        }

        if (is_string($payload['badge_name'] ?? null) && ! in_array($payload['badge_name'], self::allowedBadges(), true)) {
            $missing[] = 'badge_name';
        }

        return [
            'valid' => $missing === [],
            'missing' => array_values(array_unique($missing)),
            'payload' => $payload,
        ];
    }

    public static function sample(string $badgeName): array
    {
        return match ($badgeName) {
            self::BADGE_MOMENTUM_ARCHITECT => ForgeReportFixture::load('forge-monthly-badge-momentum-architect.json'),
            self::BADGE_INFLUENCE_COMMANDER => ForgeReportFixture::load('forge-monthly-badge-influence-commander.json'),
            self::BADGE_PRESENCE_DOMINATOR => ForgeReportFixture::load('forge-monthly-badge-presence-dominator.json'),
            default => throw new \InvalidArgumentException("Unknown Forge badge [{$badgeName}]."),
        };
    }
}
