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
        Schema::table('projects', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['project_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'priority']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['name']);
        });
    }
};
