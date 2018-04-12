<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->char('id',5);
            $table->integer('eventID');
            $table->foreign('eventID')->references('id')->on('events');
            $table->timestamps();
        });
       Schema::table('promo_codes', function($table) {
           $table->foreign('eventID')->references('id')->on('events');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promo_codes');
    }
}
