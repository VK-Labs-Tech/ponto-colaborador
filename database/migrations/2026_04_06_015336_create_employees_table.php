<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('registration')->nullable();
            $table->string('pin');
            $table->time('shift_start')->default('08:00:00');
            $table->time('shift_end')->default('17:00:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'registration']);
            $table->unique(['company_id', 'pin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
