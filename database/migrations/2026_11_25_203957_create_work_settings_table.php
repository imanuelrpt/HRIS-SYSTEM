<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_settings', function (Blueprint $table) {
            $table->id();
            $table->time('check_in')->default('08:00');
            $table->time('check_out')->default('17:00');
            $table->unsignedInteger('late_tolerance_minutes')->default(10);
            $table->json('working_days');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_settings');
    }
};
