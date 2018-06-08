<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostPointUpdated extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $channel;
    public $data;
    public $post_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($post)
    {
        $this->data = [
            'points' => $post->points
        ];
        $this->channel = $this->post_id = (int) $post->id;
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
        return 'post-point-updated';
    }
}
