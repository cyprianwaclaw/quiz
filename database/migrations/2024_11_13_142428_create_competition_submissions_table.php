<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competition_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained();
            $table->integer('correct_answers')->default(0);
            $table->integer('duration')->nullable();
            $table->integer('place')->default(0);
            $table->foreignId('user_id')->constrained();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
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
        Schema::dropIfExists('competition_submissions');
    }
}
