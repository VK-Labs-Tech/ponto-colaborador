<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_adjustments', function (Blueprint $table): void {
            $table->json('before_punches')->nullable()->after('worked_minutes');
            $table->json('after_punches')->nullable()->after('before_punches');
            $table->unsignedBigInteger('actor_id')->nullable()->after('created_by');
            $table->string('actor_role')->nullable()->after('actor_id');
            $table->string('status')->default('approved')->after('actor_role');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_reason')->nullable()->after('approved_at');
            $table->uuid('adjustment_batch')->nullable()->after('approval_reason');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_adjustments', function (Blueprint $table): void {
            $table->dropColumn([
                'before_punches',
                'after_punches',
                'actor_id',
                'actor_role',
                'status',
                'approved_by',
                'approved_at',
                'approval_reason',
                'adjustment_batch',
            ]);
        });
    }
};
