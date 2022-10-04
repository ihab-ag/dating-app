<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('age');
            $table->string('gender');
            $table->string('interest');
            $table->tinyInteger('private')->default('0');
            $table->string('bio')->nullable();
        });

        Schema::create('pictures', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('url');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id('user_id')->references('id')->on("users");
            $table->double('longitude',10,8);
            $table->double('latitude',10,8);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
