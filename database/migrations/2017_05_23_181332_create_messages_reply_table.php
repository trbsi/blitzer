<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages_reply', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('message_id');
            $table->text('reply');
            $table->bigInteger('user_id');
            $table->datetime('send_date');
            $table->tinyInteger('message_type')->default(1);
            $table->foreign('message_id')->references('id')->on('messages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages_reply');
    }
}
