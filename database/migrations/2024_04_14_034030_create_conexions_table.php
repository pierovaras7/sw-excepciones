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
       
            Schema::create('conexions', function (Blueprint $table) {
                $table->id();
                $table->string('db_type');
                $table->string('host');
                $table->integer('port');
                $table->string('database');
                $table->string('username');
                $table->string('password')->nullable();
                $table->timestamp('last_use')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->integer('user');
                $table->timestamps(false);
            });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conexions');
    }
};
