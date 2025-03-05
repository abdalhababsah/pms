<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('work_logs', function (Blueprint $table) {
            $table->foreignId('project_status_id')
                  ->nullable()
                  ->constrained('statuses')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('work_logs', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['project_status_id']);
            // Then drop the column
            $table->dropColumn('project_status_id');
        });
    }
};