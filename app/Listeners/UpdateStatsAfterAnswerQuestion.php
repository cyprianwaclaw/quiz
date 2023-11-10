<?php

namespace App\Listeners;

use App\Events\AnsweredQuestion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateStatsAfterAnswerQuestion
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AnsweredQuestion $event)
    {
        if ($event->correct_answer)
            $event->user->stats()->increment('correct_answers');
        else
            $event->user->stats()->increment('incorrect_answers');
    }
}
