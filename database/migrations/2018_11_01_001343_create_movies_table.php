<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id')->unique();
            $table->string('title', 150);
            $table->double('vote_average');
            $table->integer('vote_count');
            $table->double('popularity');
            $table->char('original_language', 4);
            $table->text('overview');
            $table->date('release_date');
            $table->string('external_poster_path')->nullable();
            $table->string('poster_path')->nullable();
            $table->boolean('adult');
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
        Schema::dropIfExists('movies');
    }
}
