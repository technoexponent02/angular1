<?php

namespace App\Http\Controllers;

use DB;
use Image;
use Mail;
use Auth;
use Response;
use Session;
use Storage;
use Carbon\Carbon;

use App\Events\PostUpvoted;
use App\Events\UserFollowed;

use App\Models\User;
use App\Models\Userview;
use App\Models\Follower;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\CategoryFollower;
use App\Models\Notification;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class AngularController extends Controller
{

    protected $per_page;
    protected $offset;
    //private $mailer;

    /**
     * AngularController constructor.
     * build the pagination logic
     *
     * @author <tuhin@technoexponent.com>
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        //$mailer = new UserMailer;
        //$this->mailer = $mailer;

        $this->per_page = 10;//config('constants.PER_PAGE');
        // Calculation for pagination
        $page = 1;
        if (!empty($request->input('page'))) {
            $page = $request->input('page');
        }
        $this->offset = ($page - 1) * $this->per_page;

    }

    public function getProfileJson(Request $request)
    {
        if ($request->has('username')) {
            $cond = [
                'username' => $request->input('username')
            ];
        } else {
            if (Auth::check()) {
                $cond = [
                    'id' => Auth::user()->id
                ];
            }
            else {
                $response = [
                    'error_message' => "Invalid request. The profile does not exists.",
                    'status' => 'INVALID_REQUEST'
                ];
                return response()->json($response, 404);
            }

        }

        // Column selection array.
        $follower_columns = ['id', 'user_id', 'follower_id'];
        $collection_columns = ['id', 'collection_name', 'collection_text'];

        $user = User::where($cond)
            ->withCount('collection')
            ->with(['follower' => function ($query) use ($follower_columns) {
                $query->addSelect($follower_columns);
            },
                'following' => function ($query) use ($follower_columns) {
                    $query->addSelect($follower_columns);
                },
                'collection' => function ($query) use ($collection_columns) {
                    $query->addSelect($collection_columns);
                }
            ])
            ->first();

        //$user->dob = $user->dob = ((string)$user->dob=="0000:00:00") ? "" : $user->dob ;

        if ($user === null) {
            $response = [
                'error_message' => "Invalid request. The profile does not exists.",
                'status' => 'INVALID_REQUEST'
            ];
            return response()->json($response, 404);
        }


        $follower_count = $user->follower->count();
        $following_count = $user->following->count();

        $posts = Post::where('created_by', $user->id);
        $viewerIp = $request->ip();

        if (Auth::check()) {
            if ($user->id != Auth::user()->id) {
                $isIamFollower = Follower::where([
                    'user_id' => $user->id,
                    'follower_id' => Auth::user()->id
                ])->get();
                if (!empty($isIamFollower)) {
                    $posts = $posts->whereIn("privacy_id", [1, 2]);
                } else {
                    $posts = $posts->whereIn("privacy_id", [1]);
                }

                $posts=$posts->where("ask_anonymous",'0');//(16-01-18) for remove the anonymous post

            } else {
                $posts = $posts->whereIn("privacy_id", [1, 2, 3]);
            }
        } else {
            $posts = $posts->whereIn("privacy_id", [1]);
            $posts=$posts->where("ask_anonymous",'0');//(16-01-18) for remove the anonymous post
        }

        $post_count = $posts->count();

        // Profile view  process ::
        if (Auth::check()) {
            if ($user->id != Auth::user()->id) {

                $profileView = Userview::where([
                    'user_id' => $user->id,
                    'viewer_id' => Auth::user()->id
                ])
                    ->orderBy('created_at', 'DESC')
                    ->skip(0)
                    ->take(1)
                    ->first();

                if ($profileView != null) {
                    $oneHourAgo = Carbon::now()->subHour(1);
                    $profileview_created_at = Carbon::createFromFormat('Y-m-d H:i:s', $profileView->created_at, 'UTC');
                    if ($profileview_created_at->lt($oneHourAgo)) {
                        Userview::create([
                            'user_id' => $user->id,
                            'viewer_id' => Auth::user()->id,
                            'ip_address' => $viewerIp
                        ]);
                    }
                } else {
                    Userview::create([
                        'user_id' => $user->id,
                        'viewer_id' => Auth::user()->id,
                        'ip_address' => $viewerIp
                    ]);
                }
            }
        } else { // For anonymous user  whose ID is 1 

            $profileView = Userview::where([
                'user_id' => $user->id,
                'viewer_id' => 1,
                'ip_address' => $viewerIp
            ])
                ->orderBy('created_at', 'DESC')
                ->skip(0)
                ->take(1)
                ->first();

            if ($profileView != null) {
                $oneHourAgo = Carbon::now()->subHour(1);
                $profileview_created_at = Carbon::createFromFormat('Y-m-d H:i:s', $profileView->created_at, 'UTC');
                if ($profileview_created_at->lt($oneHourAgo)) {
                    Userview::create([
                        'user_id' => $user->id,
                        'viewer_id' => 1,
                        'ip_address' => $viewerIp
                    ]);
                }
            } else {
                Userview::create([
                    'user_id' => $user->id,
                    'viewer_id' => 1,
                    'ip_address' => $viewerIp
                ]);
            }

        }
        // end profile view  process 
        $userDataProfileViews = Userview::where(['user_id' => $user->id])->count();

        if (!empty($user->cover_image)) {
            $user->cover_image = generate_profile_image_url('profile/cover/' . $user->cover_image);
        }

        $data = [
            'user' => $user,
            'total_post' => $post_count,
            'userDataProfileViews' => $userDataProfileViews,
            'follower_count' => $follower_count,
            'following_count' => $following_count
        ];
        return $data;
    }


    public function loadMorePost(Request $request)
    {
        // Initialize data.
        $posts = [];
        $final_posts = [];
        $total_posts = 0;
        $privacyWishTotalPost = 0;

        if ($request->has('username')) {
            $username = $request->input('username');
            $user = User::where('username', $username)->first();
            $profileID = $user->id;
        }
        else if (Auth::check()) {
        	$profileID = Auth::user()->id;
        }
        else {
            goto return_area;
        }

        // Set post_view_type
        $post_view_type = 'recent';
        if ($request->has('post_view_type')) {
            $post_view_type = $request->input('post_view_type');
        }

        $posts = Post::where('created_by', $profileID);
        //	->removeReported(Auth::user()->id);

        if (Auth::check()) {
            if ($profileID != Auth::user()->id) {
                $isIamFollower = Follower::where([
                    'user_id' => $profileID,
                    'follower_id' => Auth::user()->id
                ])
                    ->get();
                if (count($isIamFollower) > 0) {
                    $posts = $posts->whereIn("privacy_id", [1, 2]);
                } else {
                    $posts = $posts->whereIn("privacy_id", [1]);
                }


                $posts=$posts->where("ask_anonymous",'0');//(16-01-18) for remove the anonymous post


            } else {
                $posts = $posts->whereIn("privacy_id", [1, 2, 3]);
            }
        } else {
            $posts = $posts->whereIn("privacy_id", [1]);

            $posts=$posts->where("ask_anonymous",'0');//(16-01-18) for remove the anonymous post
        }

        $privacyWishTotalPost = $posts->count();

        // Condition based on post types.
        if ($post_view_type === 'recent') {
            $posts->orderBy('created_at', 'desc');
        } elseif ($post_view_type === 'popular') {

            /* 
             * Get the public and "followers only" posts of user's
             * whom the logged in user following.
             */
            if (Auth::check()) {
                $public_or_follower_post =
                    Post::where('privacy_id', 1)
                        ->orWhere(function ($query) {
                            $query->where('privacy_id', 2)
                                ->whereIn('id', function ($query) {
                                    $query->select('id')
                                        ->from('posts')
                                        ->whereIn('created_by', function ($q2) {
                                            $q2->select('user_id')
                                                ->from('followers')
                                                ->where('follower_id', Auth::user()->id);
                                        })
                                        ->orWhere('created_by', Auth::user()->id);
                                });
                        })
                        ->orderBy('id', 'desc')
                        ->get(['id']);
            } else {
                $public_or_follower_post = Post::where('privacy_id', 1)
                    ->orderBy('id', 'desc')
                    ->get(['id']);
            }
            $public_or_follower_post_ids = array_pluck($public_or_follower_post, 'id');

            $posts->where('points', '>', 0);
            $day = 7;
            $activity_posts = DB::table('activity_post')
                ->where('created_at', '>=', Carbon::now()->subDays($day));
            // ->orderBy('post_id')
            // Prepare activity_post_id for from posts created by the profile owner.
            /*$activity_post_id = Post::where('created_by', $profileID)->get(['id']);*/
            // Add post_id to query activity_post.
            $activity_posts = $activity_posts->whereIn('post_id', function ($query) use ($profileID) {
                $query->select('id')
                    ->from('posts')
                    ->where('created_by', $profileID);
            })
                // Remove activity by the post creater.
                ->where('user_id', '<>', $profileID)
                ->get(['activity_id', 'post_id']);

            $activityPostSort = [];
            foreach ($activity_posts as $key => $activity_post) {
                // Initialize.
                if (empty($activityPostSort[$activity_post->post_id]['point'])) {
                    $activityPostSort[$activity_post->post_id]['point'] = 0;
                }
                if (empty($activityPostSort[$activity_post->post_id]['upvote'])) {
                    $activityPostSort[$activity_post->post_id]['upvote'] = 0;
                }
                if (empty($activityPostSort[$activity_post->post_id]['share'])) {
                    $activityPostSort[$activity_post->post_id]['share'] = 0;
                }
                // determine point based on activity.
                $point = 0;
                if ($activity_post->activity_id == 1) {
                    $point = 2;
                    $activityPostSort[$activity_post->post_id]['upvote'] += 1;
                } elseif ($activity_post->activity_id == 2) {
                    $point = -2;
                } elseif ($activity_post->activity_id == 3) {
                    $point = 10;
                    $activityPostSort[$activity_post->post_id]['share'] += 1;
                } elseif ($activity_post->activity_id == 4) {
                    $point = 10;
                } elseif ($activity_post->activity_id == 5) {
                    $point = 10;
                } elseif ($activity_post->activity_id == 8) {
                    $point = 2;
                } elseif ($activity_post->activity_id == 9) {
                    $point = 2;
                } elseif ($activity_post->activity_id == 10) {
                    $point = 2;
                } elseif ($activity_post->activity_id == 11) {
                    $point = 2;
                }

                $activityPostSort[$activity_post->post_id]['point'] += $point;
                $activityPostSort[$activity_post->post_id]['post_id'] = $activity_post->post_id;
            }
            /* ===============================================================
             * Sort the array in followig order:
             * 1. point, 2. upvote, 3. share
            ================================================================*/
            usort($activityPostSort, function ($item1, $item2) {
                if ($item1['point'] == $item2['point']) {
                    if ($item1['upvote'] == $item2['upvote']) {
                        if ($item1['share'] == $item2['share']) {
                            // Sort by latest post.
                            return $item2['post_id'] < $item1['post_id'] ? -1 : 1;
                        }
                        return $item2['share'] < $item1['share'] ? -1 : 1;
                    }
                    return $item2['upvote'] < $item1['upvote'] ? -1 : 1;
                }
                return $item2['point'] < $item1['point'] ? -1 : 1;
            });

            /*if (!empty($_REQUEST['test'])) {
                dd($activityPostSort);
            }*/

            $sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
            // dd($post_ids);
            // Take  intersection..
            $post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);

            if (!empty($post_ids)) {
                // $post_ids_ordered = implode(',', $post_ids);
                $post_ids_ordered = implode(',', $sorted_activity_post_ids);

                $posts->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
            } else {
                goto return_area;
            }
        }

        if ($request->has('postType') && $request->input('postType') != 'all') {
            $posts = $posts->where('post_type', $request->input('postType'));
            $total_posts = $posts->where('post_type', $request->input('postType'))->count();
        } else {
            $total_posts = $posts->count();
        }

        // Collumn selection array
        $user_collumns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
        $category_collumns = ['id', 'category_name'];
        $subCategory_collumns = ['id', 'category_name'];
        $tag_collumns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
        $featurePhotoDetail_column = ['id', 'post_id', 'thumb_width', 'thumb_height', 'data_url'];
        $region_columns = ['id', 'name', 'slug_name'];

        /*if (!empty($_REQUEST['test'])) {
            \DB::connection()->enableQueryLog();
        }*/

        $posts = $posts->with([
            'user' => function ($query) use ($user_collumns) {
                $query->addSelect($user_collumns);
            },
            'category' => function ($query) use ($category_collumns) {
                $query->addSelect(array('id', 'category_name'));
            },
            'subCategory' => function ($query) use ($subCategory_collumns) {
                $query->addSelect(array('id', 'category_name'));
            },
            'country',
            'country.region' => function ($query) use ($region_columns) {
                $query->addSelect($region_columns);
            },
            'tags' => function ($query) use ($tag_collumns) {
                $query->addSelect($tag_collumns);
            },
            'featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                $query->addSelect($featurePhotoDetail_column);
            },

            'parentPostUser' => function ($query) use ($user_collumns) {
                $query->addSelect($user_collumns);
            },
            'orginalPost.user' => function ($query) use ($user_collumns) {
                $query->addSelect($user_collumns);
            },
            'orginalPost.category' => function ($query) use ($category_collumns) {
                $query->addSelect($category_collumns);
            },
            'orginalPost.subCategory' => function ($query) use ($category_collumns) {
                $query->addSelect($category_collumns);
            },
            'orginalPost.country',
            'orginalPost.country.region' => function ($query) use ($region_columns) {
                $query->addSelect($region_columns);
            },
            'orginalPost.tags' => function ($query) use ($tag_collumns) {
                $query->addSelect($tag_collumns);
            },
            'orginalPost.featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                $query->addSelect($featurePhotoDetail_column);
            },
        ])
            ->withCount('comment')
            ->skip($this->offset)->take($this->per_page)->get()->makeVisible('people_here');

            /********** tag count(20-02-18) start *********/
                foreach($posts as $p)
                {
                    foreach($p->tags as $t)
                    {
                            $question_tag_created_at= $t->question_tag_created_at;
                            if($question_tag_created_at)
                            {
                                $tagcount=$t->postsTag()->where('posts.created_at','>',$question_tag_created_at)->where('posts.post_type','<>','6')->whereNull('posts.orginal_post_id')->first();
                                $t->tagCount=$tagcount->count;
                            }
                            else
                            {
                                $t->tagCount=0;
                            }
                       
                    }
                } 
				/********** tag count(20-02-18) end *********/

        /*if (!empty($_REQUEST['test'])) {
            $query = \DB::getQueryLog();
            // $lastQuery = end($query);
            dd($query);
        }*/

        $post_count = count($posts);
        for ($p = 0; $p < $post_count; $p++) {
            $parent_post_user_id = $posts[$p]->parent_post_user_id;
            $current_post = null;
            // For shared post.
            if ($posts[$p]->orginal_post_id > 0 && $posts[$p]->orginalPost->id > 0) {
                $current_post = $posts[$p];

                $orginalPost = $posts[$p]->orginalPost->makeVisible('people_here');
                 /********** tag count(20-02-18) start *********/
                   
                 foreach($orginalPost->tags as $t)
                 {
                         $question_tag_created_at= $t->question_tag_created_at;
                         if($question_tag_created_at)
                         {
                             $tagcount=$t->postsTag()->where('posts.created_at','>',$question_tag_created_at)->where('posts.post_type','<>','6')->whereNull('posts.orginal_post_id')->first();
                             $t->tagCount=$tagcount->count;
                         }
                         else
                         {
                             $t->tagCount=0;
                         }
                     
                 }
         
                /********** tag count(20-02-18) end *********/
                // Set original post to final post.
                $final_posts[$p] = $orginalPost->toArray();

                // Set user.
                $final_posts[$p]['user'] = $orginalPost->user->toArray();
                $final_posts[$p]['first_name_letter'] = substr($orginalPost->user->first_name, 0, 1);
                // Set post owner(i.e. who shared).
                $final_posts[$p]['post_owner'] = $current_post->user->toArray();
                // dd($final_posts[$p]->post_owner->toArray());

                // Format original post caption.
                $original_caption = $orginalPost->caption;
                if (!empty($original_caption)) {
                    $final_posts[$p]['caption'] = hash_tag_url($original_caption);
                }
                // Limit article content.
                $limited_article = get_limited_article($orginalPost->content);
                if (!empty($limited_article)) {
                    $final_posts[$p]['content'] = $limited_article['content'];
                    $final_posts[$p]['time_needed'] = $limited_article['time_needed'];
                }

                // Create post url..
                $category_name = '';
                if (!empty($orginalPost->category)) {
                    $category_name = $orginalPost->category->category_name;
                    $final_posts[$p]['category']['category_name_url'] = str_slug_ovr($category_name);
                }

                $subcategory_name = '';
                $subCategory = $orginalPost->subCategory;
                if (!empty($subCategory)) {
                    $subcategory_name = $orginalPost->subCategory->category_name;
                    $final_posts[$p]['sub_category']['subcategory_name_url'] = str_slug_ovr($subcategory_name);
                }

                // Replace place_url if saved as undefined.
                if (!empty($orginalPost->place_url) && $orginalPost->place_url == 'undefined') {
                    $final_posts[$p]['place_url'] = '';
                }

                $post_url_args = [
                    'id' => $posts[$p]->id,
                    'caption' => $original_caption,
                    'title' => $posts[$p]->orginalPost->title,
                    'post_type' => $posts[$p]->orginalPost->post_type,
                    'category_name' => $category_name,
                    'subcategory_name' => $subcategory_name
                ];

                $post_url = post_url($post_url_args);
                $final_posts[$p]['post_url'] = $post_url;

                if (Auth::check()) {
                    $isBookMark = DB::table('bookmarks')
                        ->where([
                            'post_id' => $posts[$p]->orginalPost->id,
                            'user_id' => Auth::user()->id
                        ])
                        ->count();

                    $isUpvote = DB::table('activity_post')
                        ->where([
                            'post_id' => $orginalPost->id,
                            'activity_id' => 1,
                            'user_id' => Auth::user()->id
                        ])
                        ->select(['id'])
                        ->count();

                    $isDownvote = DB::table('activity_post')
                        ->where([
                            'post_id' => $orginalPost->id,
                            'activity_id' => 2,
                            'user_id' => Auth::user()->id
                        ])
                        ->select(['id'])
                        ->count();
                } else {

                    $isBookMark = 0;
                    $isUpvote = 0;
                    $isDownvote = 0;
                }

                $final_posts[$p]['isBookMark'] = ($isBookMark != 0) ? 'Y' : 'N';
                $final_posts[$p]['isUpvote'] = ($isUpvote != 0) ? 'Y' : 'N';
                $final_posts[$p]['isDownvote'] = ($isDownvote != 0) ? 'Y' : 'N';

                $sql = "select count(*) as totalComments from `comments` where post_id ='" . $orginalPost->id . "'  AND parent_id is null";
                $comment = DB::select($sql);

                $final_posts[$p]['totalComments'] = $comment[0]->totalComments;

                $final_posts[$p]['totalPostViews'] = countPostView($orginalPost->id, $orginalPost->post_type);
                $totalShare = DB::table('activity_post')
                    ->where(['post_id' => $orginalPost->id])
                    ->whereIn('activity_id', [3, 4, 5])
                    ->select(['id'])
                    ->count();

                $final_posts[$p]['totalShare'] = $totalShare;
                $final_posts[$p]['orginal_post_id'] = $current_post->orginal_post_id;

                $final_posts[$p]['child_post_id'] = $current_post->id;
                $final_posts[$p]['child_post_user_id'] = $current_post->created_by;
                $final_posts[$p]['child_postCaption'] = hash_tag_url($current_post->caption);
                $final_posts[$p]['child_user'] = $current_post->parentPostUser;
                // Set unique cardID.
                $final_posts[$p]['cardID'] = $current_post->id;
                // Set embed url info.
                if (!empty($final_posts[$p]['embed_code'])) {
                    $embedVideoInfo = getEmbedVideoInfo($final_posts[$p]['embed_code']);
                    $final_posts[$p]['embed_code_type'] = $embedVideoInfo['type'];
                    $final_posts[$p]['videoid'] = $embedVideoInfo['videoid'];
                }

                $final_posts[$p]['child_post_created_at'] = $current_post->created_at->format('Y-m-d H:i:s');

            } else {
                $original_caption = $posts[$p]->caption;
                // Format Caption for Hashtags..
                if (!empty($posts[$p]->caption)) {
                    $posts[$p]->caption = hash_tag_url($posts[$p]->caption);
                }
                // Prepare the post card content..
                $limited_article = get_limited_article($posts[$p]->content);
                if (!empty($limited_article)) {
                    $posts[$p]->content = $limited_article['content'];
                    $posts[$p]->time_needed = $limited_article['time_needed'];
                }
                // Create category urls & post url..
                $category_name = '';
                if (!empty($posts[$p]->category)) {
                    $category_name = $posts[$p]->category->category_name;
                    $posts[$p]->category->category_name_url = str_slug_ovr($category_name);
                }
                $subcategory_name = '';
                $subCategory = $posts[$p]->subCategory;
                if (!empty($subCategory)) {
                    $subcategory_name = $posts[$p]->subCategory->category_name;
                    $posts[$p]->subCategory->subcategory_name_url = str_slug_ovr($subcategory_name);
                }
                // Replace place_url if saved as undefined.
                if (!empty($posts[$p]->place_url) && $posts[$p]->place_url == 'undefined') {
                    $posts[$p]->place_url = '';
                }
                // Create the array for post url.
                $post_url_args = [
                    'id' => $posts[$p]->id,
                    'caption' => $original_caption,
                    'title' => $posts[$p]->title,
                    'post_type' => $posts[$p]->post_type,
                    'category_name' => $category_name,
                    'subcategory_name' => $subcategory_name
                ];
                $post_url = post_url($post_url_args);
                $posts[$p]->post_url = $post_url;

                // Add child post user id.
                $posts[$p]->child_post_user_id = $posts[$p]->created_by;

                if ($posts[$p]->parent_post_id != 0) {
                    $posts[$p]->orginalPostUserName = $posts[$p]->parentPost->user->first_name;
                }

                $sql = "select count(*) as totalComments from `comments` where post_id ='" . $posts[$p]->id . "'  AND parent_id is null";
                $comment = DB::select($sql);


                $posts[$p]->totalComments = $comment[0]->totalComments;
                $posts[$p]->totalPostViews = countPostView($posts[$p]->id, $posts[$p]->post_type);
                $totalShare = DB::table('activity_post')
                    ->where(['post_id' => $posts[$p]->id])
                    ->whereIn('activity_id', [3, 4, 5])
                    ->select(['id'])
                    ->count();
                $posts[$p]->totalShare = $totalShare;
                $posts[$p]->child_post_id = $posts[$p]->id;
                $posts[$p]->child_post_user_id = $posts[$p]->created_by;
                // Set to final post.

                if (Auth::check()) {
                    $isUpvote = DB::table('activity_post')
                        ->where([
                            'post_id' => $posts[$p]->id,
                            'activity_id' => 1,
                            'user_id' => Auth::user()->id
                        ])
                        ->select(['id'])
                        ->count();

                    $isDownvote = DB::table('activity_post')
                        ->where([
                            'post_id' => $posts[$p]->id,
                            'activity_id' => 2,
                            'user_id' => Auth::user()->id
                        ])
                        ->select(['id'])
                        ->count();


                    $isBookMark = DB::table('bookmarks')
                        ->where([
                            'post_id' => $posts[$p]->id,
                            'user_id' => Auth::user()->id
                        ])
                        ->count();
                } else {
                    $isUpvote = 0;
                    $isDownvote = 0;
                    $isBookMark = 0;
                }

                $posts[$p]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';
                $posts[$p]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';
                $posts[$p]->isBookMark = ($isBookMark != 0) ? 'Y' : 'N';


                $final_posts[$p] = $posts[$p];
                $final_posts[$p]['cardID'] = $posts[$p]->id;

                // Set embed url info.
                if (!empty($final_posts[$p]->embed_code)) {
                    $embedVideoInfo = getEmbedVideoInfo($final_posts[$p]->embed_code);
                    $final_posts[$p]->embed_code_type = $embedVideoInfo['type'];
                    $final_posts[$p]->videoid = $embedVideoInfo['videoid'];
                }
            }

            // Convert to array if collection object.
            if (is_object($final_posts[$p])) {
                $final_posts[$p] = $final_posts[$p]->toArray();
            }

            if (!empty($final_posts[$p]['image'])) {
                $final_posts[$p]['image'] = generate_post_image_url('post/thumbs/' . $final_posts[$p]['image']);
            }

            if (!empty($final_posts[$p]['video'])) {
                $final_posts[$p]['video'] = generate_post_video_url('video/' . $final_posts[$p]['video']);
            }
            if (!empty($final_posts[$p]['video_poster'])) {
                $final_posts[$p]['video_poster'] = generate_post_video_url('video/thumbnail/' . $final_posts[$p]['video_poster']);
            }

            if (!empty($final_posts[$p]['feature_photo_detail'])) {
                $percentage = ($final_posts[$p]['feature_photo_detail']['thumb_height'] /
                        $final_posts[$p]['feature_photo_detail']['thumb_width']) * 100;
                $final_posts[$p]['feature_photo_detail']['percentage'] = round($percentage, 2);
            }

        }

        /*************Fetch Distance Between Post Location And End User Location****************/
        $userLocationInfo = Session::get('userLocationInfo');
        if ($userLocationInfo !== null && $request->input('userLocationSaved') == "true") {
            $final_posts = addPostDistance($userLocationInfo, $final_posts);
        }
        /*************Fetch Distance Between Post Location And End User Location****************/

        return_area:
        // Prepare the return data.
        $data = [
            'allPosts' => $final_posts,
            'total_posts' => $total_posts,
            'privacyWishTotalPost' => $privacyWishTotalPost
        ];
        return $data;
    }

    public function followThisUser(Request $request)
    {
        $user_id = $request->input('user_id');
        $following = $request->input('following');

        $follower_id = Auth::user()->id;

        $follower = Follower::where(['user_id' => $user_id, 'follower_id' => $follower_id])->first();

        if (count($follower) > 0) {
            $cond = [
                'user_id' => $user_id,
                'follower_id' => $follower_id
            ];

            $delete = Follower::where($cond)->delete();
            $is_follow = 0;
            $getFollower = $follower->follower_id;
        } else {
            $follower = new Follower;
            $follower->user_id = $user_id;
            $follower->follower_id = $follower_id;
            $follower->status = 1;
            $follower->save();

            $followerID = $follower->id;

            $user_collumns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];


            if ($following == 'following') {
                $getFollower = Follower::where(['id' => $followerID])
                    ->with([
                        'following_by' => function ($query) use ($user_collumns) {
                            $query->addSelect($user_collumns);
                        }
                    ])->first();
            } else {
                $getFollower = Follower::where(['id' => $followerID])
                    ->with([
                        'followed_by' => function ($query) use ($user_collumns) {
                            $query->addSelect($user_collumns);
                        }
                    ])->first();
            }

            $is_follow = 1;
        }

        $totalFollowers = DB::table('followers')->where('user_id', $user_id)->count();

        // Broadcast user followed event.
        event(new UserFollowed($user_id, [
            'totalFollowers' => $totalFollowers
        ]));

        $data = [
            'is_follow' => $is_follow,
            'getFollower' => $getFollower,
            'totalFollowers' => $totalFollowers
        ];
        return $data;
    }


    public function authUserJson(Request $request)
    {
        $user_collumns = ['id', 'username', 'first_name', 'last_name', 'about_me', 'description', 'dob', 'sex', 'country_id', 'state_id', 'city', 'zipcode', 'address', 'profile_image', 'cover_image', 'occupation', 'points'];
        $follower_collumns = ['id', 'user_id', 'follower_id'];
        $following_collumns = ['id', 'user_id', 'follower_id'];
        /*$upvotes_collumns = ['post_id', 'user_id'];
        $downvote_collumns = ['post_id', 'user_id'];
        $category_follow = ['category_id'];
        $commentsUpvotes_collumns = ['comment_id', 'user_id'];*/

        $user = new User;

        if (Auth::check()) {
            $user = User::where('id', Auth::user()->id)
                ->with(
                    [
                        'follower' => function ($query) use ($follower_collumns) {
                            $query->addSelect($follower_collumns);
                        },
                        'following' => function ($query) use ($following_collumns) {
                            $query->addSelect($following_collumns);
                        }
                    ]
                )
                ->with('category_follow')
                ->first($user_collumns);
            $user->userProfileview = Userview::where(['user_id' => $user->id])->count();
            $user->userTotalPost = Post::where('created_by', $user->id)->count();

            // Set client ip.
            $user->ip = $request->ip();
            // total book marks...

            $totalBookMarks = DB::table('bookmarks')
                ->where(['user_id' => Auth::user()->id])
                ->select(['id'])
                ->count();

            $user->totalBookMarks = $totalBookMarks;
            $user->guest = 0;
        } else {
            $user = User::where('id', 1)->first($user_collumns);
            $user->guest = 1;
        }

        if (!empty($user->profile_image)) {
            $user->profile_image = $user->thumb_image_url = generate_profile_image_url('profile/thumbs/' . $user->profile_image);
        }
        if (!empty($user->cover_image)) {
            $user->cover_image = generate_profile_image_url('profile/cover/' . $user->cover_image);
        }

        return response()->json($user);
    }

    public function profileCoverUpload(Request $request)
    {
        if ($request->file('file')) {
            $image = $request->file('file');
            $image_ext = $image->getClientOriginalExtension();
            $original_name = $image->getClientOriginalName();
            $save_name = generateFileName($original_name) . '.' . $image_ext;

            $path = public_path() . '/uploads/profile/cover/';
            if ($image_ext == 'gif' || $image_ext == 'GIF') {
                $image->move($path, $save_name);
            }
            else {
                $width = 1230;
                $height = null;

                $image_make = Image::make($image->getRealPath());
                $image_make->resize($width, $height, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
                $fileSizeInKb = round(($image_make->filesize()) / 1024);

                $quality = 100;
                if ($fileSizeInKb <= 150) {
                    $quality = 90;
                }
                elseif ($fileSizeInKb > 150 && $fileSizeInKb <= 250) {
                    $quality = 90;
                }
                elseif ($fileSizeInKb > 250 && $fileSizeInKb <= 400) {
                    $quality = 70;
                }
                elseif ($fileSizeInKb > 400 && $fileSizeInKb <= 600) {
                    $quality = 60;
                }
                else {
                    $quality = 50;
                }
                $image_make->save($path . $save_name, $quality);
            }

            /* Upload file to aws s3 */
            move_to_s3('/profile/cover/' . $save_name, $path . $save_name);

            if (!empty(Auth::user()->cover_image)) {
                Storage::delete(['/profile/cover/' . Auth::user()->profile_image]);
            }
            Auth::user()->cover_image = $save_name;
            Auth::user()->save();
            // Show s3 link.
            Auth::user()->cover_image = generate_profile_image_url('profile/cover/' . Auth::user()->cover_image);
        }

        $data = [
            'user' => Auth::user()
        ];
        return response()->json($data);

    }

    public function upVotePost(Request $request)
    {
        $post_id = $request->input('post_id');
        $childPostId = $request->input('childPostId');
        $user_collumns = ['id', 'points'];

        $user_id = Auth::user()->id;
        $activity_id = 1;
        $status = 1;

        $post = Post::find($post_id);


        $postUser = $post->user;

        /******* CANCEL DOWNVOTE SECTION******/

        $downvote_exsist = DB::table('activity_post')->where([
            'post_id' => $post_id,
            'user_id' => $user_id,
            'activity_id' => 2])->first();
        if ($downvote_exsist !== null) {

            $activity_post = DB::table('activity_post')->where([
                'post_id' => $post_id,
                'user_id' => $user_id,
                'activity_id' => 2])->delete();

            if ($user_id != $post->created_by) {
                $postUser->points = $postUser->points + 2;
                $postUser->save();

                $post->points = $post->points + 2;

            }
            $post->downvotes = $post->downvotes - 1;
            $post->save();

        }

        /******* CANCEL DOWNVOTE SECTION******/


        $activity_post = DB::table('activity_post')->where([
            'post_id' => $post_id,
            'user_id' => $user_id,
            'activity_id' => $activity_id])->first();

        if ($activity_post !== null) {
            $affected = DB::table('activity_post')->where([
                'post_id' => $post_id,
                'user_id' => $user_id,
                'activity_id' => $activity_id])->delete();


            if ($user_id != $post->created_by) {
                $postUser->points = $postUser->points - 2;
                $postUser->save();
                $post->points = $post->points - 2;
            }
            $post->upvotes = $post->upvotes - 1;
            $post->save();
            $status = 0;
            $flag = 0;

        } else {
            DB::table('activity_post')->insert([
                'post_id' => $post_id,
                'user_id' => $user_id,
                'activity_id' => $activity_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')]);

            if ($user_id != $post->created_by) {
                $postUser->points = $postUser->points + 2;
                $postUser->save();


                $post->points = $post->points + 2;
            }

            $post->upvotes = $post->upvotes + 1;
            $post->save();
            $flag = 1;
        }
        /*
         $post = Post::where(['id'=>$post_id])
                     ->with('user', 'category', 'subCategory', 'tags')->first();
         */

        $post = Post::where('id', $post_id)
            ->with([
                'user' => function ($query) use ($user_collumns) {
                    $query->addSelect($user_collumns);
                }
            ])->first(['id', 'created_by', 'points', 'upvotes', 'downvotes']);


        $postUser = $post->user;
        if ($childPostId > 0 && $childPostId != $post_id) {
            $childPost = Post::where(['id' => $childPostId])->first();
            $postUser->childUser = User::where(['id' => $childPost->created_by])->select($user_collumns)->first();
        }

        //$userUpvote =  DB::table('activity_post')->where(['user_id' => $user_id, 'activity_id' => $activity_id])->get();
        //$userDownvote =  DB::table('activity_post')->where(['user_id' => $user_id, 'activity_id' => 2])->get();


        // if share post the execute this section ...........
        if ($childPostId > 0 && $childPostId != $post_id) {


            $childPost = Post::where(['id' => $childPostId])->first();

            $childPostUser = User::where(['id' => $childPost->created_by])->first();
            $where = [
                'post_id' => $childPostId,
                'user_id' => $user_id,
                'activity_id' => 2
            ];
            $parentDownvoteExists = DB::table('activity_post')->where($where)->first();

            if ($parentDownvoteExists != null) {
                $delWhere = [
                    'post_id' => $childPostId,
                    'user_id' => $user_id,
                    'activity_id' => 2
                ];

                $activity_post = DB::table('activity_post')->where($delWhere)->delete();

                if ($user_id != $childPost->created_by) {
                    if ($post->created_by != $childPost->created_by) {
                        $childPost->points = $childPost->points + 2;
                        // $childPost->points = $childPost->points - 2;
                        if ($post->created_by != $childPost->created_by) {
                            //  $childPostUser->points= $childPostUser->points-2;
                            $childPostUser->points = $childPostUser->points + 2;
                            $childPostUser->save();
                        }


                    }
                }


                $childPost->downvotes = $childPost->downvotes - 1;
                $childPost->save();
            }

            $upvoteWhere = [
                'post_id' => $childPostId,
                'user_id' => $user_id,
                'activity_id' => $activity_id
            ];

            $upvpteActivity_post = DB::table('activity_post')->where($upvoteWhere)->first();

            if ($upvpteActivity_post !== null) {

                $where = [
                    'post_id' => $childPostId,
                    'user_id' => $user_id,
                    'activity_id' => $activity_id
                ];

                $activity_post = DB::table('activity_post')->where($where)->delete();

                if ($user_id != $childPost->created_by) {
                    $childPost->points = $childPost->points - 2;
                    if ($post->created_by != $childPost->created_by) {
                        $childPostUser->points = $childPostUser->points - 2;
                        $childPostUser->save();
                    }
                }
                $childPost->upvotes = $childPost->upvotes - 1;
                $childPost->save();

            } else {

                if ($flag == 1) {

                    $arr = [
                        'post_id' => $childPost->id,
                        'user_id' => $user_id,
                        'activity_id' => $activity_id,
                    ];

                    DB::table('activity_post')->insert($arr);

                    if ($user_id != $childPost->created_by) {
                        $childPost->points = $childPost->points + 2;
                        if ($post->created_by != $childPost->created_by) {
                            $childPostUser->points = $childPostUser->points + 2;
                            $childPostUser->save();
                        }
                    }
                    $childPost->upvotes = $childPost->upvotes + 1;
                    $childPost->save();
                }
            }

            $whereArr = [$childPost->created_by, $post->created_by];
            $postUser = User::whereIn('id', $whereArr)
                ->orderByRaw("FIELD(id, $childPost->created_by,$post->created_by)")
                ->select($user_collumns)->get();

            // Call notificaion method .

            if ($childPost->created_by != $user_id) {
                if ($post->created_by != $childPost->created_by) {
                    $this->notificationProcess($activity_id, $childPostId);
                }
            }

        } else {
            $whereArr = [$post->created_by];
            $postUser = User::whereIn('id', $whereArr)
                ->orderByRaw("FIELD(id, $post->created_by)")
                ->select($user_collumns)->get();
        }

        // Call notificaion method .
        if ($post->created_by != Auth::user()->id) {
            $this->notificationProcess($activity_id, $post->id);
        }

        $event_data = [
            'post_id' => $post->id,
            'upvotes' => $post->upvotes,
            'downvotes' => $post->downvotes,
        ];

        // Broadcast post upvoted event.
        event(new PostUpvoted($event_data, $post_id));

        $data = [
            'user' => $postUser,
            'post' => $post,
            'status' => $status
        ];
        return $data;

    }

    public function notificationProcess($activity_id, $post_id)
    {
        $user_id = Auth::user()->id;

        $notification = Notification::where([
            'post_id' => $post_id,
            'user_id' => $user_id,
            'activity_id' => $activity_id])->first();

        $post = Post::where(['id' => $post_id])->first();
        if ($activity_id == 1) {  // Cancelling downvotes

            $downVotesNotifyArr = ['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => 2];
            $isDownVoteArr = ['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => 2];

            $downvote_notification = Notification::where($downVotesNotifyArr)->first();
            $downvote_exsist = DB::table('activity_post')->where($isDownVoteArr)->first();

            // if($downvote_exsist !== null){
            if ($downvote_notification !== null) {
                $downvote_notification->delete();
            }
            // }

        } else {

            $UpVotesNotifyArr = ['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => 1];
            $isUpArr = ['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => 1];

            $upvote_notification = Notification::where($UpVotesNotifyArr)->first();
            $upvote_exsist = DB::table('activity_post')->where($isUpArr)->first();


            // if($upvote_exsist !== null){
            if ($upvote_notification !== null) {
                $upvote_notification->delete();
            }
            // }


        }


        if ($notification != null) {
            $notification->delete();
        } else {
            Notification::create([
                'post_id' => $post_id,
                'user_id' => $user_id,
                'activity_id' => $activity_id,
                'post_user_id' => $post->created_by,
                'status' => 1
            ]);

        }
    }

    public function downVotePost(Request $request)
    {
        $post_id = $request->input('post_id');
        $childPostId = $request->input('childPostId');
        $user_collumns = ['id', 'points'];

        $user_id = Auth::user()->id;
        $activity_id = 2;
        $status = 1;

        $post = Post::find($post_id);
        $postUser = $post->user;


        /******* CANCEL UPVOTE SECTION******/
        $upvote_exsist = DB::table('activity_post')->where(['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => 1])->first();
        if ($upvote_exsist !== null) {
            $activity_post = DB::table('activity_post')->where(['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => 1])->delete();

            if ($user_id != $post->created_by) {
                $postUser->points = $postUser->points - 2;
                $postUser->save();
                $post->points = $post->points - 2;

            }
            $post->upvotes = $post->upvotes - 1;
            $post->save();

        }

        /****** CANCEL UPVOTE SECTION******/
        $activity_post = DB::table('activity_post')->where(['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => $activity_id])->first();
        $notification = Notification::where(['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => $activity_id])->first();

        if ($activity_post !== null) {
            $activity_post = DB::table('activity_post')->where(['post_id' => $post_id, 'user_id' => $user_id, 'activity_id' => $activity_id])->delete();
            if ($user_id != $post->created_by) {
                $postUser->points = $postUser->points + 2;
                $postUser->save();
                $post->points = $post->points + 2;

            }
            $post->downvotes = $post->downvotes - 1;
            $post->save();
            $status = 0;
            $flag = 0;


        } else {
            DB::table('activity_post')->insert([
                'post_id' => $post_id,
                'user_id' => $user_id,
                'activity_id' => $activity_id
            ]);

            if ($user_id != $post->created_by) {
                $postUser->points = $postUser->points - 2;
                $postUser->save();

                $post->points = $post->points - 2;
            }

            $post->downvotes = $post->downvotes + 1;
            $post->save();
            $flag = 1;
        }

        $post = Post::where('id', $post_id)
            ->with([
                'user' => function ($query) use ($user_collumns) {
                    $query->addSelect($user_collumns);
                }
            ])->first(['id', 'created_by', 'points', 'upvotes', 'downvotes']);

        $postUser = $post->user;

        if ($childPostId != $post_id) {
            $childPost = Post::where(['id' => $childPostId])->first();
            $postUser->childUser = User::where(['id' => $childPost->created_by])
                ->select($user_collumns)->first();
        }

        // if share post the execute this section ...........
        if ($childPostId > 0 && $childPostId != $post_id) {
            $childPost = Post::where(['id' => $childPostId])->first();
            $childPostUser = User::where(['id' => $childPost->created_by])->first();

            $upvoteWhere = [
                'post_id' => $childPostId,
                'user_id' => $user_id,
                'activity_id' => 1
            ];
            // First Cancelling the upvote 
            $parentUpvoteExists = DB::table('activity_post')->where($upvoteWhere)->first();
            if ($parentUpvoteExists !== null) {

                $where = [
                    'post_id' => $childPostId,
                    'user_id' => $user_id,
                    'activity_id' => 1
                ];

                $activity_post = DB::table('activity_post')->where($where)->delete();

                if ($user_id != $childPost->created_by) {
                    $childPost->points = $childPost->points - 2;
                    if ($post->created_by != $childPost->created_by) {
                        // When post creator shared own post  that time
                        // Sourav created post
                        // John shared sourav post
                        // sourav shared John post 

                        $childPostUser->points = $childPostUser->points - 2;
                        $childPostUser->save();
                    }
                }

                $childPost->upvotes = $childPost->upvotes - 1;
                $childPost->save();
            }
            // Second check downvote exists or not .If exists then downvote otherwise cancel the downvote .

            $where = [
                'post_id' => $childPostId,
                'user_id' => $user_id,
                'activity_id' => $activity_id
            ];

            $activity_post = DB::table('activity_post')->where($where)->first();

            if ($activity_post !== null) {
                $where = [
                    'post_id' => $childPostId,
                    'user_id' => $user_id,
                    'activity_id' => $activity_id
                ];

                $activity_post = DB::table('activity_post')->where($where)->delete();

                if ($user_id != $childPost->created_by) {
                    $childPost->points = $childPost->points + 2;
                    if ($post->created_by != $childPost->created_by) {
                        // When post creator shared own post  that time
                        // Sourav created post
                        // John shared sourav post
                        // sourav shared John post 

                        $childPostUser->points = $childPostUser->points + 2;
                        $childPostUser->save();
                    }
                }

                $childPost->downvotes = $childPost->downvotes - 1;
                $childPost->save();
            } else {
                if ($flag == 1) // If parent post execute then child post also execute ...
                {
                    $arr = [
                        'post_id' => $childPostId,
                        'user_id' => $user_id,
                        'activity_id' => $activity_id,
                    ];

                    DB::table('activity_post')->insert($arr);

                    if ($user_id != $childPost->created_by) {
                        $childPost->points = $childPost->points - 2;
                        if ($post->created_by != $childPost->created_by) {
                            $childPostUser->points = $childPostUser->points - 2;
                            $childPostUser->save();
                        }
                    }
                    $childPost->downvotes = $childPost->downvotes + 1;
                    $childPost->save();
                }
            }

            $whereArr = [$childPost->created_by, $post->created_by];
            $postUser = User::whereIn('id', $whereArr)
                ->orderByRaw("FIELD(id, $childPost->created_by,$post->created_by)")
                ->select($user_collumns)->get();
            if ($childPost->created_by != $user_id) {
                if ($post->created_by != $childPost->created_by) {
                    $this->notificationProcess($activity_id, $childPostId);
                }
            }
        } else {
            $whereArr = [$post->created_by];
            $postUser = User::whereIn('id', $whereArr)
                ->orderByRaw("FIELD(id, $post->created_by)")
                ->select($user_collumns)->get();
        }
        // -----------------------------------

        if ($post->created_by != $user_id) {
            $this->notificationProcess($activity_id, $post->id);
        }

        $event_data = [
            'post_id' => $post->id,
            'upvotes' => $post->upvotes,
            'downvotes' => $post->downvotes,
        ];

        // Broadcast post upvoted event.
        event(new PostUpvoted($event_data, $post_id));

        $data = [
            'user' => $postUser,
            'post' => $post,
            'status' => $status
        ];
        return $data;

    }


    /**
     * Show the Follow Category .
     *
     */
    public function followThisCategory(Request $request)
    {
        $category_id = $request->input('category_id');
        $follower_id = $user_id = Auth::user()->id;


        $follower = CategoryFollower::where(['category_id' => $category_id, 'follower_id' => $follower_id])->first();
        if (count($follower) == 0) {
            CategoryFollower::create(['category_id' => $category_id, 'follower_id' => $follower_id]);
        }
        // For tag.
        $category = Category::find($category_id);
        $name = $category->category_name;
        $tag = Tag::where('tag_name', $name)->orWhere('tag_name', str_slug($name))->first(['id']);
        if ($tag !== null) {
            $tag_user = DB::table('tag_user')
                ->where('tag_id', $tag->id)
                ->where('user_id', $user_id);
            $tag_user_data = $tag_user->get(['id']);
            if (empty($tag_user_data)) {
                // Make tag follower.
                $tag_user_insert = [
                    'tag_id' => $tag->id,
                    'user_id' => $user_id
                ];
                DB::table('tag_user')->insert($tag_user_insert);
            }
        }
        // END for tag.

        $user = User::where('id', Auth::user()->id)
            ->with('category_follow')->first();

        $data = [
            'user' => $user
        ];
        return $data;
    }


    /**
     * Show the  Unfollow Category .
     *
     * @return Response
     */
    public function unfollowThisCategory(Request $request)
    {
        $category_id = $request->input('category_id');
        $follower_id = $user_id = Auth::user()->id;

        $delete = CategoryFollower::where(['category_id' => $category_id, 'follower_id' => $follower_id])->delete();

        // For tag.
        $category = Category::find($category_id);
        $name = $category->category_name;
        $tag = Tag::where('tag_name', $name)->orWhere('tag_name', str_slug($name))->first(['id']);
        if ($tag !== null) {
            $tag_user = DB::table('tag_user')
                ->where('tag_id', $tag->id)
                ->where('user_id', $user_id)
                ->delete();
        }
        // END for tag.

        $user = User::where('id', Auth::user()->id)
            ->with('category_follow')->first();


        $data = [
            'user' => $user
        ];
        return $data;
    }

    /**
     * Method to send invite to code if applicable
     *
     * @return Response
     */
    public function sendInvite(Request $request)
    {


        /** Check for number of quota for send invitation is exhausted or not **/
        $user_id = Auth::user()->id;
        $user = DB::table('users')->join('usertype', 'users.usertype', '=', 'usertype.id')->select('usertype.description', 'users.email')->where('users.id', $user_id)->first();
        if ($user->description == "user") {
            $number = $this->checkNumberOfInvites();
            if ($number['count'] >= 3) {
                $data = [
                    'has_error' => '1',
                    'error_msg' => 'You can Invite maximum 3 friends'

                ];
                return $data;
            }
        }


        $recipientemailaddress = $request->input('recipientemailaddress');
        $message = $request->input('message');

        //First Check If Email Id is Already a valid user
        $user = DB::table('users')->where('email', $recipientemailaddress)->first();
        $already_registered = empty($user) == true ? 0 : 1;

        if ($already_registered > 0) {
            $data = [
                'has_error' => '1',
                'error_msg' => 'This email id is already registered'

            ];
        } else {
            //Check if User has already got an activation email
            $user = DB::table('invitation')->where('recipientemailaddress', $recipientemailaddress)->first();
            $already_invited = empty($user) == true ? 0 : 1;
            if ($already_invited > 0) {
                $data = [
                    'has_error' => '1',
                    'error_msg' => 'This email id is already invited to join our ranks'

                ];
            } else {
                //Generate Random Unique Code and Send Email
                $uniquecode = $this->generateRandomUniqueCode(30);
                $fieldnames = array(
                    'user_id' => $user_id,
                    'recipientemailaddress' => $recipientemailaddress,
                    'uniquecode' => $uniquecode,
                    'message' => $message,
                    'send_at' => time()
                );
                $host_details = DB::table('users')->where('id', $user_id)->first();
                $invitation_id = DB::table('invitation')->insertGetId($fieldnames);
                if ($invitation_id > 0) {

                    $data = array(
                        'uniquecode' => $uniquecode,
                        'email' => $recipientemailaddress,
                        'invite_message' => $fieldnames['message'],
                        'username' => $host_details->username,
                        'fullname' => $host_details->first_name . " " . $host_details->last_name
                    );
                    $user = (object)$data;
                    // Send email
                    //$this->mailer->invite($user);
                    $view = 'emails.invite';
                    $data = ['email' => $recipientemailaddress, 'uniquecode' => $user->uniquecode, 'invite_message' => $user->invite_message, 'useremail' => $user->username . "@swolk.com", 'fullname' => $user->fullname];
                    $subject = 'Invitation to swolk';
                    Mail::send($view, $data, function ($message) use ($user, $subject) {
                        if ($user->username !== null && $user->fullname !== null) {
                            $message->from($user->username . "@swolk.com", $user->fullname);
                        }
                        $message->to($user->email)->subject($subject);
                    });
                    // Send email

                    $data = [
                        'has_error' => '0',
                        'error_msg' => 'Email Invitation Successfully Generated'

                    ];
                }

            }
        }


        return $data;
    }

    public function sendFeedback(Request $request)
    {
        //Get User Information
        $recipientEmailAddress = "swolk.com@gmail.com";
        $topic = $request->input('topic');
        $feedbackMessage = $request->input('feedbackMessage');
        $data = [
            'email' => $recipientEmailAddress,
            'topic' => $topic,
            'feedback_message' => $feedbackMessage,
            'username' => Auth::user()->username,
            'useremail' => Auth::user()->email,
            'fullname' => Auth::user()->first_name . " " . Auth::user()->last_name
        ];
        $subject = 'Feedback from swolk user';
        $view = 'emails.feedback';
        $user = (object)$data;

        // Send Email

        Mail::send($view, $data, function ($message) use ($user, $subject) {
            if ($user->username !== null && $user->fullname !== null) {
                $message->from(Auth::user()->email, $user->fullname);
            }
            $message->to($user->email)->subject($subject);
        });

        // Send email

        $data = [
            'has_error' => '0',
            'error_msg' => 'Feedback Successfully Sent'

        ];

        return response()->json($data);

    }

    /**
     * Method to generate a random unique code
     *
     * @return random string
     */
    public function checkNumberOfInvites()
    {
        $user_id = Auth::user()->id;
        $user = DB::table('users')->join('usertype', 'users.usertype', '=', 'usertype.id')->select('usertype.description', 'users.email')->where('users.id', $user_id)->first();
        //Check For Admin visit
        if ($user->description == "admin") {
            $data = [
                'admin' => 1
            ];

        }/**/
        else {
            $response = DB::table('invitation')->where('user_id', $user_id)->get();
            $count = count($response);
            $data = [
                'count' => $count,
                'admin' => 0
            ];
        }
        return $data;
    }


    /**
     * Method to generate a random unique code
     *
     * @return random string
     */
    public function generateRandomUniqueCode($length = 0)
    {
        $randomstring = '';
        if ($length > 0) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $time = time();
            $shuffled = str_shuffle($characters);
            $substring = substr($shuffled, 0, (30 - strlen($time)));
            $randomstring = $substring . $time;
        }
        return str_shuffle($randomstring);
    }


}
