<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePremiumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premiums', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('device_id')->nullable();
            $table->integer('user_id');
            $table->integer('admin_id')->nullable();
            $table->string('transaction')->nullable();
            $table->string('code')->nullable();
            $table->string('provider');
            $table->integer('day_expired');
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
        Schema::dropIfExists('premiums');
    }
}
