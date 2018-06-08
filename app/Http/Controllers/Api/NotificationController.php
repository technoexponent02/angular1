<?php

/**
 * @author Tuhin Subhra Mandal <tuhinmanadal@yahoo.in>
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use DB;
use Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\CommentNotification;
use App\Models\Follower;

class NotificationController extends Controller
{
    /**
	 * NotificationController constructor
	 */
	public function __construct()
	{

	}

	public function seeAll()
	{
		return view('notification.all');
	}

	public function getAllNotification(Request $request)
	{
		// Calculation for pagination
		$perpage = 25;
        $page = 1;
        if(!empty($request->input('page'))) {
            $page = $request->input('page');
        }
        // dd($page);
        $offset = ($page - 1) * $perpage;
        // Initialize data.
		$notifications = [];
		// Collumn selection array
	    $user_columns = ['id', 'username', 'first_name'];
	    $post_columns = ['id', 'caption', 'title', 'category_id', 'sub_category_id', 'orginal_post_id', 'post_type'];
	    $orginal_post_columns = ['id', 'caption'];
	    $category_columns = ['id', 'category_name'];
	    /* ------------ Fetch from notifications (for post upvote, dovote and share) ----------- */
		$notifications = Notification::where('post_user_id', Auth::user()->id)
										// Remove anonymous user notification.
										->where('user_id', '<>', 1)
										->with([
							        	'post' => function ($query) use ($post_columns) {
			                                $query->addSelect($post_columns);
			                            },
			                            'post.orginalPost' => function ($query) use ($orginal_post_columns) {
			                                $query->addSelect($orginal_post_columns);
			                            },
			                            'post.category' => function ($query) use ($category_columns) {
			                                $query->addSelect($category_columns);
			                            },
			                            'post.subCategory' => function ($query)  use ($category_columns) {
			                                $query->addSelect($category_columns);
			                            },
			                            'user' => function ($query) use ($user_columns) {
			                                $query->addSelect($user_columns);
			                            }
			                        ])
									->orderBy('id', 'desc')
							        ->skip($offset)->take($perpage)->get();
        // dd($notifications->toArray());
		$comment_columns = ['id', 'post_id', 'user_id'];
		$comment_notifications = CommentNotification::where('notified_user_id', Auth::user()->id)
													// Remove anonymous user notification.
										            ->where('user_id', '<>', 1)
													->with([
											        	'comment' => function ($query) use ($comment_columns) {
							                                $query->addSelect($comment_columns);
							                            },
											        	'comment.post' => function ($query) use ($post_columns) {
							                                $query->addSelect($post_columns);
							                            },
							                            'comment.post.category' => function ($query) use ($category_columns) {
							                                $query->addSelect($category_columns);
							                            },
							                            'comment.post.subCategory' => function ($query)  use ($category_columns) {
							                                $query->addSelect($category_columns);
							                            },
							                            'comment.user' => function ($query) use ($user_columns) {
							                                $query->addSelect($user_columns);
							                            }
							                        ])
							                        ->orderBy('id', 'desc')
											        ->skip($offset)->take($perpage)->get();
        // dd($comment_notifications->toArray());
		$follow_notifications = Follower::where('user_id', Auth::user()->id)
										->orderBy('id', 'desc')
										->with([
	                                        'followed_by' => function ($query) use ($user_columns) {
	                                            $query->addSelect($user_columns);
	                                        }
	                                    ])
	                                    ->skip($offset)->take($perpage)->get();
	    // dd($follow_notifications->toArray());
		// Merge all notifications into one collection.
        $all_notifications = $notifications->merge($comment_notifications);
        $all_notifications = $all_notifications->merge($follow_notifications);
        // dd($all_notifications->toArray());
        // Sort based on time.
        $all_notifications_sorted = $all_notifications->sort(function($a, $b){
			$a = $a->created_at;
	        $b = $b->created_at;
	        if ($a === $b) {
	            return 0;
	        }
	        return ($a->gt($b)) ? -1 : 1;
		});
        
        // Assign sorted notification to original variable.
        // $all_notifications_sorted = $all_notifications_sorted->take($notification_perpage);
        // dd($notifications->toArray());
		// Create notification response.
		$notifications = [];
		foreach ($all_notifications_sorted as $key => $notification) {
			if (!empty($notification->type) && $notification->type == 'follow') {
				$notification->activity_id = 13;
				$notification->user = $notification->followed_by;
				unset($notification->followed_by);
			}
			else {
				if(!empty($notification->comment)) {
					$notification->post = $notification->comment->post;
					$notification->user = $notification->comment->user;
					unset($notification->comment->post);
					unset($notification->comment->user);
					// Set type to comments.
					$notification->type = !empty($notification->parent_id) ? 'reply' : 'comment';
				}
				else {
					$notification->type = 'general';
				}

				$original_caption = $notification->post->caption;
				// Create category urls & post url..
		        $category_name = '';
		        if (!empty($notification->post->category)) {
		            $category_name = $notification->post->category->category_name;
		            $notification->post->category->category_name_url = str_slug_ovr($category_name);
		        }
		        $subcategory_name = '';
		        $subCategory = $notification->post->subCategory;
		        if (!empty($subCategory)) {
		            $subcategory_name = $notification->post->subCategory->category_name;
		            $notification->post->subCategory->subcategory_name_url = str_slug_ovr($subcategory_name);
		        }
	        	// Create the array for post url.
	            $post_url_args = [
	                'id' => $notification->post->id,
	                'caption' => $original_caption,
	                'title' => $notification->post->title,
	                'post_type' => $notification->post->post_type,
	                'category_name' => $category_name,
	                'subcategory_name' => $subcategory_name
	            ];
	            $post_url = post_url($post_url_args);
	            $notification->post->post_url = $post_url;

	            // Set child_post_id
	            $notification->post->child_post_id = $notification->post->id;
	            // Set parent post id.
	            if($notification->post->orginal_post_id > 0 && $notification->post->orginalPost->id > 0) {
	            	$notification->post->parent_post_id = $notification->post->orginalPost->id;
	            }
	            else {
	            	$notification->post->parent_post_id = $notification->post->id;
	            }
			}
			// Push to array.
            $notifications[] = $notification;
        }

        // dd($notifications->toArray());

		$response = [
			'notifications' => $notifications
		];
		return response()->json($response);
	}

    public function getNotification(Request $request)
	{
        $notification_perpage = 10;
        $page = 1;
        $maxLimit = 50;
        if ($request->has('perpage')) {
            $perpage = $request->input('perpage');
            $notification_perpage = $perpage < $maxLimit ? $perpage : $maxLimit;
        }
        if(!empty($request->input('page'))) {
            $page = $request->input('page');
        }
        // $notification_perpage = 2; // For test only.
        $offset = ($page - 1) * $notification_perpage;

		// Initialize data.
		$notifications = [];
		
		// Column selection array
	    $user_columns = ['id', 'username', 'first_name'];
	    $post_columns = ['id', 'caption', 'title', 'category_id', 'sub_category_id', 'orginal_post_id', 'post_type'];
	    $category_columns = ['id', 'category_name'];
	    /* ------------ Fetch from notifications (for post upvote, dovote and share) ----------- */
		// Condition for limiting to votes and shares only. //
	    // ======== Count unseen (status 1) notifications ======== */
		$notifications_count =  DB::select("SELECT 
											    COUNT(N1.`id`) AS total
											FROM
											    `notifications` AS `N1`
											        INNER JOIN
											    (SELECT 
											        MAX(id) maxID
											    FROM
											        `notifications`
											    WHERE
											        `post_user_id` = " . Auth::user()->id . " AND `status` = 1
											        AND `user_id` <> 1
											        AND `activity_id` IN (1,2,3,4,5,6)
											    GROUP BY CONCAT(`post_id`, '-', `activity_id`)) N2 ON `N1`.`id` = `N2`.`maxID`
											");
		if (!empty($notifications_count)) {
			$total_notifications = $notifications_count[0]->total;
		}
		else {
			$total_notifications = 0;
		}
		// Fetch notifications along with data.
		$notifications = Notification::from('notifications AS N1')
									->selectRaw('N1.`id`, N1.`activity_id`, N1.`post_id`, N1.`post_user_id`, N1.`user_id`, N1.`status`, N1.`created_at`, N2.total')
									->join(DB::raw("(SELECT MAX(id) maxID, 
										COUNT(CONCAT_WS('-', `post_id`, `activity_id` , `status`)) AS total,
									    CONCAT_WS('-', `post_id`, `activity_id` , `status`) AS custom_group
								        FROM `notifications`
								        WHERE `post_user_id` = " . Auth::user()->id . "
								        AND `user_id` <> 1
								    	/*AND `status` <> 3*/
								    	AND `activity_id` IN (1,2,3,4,5,6)
								        GROUP BY custom_group) N2"),
									function($join) {
							            $join->on('N1.id', '=', 'N2.maxID');
							        })
									->orderByRaw('N1.id desc')
							        ->with([
							        	'post' => function ($query) use ($post_columns) {
			                                $query->addSelect($post_columns);
			                            },
			                            'post.category' => function ($query) use ($category_columns) {
			                                $query->addSelect($category_columns);
			                            },
			                            'post.subCategory' => function ($query)  use ($category_columns) {
			                                $query->addSelect($category_columns);
			                            },
			                            'user' => function ($query) use ($user_columns) {
			                                $query->addSelect($user_columns);
			                            }
			                        ])
							        ->skip($offset)->take($notification_perpage)->get();
        /* ------- Fetch from comment_notifications (for comment on a post and reply on comment) ------- */
        $total_comment_notifications = DB::select("SELECT 
												    COUNT(N1.`id`) AS total
												FROM
												    `comment_notifications` AS `N1`
												        INNER JOIN
												    (SELECT 
												        MAX(`id`) maxID,
												        `post_id`,
												        `parent_id`,
												        `user_id`,
												        CASE
                                                            WHEN `parent_id` IS NULL 
                                                                THEN CONCAT(`post_id`, '-', 'MAIN')
                                                            ELSE `parent_id`
                                                        END as custom_group
												    FROM
												        `comments`
												    WHERE
												        `user_id` <> " . Auth::user()->id . "
												    GROUP BY custom_group) C ON `N1`.`comment_id` = `C`.`maxID`
												WHERE
												    N1.`status` = 1
												    AND N1.`user_id` <> 1
												        AND notified_user_id = " . Auth::user()->id);
        if (!empty($total_comment_notifications)) {
        	$total_comment_notifications = $total_comment_notifications[0]->total;
        }
        else {
        	$total_comment_notifications = 0;
        }
        // Add total_comment_notifications to final total.
		$total_notifications += $total_comment_notifications;

        /*if(!empty($_REQUEST['test']))
            \DB::connection()->enableQueryLog();*/

		$comment_columns = ['id', 'post_id', 'user_id'];
		$comment_notifications = CommentNotification::from('comment_notifications as N1')
									->selectRaw('N1.`id`, N1.`activity_id`, N1.`comment_id`, N1.`user_id`, N1.`status`, N1.`created_at`, C.total, C.post_id, C.parent_id')
									->join(DB::raw("(
									    SELECT MAX(`id`) maxID, 
									        COUNT(DISTINCT `user_id`) as total,
									         `post_id`,
									         `parent_id`,
									         `user_id`,
									         (SELECT MAX(`status`) FROM `comment_notifications` where `comment_notifications`.`comment_id` = `comments`.`id`) AS sts,
									         CASE
                                                WHEN `parent_id` IS NULL 
                                                    THEN CONCAT_WS('-', `post_id`, 'MAIN', (SELECT sts))
                                                ELSE CONCAT_WS('-', `parent_id`, (SELECT sts))
                                             END as custom_group
                                        FROM `comments`
                                        WHERE `user_id` <> " . Auth::user()->id . "
                                        GROUP BY custom_group) C"),
									function($join) {
							            $join->on('N1.comment_id', '=', 'C.maxID');
							        })
							        /*->whereRaw('N1.`status` <> 3 AND notified_user_id = ' . Auth::user()->id)*/
							        ->whereRaw('notified_user_id = ' . Auth::user()->id . ' AND N1.`user_id` <> 1')
									->orderByRaw('N1.id desc')
							        ->with([
							        	'comment' => function ($query) use ($comment_columns) {
			                                $query->addSelect($comment_columns);
			                            },
							        	'comment.post' => function ($query) use ($post_columns) {
			                                $query->addSelect($post_columns);
			                            },
			                            'comment.post.category' => function ($query) use ($category_columns) {
			                                $query->addSelect($category_columns);
			                            },
			                            'comment.post.subCategory' => function ($query)  use ($category_columns) {
			                                $query->addSelect($category_columns);
			                            },
			                            'comment.user' => function ($query) use ($user_columns) {
			                                $query->addSelect($user_columns);
			                            }
			                        ])
							        ->skip($offset)->take($notification_perpage)->get();
        /*if(!empty($_REQUEST['test'])) {
            $query = \DB::getQueryLog();
            //$lastQuery = end($query);
            echo $query[0]['query'];
            dump($query);
            dd($comment_notifications);
        }*/
        /* ------- Fetch from comment_notifications (for up vote/down vote comment/reply) ------- */
        $total_vote_comment_notifications = DB::select("SELECT 
														    COUNT(*) AS total
														FROM
														    (SELECT 
														        N1.`id`, N1.`activity_id`, N1.`comment_id`
														    FROM
														        `comment_notifications` AS `N1`
														    INNER JOIN `comments` C ON `N1`.`comment_id` = `C`.`id`
														    WHERE
														        N1.`status` = 1
														            AND notified_user_id = ?
														            AND N1.activity_id IN (1 , 2)
														            AND N1.`user_id` <> 1
														    GROUP BY CONCAT(N1.`comment_id`, '-', N1.`activity_id`)) AS CL", 
														[Auth::user()->id]);
        if (!empty($total_vote_comment_notifications)) {
        	$total_vote_comment_notifications = $total_vote_comment_notifications[0]->total;
        }
        else {
        	$total_vote_comment_notifications = 0;
        }
        // Add total_comment_notifications to final total.
        $total_notifications += $total_vote_comment_notifications;

        $comment_columns = ['id', 'post_id', 'user_id'];
        $vote_comment_notifications = CommentNotification::from('comment_notifications as N1')
                                    ->selectRaw('N1.`id`, N1.`activity_id`, N1.`comment_id`, N1.`user_id`, N1.`status`, N1.`created_at`, COUNT(N1.`id`) AS total, C.post_id, C.parent_id')
                                    ->join(DB::raw("`comments` C"), 
                                    function($join) {
                                        $join->on('N1.comment_id', '=', 'C.id');
                                    })
                                    /*->whereRaw('N1.`status` <> 3 
                                                AND notified_user_id = ' . Auth::user()->id . 
                                                ' AND N1.activity_id IN (1 , 2)')*/
                                    ->whereRaw('notified_user_id = ' . Auth::user()->id . 
                                                ' AND N1.activity_id IN (1 , 2) AND N1.`user_id` <> 1')
                                    ->groupBY(DB::raw("CONCAT_WS('-', N1.`comment_id`, N1.`activity_id`, N1.`status`)"))
    								->orderByRaw('N1.id desc')
                                    ->with([
                                        'comment' => function ($query) use ($comment_columns) {
                                            $query->addSelect($comment_columns);
                                        },
                                        'comment.post' => function ($query) use ($post_columns) {
                                            $query->addSelect($post_columns);
                                        },
                                        'comment.post.category' => function ($query) use ($category_columns) {
                                            $query->addSelect($category_columns);
                                        },
                                        'comment.post.subCategory' => function ($query)  use ($category_columns) {
                                            $query->addSelect($category_columns);
                                        },
                                        'user' => function ($query) use ($user_columns) {
                                            $query->addSelect($user_columns);
                                        }
                                    ])
                                    ->skip($offset)->take($notification_perpage)->get();
        // dd($vote_comment_notifications->toArray());
        /* ------------------------------------------------------------------------------ */
        // Merge two types of comment notification collections.
        $merged_comm_notification = $comment_notifications->merge($vote_comment_notifications);

        /* ------------------------- Fetch follow notifications ------------------------------ */
        $total_follow_notifications = DB::select("SELECT 
												    id
												FROM
												    `followers`
												WHERE
												    user_id = ? AND status = 1
												ORDER BY id DESC
												LIMIT 1", [Auth::user()->id]);
        // dd($total_follow_notifications);
        if (!empty($total_follow_notifications)) {
        	$total_follow_notifications = 1;
        }
        else {
        	$total_follow_notifications = 0;
        }
        // Add total_follow_notifications to final total.
        $total_notifications += $total_follow_notifications;
        $follow_notifications = Follower::from('followers as F1')
									->selectRaw("F1.`id`, F1.`status`, total, 'follow' as type, F1.`follower_id`, F1.`created_at`")
									->join(DB::raw("(
									    SELECT MAX(`id`) maxID, COUNT(*) AS total
                                        FROM `followers`
                                        WHERE `user_id` = " . Auth::user()->id . "
                                        GROUP BY `status`) AS F2"),
									function($join) {
							            $join->on('F1.id', '=', 'F2.maxID');
							        })
									->where('user_id', Auth::user()->id)
									/*->where('status', '<>', 3)*/
									->with([
                                        'followed_by' => function ($query) use ($user_columns) {
                                            $query->addSelect($user_columns);
                                        }
                                    ])
                                    ->groupBY('status')
									->orderBy('id', 'desc')
									->get();
        // dd($follow_notifications->toArray());
		// Merge two types of notification collections.
        $all_notifications = $notifications->merge($merged_comm_notification);
        if (!empty($follow_notifications)) {
        	// Merge folllow notifications with all post related notifications.
        	$all_notifications = $all_notifications->merge($follow_notifications);
        }
        
        // Sort based on time.
        $all_notifications_sorted = $all_notifications->sort(function($a, $b){
			$a = $a->created_at;
	        $b = $b->created_at;
	        if ($a === $b) {
	            return 0;
	        }
	        return ($a > $b) ? -1 : 1;
		});
        
        // Assign sorted notification to original variable.
        // $all_notifications_sorted = $all_notifications_sorted->take($notification_perpage);
        // dd($notifications->toArray());
		// Create notification response.
		$notifications = [];
		foreach ($all_notifications_sorted as $key => $notification) {
			if (!empty($notification->type) && $notification->type == 'follow') {
				$notification->activity_id = 13;
				$notification->user = $notification->followed_by;
				unset($notification->followed_by);
			}
			else {
				if(!empty($notification->comment)) {
					$notification->post = $notification->comment->post;
					$notification->user = $notification->comment->user;
					unset($notification->comment->post);
					unset($notification->comment->user);
					// Set type to comments.
					$notification->type = !empty($notification->parent_id) ? 'reply' : 'comment';
				}
				else {
					$notification->type = 'general';
				}

				$original_caption = $notification->post->caption;
				// Create category urls & post url..
		        $category_name = '';
		        if (!empty($notification->post->category)) {
		            $category_name = $notification->post->category->category_name;
		            $notification->post->category->category_name_url = str_slug_ovr($category_name);
		        }
		        $subcategory_name = '';
		        $subCategory = $notification->post->subCategory;
		        if (!empty($subCategory)) {
		            $subcategory_name = $notification->post->subCategory->category_name;
		            $notification->post->subCategory->subcategory_name_url = str_slug_ovr($subcategory_name);
		        }
	        	// Create the array for post url.
	            $post_url_args = [
	                'id' => $notification->post->id,
	                'caption' => $original_caption,
	                'title' => $notification->post->title,
	                'post_type' => $notification->post->post_type,
	                'category_name' => $category_name,
	                'subcategory_name' => $subcategory_name
	            ];
	            $post_url = post_url($post_url_args);
	            $notification->post->post_url = $post_url;

	            // Set child_post_id
	            $notification->post->child_post_id = $notification->post->id;
	            // Set parent post id.
	            if($notification->post->orginal_post_id > 0 && $notification->post->orginalPost->id > 0) {
	            	$notification->post->parent_post_id = $notification->post->orginalPost->id;
	            }
	            else {
	            	$notification->post->parent_post_id = $notification->post->id;
	            }
			}
			// Push to array.
            $notifications[] = $notification;
        }

        // dd($notifications->toArray());

		$response = [
			'total_notifications' => $total_notifications,
			'notifications' => $notifications

		];
		return response()->json($response);
	}

	public function markNotificationRead(Request $request)
	{
		if (! $request->has('id')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'id' parameter",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}

        $notification = 0;

		$id = $request->input('id');
		$total = $request->input('total');
        $type = $request->input('type');
		$clear_type = $request->input('clear_type');
        if ($type == 'general') {
            // Condition for combined notification. Bulk action.
            if($total > 1) {
                $bulk_notification = Notification::find($id);
                $condition = [
                    // 'activity_id' => $bulk_notification->activity_id,
                    'post_id' => $bulk_notification->post_id,
                    'post_user_id' => Auth::user()->id
                ];
                if ($clear_type == 'single_ac') {
                    $condition['activity_id'] = $bulk_notification->activity_id;
                }
            }
            else {
                $condition = [
                    'id' => $id,
                    'post_user_id' => Auth::user()->id
                ];
            }           
            $notification = Notification::where($condition)->update(['status' => 3]);
        }
        // For comment and reply.
        else if ($type == 'comment' || $type == 'reply') {
            // Condition for combined notification. Bulk action.
            if($total > 1) {
                $activity_id = $request->input('activity_id');
                $bulk_notification = CommentNotification::find($id);
                // Fetch the comment.
                $comment = $bulk_notification->comment;
                $comment_id = $bulk_notification->comment->id;
                $parent_id = $bulk_notification->comment->parent_id;
                // Fetch the post.
                $post = $bulk_notification->comment->post;
                $post_id = $post->id;
                // For comment on post.
                if ($activity_id == 7) {
                    $notification = CommentNotification::where('notified_user_id', Auth::user()->id)
                                                        ->where('activity_id', 7)
                                                        ->whereIn('comment_id', function($query) use($post_id) {
                                                            $query->select('id')
                                                                    ->from('comments')
                                                                    ->where('post_id', $post_id);
                                                        })
                                                        ->update(['status' => 3]);
                }
                // For reply to comment.
                else if ($activity_id == 12) {
                    $notification = CommentNotification::where('notified_user_id', Auth::user()->id)
                                                        ->where('activity_id', 12)
                                                        ->whereIn('comment_id', function($query) use($parent_id) {
                                                            $query->select('id')
                                                                    ->from('comments')
                                                                    ->where('parent_id', $parent_id);
                                                        })
                                                        ->update(['status' => 3]);
                }
                // For upvote/downvote comment/reply.
                else if ($activity_id == 1 || $activity_id == 2) {
                    $notification = CommentNotification::where('notified_user_id', Auth::user()->id)
                                                        ->where('activity_id', $activity_id)
                                                        ->where('comment_id', $bulk_notification->comment_id)
                                                        ->update(['status' => 3]);
                }
            }
            else {
                $condition = [
                    'id' => $id,
                    'notified_user_id' => Auth::user()->id
                ];
                $notification = CommentNotification::where($condition)->update(['status' => 3]);
            }
        }
        // For follow.
        else if ($type == 'follow') {
        	// Condition for combined notification. Bulk action.
            if($total > 1) {
            	// Clear follower notifications.
            	$notification = Follower::where('user_id', Auth::user()->id)->update(['status' => 3]);
            }
            else {
            	$notification = Follower::where('id', $id)->update(['status' => 3]);
            }
        }
    		

		if ($notification) {
			$response['status'] = 1;
		}
		else {
			$response['status'] = 0;
		}
		return $notification;
	}

	public function markNotificationSeen(Request $request)
	{
		/*if (! $request->has('notifi')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'notifi' parameter",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}
		$notifications = $request->input('notifi');
		foreach ($notifications as $notification) {
			$this->markNotificationSeenProcess($notification);
		}*/

		// New code
		// Set status
		$changeToStatus = 2;
		$notification = Notification::where('status', 1)
									->where('post_user_id', Auth::user()->id)
									->update(['status' => $changeToStatus]);
		$comment_notification = CommentNotification::where('status', 1)
                									->where('notified_user_id', Auth::user()->id)
                									->update(['status' => $changeToStatus]);
		$follower = Follower::where('status', 1)->where('user_id', Auth::user()->id)
												->update(['status' => $changeToStatus]);

        $response = [
            'status' => 'SUCCESS'
        ];
        return response()->json($response);
	}

	protected function markNotificationSeenProcess($data)
	{
		$notification = 0;
		// Load all data.
		$id = $data['id'];
		$total = $data['total'];
        $type = $data['type'];
        $activity_id = $data['activity_id'];
		$clear_type = 'single_ac';

		// Set status
		$changeToStatus = 2;

		if ($type == 'general') {
            // Condition for combined notification. Bulk action.
            if($total > 1) {
                $bulk_notification = Notification::find($id);
                $condition = [
                    // 'activity_id' => $bulk_notification->activity_id,
                    'post_id' => $bulk_notification->post_id,
                    'post_user_id' => Auth::user()->id
                ];
                if ($clear_type == 'single_ac') {
                    $condition['activity_id'] = $bulk_notification->activity_id;
                }
            }
            else {
                $condition = [
                    'id' => $id,
                    'post_user_id' => Auth::user()->id
                ];
            }           
            $notification = Notification::where('status', 1)->where($condition)->update(['status' => $changeToStatus]);
        }
        // For comment and reply.
        else if ($type == 'comment' || $type == 'reply') {
            // Condition for combined notification. Bulk action.
            if($total >= 1) {
                $bulk_notification = CommentNotification::find($id);
                // Fetch the comment.
                $comment = $bulk_notification->comment;
                $comment_id = $bulk_notification->comment->id;
                $parent_id = $bulk_notification->comment->parent_id;
                // Fetch the post.
                $post = $bulk_notification->comment->post;
                $post_id = $post->id;
                // For comment on post.
                if ($activity_id == 7) {

                    $notification = CommentNotification::where('status', 1)
                										->where('notified_user_id', Auth::user()->id)
                                                        ->where('activity_id', 7)
                                                        ->whereIn('comment_id', function($query) use($post_id) {
                                                            $query->select('id')
                                                                    ->from('comments')
                                                                    ->where('post_id', $post_id);
                                                        })
                                                        ->update(['status' => $changeToStatus]);
                }
                // For reply to comment.
                else if ($activity_id == 12) {
                    $notification = CommentNotification::where('status', 1)
                    									->where('notified_user_id', Auth::user()->id)
                                                        ->where('activity_id', 12)
                                                        ->whereIn('comment_id', function($query) use($parent_id) {
                                                            $query->select('id')
                                                                    ->from('comments')
                                                                    ->where('parent_id', $parent_id);
                                                        })
                                                        ->update(['status' => $changeToStatus]);
                }
                // For upvote/downvote comment/reply.
                else if ($activity_id == 1 || $activity_id == 2) {
                    $notification = CommentNotification::where('status', 1)
                    									->where('notified_user_id', Auth::user()->id)
                                                        ->where('activity_id', $activity_id)
                                                        ->where('comment_id', $bulk_notification->comment_id)
                                                        ->update(['status' => $changeToStatus]);
                }
            }
            else {
                $condition = [
                    'id' => $id,
                    'notified_user_id' => Auth::user()->id
                ];
                $notification = CommentNotification::where('status', 1)->where($condition)->update(['status' => $changeToStatus]);
            }
        }
        // For follow.
        else if ($type == 'follow') {
        	// Condition for combined notification. Bulk action.
            if($total > 1) {
            	// Clear follower notifications.
            	$notification = Follower::where('status', 1)->where('user_id', Auth::user()->id)->update(['status' => $changeToStatus]);
            }
            else {
            	$notification = Follower::where('status', 1)->where('id', $id)->update(['status' => $changeToStatus]);
            }
        }
    		

		if ($notification) {
			$response['status'] = 1;
		}
		else {
			$response['status'] = 0;
		}
		return $response;
	}
}
