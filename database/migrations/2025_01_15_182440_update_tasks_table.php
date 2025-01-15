<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('priority');
            $table->unsignedBigInteger('priority_id');
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->unsignedBigInteger('user_id');

            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('priority');
            $table->dropForeign(['priority_id']);
            $table->dropForeign(['assignee_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('priority_id');
            $table->dropColumn('assignee_id');
            $table->dropColumn('user_id');
        });
    }
};
