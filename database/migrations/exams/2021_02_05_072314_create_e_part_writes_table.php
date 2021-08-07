<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEPartWritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_part_writes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id');
            $table->integer('history_id')->nullable();;
            $table->integer('user_id');
            $table->integer('question_id');
            $table->integer('order');
            $table->integer('score')->nullable();
            $table->integer('max_score');
            $table->text('content');
            $table->tinyInteger('process');
            $table->boolean('status');
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
        Schema::dropIfExists('e_part_writes');
    }
}
