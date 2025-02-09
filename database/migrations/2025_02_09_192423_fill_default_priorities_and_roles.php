<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('priorities')->insert([
            ['name' => 'none'],
            ['name' => 'urgent'],
            ['name' => 'max'],
        ]);

        DB::table('roles')->insert([
            ['name' => 'developer'],
            ['name' => 'scrum_master'],
            ['name' => 'product_owner'],
            ['name' => 'admin'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('priorities')->whereIn('name', ['none', 'urgent', 'max'])->delete();

        DB::table('roles')->whereIn('name', ['developer', 'scrum_master', 'product_owner', 'admin'])->delete();
    }
};
