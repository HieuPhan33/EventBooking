<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role');
            $table->boolean('isBanned')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->integer('age');
            $table->tinyInteger('sex');
            $table->tinyInteger('studentType');
            $table->tinyInteger('degree');
            $table->tinyInteger('favoriteClubType');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
