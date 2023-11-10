<?php

namespace App\Listeners;

class AddPointsAfterSubscribe
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
     * @param object $event
     * @return void
     */
    public function handle(object $event)
    {
        $inviting = $event->user->inviting;
        if($inviting === null)
            return;
        $points[22] = 9;
        $points[76] = 34;
        $inviting->addPoints($points[$event->plan->price]);
        $inviting->save();
    }
}
