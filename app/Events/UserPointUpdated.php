<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserPointUpdated extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $channel;
    public $user_id;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->channel = $this->user_id = (int) $user->id;
        $data = [
            'points' => $user->points
        ];
        $this->data = $data;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['open-post-channel'];
    }

    /**
     * Get the broadcast event name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'user-point-updated';
    }
}
