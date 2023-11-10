<?php

namespace App\Events;

use App\Models\Question;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnsweredQuestion
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public bool $correct_answer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, bool $correct_answer)
    {
        $this->user = $user;
        $this->correct_answer = $correct_answer;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
