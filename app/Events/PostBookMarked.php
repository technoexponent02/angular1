<?php

namespace App\Events;

use DB;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostBookMarked extends Event implements ShouldBroadcast
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
    public function __construct($post_id)
    {
        $this->channel = $this->post_id = (int) $post_id;
        // Fetch different bookmark count.
        $totalBookMark = DB::table('bookmarks')
            ->where('post_id', $this->post_id)
            ->select(['id'])
            ->count();
        
        // Assign to data.
        $data = [
            'totalBookMark' => (int) $totalBookMark
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
        return 'post-bookmarked';
    }
}
