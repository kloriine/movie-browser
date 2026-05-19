<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteMoviesTable extends Migration
{
    public function up()
    {
        Schema::create('favorite_movies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('imdb_id', 20);
            $table->string('title');
            $table->string('year', 10);
            $table->text('poster')->nullable();
            $table->string('type', 20)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'imdb_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorite_movies');
    }
}