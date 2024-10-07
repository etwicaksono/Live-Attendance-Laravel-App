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
            $table->dateTime('check_in');
          $table->dateTime('check_out')->nullable();
          $table->string('photo_check_in')->nullable();
          $table->string('photo_check_out')->nullable();
          $table->double('latitude_check_in')->nullable();
          $table->double('longitude_check_in')->nullable();
          $table->double('latitude_check_out')->nullable();
            $table->double('longitude_check_out')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('presences');
    }
};
