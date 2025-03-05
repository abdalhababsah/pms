<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add shift_id as a nullable foreign key referencing shifts.id.
            $table->foreignId('shift_id')
                  ->nullable()
                  ->constrained('shifts')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key and column.
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
    }
};