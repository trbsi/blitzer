<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFavoriteUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorite_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->bigInteger('favorited')->comment('user who was favorited');
            $table->bigInteger('favorited_by')->comment('user who favorited another user');
            $table->foreign('favorited')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('favorited_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['favorited', 'favorited_by']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorite_users');
    }
}
