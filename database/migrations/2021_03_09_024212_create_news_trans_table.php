<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_trans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('news_id');
            $table->integer('user_id');
            $table->text('content');
            $table->integer('like')->default(0);
            $table->integer('dislike')->default(0);
            $table->string('country');
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
        Schema::dropIfExists('news_trans');
    }
}
