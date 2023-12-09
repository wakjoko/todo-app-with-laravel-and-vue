<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color');
            $table->boolean('default');
        });

        DB::table('priorities')->insert([
            [
                'name' => 'Low',
                'color' => 'info',
                'default' => false,
            ],
            [
                'name' => 'Normal',
                'color' => 'primary',
                'default' => true,
            ],
            [
                'name' => 'High',
                'color' => 'warning',
                'default' => false,
            ],
            [
                'name' => 'Urgent',
                'color' => 'danger',
                'default' => false,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priorities');
    }
};
