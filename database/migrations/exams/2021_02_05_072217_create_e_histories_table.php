<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id');
            $table->integer('user_id');
            $table->integer('score');
            $table->integer('process');
            $table->text('part_listen')->nullable();
            $table->text('part_read')->nullable();
            $table->text('part_write')->nullable();
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
        Schema::dropIfExists('e_histories');
    }
}
