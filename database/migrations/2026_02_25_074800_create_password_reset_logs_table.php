<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('password_resets', function (Blueprint $table) {
            if (!Schema::hasColumn('password_resets', 'code')) {
                $table->string('code', 6)->after('email');
            }
            if (!Schema::hasColumn('password_resets', 'used')) {
                $table->boolean('used')->default(false)->after('code');
            }
            if (!Schema::hasColumn('password_resets', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('used');
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_resets', function (Blueprint $table) {
            if (Schema::hasColumn('password_resets', 'code')) {
                $table->dropColumn('code');
            }
            if (Schema::hasColumn('password_resets', 'used')) {
                $table->dropColumn('used');
            }
            if (Schema::hasColumn('password_resets', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};