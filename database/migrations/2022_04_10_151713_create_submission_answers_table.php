<?php

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuizSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(QuizSubmission::class)->constrained();
            $table->foreignIdFor(Question::class)->constrained();
            $table->foreignIdFor(Answer::class)->nullable()->constrained();
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
        Schema::dropIfExists('submission_answers');
    }
}
