<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostViewed extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $channel;
    public $data;
    public $post_id;

    public function __construct($post_id, $data)
    {
        $this->channel = $this->post_id = (int) $post_id;
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
        return 'post-view-updated';
    }
}
