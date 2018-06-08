<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Events\CommentPosted;
use App\Events\ReplyPosted;
use App\Events\CommentDeleted;
use App\Events\CommentVoted;
use App\Models\CommentNotification;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
	public function postComment(Request $request)
    {
      
        $user_id = Auth::user()->id;
        $post_id = $request->input('post_id');
        $child_post_id = $request->input('child_post_id');
       // dd($request->all());
        $parent_id = $request->input('parent_id');
        $message = $request->input('message');

        if (empty($message)) {
            $response = [
                'status' => 'failed'
            ];
            return response()->json($response);
        }
		
        $newComment = Comment::create([
            'post_id' => $post_id,
            'user_id' => $user_id,

            'message' => $message
        ]);
        $lastInsertId = $newComment->id;

        // Child post comments .......
        if ($post_id != $child_post_id) {
            $child_post_id = $request->input('child_post_id');
            $childPostComment = Comment::create([
                'post_id' => $child_post_id,
                'user_id' => $user_id,

                'message' => $message
            ]);
        }

        $user_collumns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'points'];

      

        $postTotalComment = Comment::where('post_id', $post_id)->get()->count();
        $postParentComment = Comment::where(['post_id' => $post_id])->whereNull('parent_id')->get()->count();

        $lastComment = Comment::where('id', $lastInsertId)
            ->with([
                'user' => function ($query) use ($user_collumns) {
                    $query->addSelect($user_collumns);
                },
                'childComment'
            ])->first();

     


        $lastComment->show = 'true';

        /*---------------------------------*/
        $post = Post::find($post_id);
        $postUser = User::where(['id' => $post->created_by])->select($user_collumns)->first();
        // Create comment notification.
        if ($user_id != $post->created_by) {
            $notification = CommentNotification::create([
                'activity_id' => 7,
                'user_id' => $user_id,
                'comment_id' => $lastInsertId,
                'notified_user_id' => $postUser->id
            ]);
        }

        $event_data = [
            'post_id' => $post_id,
            'lastComment' => $lastComment->toArray(),
            'postTotalComment' => $postTotalComment,
            'postParentComment' => $postParentComment
        ];

       

        // Broadcast comment posted event.
        event(new CommentPosted($event_data, $post_id));

        $response = [
        	'status' => 'success'
        ];

        return response()->json($response);
    }

    public function postReply(Request $request)
    {
        $user_id = Auth::user()->id;
        
        $post_id = $request->input('post_id');
        $parent_id = $request->input('parent_id');
        $message = $request->input('message');
        $child_post_id = $request->input('child_post_id');

        $lastComment = [];

        $user_collumns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'points'];

        $newComment = Comment::create([
            'post_id' => $post_id,
            'user_id' => $user_id,
            'parent_id' => $parent_id,
            'message' => $message
        ]);
        $lastInsertId = $newComment->id;

        if ($request->has('child_post_id') && $child_post_id != $post_id) {

            $childReplyComments = Comment::create([
                'post_id' => $child_post_id,
                'user_id' => $user_id,
                'parent_id' => $parent_id,
                'message' => $message
            ]);
        }

        $count_child = Comment::where(['post_id' => $post_id, 'parent_id' => $parent_id])->count();

        $lastComment = Comment::select(DB::raw('*, GetRootCommentById(id) AS root_comment_id'))
            ->where('id', $lastInsertId)
            ->with([
                'user' => function ($query) use ($user_collumns) {
                    $query->addSelect($user_collumns);
                },
                'childComment'
            ])
            ->first();
        $lastComment->count_child = 0;
        $lastComment->parentCommentTotalchildPost = $count_child;
        $lastComment->show = 'true';
		$lastComment->isUpvote = 'N';
        $lastComment->isDownvote = 'N';
		$lastComment->total_upvotes = 0;


       // $postTotalComment = Comment::where('post_id', $post_id)->get()->count();(3-11-17)
       /*****added on (3-11-*17) */
       $sqlQuery = "SELECT C.id,sum(IF(A.activity_id = '1', 1, NULL)) as `upvotes`, 
       sum(IF(A.activity_id = '2', 1, NULL)) as `downvotes`, 
       (upvotes-downvotes) as 'total_upvotes'
       FROM comments  AS C 
       JOIN  activity_comment AS A ON C.id=A.comment_id 
       WHERE (C.post_id='" . $post_id . "' AND C.parent_id is null)
      
       AND ((upvotes-downvotes) > 0 AND C.user_id!=A.user_id)
       GROUP BY C.id ORDER BY (upvotes-downvotes) desc ,C.created_at desc";

       $postTotalComment = count(DB::select($sqlQuery));

        /*****added on (3-11-*17) end  */


        $postParentComment = Comment::where(['post_id' => $post_id])->whereNull('parent_id')->get()->count();

        // Create comment notification.
        if ($user_id != $newComment->parentComment->user_id) {
            $notification = CommentNotification::create([
                'activity_id' => 12,
                'user_id' => $user_id,
                'comment_id' => $lastInsertId,
                'notified_user_id' => $newComment->parentComment->user_id
            ]);
        }

        $event_data = [
            'post_id' => $post_id,
            'lastComment' => $lastComment->toArray(),
            'postTotalComment' => $postTotalComment,
            'postParentComment' => $postParentComment
        ];

        // Broadcast reply posted event.
        event(new ReplyPosted($event_data, $post_id));

        $response = [
        	'status' => 'success'
        ];

        return response()->json($response);
    }

    public function deleteComment(Request $request)
    {
        $comment_id = $request->input('comment_id');
        $parent_id = $request->input('parent_id');
        $post_id = $request->input('postId');

        $fetchComments = Comment::where('id', $comment_id)->first();

        $comment = Comment::where([
        	'id' => $comment_id,
        	'user_id' => Auth::user()->id
        ]);

        if ($comment->get() !== null) {
            $comment->delete();
            $postTotalComment = Comment::where(['post_id' => $post_id])->get()->count();
            $postParentComment = Comment::where(['post_id' => $post_id])->whereNull('parent_id')->get()->count();

            $event_data = [
	            'comment_id' => $comment_id,
	            'user_id' => Auth::user()->id,
	            'parent_id' => $parent_id,
	            'postParentComment' => $postParentComment,
	            'postTotalComment' => $postTotalComment,
	            'comment_parent_id' => $fetchComments->parent_id
	        ];
	        event(new CommentDeleted($event_data, $post_id));
	        $response['status'] = 'SUCCESS';
        }
        else {
        	$response['status'] = 'INVALID_REQUEST';
        	return response()->json($response, 400);
        }
        return response()->json($response);
    }

    public function voteComment(Request $request)
    {
        $user_id = Auth::user()->id;
        $comment_id = $request->input('commentID');
        $activityType = $request->input('activityType');
        $post_id = $event_post_id = $request->input('post_id');
        $temp = $request->input('temp');

		$user_collumns = ['id', 'points'];
		 
		$comment = Comment::where('id', $comment_id)
							->with([
			'user' => function ($query) use ($user_collumns) {
				$query->addSelect($user_collumns);
			}
		])->first();
        $commentUser = $comment->user;

        /******** DELETE OTHER ACTIVITY TYPE AND CALCULATION POINT ********/
        if ($activityType == 1) {
            $otherActivityType = 2;
        }
        if ($activityType == 2) {
            $otherActivityType = 1;
        }

        $otherActivity_comment = DB::table('activity_comment')->where([
        	'comment_id' => $comment_id,
        	'user_id' => $user_id,
        	'activity_id' => $otherActivityType
        ])->first();

        $otherCommentNotification = CommentNotification::where([
        	'comment_id' => $comment_id,
        	'user_id' => $user_id,
        	'activity_id' => $otherActivityType
        ])->first();

        if ($otherActivity_comment !== null) {
            $otherActivity_post = DB::table('activity_comment')->where([
                'comment_id' => $comment_id,
                'user_id' => $user_id,
                'activity_id' => $otherActivityType])->delete();

            if ($otherCommentNotification !== null) {
                $otherCommentNotification->delete();
            }

            if ($user_id != $commentUser->id) {
                if ($otherActivityType == 1) {
                    $commentUser->points = $commentUser->points - 2;
                    $commentUser->save();
                }
                if ($otherActivityType == 2) {
                    $commentUser->points = $commentUser->points + 2;
                    $commentUser->save();
                }
            }

            if ($otherActivityType == 1) {
                $comment->upvotes = $comment->upvotes - 1;
            } elseif ($otherActivityType == 2) {
                $comment->downvotes = $comment->downvotes - 1;
            }
            $comment->save();
        }

        /******** DELETE OTHER ACTIVITY TYPE AND CALCULATION POINT ********/
        $activity_comment = DB::table('activity_comment')->where(['comment_id' => $comment_id, 'user_id' => $user_id, 'activity_id' => $activityType])->first();

        $commentNotification = CommentNotification::where(['comment_id' => $comment_id, 'user_id' => $user_id, 'activity_id' => $activityType])->first();

        if ($activity_comment !== null) {
            $activity_post = DB::table('activity_comment')->where(['comment_id' => $comment_id, 'user_id' => $user_id, 'activity_id' => $activityType])->delete();

            if ($commentNotification !== null) {
                $commentNotification->delete();
            }

            if ($user_id != $commentUser->id) {
                if ($activityType == 1) {
                    $commentUser->points = $commentUser->points - 2;
                    $commentUser->save();
                }
                if ($activityType == 2) {
                    $commentUser->points = $commentUser->points + 2;
                    $commentUser->save();
                }
            }

            if ($activityType == 1) {
                $comment->upvotes = $comment->upvotes - 1;
            } elseif ($activityType == 2) {
                $comment->downvotes = $comment->downvotes - 1;
            }
            $comment->save();
        } 
        else {
            DB::table('activity_comment')->insert([
                'comment_id' => $comment_id,
                'user_id' => $user_id,
                'activity_id' => $activityType
            ]);

            if ($user_id != $commentUser->id) {
                if ($commentNotification == null) {
                    CommentNotification::create([
                        'comment_id' => $comment_id,
                        'user_id' => $user_id,
                        'activity_id' => $activityType,
                        'notified_user_id' => $commentUser->id,
                        'status' => 1
                    ]);
                }

                if ($activityType == 1) {
                    $commentUser->points = $commentUser->points + 2;
                    $commentUser->save();
                }
                if ($activityType == 2) {
                    $commentUser->points = $commentUser->points - 2;
                    $commentUser->save();
                }
            }

            if ($activityType == 1) {
                $comment->upvotes = $comment->upvotes + 1;
            } elseif ($activityType == 2) {
                $comment->downvotes = $comment->downvotes + 1;
            }
            $comment->save();
        }

        $post = Post::where('id', $post_id)
        ->with([
			'user' => function ($query) use ($user_collumns) {
                $query->addSelect($user_collumns);
            }
        ])
        ->first(['id', 'created_by', 'parent_post_user_id', 'orginal_post_id']);
        if (!empty($post->orginal_post_id)) {
            $post->createdUser = DB::table('posts')
            ->join('users', 'posts.created_by', '=', 'users.id')
            ->select('users.id', 'users.points')
            ->where('posts.id', $post->orginal_post_id)
            ->first();
            // For shared post.
            $event_post_id = $post->orginal_post_id;
        }

        $event_data = [
            'post_id' => $event_post_id,
            'user_id' => $user_id,
            'activityType' => $activityType,
            'comment' => $comment->toArray()
        ];

        // Broadcast comment posted event.
        event(new CommentVoted($event_data, $event_post_id));

        $response = [
            'event_post_id' => $event_post_id,
        	'status' => 'success'
        ];
        return response()->json($response);
    }

    public function fetchReportCommentData(Request $request)
    {
        if (! $request->has('comment_id')) {
            $response = [
                'error_message' => "Invalid request. Missing the 'comment_id' parameter",
                'status' => 'INVALID_REQUEST'
            ];
            return response()->json($response, 400);
        }
        $comment_id = $request->input('comment_id');

        $cond = [
            'comment_id' => $comment_id,
            'user_id' => Auth::user()->id
        ];

        $post_report_ids = DB::table('comment_report')->where($cond)->pluck('report_id');

        $response = [
            'report_ids' => $post_report_ids
        ];
        return response()->json($response);
    }
}
