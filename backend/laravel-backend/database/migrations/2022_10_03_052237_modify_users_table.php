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
            $table->string('bio');
        });

        Schema::create('pictures', function (Blueprint $table) {
            $table->integer('user_id');
            $table->string('url');
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->integer('user_id');
            $table->float('longitude');
            $table->float('latitude');
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
