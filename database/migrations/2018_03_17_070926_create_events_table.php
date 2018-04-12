<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->tinyInteger('category');
            $table->dateTime('time');
            $table->string('location');
            $table->mediumText('description');
            $table->integer('capacity');
            $table->integer('slotsLeft');
            $table->integer('hostID');
            $table->boolean('isFinalized')->default(false);
            $table->timestamps();
            $table->char('promoCode',5);
            $table->integer('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
