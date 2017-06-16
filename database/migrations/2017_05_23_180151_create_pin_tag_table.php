<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pin_tag', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('tag_id');
            $table->bigInteger('pin_id');
            $table->foreign('tag_id')->references('id')->on('tags')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('pin_id')->references('id')->on('pins')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pin_tag');
    }
}
