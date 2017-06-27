<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigInteger('id', true);
            $table->string('tag_name', 50);
            $table->integer('popularity')->default(0);
        });
        
        //https://laracasts.com/discuss/channels/general-discussion/fulltext-indexes-at-migrations
        \DB::statement('ALTER TABLE tags ADD FULLTEXT INDEX tags_ft_index (tag_name);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
