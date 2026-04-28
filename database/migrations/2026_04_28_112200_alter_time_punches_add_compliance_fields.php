<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('time_punches', function (Blueprint $table): void {
            $table->uuid('adjustment_batch')->nullable()->after('note');
            $table->string('ip_address', 45)->nullable()->after('adjustment_batch');
            $table->decimal('latitude', 10, 7)->nullable()->after('ip_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('device_fingerprint')->nullable()->after('longitude');

            $table->index(['employee_id', 'punched_at', 'adjustment_batch'], 'punch_employee_date_batch_idx');
        });
    }

    public function down(): void
    {
        Schema::table('time_punches', function (Blueprint $table): void {
            $table->dropIndex('punch_employee_date_batch_idx');
            $table->dropColumn([
                'adjustment_batch',
                'ip_address',
                'latitude',
                'longitude',
                'device_fingerprint',
            ]);
        });
    }
};
