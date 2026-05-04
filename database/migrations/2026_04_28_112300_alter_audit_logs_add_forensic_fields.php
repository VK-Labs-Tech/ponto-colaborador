<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->unsignedBigInteger('actor_id')->nullable()->after('actor');
            $table->string('actor_type')->nullable()->after('actor_id');
            $table->string('actor_role')->nullable()->after('actor_type');
            $table->string('ip_address', 45)->nullable()->after('actor_role');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->json('before')->nullable()->after('payload');
            $table->json('after')->nullable()->after('before');
            $table->text('reason')->nullable()->after('after');
            $table->timestamp('occurred_at')->nullable()->after('reason');

            $table->index(['company_id', 'event', 'occurred_at'], 'audit_company_event_time_idx');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->dropIndex('audit_company_event_time_idx');
            $table->dropColumn([
                'actor_id',
                'actor_type',
                'actor_role',
                'ip_address',
                'user_agent',
                'before',
                'after',
                'reason',
                'occurred_at',
            ]);
        });
    }
};
