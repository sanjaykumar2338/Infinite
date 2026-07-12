<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'extension_connected_at')) {
                $table->timestamp('extension_connected_at')->nullable()->after('call_minutes_used');
            }

            if (! Schema::hasColumn('users', 'extension_last_seen_at')) {
                $table->timestamp('extension_last_seen_at')->nullable()->after('extension_connected_at');
            }

            if (! Schema::hasColumn('users', 'extension_version')) {
                $table->string('extension_version', 32)->nullable()->after('extension_last_seen_at');
            }

            if (! Schema::hasColumn('users', 'extension_platform')) {
                $table->string('extension_platform')->nullable()->after('extension_version');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['extension_platform', 'extension_version', 'extension_last_seen_at', 'extension_connected_at'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
