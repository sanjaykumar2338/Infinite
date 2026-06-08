<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'subscription_status')) {
                $table->string('subscription_status')->nullable()->index();
            }

            if (! Schema::hasColumn('users', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable();
            }

            if (! Schema::hasColumn('users', 'current_period_ends_at')) {
                $table->timestamp('current_period_ends_at')->nullable()->index();
            }
        });

        DB::table('users')
            ->whereNull('subscription_status')
            ->update(['subscription_status' => DB::raw('status')]);

        DB::table('users')
            ->whereNull('current_period_ends_at')
            ->whereNotNull('current_period_end')
            ->update(['current_period_ends_at' => DB::raw('current_period_end')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'current_period_ends_at')) {
                $table->dropColumn('current_period_ends_at');
            }

            if (Schema::hasColumn('users', 'trial_ends_at')) {
                $table->dropColumn('trial_ends_at');
            }

            if (Schema::hasColumn('users', 'subscription_status')) {
                $table->dropColumn('subscription_status');
            }
        });
    }
};
