<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->datetime('lastlogin')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
