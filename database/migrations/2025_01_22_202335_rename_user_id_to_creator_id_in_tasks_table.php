<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('user_id', 'creator_id');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('creator_id', 'user_id');
        });
    }
};
