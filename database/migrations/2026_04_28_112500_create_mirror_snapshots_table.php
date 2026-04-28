<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mirror_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->date('period_from');
            $table->date('period_to');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->json('rows');
            $table->json('punch_rows');
            $table->json('totals');
            $table->string('content_hash', 64);
            $table->unsignedInteger('version')->default(1);
            $table->unsignedBigInteger('signed_by')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'period_from', 'period_to', 'employee_id'], 'mirror_snapshot_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mirror_snapshots');
    }
};
