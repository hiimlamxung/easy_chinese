<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleOffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_off', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('country');
            $table->integer('sale');
            $table->string('version');
            $table->text('title_ios');
            $table->text('title_android');
            $table->text('description_ios');
            $table->text('description_android'); 
            $table->text('link_ios')->nullable();
            $table->text('link_android')->nullable();
            $table->tinyInteger('active');
            $table->string('start_ios');
            $table->string('end_ios');
            $table->string('start_android');
            $table->string('end_android');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_off');
    }
}
