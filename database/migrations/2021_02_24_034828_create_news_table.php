<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('pub_date');
            $table->integer('news_order')->default(0);
            $table->text('title');
            $table->string('kind')->nullable();
            $table->text('link')->nullable();
            $table->string('video')->nullable();
            $table->text('name_link')->nullable();
            $table->string('image')->nullable();
            $table->text('description');
            $table->longText('content');
            $table->tinyInteger('status')->default(0); //0: created, 1: posted, -1: deleted, 2: success
            $table->integer('user_post')->default(0);
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
        Schema::dropIfExists('news');
    }
}
