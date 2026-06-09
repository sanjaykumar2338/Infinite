<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ($this->content() as $sectionKey => $content) {
            DB::table('page_contents')->updateOrInsert(
                [
                    'page_key' => 'home',
                    'section_key' => $sectionKey,
                ],
                array_merge($content, [
                    'sort_order' => $sectionKey === 'intelligence_sunday' ? 100 : 110,
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]),
            );
        }
    }

    public function down(): void
    {
        $now = now();

        DB::table('page_contents')->updateOrInsert(
            [
                'page_key' => 'home',
                'section_key' => 'intelligence_sunday',
            ],
            [
                'title' => '11 execution signals. 2 decisive charts.',
                'subtitle' => 'Sunday Performance Brief · 9 PM',
                'body' => 'Deal Momentum Verdict.'."\n".'Clear leverage. Clear risk.',
                'button_text' => null,
                'sort_order' => 100,
                'is_active' => true,
                'updated_at' => $now,
                'created_at' => $now,
            ],
        );

        DB::table('page_contents')->updateOrInsert(
            [
                'page_key' => 'home',
                'section_key' => 'intelligence_badge',
            ],
            [
                'title' => 'Cumulative performance analysis. One earned badge.',
                'subtitle' => 'Monday Progress Summary · 9 AM',
                'body' => 'Awarded only when behavior consistently converts into measurable deal progress.',
                'button_text' => null,
                'sort_order' => 110,
                'is_active' => true,
                'updated_at' => $now,
                'created_at' => $now,
            ],
        );
    }

    private function content(): array
    {
        return [
            'intelligence_sunday' => [
                'title' => 'Weekly Intelligence Brief',
                'subtitle' => 'WEEKLY PERFORMANCE BRIEF • SUNDAY 9 PM',
                'body' => 'Meaningful patterns surfaced'."\n".'Emerging opportunities identified'."\n".'Key moments highlighted'."\n".'Strategic observations delivered',
                'button_text' => 'The week, distilled.',
            ],
            'intelligence_badge' => [
                'title' => 'Achievement Review',
                'subtitle' => 'MONTHLY PERFORMANCE SUMMARY • MONDAY 8 AM',
                'body' => 'One earned badge'."\n".'Performance milestones recognized'."\n".'Consistency measured over time'."\n".'Growth documented month after month',
                'button_text' => 'No fluff. Just proof the edge is repeatable.',
            ],
        ];
    }
};
