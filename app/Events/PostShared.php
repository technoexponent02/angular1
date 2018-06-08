<?php

namespace App\Events;

use DB;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PostShared extends Event implements ShouldBroadcast
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
        // Fetch different share counts.
        $activity_post = DB::table('activity_post')
            ->select(DB::raw('SUM(IF(activity_id = 3,1,0)) AS normalShare ,SUM(IF(activity_id = 4,1,0)) AS totalFBshare, SUM(IF(activity_id = 5,1,0)) AS totalTwittershare'))
            ->where(['post_id' => $post_id])
            ->whereIn('activity_id', [3, 4, 5])
            ->get();
        // Assign to data.
        $data = [
            'normalShare' => (int) $activity_post[0]->normalShare,
            'totalFBshare' => (int) $activity_post[0]->totalFBshare,
            'totalTwittershare' => (int) $activity_post[0]->totalTwittershare
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
        return 'post-shared';
    }
}
