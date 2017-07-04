<?php
//@FAKEPINSSTART
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFakeMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages_fake', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('pin_one');
            $table->bigInteger('pin_two');
            $table->bigInteger('user_one');
            $table->bigInteger('user_two');
            $table->tinyInteger('user_one_read')->default(1);
            $table->tinyInteger('user_two_read')->default(1);
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
        Schema::dropIfExists('messages_fake');
    }
}
//@FAKEPINSEND