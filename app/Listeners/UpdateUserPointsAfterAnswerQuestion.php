<?php

namespace App\Listeners;

use App\Events\AnsweredQuestion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserPointsAfterAnswerQuestion
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
     * @param AnsweredQuestion $event
     * @return void
     */
    public function handle(AnsweredQuestion $event)
    {
        if($event->user->hasPremium())
            $event->user->addPoints(1);
    }
}
