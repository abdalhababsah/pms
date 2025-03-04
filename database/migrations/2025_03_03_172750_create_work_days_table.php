<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->string('location'); // Work from home or office
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_days');
    }
};