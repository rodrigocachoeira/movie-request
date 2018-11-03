<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignInGenreRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('genre_relations', function (Blueprint $table) {
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
            $table->foreign('genre_other_id')->references('id')->on('genres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('genre_relations', function (Blueprint $table) {
            $table->dropForeign('genre_relations_genre_id_foreign');
            $table->dropForeign('genre_relations_genre_other_id_foreign');
        });
    }
}
