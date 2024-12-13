<?php

use App\Enums\QuizDifficulty;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('first_points')->nullable();
            $table->integer('second_points')->nullable();
            $table->integer('third_points')->nullable();
            $table->dateTime('time_start')->nullable();
            $table->dateTime('time_end')->nullable();
            $table->enum('difficulty', QuizDifficulty::TYPES)->default(QuizDifficulty::EASY);
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
        Schema::dropIfExists('competitions');
    }
}
