<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('presence_type');
            $table->dateTime('check_in');
            $table->dateTime('check_out')->nullable();
            $table->string('photo')->nullable();
            $table->string('coordinate')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('presence_type')->references('id')->on('presence_types')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('presences');
    }
};
