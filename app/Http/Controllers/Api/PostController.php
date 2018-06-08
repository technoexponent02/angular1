<?php
namespace App\Http\Controllers\Api;

use App\Models\FeaturePhotoDetail;
use App\Models\Video;
use DB;
use File;
use Auth;
use Image;
use Validator;
use Session;
use Storage;

use Exception;
use Carbon\Carbon;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Coordinate\TimeCode;
use Facebook;
use App\Events\PostViewed;
use App\Models\Country;
use App\Models\Region;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\Photo;
use App\Models\Place;
use App\Models\Tag;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Userview;
use App\Models\Follower;


use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Pagination\Paginator;

class PostController extends Controller
{
    protected $clientIp;

    protected $image_width;
    protected $image_height;

    protected $page;
    protected $per_page;
    protected $offset;

    protected $allowed_post_types;
    protected $filter_post_type;

    private $known_image_extensions;

    protected $bots_array = [];
    protected $default_description;

    /**
     * PostController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        // Set client ip.
        $this->clientIp = $request->ip();

        $this->bots_array           = config('constants.CRAWLER_BOTS');
        $this->default_description  = config('constants.DEFAULT_DESCRIPTION');

        $this->image_width  = config('constants.POST_IMAGE_WIDTH');
        $this->image_height = config('constants.POST_IMAGE_HEIGHT');
        /*--- build the pagination logic ---*/
        $this->per_page = config('constants.PER_PAGE');
        // Calculation for pagination
        $page = 1;
        if (!empty($request->input('page'))) {
            $page = $request->input('page');
        }
        $this->page = $page;
        $this->offset = ($page - 1) * $this->per_page;

        /*------ Set post type for filtering posts ------*/
        $this->filter_post_type = 0;
        $this->allowed_post_types = config('constants.ALLOWED_POST_TYPES');
        if ($request->has('card_post_type') && in_array($request->input('card_post_type'), $this->allowed_post_types)) {
            $this->filter_post_type = $request->input('card_post_type');
        }

        $this->known_image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tif', 'tiff', 'exif'];
    }

    /**
     * Post details page..
     */
    public function details()
    {
        return view('post.details');
    }

    /**
     * Post details json.
     * @param int $id post_id
     */
    public function detailsJSON($id)
    {
        $findOriginalPost = Post::where(['id' => $id])->firstOrFail();
        if (Auth::check()) {
            $user_id = Auth::user()->id;
        } else {
            $user_id = 1; // anonymous user
        }

        if ($findOriginalPost->orginal_post_id != NULL) {
            $post_id = $findOriginalPost->orginal_post_id;
            $child_post_id = $findOriginalPost->id;
        } else {
            $post_id = $findOriginalPost->id;
            $child_post_id = $post_id;
        }


        $category_collumn = ['id', 'category_name'];
        $subCategory_collumn = ['id', 'category_name'];
        $tags = ['tags.id','tag_name','tag_text','question_tag','question_tag_created_at'];
        $user_collumns = ['id', 'username', 'first_name', 'last_name', 'about_me', 'description',
            'sex', 'city', 'profile_image', 'cover_image', 'occupation', 'points'];

        $post = Post::where('id', $post_id)
            ->with(
                [
                    'category' => function ($query) use ($category_collumn) {
                        $query->addSelect($category_collumn);
                    },
                    'subCategory' => function ($query) use ($subCategory_collumn) {
                        $query->addSelect($subCategory_collumn);
                    },
                    'tags' => function ($query) use ($tags) {
                        $query->addSelect($tags);
                    }
                ]
            )
            ->first();
         /********** tag count(20-02-18) start *********/
             
                 foreach($post->tags as $t)
                 {
                        $tag_user_count=0;
                        /*********(10-05-18) add tag follow status start ****/
                            if (Auth::check()) {
                                
                               
                                    $tag_user_count = DB::table('tag_user')
                                        ->where('tag_id', $t->id)
                                        ->where('user_id', Auth::user()->id)
                                        ->count();
                                
                            }
                            if ($tag_user_count > 0) {
                                $t->tagFollowStatus = 1;
                            }
                            else
                            {
                                $t->tagFollowStatus = 0;
                            }
                        /*********(10-05-18) add tag follow status start ****/



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

        $post->caption = hash_tag_url($post->caption);
        $limited_article = get_limited_article($post->content);
        if (!empty($limited_article)) {
            $post->time_needed = $limited_article['time_needed'];
        }

        if ($findOriginalPost->orginal_post_id !== NULL) {
            $post->orginal_post_id = $findOriginalPost->orginal_post_id;
        }

        if (!empty($post->content)) {
            $content = $post->content;

            // Ensure all tags are closed, without adding any html/head/body tags around it.
            /*$tidy_config = array(
//                'clean' => true,
                'input-xml' => true,
                'output-xhtml' => true,
                'show-body-only' => true,
                'wrap' => 0,
            );
            $tidy = tidy_parse_string($content, $tidy_config, 'UTF8');
            $tidy->cleanRepair();
            $content = $tidy->value;*/

            // echo htmlspecialchars($content);exit;

            // Add <br /> after every <img> tag.
            $content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '$1<br />', $content);
            $post->content = $content;
        }
/**************** change show post logic (22-12-17) ******************/  
        /********block the previous code***********/
        // if ($post->post_type == 1 || $post->post_type == 5) { // view post for image and status
        //     $activity_id = 8; //  activity ID 8 ::  View
        //     $flag = $this->viewPostProcess($activity_id, $post_id, $user_id);

        //     if ($post_id != $child_post_id && $flag == 1) {
        //         $flag = $this->viewPostProcess($activity_id, $child_post_id, $user_id);
        //     }
        // } else if ($post->post_type == 3) {   // view post for article
        //     $activity_id = 9; //  activity ID 9 ::  Read
        //     $flag = $this->viewPostProcess($activity_id, $post_id, $user_id);

        //     if ($post_id != $child_post_id && $flag == 1) {
        //         $flag = $this->viewPostProcess($activity_id, $child_post_id, $user_id);
        //     }
        // }
        if ($post->post_type == 1 || $post->post_type == 5 || $post->post_type == 2 || $post->post_type == 4 || $post->post_type == 6 ) { // view post for image and status
            $activity_id = 8; //  activity ID 8 ::  View
            $flag = $this->viewPostProcess($activity_id, $post_id, $user_id);

            if ($post_id != $child_post_id && $flag == 1) {
                $flag = $this->viewPostProcess($activity_id, $child_post_id, $user_id);
            }
        } else if ($post->post_type == 3) {   // view post for article
            $activity_id = 9; //  activity ID 9 ::  Read
            $flag = $this->viewPostProcess($activity_id, $post_id, $user_id);

            if ($post_id != $child_post_id && $flag == 1) {
                $flag = $this->viewPostProcess($activity_id, $child_post_id, $user_id);
            }
           
        }
          /**************New Code*******************/

/**************** change show post logic (22-12-17) ******************/

        //Take Record for all type of post views for analytics purpose
        //Take record for child post and parent post both following 1hour rule
        //Only For Non Logged In User
        $activityId = 8; //for post view
        $activityViewStatus = $this->recordPostView($activityId, $post_id, $user_id);
        if ($post_id != $child_post_id) {
            $activityViewStatus = $this->recordPostView($activityId, $child_post_id, $user_id);
        }

        $postTotalComment = Comment::where('post_id', $post_id)->get()->count();
        // get total share post ...
        // totalShare= normalShare + totalFBshare + totalTwittershare
        $post->totalShare = DB::table('activity_post')
            ->where(['post_id' => $post_id])
            ->whereIn('activity_id', [3, 4, 5])
            ->select(['id'])
            ->count();
        // get total normal shared 
        $post->normalShare = DB::table('activity_post')
            ->where(['post_id' => $post->id, 'activity_id' => 3])
            ->select(['id'])
            ->count();
        // get total facebook shared
        $post->totalFBshare = DB::table('activity_post')
            ->where(['post_id' => $post_id, 'activity_id' => 4])
            ->select(['id'])
            ->count();
        // get total twitter shared
        $post->totalTwittershare = DB::table('activity_post')
            ->where(['post_id' => $post_id, 'activity_id' => 5])
            ->select(['id'])
            ->count();

        // fetch comment sections ...

        // $post->allComments=$allComments;
        $post->postTotalComment = $postTotalComment;
        $post->postParentComment = Comment::where([
            'post_id' => $post_id,
            'parent_id' => 0])
            ->get()->count();


        $post->child_post_id = $child_post_id;

        // It is execute when post is shared ....
        if ($post_id != $child_post_id) {
            $childPost = Post::where(['id' => $child_post_id])->select('created_by')->first();

            $whereArr = [$childPost->created_by, $post->created_by];
            $getUsers = User::whereIn('id', $whereArr)
                ->orderByRaw("FIELD(id, $childPost->created_by,$post->created_by)")
                ->select($user_collumns)->get();

            $post->child_post_user_id = $childPost->created_by;
        } else {
            $whereArr = [$post->created_by];
            $getUsers = User::whereIn('id', $whereArr)
                ->orderByRaw("FIELD(id, $post->created_by)")
                ->select($user_collumns)->get();
            $post->child_post_user_id = $post->created_by;
        }


        foreach ($getUsers as $key => $value) {
            $getUsers[$key]->userDataProfileViews = Userview::where([
                'user_id' => $value->id
            ])->count();
            $getUsers[$key]->is_follow = Follower::where(['user_id' => $value->id])->count();

            if (!empty($getUsers[$key]->cover_image)) {
                $getUsers[$key]->cover_image = generate_profile_image_url('profile/cover/' . $getUsers[$key]->cover_image);
            }
        }

        $post->getUser = $getUsers;

        $post->totalPostViews = countPostView($post_id, $post->post_type, $type = 'viewed');

        // Just find out at least one best comments have in 
        $sql = "SELECT C.*,sum(IF(A.activity_id = '1', 1, NULL)) `upvotes`, 
                            sum(IF(A.activity_id = '2', 1, NULL)) `downvotes`, 
                            (upvotes-downvotes) as 'total_upvotes'
                            FROM comments  AS C 
                            JOIN  activity_comment AS A ON C.id=A.comment_id 
                            WHERE (C.post_id='" . $post_id . "' AND C.parent_id is null )
                            AND (A.user_id!=C.user_id ) 
                            AND (upvotes-downvotes) > 0
                            GROUP BY C.id ORDER BY (upvotes-downvotes),C.created_at";


        $bestTab = DB::select($sql);
        $post->bestTab = count($bestTab);

        if (Auth::check()) {
            $isUpvote = DB::table('activity_post')
                ->where([
                    'post_id' => $post_id,
                    'activity_id' => 1,
                    'user_id' => Auth::user()->id
                ])
                ->select(['id'])
                ->count();

            $isDownvote = DB::table('activity_post')
                ->where([
                    'post_id' => $post_id,
                    'activity_id' => 2,
                    'user_id' => Auth::user()->id
                ])
                ->select(['id'])
                ->count();

            $isBookMark = DB::table('bookmarks')
                ->where([
                    'post_id' => $post->id,
                    'user_id' => Auth::user()->id
                ])
                ->count();
        } else {
            $isUpvote = 0;
            $isDownvote = 0;
            $isBookMark = 0;
        }

        $post->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';
        $post->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';
        $post->isBookMark = ($isBookMark != 0) ? 'Y' : 'N';

        // Set unique cardID.
        $post->cardID = $post->id;

        // Set embed url info.
        if (!empty($post->embed_code)) {
            $embedVideoInfo = getEmbedVideoInfo($post->embed_code);
            $post->embed_code_type = $embedVideoInfo['type'];
            $post->videoid = $embedVideoInfo['videoid'];
        }

        if (!empty($post->image)) {
            $post->image = generate_post_image_url('post/' . $post->image);
        }

        if (!empty($post->video)) {
            $post->video = generate_post_video_url('video/' . $post->video);
        }
        if (!empty($post->video_poster)) {
            $post->video_poster = generate_post_video_url('video/thumbnail/' . $post->video_poster);
        }

        $totalBookMark = DB::table('bookmarks')
            ->where([
                'post_id' => $post->id,
            ])
            ->count();
        $post->totalBookMark = $totalBookMark;

        // Create category urls & post url..
        $category_name = '';
        if (!empty($post->category)) {
            $category_name = $post->category->category_name;
            $post->category->category_name_url = str_slug_ovr($category_name);
        }
        $subcategory_name = '';
        $subCategory = $post->subCategory;
        if (!empty($subCategory)) {
            $subcategory_name = $post->subCategory->category_name;
            $post->subCategory->subcategory_name_url = str_slug_ovr($subcategory_name);
        }
        // Replace place_url if saved as undefined.
        if (!empty($post->place_url) && $post->place_url == 'undefined') {
            $post->place_url = '';
        }

        $post_url_args = [
            'id' => $post->id,
            'caption' => $post->caption,
            'title' => $post->title,
            'post_type' => $post->post_type,
            'category_name' => $category_name,
            'subcategory_name' => $subcategory_name
        ];
        $post_url = post_url($post_url_args);
        $post->post_url = $post_url;

        //Find Distance between post location and user location
        $userLocationInfo = Session::get('userLocationInfo');
        $userLatitude = $userLocationInfo['lat'];
        $userLongitude = $userLocationInfo['lon'];
        if ($post->lat != '' && $post->lon != '' && $userLatitude != '' && $userLongitude != '') {
            $latitudeTo = floatval($post->lat);
            $longitudeTo = floatval($post->lon);
            $distance = haversineGreatCircleDistance($userLatitude, $userLongitude, $latitudeTo, $longitudeTo);
        } else {
            $distance = null;
        }
        $post->distance = $distance;

        return $post;
    }

    public function editPostJson($id)
    {
        $response = [];
        if (empty($id)) {
            $response = [
                'error_message' => "Invalid request. Missing the 'id' parameter",
                'status' => 'INVALID_REQUEST'
            ];
            return response()->json($response, 400);
        }

        $category_column = ['id', 'category_name'];
        $subCategory_column = ['id', 'category_name'];
        $tag_column = ['tags.id', 'tag_name','tag_text'];
        $privacy_column = ['id', 'privacy_name'];

        $post = Post::where('id', $id)
            ->with(
                [
                    'category' => function ($query) use ($category_column) {
                        $query->addSelect($category_column);
                    },
                    'subCategory' => function ($query) use ($subCategory_column) {
                        $query->addSelect($subCategory_column);
                    },
                    'tags' => function ($query) use ($tag_column) {
                        $query->addSelect($tag_column);
                    },
                    'privacy' => function ($query) use ($privacy_column) {
                        $query->addSelect($privacy_column);
                    }
                ]
            )
            ->first();

        if ($post === null) {
            $response = [
                'error_message' => "No post found.",
                'status' => 'ZERO_RESULT'
            ];
            return response()->json($response, 400);
        }

        if ($post->created_by !== Auth::user()->id) {
            $response = [
                'error_message' => "Sorry! unable to process your request.",
                'status' => 'ZERO_RESULT'
            ];
            return response()->json($response, 400);
        }

        // Set embed url info.
        /*if (!empty($post->embed_code)) {
            $embedVideoInfo = getEmbedVideoInfo($post->embed_code);
            $post->embed_code_type = $embedVideoInfo['type'];
            $post->videoid = $embedVideoInfo['videoid'];
        }*/

        if (!empty($post->image)) {
            $post->imageSrc = generate_post_image_url('post/' . $post->image);
        }

        if (!empty($post->video)) {
            $post->video = generate_post_video_url('video/' . $post->video);
        }
        if (!empty($post->video_poster)) {
            $post->video_poster = generate_post_video_url('video/thumbnail/' . $post->video_poster);
        }

        return response()->json($post);
    }
    public function tagDetailsJson($tag)
    {
        $tagDetails = Tag::where('tag_name',$tag)->first();
        if ($tagDetails === null) {
            $response = [
                'error_message' => "No post found.",
                'status' => 'ZERO_RESULT'
            ];
            return response()->json($response, 400);
        }
        return response()->json($tagDetails);
    }

    public function fetchReportPostData(Request $request)
    {
        $response = [];
        if (! $request->has('post_id')) {
            $response = [
                'error_message' => "Invalid request. Missing the 'post_id' parameter",
                'status' => 'INVALID_REQUEST'
            ];
            return response()->json($response, 400);
        }
        $post_id = $request->input('post_id');

        $cond = [
            'post_id' => $post_id,
            'user_id' => Auth::user()->id
        ];

        $post_report_ids = DB::table('post_report')->where($cond)->pluck('report_id');

        $response = [
            'report_ids' => $post_report_ids
        ];
        return response()->json($response);
    }

    /**
     * Method to record activity id = 8 for post view for non logged in user
     * Author : Alapan Chatterjee; Date:23-01-2017
     */
    public function recordPostView($activity_id, $post_id, $user_id)
    {
        $flag = 0;
        $post = Post::where(['id' => $post_id])->first();

        $viewerIp = $_SERVER['REMOTE_ADDR'];

        if (Auth::check()) {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id
            ];
        }
        else {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id,
                'ip_address' => $viewerIp
            ];
        }

        $postview = DB::table('activity_post_analytics')->where($cond)
            ->orderBy('created_at', 'DESC')
            ->skip(0)
            ->take(1)
            ->first();

        // When logged in user is same as post author id then view is no added ....
        if ($post->created_by != $user_id) {
            if ($postview != null) {
                $oneHourAgo = Carbon::now()->subHour(1);
                $postview_created_at = Carbon::createFromFormat('Y-m-d H:i:s', $postview->created_at, 'UTC');

                if ($postview_created_at->lt($oneHourAgo)) {
                    $postviewArry = [
                        'activity_id' => $activity_id,
                        'post_id' => $post_id,
                        'user_id' => $user_id,
                        'ip_address' => $viewerIp
                    ];
                    DB::table('activity_post_analytics')->insert($postviewArry);
                    $flag = 1;
                }
            } else {
                $postviewArry = [
                    'activity_id' => $activity_id,
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'ip_address' => $viewerIp
                ];
                DB::table('activity_post_analytics')->insert($postviewArry);
                $flag = 1;
            }
        }
        return $flag;
    }


    public function viewPostProcess($activity_id, $post_id, $user_id)
    {
        $flag = 0;
        $post = Post::where(['id' => $post_id])->first();
        $user = User::where(['id' => $post->created_by])->first();
        $viewerIp = $_SERVER['REMOTE_ADDR'];

        // \DB::connection()->enableQueryLog();

        if (Auth::check()) {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id];
        } else {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id,
                'ip_address' => $viewerIp];
        }

        $postview = DB::table('activity_post')->where($cond)
            ->orderBy('created_at', 'DESC')
            ->skip(0)
            ->take(1)
            ->first();

        // $query = \DB::getQueryLog();
        // $lastQuery = end($query);
        //dd($query);

        // When logged in user is same as post author id then view is no added ....

        if ($post->created_by != $user_id) {
            if ($postview != null) {
                $oneHourAgo = Carbon::now()->subHour(1);
                $postview_created_at = Carbon::createFromFormat('Y-m-d H:i:s', $postview->created_at, 'UTC');

                if ($postview_created_at->lt($oneHourAgo)) {
                    $postviewArry = [
                        'activity_id' => $activity_id,
                        'post_id' => $post_id,
                        'user_id' => $user_id,
                        'ip_address' => $viewerIp
                    ];
                    DB::table('activity_post')->insert($postviewArry);

                    if($post->post_type == 3)
                    {
                        $postviewArry = [
                            'activity_id' => 8,
                            'post_id' => $post_id,
                            'user_id' => $user_id,
                            'ip_address' => $viewerIp
                        ];
                        DB::table('activity_post')->insert($postviewArry);
                    }

                    $flag = 1;
                }
            } else {
                $postviewArry = [
                    'activity_id' => $activity_id,
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'ip_address' => $viewerIp
                ];
                DB::table('activity_post')->insert($postviewArry);

                if($post->post_type == 3)
                {
                    $postviewArry = [
                        'activity_id' => 8,
                        'post_id' => $post_id,
                        'user_id' => $user_id,
                        'ip_address' => $viewerIp
                    ];
                    DB::table('activity_post')->insert($postviewArry);
                }

                $flag = 1;
            }
        }

        if ($flag == 1) {
            if ($post->created_by != $user_id) {
                 /**********  comment for change seen algo  (22-12-17)*******/
                // For Image or status post
                // if ($post->post_type == 1 || $post->post_type == 5) {
                //     $post_point = $activity_id == 8 ? 2 : 1;
                //     $post->points = $post->points + $post_point;
                //     $post->save();

                //     $user->points = $user->points + 1;
                //     $user->save();

                // } else if ($post->post_type == 2) {   // Post Type 2 :: Video Post
                //     $post->points = $post->points + 2;
                //     $post->save();

                //     $user->points = $user->points + 1;
                //     $user->save();
                // } else if ($post->post_type == 3) {    // Post Type 3 :: Article Post
                //     $post->points = $post->points + 2;
                //     $post->save();

                //     $user->points = $user->points + 1;
                //     $user->save();

                // } else if ($post->post_type == 4) {  // Post Type 3 :: Link Post
                //     $post->points = $post->points + 2;
                //     $post->save();

                //     $user->points = $user->points + 1;
                //     $user->save();

                // }
                  /**********  comment for change seen algo  (22-12-17)*******/
                if($post->post_type == 3)  
                {
                     $post_point = 4;
                }
                else
                {
                    $post_point = 2;
                }

                $post->points = $post->points + $post_point;
                $post->save();
                $user->points = $user->points + 1;
                $user->save();
             
            }
            // Broadcast post viewed event.
            $totalPostViews = countPostView($post_id, $post->post_type);
            $event_data = [
                'totalPostViews' => $totalPostViews
            ];
            event(new PostViewed($post_id, $event_data));
        }

        return $flag;
    }

    /**
     * Category-tags listing page..
     */
    public function categoryTag(Request $request)
    {
        return view('post.category-tags');
    }

    /**
     * Place index page..
     */
    public function place()
    {
        return view('place.index');
    }

    /*==================== Tag Page ======================*/

    public function categoryTagJson(Request $request)
    {
        $response = [];
        $name = $request->input('name');
        // Set view post type
        $post_type = 'recent';
        if ($request->has('type')) {
            $post_type = $request->input('type');
        }

        // \DB::connection()->enableQueryLog();

        $postByCategoryTagName = $this->getPostByCategoryTagName($name, $post_type);

        /*$query = \DB::getQueryLog();
        //$lastQuery = end($query);
        dd($query);*/

        $posts = $postByCategoryTagName['posts'];
        // Add data to each post.
        $post_count = count($posts);
        for ($p = 0; $p < $post_count; $p++) {
            $original_caption = $posts[$p]->caption;
            // Format Caption for Hash tags..
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

            // Replace place_url if saved as undefined.
            if (!empty($posts[$p]->place_url) && $posts[$p]->place_url == 'undefined') {
                $posts[$p]->place_url = '';
            }

            // Add child post user id
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;

            if ($posts[$p]->parent_post_id != 0) {
                $posts[$p]->orginalPostUserName = $posts[$p]->parentPost->user->first_name;
            }
            // $posts[$p]->totalComments = count($posts[$p]->comment);

            // total comments
            $totalComments = Comment::where(['post_id' => $posts[$p]->id])->whereNull('parent_id')->count();

            $posts[$p]->totalComments = $totalComments;
            $posts[$p]->totalPostViews = countPostView($posts[$p]->id, $posts[$p]->post_type);
            $totalShare = DB::table('activity_post')
                //->where(['post_id' => $posts[$p]->id, 'activity_id' => 3])
                ->where(['post_id' => $posts[$p]->id])
                ->whereIn('activity_id', [3, 4, 5])
                ->select(['id'])
                ->count();
            $posts[$p]->totalShare = $totalShare;
            $posts[$p]->child_post_id = $posts[$p]->id;
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;
            // Set embed url info.
            if (!empty($posts[$p]->embed_code)) {
                $embedVideoInfo = getEmbedVideoInfo($posts[$p]->embed_code);
                $posts[$p]->embed_code_type = $embedVideoInfo['type'];
                $posts[$p]->videoid = $embedVideoInfo['videoid'];
            }
            if (Auth::check()) {
                $isBookMark = DB::table('bookmarks')
                    ->where([
                        'post_id' => $posts[$p]->id,
                        'user_id' => Auth::user()->id
                    ])
                    ->count();
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
            } else {
                $isBookMark = 0;
                $isUpvote = 0;
                $isDownvote = 0;
            }

            $posts[$p]->isBookMark = ($isBookMark != 0) ? 'Y' : 'N';
            $posts[$p]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';
            $posts[$p]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';

            // Set unique cardID.
            $posts[$p]->cardID = $posts[$p]->id;

            if (!empty($posts[$p]->image)) {
                $posts[$p]->image = generate_post_image_url('post/thumbs/' . $posts[$p]->image);
            }

            if (!empty($posts[$p]->video)) {
                $posts[$p]->video = generate_post_video_url('video/' . $posts[$p]->video);
            }
            if (!empty($posts[$p]->video_poster)) {
                $posts[$p]->video_poster = generate_post_video_url('video/thumbnail/' . $posts[$p]->video_poster);
            }

            if (!empty($posts[$p]->feature_photo_detail)) {
                $percentage = ($posts[$p]->feature_photo_detail->thumb_height /
                        $posts[$p]->feature_photo_detail->thumb_width) * 100;
                $posts[$p]->feature_photo_detail->percentage = round($percentage, 2);
            }
        }

        $featured_cover_image_post = '';
        // Get the cover photo for first time.
        if ($this->page === 1 && $request->input('cover_photo') == "yes") {
            // $cover_photo = $this->getCoverPhoto($name, $post_type, $posts);
            $post_type = 'cover_photo';
            $featured_cover_image_post = $this->getPostByCategoryTagName($name, $post_type);
            if (!empty($featured_cover_image_post->image)) {
                $featured_cover_image_post->image = generate_post_image_url('post/' . $featured_cover_image_post->image);
            }
        }

        $related_categories = [];
        // Get the related categories for first time.
        if ($this->page === 1 && $request->input('rel_cat') == "yes") {
            // Get related categories first time.
            $related_categories = $this->getRelatedCategory($name);
            // dump($related_categories);
            $related_categories = $this->populateRelatedCategoryByTags($name, $related_categories);
        }

        // dd($related_categories);

        /*************Fetch Distance Between Post Location And End User Location****************/
        $userLocationInfo = Session::get('userLocationInfo');
        if ($userLocationInfo !== null && $request->input('userLocationSaved') == "true") {
            $posts = addPostDistance($userLocationInfo, $posts);
        }
        /*************Fetch Distance Between Post Location And End User Location****************/

        $response = [
            'related_categories' => $related_categories,
            'tagFollowStatus' => $postByCategoryTagName['tagFollowStatus'],
            'totalFollower' => $postByCategoryTagName['totalFollower'],
            'totalPost' => $postByCategoryTagName['totalPost'],
            'posts' => $posts,
            'featured_image_post' => $featured_cover_image_post
        ];
        return response()->json($response);
    }

    protected function getPostByCategoryTagName($name, $post_type, $per_page=0)
    {
        // Initialize variables.
        $tagFollowStatus = 0;
        $category_followers = [];
        $tag_followers = [];
        $totalFollower = 0;
        $totalPost = 0;
        $posts = [];

        $name = strtolower($name);

        // Get category case by searching insensitive category.
        $category = Category::searchByName($name)->first(['id']);

        // Get tag and post_id related to the tag.
        $tag = Tag::where('tag_name', $name)->orWhere('tag_name', str_slug($name))->first();


        // Check if user following the tag.
        $category_follower_count = 0;
        $tag_user_count = 0;
        if (Auth::check()) {
            if ($category !== null) {
                $category_follower_count = DB::table('category_follower')
                    ->where('category_id', $category->id)
                    ->where('follower_id', Auth::user()->id)
                    ->count();
            }
            if ($tag !== null) {
                $tag_user_count = DB::table('tag_user')
                    ->where('tag_id', $tag->id)
                    ->where('user_id', Auth::user()->id)
                    ->count();
            }
        }
        if ($category_follower_count > 0 || $tag_user_count > 0) {
            $tagFollowStatus = 1;
        }

        if ($tag !== null || $category !== null/* || $location_post !== null*/) {
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

                $public_or_follower_post =
                    Post::where('privacy_id', 1)
                        ->orderBy('id', 'desc')
                        ->get(['id']);
            }

            $public_or_follower_post_ids = array_pluck($public_or_follower_post, 'id');

            // Prepare the post query.
            $post = Post::whereNull('orginal_post_id');

            // Add filter based on post type.
            if ($this->filter_post_type > 0) {
                $post->where('post_type', $this->filter_post_type);
            }

            // No status post.
            // $post->where('post_type', '<>', 5);
            /*----------- For category/tag -----------*/
            $tag_post_id = [];
            if ($tag !== null) {
                $post_tag = DB::table('post_tag')->where('tag_id', $tag->id)->get(['post_id']);
                $tag_post_id = array_pluck($post_tag, 'post_id');
                // Get the no of tag followers.
                $tag_followers = DB::table('tag_user')
                    ->where('tag_id', $tag->id)
                    ->get(['user_id']);
                if (!empty($tag_followers)) {
                    $tag_followers = array_pluck($tag_followers, 'user_id');
                }
            }
            // Merge $location_post_ids and $tag_post_id.
            // $tag_post_id = array_merge($location_post_ids, $tag_post_id);

            /*==================== Here we go ======================*/
            // Get posts for category & tag.
            if ($category !== null && !empty($tag_post_id)) {
                $post->where(function ($query) use ($category, $tag_post_id) {
                    $query->where('category_id', $category->id)
                        ->orWhere('sub_category_id', $category->id)
                        ->orWhereIn('id', $tag_post_id);
                });
                // Get the no of category followers.
                $category_followers = DB::table('category_follower')
                    ->where('category_id', $category->id)
                    ->get(['follower_id']);
                if (!empty($category_followers)) {
                    $category_followers = array_pluck($category_followers, 'follower_id');
                }
            } // Get posts for category
            elseif ($category !== null) {
                $post->where(function ($query) use ($category) {
                    $query->where('category_id', $category->id)
                        ->orWhere('sub_category_id', $category->id);
                });
                // Get the no of category followers.
                $category_followers = DB::table('category_follower')
                    ->where('category_id', $category->id)
                    ->get(['follower_id']);;
                if (!empty($category_followers)) {
                    $category_followers = array_pluck($category_followers, 'follower_id');
                }
            } // Get posts for tag.
            elseif (!empty($tag_post_id)) {
                if ($post_type !== 'trending' && $post_type !== 'popular') {
                    $final_post_ids = array_intersect($public_or_follower_post_ids, $tag_post_id);
                    $post->whereIn('id', $final_post_ids);
                }
            } else {
                $unique_follower = array_unique($tag_followers);
                $totalFollower = count($unique_follower);
                goto return_area;
            }
            /*------------------ END For category/tag ------------------*/
            // Condition based on post types.
            if ($post_type === 'recent') {
                $post->whereIn('privacy_id', [1, 2])
                    ->orderBy('created_at', 'desc')
                    ->whereIn('id', $public_or_follower_post_ids);
            }
            elseif ($post_type === 'trending') {
                $day = 3;
                $post->where('points', '>', 0)
                    ->sinceDaysAgo($day)
                    ->orderBy('points', 'desc');

                $activity_posts = DB::table('activity_post')
                    ->where('created_at', '>=', Carbon::now()->subDays($day));
                // ->orderBy('post_id')
                // Get activity_post_id for category & tag.
                $activity_post_id = [];
                if ($category !== null && !empty($tag_post_id)) {
                    $activity_post_id = Post::where(function ($query) use ($category, $tag_post_id) {
                        $query->where('category_id', $category->id)
                            ->orWhere('sub_category_id', $category->id)
                            ->orWhereIn('id', $tag_post_id);
                    })
                        ->get(['id']);
                } // Get activity_post_id for category
                elseif ($category !== null) {
                    $activity_post_id = Post::where(function ($query) use ($category) {
                        $query->where('category_id', $category->id)
                            ->orWhere('sub_category_id', $category->id);
                    })
                        ->get(['id']);
                } // Get activity_post_id for tag.
                elseif (!empty($tag_post_id)) {
                    $activity_post_id = Post::whereIn('id', $tag_post_id)->get(['id']);
                }
                // Add post_id to query activity_post.
                if (!empty($activity_post_id)) {
                    // Get the post creaters..
                    $post_users = Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
                    $activity_posts->whereIn('post_id', $activity_post_id)
                        // Remove activity by the post creater.
                        ->whereNotIn('id', function ($query) use ($post_users) {
                            $query->select('id')
                                ->from('activity_post');
                            foreach ($post_users as $post_user) {
                                $query->orWhere(function ($query) use ($post_user) {
                                    $query->where('user_id', $post_user->created_by)
                                        ->where('post_id', $post_user->id);
                                });
                            }
                        });
                }
                $activity_posts = $activity_posts->get(['activity_id', 'post_id']);

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
                 * Sort the array in following order:
                 * 1. point, 2. up vote, 3. share
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

                $sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
                // dd($post_ids);
                // Take  intersection..
                $post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);
                if (!empty($post_ids)) {
                    // $post_ids_ordered = implode(',', $post_ids);
                    $post_ids_ordered = implode(',', $sorted_activity_post_ids);

                    $post->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
                } else {
                    goto return_area;
                }
            }
            elseif ($post_type === 'popular') {
                $post->where('points', '>', 0);
               // ->orderBy('points', 'desc');//(08-02-18)
                $day = 7;
                $activity_posts = DB::table('activity_post')
                    ->where('created_at', '>=', Carbon::now()->subDays($day));
                    
                // ->orderBy('post_id')
                // Get activity_post_id for category & tag.
                $activity_post_id = [];
                if ($category !== null && !empty($tag_post_id)) {
                    $activity_post_id = Post::where(function ($query) use ($category, $tag_post_id) {
                        $query->where('category_id', $category->id)
                            ->orWhere('sub_category_id', $category->id)
                            ->orWhereIn('id', $tag_post_id);
                    })
                        ->get(['id']);
                } // Get activity_post_id for category
                elseif ($category !== null) {
                    $activity_post_id = Post::where(function ($query) use ($category) {
                        $query->where('category_id', $category->id)
                            ->orWhere('sub_category_id', $category->id);
                    })
                        ->get(['id']);
                } // Get activity_post_id for tag.
                elseif (!empty($tag_post_id)) {
                    $activity_post_id = Post::whereIn('id', $tag_post_id)->get(['id']);
                }
                // Add post_id to query activity_post.
                if (!empty($activity_post_id)) {
                    // Get the post creators..
                    $post_users = Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
                    $activity_posts->whereIn('post_id', $activity_post_id)
                        // Remove activity by the post creater.
                        ->whereNotIn('id', function ($query) use ($post_users) {
                            $query->select('id')
                                ->from('activity_post');
                            foreach ($post_users as $post_user) {
                                $query->orWhere(function ($query) use ($post_user) {
                                    $query->where('user_id', $post_user->created_by)
                                        ->where('post_id', $post_user->id);
                                });
                            }
                        });
                    $activity_posts = $activity_posts->get(['activity_id', 'post_id']);
                }

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
                    $point = 0;
                    // determine point based on activity.
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
                 * Sort the array in following order:
                 * 1. point, 2. upvote, 3. share
                ================================================================ */
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

                $sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
                // dd($post_ids);
                // Take  intersection..
                $post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);
                if (!empty($post_ids)) {
                    // $post_ids_ordered = implode(',', $post_ids);
                    $post_ids_ordered = implode(',', $sorted_activity_post_ids);

                    $post->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
                       
                } else {
                    goto return_area;
                }


               



            }
            elseif ($post_type === 'location') {
                // Original code in explore.
                // $post->where('points', '>=', 0);// //changes(19-01-18) add for include post which have 0 point
 
                // $api_link = 'https://ipinfo.io/' . $this->clientIp;
                // $guzzle = new GuzzleClient();
                // $response_body = $guzzle->get($api_link, [
                //     'verify' => false
                // ])->getBody();
                // $ipinfo_obj = json_decode($response_body);
                // // Query country.
                // // what if we find no country from ip
                // if (!empty($ipinfo_obj->country)) {
                //     $day = 7;
                //     $allowedCreationTime = Carbon::now()->subDays($day);
                //     // Query based on
                //     $post->where('country_code', $ipinfo_obj->country)
                //         /*->where('created_at', '>=', $allowedCreationTime)*/;
                //     /*--- Sort posts based on activity, posts must be less that 7 days old ---*/
                //     $activity_posts = DB::table('activity_post');
                //     //     // Get activities of atmost 7 days posts.
                //     //     ->whereIn('post_id', function($query) use ($allowedCreationTime) {
                //     //         $query->select("id")
                //     //             ->from('posts');
                //     //            // ->where('created_at', '>=', $allowedCreationTime);//(13-11-17) remove post age condition
                //     //     });
                //     // // Get the post creaters..
                //     $post_users =  Post::where('created_at', '>=', $allowedCreationTime)
                //         ->get(['id', 'created_by']);
                //     // Remove activity by the post creater.
                //     $activity_posts->whereNotIn('id', function($query) use ($post_users) {
                //         $query->select('id')
                //             ->from('activity_post');
                //         foreach ($post_users as $post_user) {
                //             $query->orWhere(function($query) use ($post_user) {
                //                 $query->where('user_id', $post_user->created_by)
                //                     ->where('post_id', $post_user->id);
                //             });
                //         }
                //     });
                //     $activity_posts = $activity_posts->get(['activity_id', 'post_id']);

                //     $activityPostSort = [];

                // if(!empty($activity_posts))
				// {
                //     foreach ($activity_posts as $key => $activity_post) {
                //         // Initialize.
                //         if (empty($activityPostSort[$activity_post->post_id]['point'])) {
                //             $activityPostSort[$activity_post->post_id]['point'] = 0;
                //         }
                //         if (empty($activityPostSort[$activity_post->post_id]['upvote'])) {
                //             $activityPostSort[$activity_post->post_id]['upvote'] = 0;
                //         }
                //         if (empty($activityPostSort[$activity_post->post_id]['share'])) {
                //             $activityPostSort[$activity_post->post_id]['share'] = 0;
                //         }
                //         // determine point based on activity.
                //         $point = 0;
                //         if ($activity_post->activity_id == 1) {
                //             $point = 2;
                //             $activityPostSort[$activity_post->post_id]['upvote'] += 1;
                //         }
                //         elseif ($activity_post->activity_id == 2) {
                //             $point = -2;
                //         }
                //         elseif ($activity_post->activity_id == 3) {
                //             $point = 10;
                //             $activityPostSort[$activity_post->post_id]['share'] += 1;
                //         }
                //         elseif ($activity_post->activity_id == 4) {
                //             $point = 10;
                //         }
                //         elseif ($activity_post->activity_id == 5) {
                //             $point = 10;
                //         }
                //         elseif ($activity_post->activity_id == 8) {
                //             $point = 2;
                //         }
                //         elseif ($activity_post->activity_id == 9) {
                //             $point = 2;
                //         }
                //         elseif ($activity_post->activity_id == 10) {
                //             $point = 2;
                //         }
                //         elseif ($activity_post->activity_id == 11) {
                //             $point = 2;
                //         }

                //         $activityPostSort[$activity_post->post_id]['point'] += $point;
                //         $activityPostSort[$activity_post->post_id]['post_id'] = $activity_post->post_id;

                //         /*****get distance for sorting (13-11-17)**************/
				// 				$userLocationInfo   = Session::get('userLocationInfo');
				// 				if($userLocationInfo !== null ){
				// 					$postInformation=Post::where('id',  $activity_post->post_id)
				// 					->first();
				// 					$userLatitude       = $userLocationInfo['lat'];
				// 					$userLongitude      = $userLocationInfo['lon'];
				// 					$latitudeTo  = floatval($postInformation->lat);
				// 					$longitudeTo = floatval($postInformation->lon); 
									
				// 					$distance1 = haversineGreatCircleDistance($userLatitude, $userLongitude, $latitudeTo, $longitudeTo);
				// 					$activityPostSort[$activity_post->post_id]['distance1'] = $distance1;
									
				// 				}
								
				// 		/*******get distance for sorting(13-11-17)******/	
                //     }
                // }
                //     /* ===============================================================
                //      * Sort the array in following order:
                //       * 1.distance 2. point, 3. up vote, 4. share
                //     ================================================================ */
                //     usort($activityPostSort, function ($item1, $item2) {
                //         if ($item1['distance1'] == $item2['distance1']) {
                //             if ($item1['point'] == $item2['point']) {
                //                 if ($item1['upvote'] == $item2['upvote']) {
                //                     if ($item1['share'] == $item2['share']) {
                //                         // Sort by latest post.
                //                         return $item2['post_id'] < $item1['post_id'] ? -1 : 1;
                //                     }
                //                     return $item2['share'] < $item1['share'] ? -1 : 1;
                //                 }
                //                 return $item2['upvote'] < $item1['upvote'] ? -1 : 1;
                //             }
                //             return $item2['point'] < $item1['point'] ? -1 : 1;
                //         }
                //         return $item2['distance1'] > $item1['distance1'] ? -1 : 1;
						
                //     });

                //     /*if (!empty($_REQUEST['test'])) {
                //         dd($activityPostSort);
                //     }*/

                //     $sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
                //     // dd($post_ids);
                //     // Take  intersection..
                //     $post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);
                //     // Allow 0 point posts also.
                //     // $post_ids = $public_or_follower_post_ids;
                //     if (!empty($post_ids)) {
                //         // $post_ids_ordered = implode(',', $post_ids);
                //         $post_ids_ordered = implode(',', $sorted_activity_post_ids);

                //         $post->whereIn('id', $post_ids)
                //             // ->orderByRaw("FIELD(id, $post_ids_ordered)")
                //             ->orderByRaw("IF(FIELD(id,$post_ids_ordered)=0,1,0) ,FIELD(id,$post_ids_ordered)")
                //         ;
                //     }

                //     /*===================================================================*/
				// 	// Order posts based on lat and lon
				// 	// $userLocationInfo   = Session::get('userLocationInfo');
					
				// 	// if (!empty($this->position['lat']) && !empty($this->position['lng'])) {
				// 	// 	//$lat = $this->position['lat'];
				// 	// 	//$lon = $this->position['lng'];
				// 	// 	$lat = $userLocationInfo['lat'];
				// 	// 	$lon = $userLocationInfo['lon'];

				// 	// 	$post->orderByRaw("3956 * 2 * ASIN(SQRT( POWER(SIN(($lat - abs(lat)) *  pi()/180 / 2), 2) + COS($lat * pi()/180) * COS(abs(lat) * pi()/180) * POWER(SIN(($lon - lon) * pi()/180 / 2), 2) )) ");
				// 	// }
				// 	// else {
				// 	// 	$post->orderBY('id', 'desc');
				// 	// }
                //     /*==================================================================*/
                    


                // }
                // else {
                //     goto return_area;
                // }


                $userLocationInfo   = Session::get('userLocationInfo');

					$latitudeFrom 		= $userLocationInfo['lat'];
					$longitudeFrom		= $userLocationInfo['lon'];
					$country_code  		= $userLocationInfo['country_code'];
					
					$currentDateTime 	= Carbon::now()->toDateTimeString();
        			$dayBeforeDateTime  = Carbon::now()->addDays(-1);

						$lastActivityDate   = Carbon::now()->addDays(-3); 
						if(Auth::check())
						{
							$sql = "SELECT `id`, `created_at`, ( 6371 * acos( cos( radians(".$latitudeFrom.") ) * cos( radians( `lat` ) ) * cos( radians(  `lon` ) - radians(".$longitudeFrom.") ) + sin( radians(".$latitudeFrom.") ) * sin( radians( `lat` ) ) ) ) AS distance  FROM `posts`
		
							WHERE 
								   (`country_code`='".$country_code."' AND `lat`!='' AND  `lon`!='') 
							   AND 
								   (`privacy_id`='1' 
									   OR 
                                    (`privacy_id`='2' AND `created_by` IN (SELECT `user_id` FROM `followers` WHERE `follower_id`='".Auth::user()->id."')))
						   
                                     AND 
                                    (`orginal_post_id` IS NULL 
                                     AND 
                                    `created_by` != '".Auth::user()->id."')
									
							   ";
						}
						else
						{
							$sql = "SELECT `id`, `created_at`, ( 6371 * acos( cos( radians(".$latitudeFrom.") ) * cos( radians( `lat` ) ) * cos( radians(  `lon` ) - radians(".$longitudeFrom.") ) + sin( radians(".$latitudeFrom.") ) * sin( radians( `lat` ) ) ) ) AS distance  FROM `posts`
		
							WHERE 
								   (`country_code`='".$country_code."' AND `lat`!='' AND  `lon`!='') 
							   AND 
								   (`privacy_id`='1' 
									   )
						   
							   AND 
								   (`orginal_post_id` IS NULL 
									  )"
									
							 ;
						}

						if ($category !== null && !empty($tag_post_id)) {
							$sql=$sql." AND ( category_id = '".$category->id."' OR sub_category_id='".$category->id."' OR  id IN ('".implode("','",$tag_post_id)."') )";
						}
						else if ($category !== null) {
							$sql=$sql." AND ( category_id = '".$category->id."' OR sub_category_id='".$category->id."')";
						}
						else if (!empty($tag_post_id)) {
							
                                $final_post_ids = array_intersect($public_or_follower_post_ids, $tag_post_id);
                                $sql=$sql." AND id IN ('".implode("','",$final_post_ids)."') ";
                            
                        }
                        


						$sql=$sql." AND 
						`points`>=0
						   
							".($this->filter_post_type > 0 ? " AND `post_type`='$this->filter_post_type' " : "")." GROUP BY `id` ORDER BY  `distance` ASC, `created_at` DESC ";  //remove `points` DESC, 


				
		


		$nearBylocation = DB::select($sql);
		$searchNearby = array_pluck($nearBylocation, 'id');

        $arrayImplode = implode(",", $searchNearby);

        // \DB::connection()->enableQueryLog();

        if(!empty($searchNearby)) {

			$post = Post::whereIn('id', $searchNearby);
			$post->orderByRaw("FIELD(id, $arrayImplode)");
		
		}
		else
		{
			$post = array();
		}




    }

            // For cover photo.
            if ($post_type === 'cover_photo') {
                // \DB::connection()->enableQueryLog();

                /********change logic in fetch feture image(12-02-18) start ***********/

                    $day = 7;
                    $activity_posts = DB::table('activity_post')
                        ->where('created_at', '>=', Carbon::now()->subDays($day));

                    $activity_post_id = [];
                    if ($category !== null && !empty($tag_post_id)) {
                        $activity_post_id = Post::where(function ($query) use ($category, $tag_post_id) {
                            $query->where('category_id', $category->id)
                                ->orWhere('sub_category_id', $category->id)
                                ->orWhereIn('id', $tag_post_id);
                        })
                            ->get(['id']);
                    } // Get activity_post_id for category
                    elseif ($category !== null) {
                        $activity_post_id = Post::where(function ($query) use ($category) {
                            $query->where('category_id', $category->id)
                                ->orWhere('sub_category_id', $category->id);
                        })
                            ->get(['id']);
                    } // Get activity_post_id for tag.
                    elseif (!empty($tag_post_id)) {
                        $activity_post_id = Post::whereIn('id', $tag_post_id)->get(['id']);
                    }  
                    
                    // Add post_id to query activity_post.
                    if (!empty($activity_post_id)) {
                        // Get the post creators..
                        $post_users = Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
                        $activity_posts->whereIn('post_id', $activity_post_id)
                            // Remove activity by the post creater.
                            ->whereNotIn('id', function ($query) use ($post_users) {
                                $query->select('id')
                                    ->from('activity_post');
                                foreach ($post_users as $post_user) {
                                    $query->orWhere(function ($query) use ($post_user) {
                                        $query->where('user_id', $post_user->created_by)
                                            ->where('post_id', $post_user->id);
                                    });
                                }
                            });
                        $activity_posts = $activity_posts->get(['activity_id', 'post_id']);
                    }
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
                        $point = 0;
                        // determine point based on activity.
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
                    * Sort the array in following order:
                    * 1. point, 2. upvote, 3. share
                    ================================================================ */
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
                    
                    $sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
                    $post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);

                    $post_ids_ordered = implode(',', $sorted_activity_post_ids);
                
                 /********change logic in fetch feture image(12-02-18) end ***********/
                
                 if(!empty($post_ids_ordered))
                 {
             
                     $post = $post->where('image', '<>', '')
                         ->where('privacy_id', 1)
                         ->where('points', '>', 0)
                         // ->whereIn('id', $sorted_activity_post_ids)
                     // ->sinceDaysAgo(7)
                         // ->whereIn('id', $public_or_follower_post_ids)
                         ->with('user')
                     // ->orderBy('points', 'desc');
                 
                     ->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
                 }
                 else
                 {
                     $post = $post->where('image', '<>', '')
                         ->where('privacy_id', 1)
                         ->where('points', '>', 0)
                         // ->whereIn('id', $sorted_activity_post_ids)
                     // ->sinceDaysAgo(7)
                         // ->whereIn('id', $public_or_follower_post_ids)
                         ->with('user')
                     // ->orderBy('points', 'desc');
                 
                     ->whereIn('id', $post_ids);
                 }
                 
                    //->first();
                /*$query = \DB::getQueryLog();
                // $lastQuery = end($query);
                dd($query);*/
                $post=$post->first();
              

                if (!empty($post)) {
                    $post->child_post_id = $post->id;
                }
                return $post;
            }

            // \DB::connection()->enableQueryLog();
            // Count total posts..
            if(!empty($post))
			{
                    $totalPost = $post->count();

                    /*$query = \DB::getQueryLog();
                    dd($query);*/

                    // Column selection array for eager loaded data.
                    $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
                    $category_columns = ['id', 'category_name'];
                    $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
                    $region_columns = ['id', 'name', 'slug_name'];
                    $featurePhotoDetail_column = ['id', 'post_id', 'thumb_width', 'thumb_height', 'data_url'];

                    // Eager load data from relations.
                    $post->with([
                        'user' => function ($query) use ($user_columns) {
                            $query->addSelect($user_columns);
                        },
                        'category' => function ($query) use ($category_columns) {
                            $query->addSelect($category_columns);
                        },
                        'subCategory' => function ($query) use ($category_columns) {
                            $query->addSelect($category_columns);
                        },
                        'country',
                        'country.region' => function ($query) use ($region_columns) {
                            $query->addSelect($region_columns);
                        },
                        'tags' => function ($query) use ($tag_columns) {
                            $query->addSelect($tag_columns);
                        },
                        'featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                            $query->addSelect($featurePhotoDetail_column);
                        }
                    ])
                        ->withCount('comment');


                    // Get the paginated result.
                    if($per_page !== 0){
                        $posts = $post->skip(0)->take($per_page)->get()->makeVisible('people_here');
                    }
                    else{
                        $posts = $post->skip($this->offset)->take($this->per_page)->get()->makeVisible('people_here');
                    }

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
                    $post_count = count($posts);
                    

                   

          }

          $merged_follower = array_merge($tag_followers, $category_followers);
          $unique_follower = array_unique($merged_follower);
          $totalFollower = count($unique_follower);
        }
        return_area:
        // Prepare the return data.
        $return_data = [
            'tagFollowStatus' => $tagFollowStatus,
            'totalFollower' => $totalFollower,
            'totalPost' => $totalPost,
            'posts' => $posts
        ];
        return $return_data;
    }

    /**
     * Redirect to post details page
     */
    public function redirectToPostDetails(Request $request, $category = null, $subcategory = null, $title = null, $id = null){

        $data = [];

        $user_agent = $request->header('User-Agent');
        $crawler_bot = false;
        foreach ($this->bots_array as $key => $bot) {
            if (strstr(strtolower($user_agent), $bot)) {
                $crawler_bot = true;
                break;
            }
        }

        if($crawler_bot){
            if (empty($id)) {
                if (!empty($title)) {
                    $id = $title;
                }
                else if (!empty($subcategory)) {
                    $id = $subcategory;
                }
                else if (!empty($category)) {
                    $id = $category;
                }
            }

            //Check if id is numeric or not
            if(is_numeric($id)){
                $post_details = $this->detailsJSON($id);
//                dd($post_details->tags);
                //If post id is not valid then abort again
                if(empty($post_details)){
                    abort(404);
                }
                $data['post'] = $post_details;
                return view('post/details-seo', $data);
            }
            else{
                abort(404);
            }
        }
        else{
            return view('index');
        }

    }

    /**
     * Redirect to tag details page
     */
    public function redirectToTagDetails(Request $request, $tag_name=''){
     
        $data = [];

        $user_agent = $request->header('User-Agent');
        $crawler_bot = false;
        foreach ($this->bots_array as $key => $bot) {
            if (strstr(strtolower($user_agent), $bot)) {
                $crawler_bot = true;
                break;
            }
        }

        if($crawler_bot){
            /*** If tag name is empty prohibit user from proceeding further else proceed ***/
            if(empty($tag_name)){
                abort(404);
            }
            else {
                //Format tag name properly
                $formatted_tag_name = str_ireplace('-&-', ' and ', trim($tag_name));
                $formatted_tag_name = str_ireplace('-', ' ', $formatted_tag_name);

                $data               = $this->getTagDetails($formatted_tag_name);
                /*** If tag name is invalid then abort user ***/

                /********* add for change title tag_name to tag_text (11-04-18) start*********/
                        $tagDetails = Tag::where('tag_name',$tag_name)->first();
                        if ($tagDetails === null) {
                            abort(404);
                        }

                     if($tagDetails->question_tag!='')
                     {
                        $tag_text=$tagDetails->question_tag;   
                     }
                     else
                     {
                        $tag_text=$tagDetails->tag_text; 
                     }

                     $formatted_tag_text = str_ireplace('-&-', ' and ', trim($tag_text));
                     $formatted_tag_text = str_ireplace('-', ' ', $formatted_tag_text);

                 /********* add for change title tag_name to tag_text (11-04-18) start*********/

                if(empty($data) || empty($data['posts'])){
                    abort(404);
                }
                //$data['tag_name']   = strtoupper(trim($tag_name));//add for change title tag_name to tag_text (11-04-18)
                
                $data['tag_name']   = trim($formatted_tag_text);//add for change title tag_name to tag_text (11-04-18)

                $data['tag_url']    = '/tag/' . $tag_name;
            }

            return view('post/tag-seo', $data);
        }
        else{
            return view('index');
        }


    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectToPlaceDetails(Request $request){

        $user_agent = $request->header('User-Agent');
        $crawler_bot = false;
        foreach ($this->bots_array as $key => $bot) {
            if (strstr(strtolower($user_agent), $bot)) {
                $crawler_bot = true;
                break;
            }
        }

        if($crawler_bot) {
            $query_string_arr = $request->all();

            /*** If no condition is supplied with url then abort else proceed ***/
            if (empty($query_string_arr)) {
                abort(404);
            }

            //Prepare query string
            $query_string = [];
            foreach ($query_string_arr as $key => $val) {
                $query_string[] = "$key=$val";
            }
            $query_string = implode('&', $query_string);

            //Change Per page data and
            $this->offset = 0;
            $this->per_page = 50;

            $data = $this->getPlaceDetails($query_string_arr);

            if ($data['posts']->isEmpty()) {
                abort(404);
            }

            $lowest_region_name = array_shift($query_string_arr);
            /*** If no data found then place is invaid and abort ***/
            if (empty($data)) {
                abort(404);
            }
            $data['lowest_region_name'] = $lowest_region_name;
            $data['place_url'] = '/place?' . $query_string;
            //dump($data);
            return view('place/place-seo', $data);
        }
        else{
            return view('index');
        }
    }

    public function getPlaceDetails($condition=array()){

        $response = [];

        if(empty($condition)){
            return $response;
        }

        $input = $condition;

        // Set view post type
        $post_type = 'recent';

//        \DB::connection()->enableQueryLog();

        $postForPlace = $this->getPostForPlace($input, $post_type);

        /*$query = \DB::getQueryLog();
        //$lastQuery = end($query);
        dd($query);*/

        $posts = $postForPlace['posts'];
        // Add data to each post.
        $post_count = count($posts);
        for ($p = 0; $p < $post_count; $p++) {
            $original_caption = $posts[$p]->caption;
            // Format Caption for Hash tags..
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

            // Add child post user id
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;

            if ($posts[$p]->parent_post_id != 0) {
                $posts[$p]->orginalPostUserName = $posts[$p]->parentPost->user->first_name;
            }
            // $posts[$p]->totalComments = count($posts[$p]->comment);

            // total comments
            $totalComments = Comment::where(['post_id' => $posts[$p]->id])->whereNull('parent_id')->count();

            $posts[$p]->totalComments = $totalComments;
            $posts[$p]->totalPostViews = countPostView($posts[$p]->id, $posts[$p]->post_type);
            $totalShare = DB::table('activity_post')
                //->where(['post_id' => $posts[$p]->id, 'activity_id' => 3])
                ->where(['post_id' => $posts[$p]->id])
                ->whereIn('activity_id', [3, 4, 5])
                ->select(['id'])
                ->count();
            $posts[$p]->totalShare = $totalShare;
            $posts[$p]->child_post_id = $posts[$p]->id;
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;
            // Set embed url info.
            if (!empty($posts[$p]->embed_code)) {
                $embedVideoInfo = getEmbedVideoInfo($posts[$p]->embed_code);
                $posts[$p]->embed_code_type = $embedVideoInfo['type'];
                $posts[$p]->videoid = $embedVideoInfo['videoid'];
            }

            $isBookMark = 0;
            $isUpvote = 0;
            $isDownvote = 0;

            $posts[$p]->isBookMark = ($isBookMark != 0) ? 'Y' : 'N';
            $posts[$p]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';
            $posts[$p]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';

            // Set unique cardID.
            $posts[$p]->cardID = $posts[$p]->id;

            if (!empty($posts[$p]->image)) {
                $posts[$p]->image = generate_post_image_url('post/thumbs/' . $posts[$p]->image);
            }

            if (!empty($posts[$p]->video)) {
                $posts[$p]->video = generate_post_video_url('video/' . $posts[$p]->video);
            }
            if (!empty($posts[$p]->video_poster)) {
                $posts[$p]->video_poster = generate_post_video_url('video/thumbnail/' . $posts[$p]->video_poster);
            }

            if (!empty($posts[$p]->feature_photo_detail)) {
                $percentage = ($posts[$p]->feature_photo_detail->thumb_height /
                        $posts[$p]->feature_photo_detail->thumb_width) * 100;
                $posts[$p]->feature_photo_detail->percentage = round($percentage, 2);
            }
        }

        // Get the cover photo
        $post_type = 'cover_photo';
        $featured_cover_image_post = $this->getPostForPlace($input, $post_type);
        if (!empty($featured_cover_image_post->image)) {
            $featured_cover_image_post->image = generate_post_image_url('post/' . $featured_cover_image_post->image);
        }
        
        // Get the recommended topic
        $recommended_topics = $this->getRecommendedTopicForLocation($input);

        $response = [
            'related_categories' => $recommended_topics,
            'tagFollowStatus' => $postForPlace['tagFollowStatus'],
            'totalFollower' => $postForPlace['totalFollower'],
            'totalPost' => $postForPlace['totalPost'],
            'posts' => $posts,
            'featured_image_post' => $featured_cover_image_post
        ];

        /**Enable default short desription**/
        $response['featured_image_post']['short_description'] = !empty($response['featured_image_post']['short_description']) && strlen($response['featured_image_post']['short_description']) > 100 ?
            $response['featured_image_post']['short_description'] : $this->default_description;

        return $response;

    }

    /**
     * Get Tag details foe seo purpose
     *
     * @param string $name
     * @return array
     */
    public function getTagDetails($name=''){
        $response = [];

        if(empty($name)){
            return $response;
        }

        // Set view post type
        $post_type = 'recent';

        // \DB::connection()->enableQueryLog();

        $postByCategoryTagName = $this->getPostByCategoryTagName($name, $post_type, 50);

        /*$query = \DB::getQueryLog();
        //$lastQuery = end($query);
        dd($query);*/

        $posts = $postByCategoryTagName['posts'];

        // Add data to each post.
        $post_count = count($posts);
        for ($p = 0; $p < $post_count; $p++) {
            $original_caption = $posts[$p]->caption;
            // Format Caption for Hash tags..
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

            // Replace place_url if saved as undefined.
            if (!empty($posts[$p]->place_url) && $posts[$p]->place_url == 'undefined') {
                $posts[$p]->place_url = '';
            }

            // Add child post user id
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;

            if ($posts[$p]->parent_post_id != 0) {
                $posts[$p]->orginalPostUserName = $posts[$p]->parentPost->user->first_name;
            }
            // $posts[$p]->totalComments = count($posts[$p]->comment);

            // total comments
            $totalComments = Comment::where(['post_id' => $posts[$p]->id])->whereNull('parent_id')->count();

            $posts[$p]->totalComments = $totalComments;
            $posts[$p]->totalPostViews = countPostView($posts[$p]->id, $posts[$p]->post_type);
            $totalShare = DB::table('activity_post')
                //->where(['post_id' => $posts[$p]->id, 'activity_id' => 3])
                ->where(['post_id' => $posts[$p]->id])
                ->whereIn('activity_id', [3, 4, 5])
                ->select(['id'])
                ->count();
            $posts[$p]->totalShare = $totalShare;
            $posts[$p]->child_post_id = $posts[$p]->id;
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;
            // Set embed url info.
            if (!empty($posts[$p]->embed_code)) {
                $embedVideoInfo = getEmbedVideoInfo($posts[$p]->embed_code);
                $posts[$p]->embed_code_type = $embedVideoInfo['type'];
                $posts[$p]->videoid = $embedVideoInfo['videoid'];
            }

            $isBookMark = 0;
            $isUpvote = 0;
            $isDownvote = 0;

            $posts[$p]->isBookMark = ($isBookMark != 0) ? 'Y' : 'N';
            $posts[$p]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';
            $posts[$p]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';

            // Set unique cardID.
            $posts[$p]->cardID = $posts[$p]->id;

            if (!empty($posts[$p]->image)) {
                $posts[$p]->image = generate_post_image_url('post/thumbs/' . $posts[$p]->image);
            }

            if (!empty($posts[$p]->video)) {
                $posts[$p]->video = generate_post_video_url('video/' . $posts[$p]->video);
            }
            if (!empty($posts[$p]->video_poster)) {
                $posts[$p]->video_poster = generate_post_video_url('video/thumbnail/' . $posts[$p]->video_poster);
            }

            if (!empty($posts[$p]->feature_photo_detail)) {
                $percentage = ($posts[$p]->feature_photo_detail->thumb_height /
                        $posts[$p]->feature_photo_detail->thumb_width) * 100;
                $posts[$p]->feature_photo_detail->percentage = round($percentage, 2);
            }
        }

        $featured_cover_image_post = '';

        // Get the cover photo
        $post_type = 'cover_photo';
        $featured_cover_image_post = $this->getPostByCategoryTagName($name, $post_type);
        if (!empty($featured_cover_image_post->image)) {
            $featured_cover_image_post->image = generate_post_image_url('post/' . $featured_cover_image_post->image);
        }


        $related_categories = [];
        // Get the related categories
        $related_categories = $this->getRelatedCategory($name);
        // dump($related_categories);
        $related_categories = $this->populateRelatedCategoryByTags($name, $related_categories);


        // dd($related_categories);


        $response = [
            'related_categories' => $related_categories,
            'tagFollowStatus' => $postByCategoryTagName['tagFollowStatus'],
            'totalFollower' => $postByCategoryTagName['totalFollower'],
            'totalPost' => $postByCategoryTagName['totalPost'],
            'posts' => $posts,
            'featured_image_post' => $featured_cover_image_post
        ];

        /** Enable default short desription **/
        $response['featured_image_post']['short_description'] = !empty($response['featured_image_post']['short_description']) && strlen($response['featured_image_post']['short_description']) > 100 ?
            $response['featured_image_post']['short_description'] : $this->default_description;


        return $response;
    }

    protected function getCoverPhoto($name, $post_type, $posts = [])
    {
        if (empty($posts)) {
            $postByCategoryTagName = $this->getPostByCategoryTagName($name, $post_type);
            $posts = $postByCategoryTagName['posts'];
        }
        $cover_photo = '';
        $first_cover_photo = '';

        foreach ($posts as $post) {
            if ($post->image) {
                try {
                    // open image file
                    $image_make = Image::make('uploads/post/' . $post->image);
                    $width = $image_make->width();
                    $height = $image_make->height();
                    // Set the first image
                    if ($first_cover_photo === '') {
                        $first_cover_photo = $post->image;
                    }
                    // set the image only when it is large enough.
                    if ($width >= 1100 && $height >= 480) {
                        $cover_photo = $post->image;
                        break;
                    }

                } catch (Exception $e) {
                    // Nothing here.
                }
            }
        }

        // Return if result found.
        if ($cover_photo) {
            return $cover_photo;
        } elseif ($this->page <= 3) {
            $this->page++;
            $this->offset = ($this->page - 1) * $this->per_page;
            $this->getCoverPhoto($name, $post_type);
        }
        // If cover photo is not set then set first image.
        if ($cover_photo === '') {
            $cover_photo = $first_cover_photo;
        }
        return $cover_photo;
    }

    protected function getRelatedCategory($name)
    {
        $name = strtolower($name);
        $related_categories = [];
        // Get category case by searching insensitive category.
        $category = Category::searchByName($name)->first();

        if ($category !== null) {
            // Get parent category.
            if ($category->parent_id > 0) {
                $related_categories = DB::table('categories')
                    ->where('id', $category->parent_id)
                    ->orWhere(function ($q) use ($category) {
                        $q->where('id', '<>', $category->id)
                            ->where('parent_id', $category->parent_id);

                    })
                    ->get(['id','category_name']);
            } else {
                $related_categories = DB::table('categories')
                    ->orWhere('parent_id', $category->id)
                    ->get(['id', 'category_name']);
            }
            if (!empty($related_categories)) {
                // dump($related_categories);
                foreach ($related_categories as $key => $category) {
                    $related_categories[$key]->type = 'cat';
                    $name = strtolower($category->category_name);
                    $post_type = 'cover_photo';
                    $related_category_featured_post = $this->getPostByCategoryTagName($name, $post_type);
                    if ($related_category_featured_post !== null) {
                       
                        $related_categories[$key]->featured_post_image = $related_category_featured_post->image;
                    } else {
                        
                        $related_categories[$key]->featured_post_image = '';
                    }
                }
            }
        }
        return $related_categories;
    }

    protected function populateRelatedCategoryByTags($name, $related_categories)
    {
        $related_category_count = count($related_categories);

        // Prepare already included tags.
        $included_related_categories = array_pluck($related_categories, 'category_name');
        $included_related_categories = array_map(function ($item) {
            return str_slug_ovr($item);
        }, $included_related_categories);

        $related_category_limit = config('constants.TAG_RELATED_CATEGORY_LIMIT');

        // Load 100 popular posts.
        $this->per_page = 100;
        $postByCategoryTagName = $this->getPostByCategoryTagName($name, $post_type = 'popular');

        $posts = $postByCategoryTagName['posts'];

        // Initialize related tags array.
        $related_tags = [];
        if ($related_category_count > 0) {
            $related_category_limit -= $related_category_count;
        }
        // Get at most extra 15 related tags from popular.
        foreach ($posts as $post) {
            if (count($related_tags) >= $related_category_limit) {
                break;
            }
            // Get related tags.
            foreach ($post->tags as $tag) {
                if (count($related_tags) >= $related_category_limit) {
                    break;
                }
                $tag_name = $tag->tag_name;
                // Prepare already included tags.
                $included_name = array_pluck($related_tags, 'category_name');
                $included_name = array_map('strtolower', $included_name);
                $tag_name_lower = strtolower($tag_name);
                // Not include if.
                if ($name !== $tag_name &&
                    str_slug($name) !== $tag_name_lower &&
                    !in_array($tag_name_lower, $included_name) &&
                    !in_array($tag_name_lower, $included_related_categories)
                ) {
                    // Insert into included_related_categories
                    $included_related_categories[] = $tag_name_lower;

                    /*$featured_image_post = $this->getPostByCategoryTagName($tag_name, $post_type = 'cover_photo');
                    $featured_image_post_image = !empty($featured_image_post) ? $featured_image_post->image : '';*/

                    $related_tags[] = [
                        'id' => $tag->id,
                        /*'category_name' => $tag->tag_name,
                        'featured_post_image' => $featured_image_post_image,
                        'type' => 'tag'*/
                    ];
                }
            }
        }
        // Get the popular post ids.
        $popular_post_ids = array_pluck($posts, 'id');

        $related_tags_ranked = DB::table('post_tag')
            ->selectRaw("tag_id, tags.tag_name AS category_name,tags.tag_text AS tag_text, COUNT('post_id') AS totalPosts, tags.question_tag as question")
            ->join('tags', 'tags.id', '=', 'post_tag.tag_id')
            ->whereIn('tag_id', $related_tags)
            ->whereIn('post_id', $popular_post_ids)
            ->groupBy('tag_id')
            ->orderByRaw("totalPosts DESC")
            ->take(20)
            ->get();
        // Populate Featured image for tags.
        foreach ($related_tags_ranked as $key => $tag) {
             $featured_image_post = $this->getPostByCategoryTagName($tag->category_name, $post_type = 'cover_photo');
            $featured_image_post_image = !empty($featured_image_post->image) ? $featured_image_post->image : '';
            // Remove totalPosts from object.
            // unset($related_tags_ranked[$key]->totalPosts);
            // Add featured post to object.
            $related_tags_ranked[$key]->featured_post_image = $featured_image_post_image;
            
            $related_tags_ranked[$key]->type = 'tag';
        }

        // Merge related categories with ranked tags.
        $related_categories = array_merge($related_categories, $related_tags_ranked);
        $category_colors = config('constants.CATEGORY_COLORS');
        $total_color_count = count($category_colors);
        // randomize the colors.
        shuffle($category_colors);
        $category_color_count = 0;
        foreach ($related_categories as $key => $value) {
            $value = (array)$value;
            $value['category_name'] = str_replace('-', ' ', (strtolower($value['category_name'])));
            $value['category_name'] = str_replace(' & ', ' and ', $value['category_name']);
            $value['category_name_url'] = str_slug_ovr($value['category_name']);
            // Set colors.
            if (!empty($value['featured_post_image'])) {
                $value['featured_post_image'] = generate_post_image_url('post/thumbs/' . $value['featured_post_image']);
            } else {
                $category_color_count = $category_color_count == $total_color_count ? 0 : $category_color_count;
                $value['color'] = $category_colors[$category_color_count];
                $category_color_count++;
            }

            $related_categories[$key] = $value;
        }
        return $related_categories;
    }

    public function tagTopChannel(Request $request)
    {
        $response = [];
        $users = [];
        if (!$request->has('name')) {
            $response = [
                'error_message' => "Invalid request. Missing the 'name' parameter",
                'status' => 'INVALID_REQUEST'
            ];
            return response()->json($response, 400);
        }
        $name = $request->input('name');
        $name = strtolower($name);
        // Get category case by searching insensitive category.
        $category = Category::searchByName($name)->first(['id']);
        // Get tag and post_id related to the tag.
        $tag = Tag::where('tag_name', $name)->orWhere('tag_name', str_slug($name))->first();

        if ($tag !== null || $category !== null) {
            // Prepare the post query.
            $post = Post::whereNull('orginal_post_id');

            /*
             * Get the "followers only" posts of user's
             * whom the logged in user following.
             */
            $public_or_follower_post = Post::where('privacy_id', 1);
            if (Auth::check()) {
                $public_or_follower_post->orWhere(function ($query) {
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
                });
            }

            $public_or_follower_post = $public_or_follower_post->orderBy('id', 'desc')
                ->get(['id']);
            $public_or_follower_post_ids = array_pluck($public_or_follower_post, 'id');

            // No status post.
            // $post->where('post_type', '<>', 5);
            /*----------- For category/tag -----------*/
            if ($tag !== null) {
                $post_tag = DB::table('post_tag')->where('tag_id', $tag->id)->get(['post_id']);
                $tag_post_id = array_pluck($post_tag, 'post_id');
            }

            // Get posts for category & tag.
            if ($category !== null && !empty($tag_post_id)) {
                $post->where(function ($query) use ($category, $tag_post_id) {
                    $query->where('category_id', $category->id)
                        ->orWhere('sub_category_id', $category->id)
                        ->orWhereIn('id', $tag_post_id);
                });
            } // Get posts for category
            elseif ($category !== null) {
                $post->where(function ($query) use ($category) {
                    $query->where('category_id', $category->id)
                        ->orWhere('sub_category_id', $category->id);
                });
                // Get the no of category followers.
                $category_followers = DB::table('category_follower')->where('category_id', $category->id)->count();
            } // Get posts for tag.
            elseif (!empty($tag_post_id)) {
                $post->whereIn('id', $tag_post_id);
            } else {
                goto return_area;
            }

            $post = $post->groupBy('created_by')
                ->selectRaw('sum(points) as totalPoints, created_by')
                ->orderBy('totalPoints', 'desc')
                ->whereIn('id', $public_or_follower_post_ids)
                ->get();
            $user_ids = array_pluck($post, 'created_by');
            if (!empty($user_ids)) {
                $user_ids_ordered = implode(',', $user_ids);
                $user = User::whereIn('id', $user_ids)
                    ->orderByRaw("FIELD(id, $user_ids_ordered)");
                // Column selection array.
                $select_columns = [
                    'id',
                    'first_name',
                    'last_name',
                    'username',
                    'profile_image',
                    'cover_image',
                    'about_me'
                ];

                // Eager load data.
                $user->withCount('originalPost', 'follower');
                // Get the paginated result.
                $users = $user->skip($this->offset)->take($this->per_page)->get($select_columns);

                foreach ($users as $user) {
                    if (!empty($user->cover_image)) {
                        $user->cover_image = generate_profile_image_url('profile/cover/' . $user->cover_image);
                    }
                }
            }

            return_area:

            $response = [
                'users' => $users
            ];
            return response()->json($response);
        }
        $response = [
            'users' => $users
        ];
        return response()->json($response);
    }

    /*==================== END Tag Page ======================*/
    /*==================== Place Page ======================*/
    /**
     * Format the place name to show two values when landed with one param.
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getPlaceName(Request $request)
    {
        $input = $request->all();
        $response['hasText'] = 0;
        if (!empty($input['continent'])) {
            return response()->json($response);
        } elseif (!empty($input['region'])) {
            $region = Region::where('slug_name', 'LIKE', $input['region'])
                ->orWhere('name', 'LIKE', $input['region'])
                ->first(['continent']);
            if ($region !== null) {
                $response = [
                    'hasText' => 1,
                    'text' => str_slug_ovr($region->continent),
                    'type' => 'continent'
                ];
            }
        } elseif (!empty($input['country'])) {
            $country = Country::where('country_name', 'LIKE', $input['country'])
                ->orWhere('continent', 'LIKE', $input['country'])
                ->first();
            if ($country !== null) {
                if (!empty($country->region->slug_name)) {
                    $response = [
                        'hasText' => 1,
                        'text' => str_slug_ovr($country->region->slug_name),
                        'type' => 'region'
                    ];
                } else {
                    $response = [
                        'hasText' => 1,
                        'text' => str_slug_ovr($country->continent),
                        'type' => 'continent'
                    ];
                }
            }
        }
        return response()->json($response);
    }

    public function placeJson(Request $request)
    {
        $input = $request->all();

//        return response()->json($input, 400);

        // Set view post type
        $post_type = 'recent';
        if (!empty($input['type'])) {
            $post_type = $input['type'];
        }

//        \DB::connection()->enableQueryLog();

        $postForPlace = $this->getPostForPlace($input, $post_type);
        
        /*************(17-01-18) ***********/
       
        // $recommended_topics=array();
        // $featured_cover_image_post='';
        // $posts = $postForPlace['posts'];
        // $response = [
        //     'related_categories' => $recommended_topics,
        //     // 'tagFollowStatus' => $postForPlace['tagFollowStatus'],
        //     // 'totalFollower' => $postForPlace['totalFollower'],
        //     // 'totalPost' => $postForPlace['totalPost'],
        //     'posts' => $posts,
        //     'featured_image_post' => $featured_cover_image_post,
        
        // ];
        // return response()->json($response);

        /*************(17-01-18) ***********/


        /*$query = \DB::getQueryLog();
        //$lastQuery = end($query);
        dd($query);*/

        $posts = $postForPlace['posts'];
        // Add data to each post.
        $post_count = count($posts);
        for ($p = 0; $p < $post_count; $p++) {
            $original_caption = $posts[$p]->caption;
            // Format Caption for Hash tags..
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

            // Add child post user id
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;

            if ($posts[$p]->parent_post_id != 0) {
                $posts[$p]->orginalPostUserName = $posts[$p]->parentPost->user->first_name;
            }
            // $posts[$p]->totalComments = count($posts[$p]->comment);

            // total comments
            $totalComments = Comment::where(['post_id' => $posts[$p]->id])->whereNull('parent_id')->count();

            $posts[$p]->totalComments = $totalComments;
            $posts[$p]->totalPostViews = countPostView($posts[$p]->id, $posts[$p]->post_type);
            $totalShare = DB::table('activity_post')
                //->where(['post_id' => $posts[$p]->id, 'activity_id' => 3])
                ->where(['post_id' => $posts[$p]->id])
                ->whereIn('activity_id', [3, 4, 5])
                ->select(['id'])
                ->count();
            $posts[$p]->totalShare = $totalShare;
            $posts[$p]->child_post_id = $posts[$p]->id;
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;
            // Set embed url info.
            if (!empty($posts[$p]->embed_code)) {
                $embedVideoInfo = getEmbedVideoInfo($posts[$p]->embed_code);
                $posts[$p]->embed_code_type = $embedVideoInfo['type'];
                $posts[$p]->videoid = $embedVideoInfo['videoid'];
            }

            if (Auth::check()) {
                $isBookMark = DB::table('bookmarks')
                    ->where([
                        'post_id' => $posts[$p]->id,
                        'user_id' => Auth::user()->id
                    ])
                    ->count();

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
            } else {
                $isBookMark = 0;
                $isUpvote = 0;
                $isDownvote = 0;
            }

            $posts[$p]->isBookMark = ($isBookMark != 0) ? 'Y' : 'N';
            $posts[$p]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';
            $posts[$p]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';

            // Set unique cardID.
            $posts[$p]->cardID = $posts[$p]->id;

            if (!empty($posts[$p]->image)) {
                $posts[$p]->image = generate_post_image_url('post/thumbs/' . $posts[$p]->image);
            }

            if (!empty($posts[$p]->video)) {
                $posts[$p]->video = generate_post_video_url('video/' . $posts[$p]->video);
            }
            if (!empty($posts[$p]->video_poster)) {
                $posts[$p]->video_poster = generate_post_video_url('video/thumbnail/' . $posts[$p]->video_poster);
            }

            if (!empty($posts[$p]->feature_photo_detail)) {
                $percentage = ($posts[$p]->feature_photo_detail->thumb_height /
                        $posts[$p]->feature_photo_detail->thumb_width) * 100;
                $posts[$p]->feature_photo_detail->percentage = round($percentage, 2);
            }
        }

        $featured_cover_image_post = '';
        // Get the cover photo for first time.
        if ($this->page == 1 && $request->input('cover_photo') == "yes") {
            $post_type = 'cover_photo';
            $featured_cover_image_post = $this->getPostForPlace($input, $post_type);
            if (!empty($featured_cover_image_post->image)) {
                $featured_cover_image_post->image = generate_post_image_url('post/' . $featured_cover_image_post->image);
            }
        }

        $recommended_topics = [];

        // Get the recommended topic for first time.
        if ($this->page == 1 && !empty($input['rel_cat']) && $input['rel_cat'] == "yes") {
            $recommended_topics = $this->getRecommendedTopicForLocation($input);
        }

        // dd($recommended_topics);

        /*************Fetch Distance Between Post Location And End User Location****************/
        $userLocationInfo = Session::get('userLocationInfo');
        if ($userLocationInfo !== null && $request->input('userLocationSaved') != "true") {
            $posts = addPostDistance($userLocationInfo, $posts);
        }
        /*************Fetch Distance Between Post Location And End User Location****************/

        $response = [
            'related_categories' => $recommended_topics,
            'tagFollowStatus' => $postForPlace['tagFollowStatus'],
            'totalFollower' => $postForPlace['totalFollower'],
            'totalPost' => $postForPlace['totalPost'],
            'posts' => $posts,
            'featured_image_post' => $featured_cover_image_post
        ];
        return response()->json($response);
    }

    /**
     * Get location page posts data from database.
     *
     * @param $input
     * @param $post_type
     * @return array
     */
    protected function getPostForPlace($input, $post_type)
    {
        // Initialize variables.
        $tagFollowStatus = 0;
        $category_followers = [];
        $tag_followers = [];
        $totalFollower = 0;
        $totalPost = 0;
        $posts = [];

        $location = !empty($input['location']) ? $input['location'] : '';
        $city = !empty($input['city']) ? $input['city'] : '';
        $state = !empty($input['state']) ? $input['state'] : '';

        $region = !empty($input['region']) ? $input['region'] : '';
        $country = !empty($input['country']) ? $input['country'] : '';
        $continent = !empty($input['continent']) ? $input['continent'] : '';

        if (empty($location) && empty($city) && empty($state) && empty($region) && empty($country) && empty($continent)) {
            goto return_area;
        } else {
            /*
             * Get the public and "followers only" posts of users'
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
                $public_or_follower_post =
                    Post::where('privacy_id', 1)
                        ->orderBy('id', 'desc')
                        ->get(['id']);
            }
            $public_or_follower_post_ids = array_pluck($public_or_follower_post, 'id');

            // Prepare the post query.
            $post = Post::whereNull('orginal_post_id');

            // Add filter based on post type.
            if ($this->filter_post_type > 0) {
                $post->where('post_type', $this->filter_post_type);
            }

            /*==================== Here we go ======================*/
            // For params which are in posts table.
            if (!empty($location)) {
//                $post->where('location', $location);
                $post->searchByAddress('location', $location);
            }
            if (!empty($city)) {
//                $post->where('city', $city);
                $post->searchByAddress('city', $city);
            }
            if (!empty($state)) {
//                $post->where('state', $state);
                $post->searchByAddress('state', $state);
            }

            //*============== Condition based on post types. ================*//
            if ($post_type === 'recent') {
                $post->whereIn('privacy_id', [1, 2])
                    ->orderBy('created_at', 'desc')
                    ->whereIn('id', $public_or_follower_post_ids);
                /*
                 * For params which are dependent country.
                 * Query against country data of X when it is present irrespective of others.
                 * where X's priority order is below:
                 * 1. region >> 2. country >> 3. continent
                 */
                if (!empty($region) || !empty($country) || !empty($continent)) {
                    $post->whereIn('country_code', function ($query) use ($region, $country, $continent) {
                        if (!empty($country)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->where('country_name', 'LIKE', $country)
                                ->orWhere('country_code', 'LIKE', $country);
                        } elseif (!empty($region)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->whereIn('region_id', function ($q2) use ($region) {
                                    $q2->select('id')
                                        ->from('regions')
                                        ->where('name', 'LIKE', $region)
                                        ->orWhere('slug_name', 'LIKE', $region);
                                });
                        } elseif (!empty($continent)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->where('continent', 'LIKE', $continent);
                        }
                        // Add ORed condition for location.
                        $location_data_count = 0;
                        if (!empty($region)) {
                            $location_data_count++;
                            $location_data = $region;
                        }
                        if (!empty($country)) {
                            $location_data_count++;
                            $location_data = $country;
                        }
                        if (!empty($continent)) {
                            $location_data_count++;
                            $location_data = $continent;
                        }
                        if ($location_data_count <= 1) {
                            $ld1 = str_replace('-', ' ', strtolower($location_data));
                            $ld2 = str_replace(' and ', ' & ', strtolower($location_data));
                            $query->orWhereRaw("(`location` like ? OR `location` LIKE ?)", array($ld1, $ld2));
                        }
                        //*========= END ORed condition for location ==========*//
                    });

                }
            }
            elseif ($post_type === 'trending') {
                $day = 3;
                $post->where('points', '>', 0)
                    ->sinceDaysAgo($day)
                    ->orderBy('points', 'desc');

                $activity_posts = DB::table('activity_post')
                    ->where('created_at', '>=', Carbon::now()->subDays($day));
                // ->orderBy('post_id')

                /*--------------------------------------------------------------------------*/
                // Get activity_post_id for category & tag.
                $activity_post_id = Post::whereNull('orginal_post_id');

                // Add filter based on post type.
                if ($this->filter_post_type > 0) {
                    $activity_post_id->where('post_type', $this->filter_post_type);
                }

                /*==================== Here we go ======================*/
                // For params which are in posts table.
                if (!empty($location)) {
                    $post->searchByAddress('location', $location);
                }
                if (!empty($city)) {
                    $post->searchByAddress('city', $city);
                }
                if (!empty($state)) {
                    $post->searchByAddress('state', $state);
                }

                /*
                 * For params which are dependent country.
                 * Query against country data of X when it is present irrespective of others.
                 * where X's priority order is below:
                 * 1. region >> 2. country >> 3. continent
                 */
                if (!empty($region) || !empty($country) || !empty($continent)) {
                    $activity_post_id->whereIn('country_code', function ($query) use ($region, $country, $continent) {
                        if (!empty($country)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->where('country_name', 'LIKE', $country)
                                ->orWhere('country_code', 'LIKE', $country);
                        } elseif (!empty($region)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->whereIn('region_id', function ($q2) use ($region) {
                                    $q2->select('id')
                                        ->from('regions')
                                        ->where('name', 'LIKE', $region)
                                        ->orWhere('slug_name', 'LIKE', $region);
                                });
                        } elseif (!empty($continent)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->where('continent', 'LIKE', $continent);
                        }
                    });
                    // Add ORed condition for location.
                    $location_data_count = 0;
                    if (!empty($region)) {
                        $location_data_count++;
                        $location_data = $region;
                    }
                    if (!empty($country)) {
                        $location_data_count++;
                        $location_data = $country;
                    }
                    if (!empty($continent)) {
                        $location_data_count++;
                        $location_data = $continent;
                    }
                    if ($location_data_count <= 1) {
                        $ld1 = str_replace('-', ' ', strtolower($location_data));
                        $ld2 = str_replace(' and ', ' & ', strtolower($location_data));
//                        $post->orWhereRaw( "(`location` like ? OR `location` LIKE ?)", array( $ld1,  $ld2) );
                        $activity_post_id->orWhereRaw("(`location` like ? OR `location` LIKE ?)", array($ld1, $ld2));
                    }
                    //*========= END ORed condition for location ==========*//
                }

                $activity_post_id = $activity_post_id->get(['id']);
                /*-------------------------------------------------------------------------*/

                // Add post_id to query activity_post.
                if (!empty($activity_post_id)) {
                    // Get the post creators..
                    $post_users = Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
                    $activity_posts->whereIn('post_id', $activity_post_id)
                        // Remove activity by the post creator.
                        ->whereNotIn('id', function ($query) use ($post_users) {
                            $query->select('id')
                                ->from('activity_post');
                            foreach ($post_users as $post_user) {
                                $query->orWhere(function ($query) use ($post_user) {
                                    $query->where('user_id', $post_user->created_by)
                                        ->where('post_id', $post_user->id);
                                });
                            }
                        });
                }
                $activity_posts = $activity_posts->get(['activity_id', 'post_id']);

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
                 * Sort the array in following order:
                 * 1. point, 2. up vote, 3. share
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

//                dd($activityPostSort);

                $sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
                // dd($post_ids);
                // Take  intersection..
                $post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);

                if (!empty($post_ids)) {
                    // $post_ids_ordered = implode(',', $post_ids);
                    $post_ids_ordered = implode(',', $sorted_activity_post_ids);

                    $post->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
                } else {
                    goto return_area;
                }
            }
            elseif ($post_type === 'popular') {
                $post->where('points', '>', 0);
                $day = 7;
                $activity_posts = DB::table('activity_post')
                    ->where('created_at', '>=', Carbon::now()->subDays($day));
                // ->orderBy('post_id')
                /*--------------------------------------------------------------------------*/
                // Get activity_post_id for category & tag.
                $activity_post_id = Post::whereNull('orginal_post_id');

                // Add filter based on post type.
                if ($this->filter_post_type > 0) {
                    $activity_post_id->where('post_type', $this->filter_post_type);
                }

                /*==================== Here we go ======================*/
                // For params which are in posts table.
                if (!empty($location)) {
                    $post->searchByAddress('location', $location);
                }
                if (!empty($city)) {
                    $post->searchByAddress('city', $city);
                }
                if (!empty($state)) {
                    $post->searchByAddress('state', $state);
                }

                /*
                 * For params which are dependent country.
                 * Query against country data of X when it is present irrespective of others.
                 * where X's priority order is below:
                 * 1. region >> 2. country >> 3. continent
                 */
                if (!empty($region) || !empty($country) || !empty($continent)) {
                    $activity_post_id->whereIn('country_code', function ($query) use ($region, $country, $continent) {
                        if (!empty($country)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->where('country_name', 'LIKE', $country)
                                ->orWhere('country_code', 'LIKE', $country);
                        } elseif (!empty($region)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->whereIn('region_id', function ($q2) use ($region) {
                                    $q2->select('id')
                                        ->from('regions')
                                        ->where('name', 'LIKE', $region)
                                        ->orWhere('slug_name', 'LIKE', $region);
                                });
                        } elseif (!empty($continent)) {
                            $query->select('country_code')
                                ->from('countries')
                                ->where('continent', 'LIKE', $continent);
                        }
                    });
                    // Add ORed condition for location.
                    $location_data_count = 0;
                    if (!empty($region)) {
                        $location_data_count++;
                        $location_data = $region;
                    }
                    if (!empty($country)) {
                        $location_data_count++;
                        $location_data = $country;
                    }
                    if (!empty($continent)) {
                        $location_data_count++;
                        $location_data = $continent;
                    }
                    if ($location_data_count <= 1) {
                        $ld1 = str_replace('-', ' ', strtolower($location_data));
                        $ld2 = str_replace(' and ', ' & ', strtolower($location_data));
//                        $post->orWhereRaw( "(`location` like ? OR `location` LIKE ?)", array( $ld1,  $ld2) );
                        $activity_post_id->orWhereRaw("(`location` like ? OR `location` LIKE ?)", array($ld1, $ld2));
                    }
                    //*========= END ORed condition for location ==========*//
                }

                $activity_post_id = $activity_post_id->get(['id']);
                /*-------------------------------------------------------------------------*/
                // Add post_id to query activity_post.
                if (!empty($activity_post_id)) {
                    // Get the post creators..
                    $post_users = Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
                    $activity_posts->whereIn('post_id', $activity_post_id)
                        // Remove activity by the post creater.
                        ->whereNotIn('id', function ($query) use ($post_users) {
                            $query->select('id')
                                ->from('activity_post');
                            foreach ($post_users as $post_user) {
                                $query->orWhere(function ($query) use ($post_user) {
                                    $query->where('user_id', $post_user->created_by)
                                        ->where('post_id', $post_user->id);
                                });
                            }
                        });
                    $activity_posts = $activity_posts->get(['activity_id', 'post_id']);
                }

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
                    $point = 0;
                    // determine point based on activity.
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
                 * Sort the array in following order:
                 * 1. point, 2. upvote, 3. share
                ================================================================ */
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

                $sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
                // dd($post_ids);
                // Take  intersection..
                $post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);
                if (!empty($post_ids)) {
                    // $post_ids_ordered = implode(',', $post_ids);
                    $post_ids_ordered = implode(',', $sorted_activity_post_ids);

                    $post->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
                } else {
                    goto return_area;
                }
            }

            // For cover photo.
            if ($post_type === 'cover_photo') {
                /*
                 * For params which are dependent country.
                 * Query against country data of X when it is present irrespective of others.
                 * where X's priority order is below:
                 * 1. region >> 2. country >> 3. continent
                 */

                /************ code is blocked because change in logic of feture image (13-02-18) ************/

                        // if (!empty($region) || !empty($country) || !empty($continent)) {
                        //     $post->whereIn('country_code', function ($query) use ($region, $country, $continent) {
                        //         if (!empty($country)) {
                        //             $query->select('country_code')
                        //                 ->from('countries')
                        //                 ->where('country_name', 'LIKE', $country)
                        //                 ->orWhere('country_code', 'LIKE', $country);
                        //         } elseif (!empty($region)) {
                        //             $query->select('country_code')
                        //                 ->from('countries')
                        //                 ->whereIn('region_id', function ($q2) use ($region) {
                        //                     $q2->select('id')
                        //                         ->from('regions')
                        //                         ->where('name', 'LIKE', $region)
                        //                         ->orWhere('slug_name', 'LIKE', $region);
                        //                 });
                        //         } elseif (!empty($continent)) {
                        //             $query->select('country_code')
                        //                 ->from('countries')
                        //                 ->where('continent', 'LIKE', $continent);
                        //         }
                        //     });
                        // }

                        // $post = $post->where('image', '<>', '')
                        //     ->where('privacy_id', 1)
                        //     ->where('points', '>', 0)
                        //     ->sinceDaysAgo(7)
                        //     // ->whereIn('id', $public_or_follower_post_ids)
                        //     ->with('user')
                        //     ->orderBy('points', 'desc')->first();
                        // // Set child post id.
                        // if (!empty($post)) {
                        //     $post->child_post_id = $post->id;
                        // }
                        // return $post;
                /************ code is blocked because change in logic of feture image (13-02-18) end ************/



                /************** code for implement new logic()start ****************/

                        $day = 7;
                        $activity_posts = DB::table('activity_post')
                            ->where('created_at', '>=', Carbon::now()->subDays($day));
                        // ->orderBy('post_id')
                        /*--------------------------------------------------------------------------*/
                        // Get activity_post_id for category & tag.
                        $activity_post_id = Post::whereNull('orginal_post_id');

                        // Add filter based on post type.
                        // if ($this->filter_post_type > 0) {
                        //     $activity_post_id->where('post_type', $this->filter_post_type);
                        // }

                        // /*==================== Here we go ======================*/
                        // // For params which are in posts table.
                        // if (!empty($location)) {
                        //     $post->searchByAddress('location', $location);
                        // }
                        // if (!empty($city)) {
                        //     $post->searchByAddress('city', $city);
                        // }
                        // if (!empty($state)) {
                        //     $post->searchByAddress('state', $state);
                        // }

                        /*
                        * For params which are dependent country.
                        * Query against country data of X when it is present irrespective of others.
                        * where X's priority order is below:
                        * 1. region >> 2. country >> 3. continent
                        */
                        if (!empty($region) || !empty($country) || !empty($continent)) {
                            $activity_post_id->whereIn('country_code', function ($query) use ($region, $country, $continent) {
                                if (!empty($country)) {
                                    $query->select('country_code')
                                        ->from('countries')
                                        ->where('country_name', 'LIKE', $country)
                                        ->orWhere('country_code', 'LIKE', $country);
                                } elseif (!empty($region)) {
                                    $query->select('country_code')
                                        ->from('countries')
                                        ->whereIn('region_id', function ($q2) use ($region) {
                                            $q2->select('id')
                                                ->from('regions')
                                                ->where('name', 'LIKE', $region)
                                                ->orWhere('slug_name', 'LIKE', $region);
                                        });
                                } elseif (!empty($continent)) {
                                    $query->select('country_code')
                                        ->from('countries')
                                        ->where('continent', 'LIKE', $continent);
                                }
                            });
                            // Add ORed condition for location.
                            $location_data_count = 0;
                            if (!empty($region)) {
                                $location_data_count++;
                                $location_data = $region;
                            }
                            if (!empty($country)) {
                                $location_data_count++;
                                $location_data = $country;
                            }
                            if (!empty($continent)) {
                                $location_data_count++;
                                $location_data = $continent;
                            }
                            if ($location_data_count <= 1) {
                                $ld1 = str_replace('-', ' ', strtolower($location_data));
                                $ld2 = str_replace(' and ', ' & ', strtolower($location_data));
        //                        $post->orWhereRaw( "(`location` like ? OR `location` LIKE ?)", array( $ld1,  $ld2) );
                                $activity_post_id->orWhereRaw("(`location` like ? OR `location` LIKE ?)", array($ld1, $ld2));
                            }
                            //*========= END ORed condition for location ==========*//
                        }

                        $activity_post_id = $activity_post_id->get(['id']);
                        /*-------------------------------------------------------------------------*/
                        // Add post_id to query activity_post.
                        if (!empty($activity_post_id)) {
                            // Get the post creators..
                            $post_users = Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
                            $activity_posts->whereIn('post_id', $activity_post_id)
                                // Remove activity by the post creater.
                                ->whereNotIn('id', function ($query) use ($post_users) {
                                    $query->select('id')
                                        ->from('activity_post');
                                    foreach ($post_users as $post_user) {
                                        $query->orWhere(function ($query) use ($post_user) {
                                            $query->where('user_id', $post_user->created_by)
                                                ->where('post_id', $post_user->id);
                                        });
                                    }
                                });
                            $activity_posts = $activity_posts->get(['activity_id', 'post_id']);
                        }

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
                            $point = 0;
                            // determine point based on activity.
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
                        * Sort the array in following order:
                        * 1. point, 2. upvote, 3. share
                        ================================================================ */
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

                        $sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
                        // dd($post_ids);
                        // Take  intersection..
                        $post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);

                        $post_ids_ordered = implode(',', $sorted_activity_post_ids);
						
					
				
					
						if(!empty($post_ids_ordered))
                        {
					
                            $post = $post->where('image', '<>', '')
                                ->where('privacy_id', 1)
                                ->where('points', '>', 0)
                                // ->whereIn('id', $sorted_activity_post_ids)
                            // ->sinceDaysAgo(7)
                                // ->whereIn('id', $public_or_follower_post_ids)
                                ->with('user')
                            // ->orderBy('points', 'desc');
                        
                            ->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
                        }
                        else
                        {
                            $post = $post->where('image', '<>', '')
                                ->where('privacy_id', 1)
                                ->where('points', '>', 0)
                                // ->whereIn('id', $sorted_activity_post_ids)
                            // ->sinceDaysAgo(7)
                                // ->whereIn('id', $public_or_follower_post_ids)
                                ->with('user')
                            // ->orderBy('points', 'desc');
                        
                            ->whereIn('id', $post_ids);
                        }
                        
							//->first();
						/*$query = \DB::getQueryLog();
						// $lastQuery = end($query);
						dd($query);*/
						$post=$post->first();
					
		
						if (!empty($post)) {
							$post->child_post_id = $post->id;
						}
						return $post;



             /************** code for implement new logic()end ****************/



            }

//            \DB::connection()->enableQueryLog();
            // Count total posts..
            $totalPost = $post->count();
//            $query = \DB::getQueryLog();
            //dd($query);

            // Column selection array for eager loaded data.
            $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
            $category_columns = ['id', 'category_name'];
            $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
            $region_columns = ['id', 'name', 'slug_name'];
            $featurePhotoDetail_column = ['id', 'post_id', 'thumb_width', 'thumb_height', 'data_url'];

            // Eager load data from relations.
            $post->with([
                'user' => function ($query) use ($user_columns) {
                    $query->addSelect($user_columns);
                },
                'category' => function ($query) use ($category_columns) {
                    $query->addSelect($category_columns);
                },
                'subCategory' => function ($query) use ($category_columns) {
                    $query->addSelect($category_columns);
                },
                'country',
                'country.region' => function ($query) use ($region_columns) {
                    $query->addSelect($region_columns);
                },
                'tags' => function ($query) use ($tag_columns) {
                    $query->addSelect($tag_columns);
                },
                'featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                    $query->addSelect($featurePhotoDetail_column);
                }
            ])
                ->withCount('comment');

            // Get the paginated result.
            $posts = $post->skip($this->offset)->take($this->per_page)->get()->makeVisible('people_here');

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

            /*$merged_follower = array_merge($tag_followers, $category_followers);
            $unique_follower = array_unique($merged_follower);
            $totalFollower = count($unique_follower);*/
        }
        return_area:
        // Get total numer of follwers following the place.
        $place_url = place_url_from_array($input);
        $place_follower = DB::table('place_follower')->where('place_url', $place_url);
        $all_followers = $place_follower->get(['id']);
        $totalFollower = count($all_followers);
        // Check if the current user following the place.
        if (Auth::check()) {
            $place_follower_user = $place_follower->where('user_id', Auth::user()->id)->first(['id']);
            if (!empty($place_follower_user)) {
                $tagFollowStatus = 1;
            }
        }
        // Prepare the return data.
        $return_data = [
            'tagFollowStatus' => $tagFollowStatus,
            'totalFollower' => $totalFollower,
            'totalPost' => $totalPost,
            'posts' => $posts
        ];
        return $return_data;
    }

    protected function getRecommendedTopicForLocation($input)
    {
       
        // Related places limit.
        $related_places_limit = 20;
        // Initialize related addresses.
        $related_addresses = [];

        $location = !empty($input['location']) ? $input['location'] : '';
        $city = !empty($input['city']) ? $input['city'] : '';
        $state = !empty($input['state']) ? $input['state'] : '';
        // Reverse str_slug_ovr to query against original.
        $location_arr = slug_ovr_rev($location);
        $city_arr = slug_ovr_rev($city);
        $state_arr = slug_ovr_rev($state);

        // Initialize country, region.
        $country = [];
        $region = [];
        $region_columns = ['id', 'name', 'slug_name'];
        // Get country_codes to search against country related data.
        $country_codes = [];
        if (!empty($input['country'])) {
            $country = Country::where('country_name', 'LIKE', $input['country'])
                ->orWhere('country_name_slug', 'LIKE', $input['country'])
                ->first();
            // $country_codes = array_pluck($country, 'country_code');
            $country_codes = [$country->country_code];
        } elseif (!empty($input['region'])) {
            $region = Region::where('name', 'LIKE', $input['region'])
                ->orWhere('slug_name', 'LIKE', $input['region'])
                ->first();
            $country = $region->country;
            $country_codes = array_pluck($country, 'country_code');
        } elseif (!empty($input['continent'])) {
            $formatted_continent = str_replace('-', ' ', $input['continent']);
            $country = Country::where('continent', 'LIKE', $input['continent'])
                ->orWhere('continent', 'LIKE', $formatted_continent)
                ->get(['country_code']);
            $country_codes = array_pluck($country, 'country_code');
        }
        /* Get 20 location places for when there is all params.
         * Get related locations under city
         */
        if (
            !empty($city) && !empty($state)
            /*&& (!empty($input['country']) || !empty($input['region']) || !empty($input['continent']))*/
        ) {
            $post = Post::distinct()
                ->select(['location', 'place_url'])
                /*->where('location', '<>', '')*/
                ->where(function ($query) use ($city_arr) {
                    $query->where('city', 'LIKE', $city_arr[0])
                        ->orWhere('city', 'LIKE', $city_arr[1]);
                })
                // Remove current city from locations results.
                ->where(function ($query) use ($city_arr) {
                    $query->where('location', '<>', '')
                        ->where('location', 'NOT LIKE', $city_arr[0])
                        ->where('location', 'NOT LIKE', $city_arr[1]);
                })
                ->where(function ($query) use ($state_arr) {
                    $query->where('state', 'LIKE', $state_arr[0])
                        ->orWhere('state', 'LIKE', $state_arr[1]);
                });
            if (!empty($location)) {
                $post->where('location', 'NOT LIKE', $location_arr[0])
                    ->where('location', 'NOT LIKE', $location_arr[1]);
            }
            // Add condition for country related data.
            if (!empty($country_codes)) {
                $post->whereIn('country_code', $country_codes);
            }

            // \DB::connection()->enableQueryLog();
            // Get posts to suggest related places.
            $posts = $post->skip(0)->take($related_places_limit)->get();
            // Create locations url.
            $location_addresses = $this->create_location_url_from_posts($posts, $country, $region, $input);
            // dd($location_addresses);
            // Merge with existing.
            $related_addresses = array_merge($related_addresses, $location_addresses);
            $related_places_limit -= count($related_addresses);
        }
        /* 
         * Create urls using state if related place is not filled.
         */
        if ($related_places_limit > 0 && !empty($state)) {
            /*
             * For urls starting with location
             * This will execute when no city param in requesting url.
             */
            if (empty($city)) {
                // for url starting with location.
                $post = Post::distinct()
                    ->select(['location', 'place_url'])
                    ->where(function ($query) use ($state_arr) {
                        $query->where('state', 'LIKE', $state_arr[0])
                            ->orWhere('state', 'LIKE', $state_arr[1]);
                    })
                    // Remove url city & state from city results.
                    ->where(function ($query) use ($city_arr, $state_arr) {
                        $query->where('location', '<>', '')
                            ->where('location', 'NOT LIKE', $state_arr[0])
                            ->where('location', 'NOT LIKE', $state_arr[1])
                            ->where('city', 'NOT LIKE', $state_arr[0])
                            ->where('city', 'NOT LIKE', $state_arr[1]);
                    });
                // Remove current location from recommedations.
                if (!empty($location)) {
                    $post->where('location', 'NOT LIKE', $location_arr[0])
                        ->where('location', 'NOT LIKE', $location_arr[1]);
                }
                // Get posts to suggest related places.
                $posts = $post->skip(0)->take($related_places_limit)->get();
                // Create locations url.
                $location_addresses = $this->create_location_url_from_posts($posts, $country, $region, $input);
                // dd($location_addresses);
                // Merge with existing.
                $related_addresses = array_merge($related_addresses, $location_addresses);
                $related_places_limit -= count($related_addresses);
            }
            /*---- For urls starting with city. ----*/
            if ($related_places_limit > 0) {
                $post = Post::distinct()
                    ->select(['city', 'place_url'])
                    // Fetch only when location = city.
                    ->whereRaw("location = city AND  city IS NOT NULL")
                    ->where(function ($query) use ($state_arr) {
                        $query->where('state', 'LIKE', $state_arr[0])
                            ->orWhere('state', 'LIKE', $state_arr[1]);
                    })
                    // Remove url city & state from city results.
                    ->where(function ($query) use ($city_arr, $state_arr) {
                        $query->where('city', '<>', '')
                            ->where('city', 'NOT LIKE', $city_arr[0])
                            ->where('city', 'NOT LIKE', $city_arr[1])
                            ->where('city', 'NOT LIKE', $state_arr[0])
                            ->where('city', 'NOT LIKE', $state_arr[1]);
                    });
                if (!empty($location)) {
                    $post->where('location', 'NOT LIKE', $location_arr[0])
                        ->where('location', 'NOT LIKE', $location_arr[1]);
                }
                // Add condition for country related data.
                if (!empty($country_codes)) {
                    $post->whereIn('country_code', $country_codes);
                }
                // Get posts to suggest related places.
                $posts = $post->skip(0)->take($related_places_limit)->get();

                foreach ($posts as $post) {
                    $location_url = $post->place_url;
                    // Prepare query input for featured post image.
                    parse_str($location_url, $query_input);
                    // Append place page to url.
                    $location_url = '/place?' . $location_url;
                    // Get the featured post for the url.
                    $featured_image_post = $this->getPostForPlace($query_input, $post_type = 'cover_photo');
                    $featured_image_post_image = !empty($featured_image_post) ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

                    $related_addresses[] = [
                        'category_name' => $post->city,
                        'addr_comp' => 'city',
                        'featured_post_image' => $featured_image_post_image,
                        'type' => 'addr',
                        'location_url' => $location_url
                    ];
                }
                $related_places_limit -= count($related_addresses);
            }
        }
        /*
         * Create urls using country if related place is not filled.
         */
        if ($related_places_limit > 0 && !empty($input['country'])) {
            $country = Country::where('country_name', 'LIKE', $input['country'])
                ->orWhere('country_name_slug', 'LIKE', $input['country'])
                ->first();
            /*
             * For urls starting with location
             * This will execute when no city && state param in requesting url.
             */
            if (empty($city) && empty($state)) {
                // for url starting with location.
                $post = Post::distinct()
                    ->select(['location', 'place_url'])
                    ->where('country_code', $country->country_code)
                    // Remove url city & state from city results.
                    ->where(function ($query) use ($input) {
                        $query->where('location', '<>', '')
                            ->where('location', 'NOT LIKE', $input['country'])
                            ->where('location', 'NOT LIKE', $input['country'])
                            ->where('city', 'NOT LIKE', $input['country'])
                            ->where('city', 'NOT LIKE', $input['country'])
                            ->where('state', 'NOT LIKE', $input['country'])
                            ->where('state', 'NOT LIKE', $input['country']);
                    });
                if (!empty($location)) {
                    $post->where('location', 'NOT LIKE', $location_arr[0])
                        ->where('location', 'NOT LIKE', $location_arr[1]);
                }
                // Get posts to suggest related places.
                $posts = $post->skip(0)->take($related_places_limit)->get();
                // Create locations url.
                
                      $location_addresses = $this->create_location_url_from_posts($posts, $country, $region, $input);
                      // Merge with existing.
                      $related_addresses = array_merge($related_addresses, $location_addresses);
  //                dd($related_addresses);
                      $related_places_limit -= count($related_addresses);
                
            }

            /*
             * For urls starting with city
             * This will execute when no city && state param in requesting url.
             */
            if ($related_places_limit > 0 && empty($city) && empty($state)) {
                $post = Post::distinct()
                    ->select(['city', 'place_url'])
                    // Fetch only when location = city.
                    ->whereRaw("location = city AND  city IS NOT NULL")
                    ->where('country_code', $country->country_code)
                    // Remove url location, city & state from city results.
                    ->where(function ($query) use ($input) {
                        $query->where('city', '<>', '')
                            ->where('city', 'NOT LIKE', $input['country'])
                            ->where('city', 'NOT LIKE', $input['country'])
                            ->where('state', 'NOT LIKE', $input['country'])
                            ->where('state', 'NOT LIKE', $input['country']);
                    });
                // Get posts to suggest related places.
                $posts = $post->skip(0)->take($related_places_limit)->get();
                // Create locations url.
                $city_addresses = $this->create_city_url_from_posts($posts, $country, $region, $input);
                // Merge with existing.
                $related_addresses = array_merge($related_addresses, $city_addresses);
                // dd($related_addresses);
                $related_places_limit -= count($related_addresses);
            }

            /*---- For urls starting with state. ----*/
            if ($related_places_limit > 0) {
                $post = Post::distinct()
                    ->select(['state', 'place_url'])
                    ->where('country_code', $country->country_code)
                    // Fetch only when city is empty and location = state
                    ->whereRaw("location = state AND (city = '' OR city IS NULL)")
                    // Remove url state & country_name from state results.
                    ->where(function ($query) use ($state_arr, $country) {
                        $query->where('state', '<>', '')
                            ->where('state', 'NOT LIKE', $state_arr[0])
                            ->where('state', 'NOT LIKE', $state_arr[1]);
                    })
                    // Remove current state from city results.
                    ->where(function ($query) use ($country) {
                        $query->where('state', 'NOT LIKE', $country->country_name)
                            ->where('state', 'NOT LIKE', $country->country_name_slug);
                    });

                // Get posts to suggest related places.
                $posts = $post->skip(0)->take($related_places_limit)->get();
                // Create locations url.
                $state_addresses = $this->create_state_url_from_posts($posts, $country, $region, $input);
                // Merge with existing.
                $related_addresses = array_merge($related_addresses, $state_addresses);
//                dd($related_addresses);
                $related_places_limit -= count($related_addresses);
            }
        }
        /*
         * Create urls using region if related place is not filled.
         */
        if ($related_places_limit > 0 && !empty($input['region'])) {
            if (empty($region)) {
                $region = Region::where('name', 'LIKE', $input['region'])
                    ->orWhere('slug_name', 'LIKE', $input['region'])
                    ->first();
            }
            $countries = $region->country;
            /*
             * For urls starting with location
             * This will execute when no city && state && country param in requesting url.
             */
            if (empty($city) && empty($state) && empty($input['country'])) {
                // for url starting with location.
                $post = Post::distinct()
                    ->select(['location', 'place_url'])
                    ->whereIn('country_code', $country_codes)
                    // Remove url city & state from city results.
                    ->where(function ($query) use ($input) {
                        $query->where('location', '<>', '')
                            ->where('location', 'NOT LIKE', $input['region'])
                            ->where('location', 'NOT LIKE', $input['region'])
                            ->where('city', 'NOT LIKE', $input['region'])
                            ->where('city', 'NOT LIKE', $input['region'])
                            ->where('state', 'NOT LIKE', $input['region'])
                            ->where('state', 'NOT LIKE', $input['region']);
                    });
                if (!empty($location)) {
                    $post->where('location', 'NOT LIKE', $location_arr[0])
                        ->where('location', 'NOT LIKE', $location_arr[1]);
                }
                // Get posts to suggest related places.
                $posts = $post->skip(0)->take($related_places_limit)->get();
                // Create locations url.
                $location_addresses = $this->create_location_url_from_posts($posts, $country = [], $region, $input);
                // Merge with existing.
                $related_addresses = array_merge($related_addresses, $location_addresses);
//                dd($related_addresses);
                $related_places_limit -= count($related_addresses);
            }
            /*
             * For urls starting with city
             * This will execute when no city && state && country param in requesting url.
             */
            if ($related_places_limit > 0 && empty($city) && empty($state) && empty($input['country'])) {
                $post = Post::distinct()
                    ->select(['city', 'place_url'])
                    // Fetch only when location = city.
                    ->whereRaw("location = city AND  city IS NOT NULL")
                    ->whereIn('country_code', $country_codes)
                    // Remove url location, city & state from city results.
                    ->where(function ($query) use ($input) {
                        $query->where('city', '<>', '')
                            ->where('city', 'NOT LIKE', $input['region'])
                            ->where('city', 'NOT LIKE', $input['region'])
                            ->where('state', 'NOT LIKE', $input['region'])
                            ->where('state', 'NOT LIKE', $input['region']);
                    });
                if (!empty($location)) {
                    $post->where('location', 'NOT LIKE', $location_arr[0])
                        ->where('location', 'NOT LIKE', $location_arr[1]);
                }
                // Get posts to suggest related places.
                $posts = $post->skip(0)->take($related_places_limit)->get();
                // Create locations url.
                $city_addresses = $this->create_city_url_from_posts($posts, $country = [], $region, $input);
                // Merge with existing.
                $related_addresses = array_merge($related_addresses, $city_addresses);
                // dd($related_addresses);
                $related_places_limit -= count($related_addresses);
            }
            /*
             * For urls starting with state
             * This will execute when no city && state && country param in requesting url.
             */
            if ($related_places_limit > 0 && empty($city) && empty($state) && empty($input['country'])) {
                $post = Post::distinct()
                    ->select(['state', 'place_url'])
                    // Fetch only when city is empty and location = state
                    ->whereRaw("location = state AND (city = '' OR city IS NULL)")
                    ->whereIn('country_code', $country_codes)
                    // Remove url state & country_name from state results.
                    ->where(function ($query) use ($input) {
                        $query->where('state', '<>', '')
                            ->where('state', 'NOT LIKE', $input['region'])
                            ->where('state', 'NOT LIKE', $input['region']);
                    });
                if (!empty($location)) {
                    $post->where('location', 'NOT LIKE', $location_arr[0])
                        ->where('location', 'NOT LIKE', $location_arr[1]);
                }
                // Get posts to suggest related places.
                $posts = $post->skip(0)->take($related_places_limit)->get();
                // Create locations url.
                $state_addresses = $this->create_state_url_from_posts($posts, $country = [], $region, $input);
                // Merge with existing.
                $related_addresses = array_merge($related_addresses, $state_addresses);
//                dd($state_addresses);
                $related_places_limit -= count($related_addresses);
            }

            /*------- Fetch sibling countries -------*/
            foreach ($countries as $country) {
                if (
                    !empty($input['country']) &&
                    (
                        $country->country_name_slug == $input['country'] ||
                        $country->country_name == $input['country']
                    )
                ) {
                    continue;
                }
                $location_url = 'country=' . rawurlencode($country->country_name) . '&' .
                    'region=' . rawurlencode($region->name) . '&' .
                    'continent=' . rawurlencode($country->continent);
                // Check if post exists for the url.
                $checkCountryPost = Post::where('place_url', 'LIKE', $location_url)->count();
                if (!$checkCountryPost) {
                    continue;
                }

                // Prepare query input for featured post image.
                parse_str($location_url, $query_input);
                // Append place page to url.
                $location_url = '/place?' . $location_url;
                // Get the featured post for the url.
                $featured_image_post = $this->getPostForPlace($query_input, $post_type = 'cover_photo');
                $featured_image_post_image = !empty($featured_image_post) ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

                $related_addresses[] = [
                    'category_name' => $country->country_name,
                    'addr_comp' => 'country',
                    'featured_post_image' => $featured_image_post_image,
                    'type' => 'addr',
                    'location_url' => $location_url
                ];
            }
            $related_places_limit -= count($related_addresses);
        }
        /*
         * Try to get regions as places using continent if related place is not filled.
         */
        if ($related_places_limit > 0 && !empty($input['continent'])) {
            $formatted_continent = str_replace('-', ' ', $input['continent']);
            $regions = Region::where('continent', 'LIKE', $input['continent'])
                ->orWhere('continent', 'LIKE', $formatted_continent)
                ->get(['name', 'slug_name', 'continent']);
            foreach ($regions as $region) {
                if (
                    !empty($input['region']) &&
                    (
                        $region->name == $input['region'] ||
                        $region->slug_name == $input['region']
                    )
                ) {
                    continue;
                }
                $location_url = 'region=' . rawurlencode($region->name) . '&' .
                    'continent=' . rawurlencode($region->continent);

                // Check if post exists for the url.
                $checkCountryPost = Post::where('place_url', 'LIKE', $location_url)->count();
                if (!$checkCountryPost) {
                    continue;
                }
                // Prepare query input for featured post image.
                parse_str($location_url, $query_input);
                // Append place page to url.
                $location_url = '/place?' . $location_url;
                // Get the featured post for the url.
                $featured_image_post = $this->getPostForPlace($query_input, $post_type = 'cover_photo');
                $featured_image_post_image = !empty($featured_image_post) ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

                $related_addresses[] = [
                    'category_name' => $region->name,
                    'addr_comp' => 'region',
                    'featured_post_image' => $featured_image_post_image,
                    'type' => 'addr',
                    'location_url' => $location_url
                ];
            }
            $related_places_limit -= count($related_addresses);
        }

        // dd($related_addresses);
        // 
        $related_topics = [];
        /*===== Prepare included related addresses array =======*/
        $included_related_addresses = [];
        foreach ($related_addresses as $address) {
            // $included_related_addresses[] = str_slug_ovr($address['category_name']);
            $included_related_addresses[] = rawurlencode(strtolower($address['category_name']));
        }
        // dd($included_related_addresses);
        // Get related tags from popular.
        $related_tags_limit = 10;
        /*===== Load 100 popular posts. =======*/
        $this->per_page = 100;
        $postForPlace = $this->getPostForPlace($input, $post_type = 'popular');
//        dd($postForPlace);
        $posts = $postForPlace['posts'];
        // Get the popular post ids.
        $popular_post_ids = array_pluck($posts, 'id');

        // Initialize related tags.
        $related_tags = [];
        foreach ($posts as $post) {
            if (count($related_tags) >= $related_tags_limit) {
                break;
            }
            // Get related tags.
            foreach ($post->tags as $tag) {
                if (count($related_tags) >= $related_tags_limit) {
                    break;
                }
                $tag_name = $tag->tag_name;
                $tag_name_lower = strtolower($tag_name);
                // Not include if.
                if (!in_array($tag_name_lower, $included_related_addresses)) {
                    // Insert into included_related_categories
                    $included_related_addresses[] = $tag_name_lower;

                    $related_tags[] = [
                        'id' => $tag->id
                    ];
                }
            }
        }

        $related_tags_ranked = DB::table('post_tag')
            ->selectRaw("tag_id, tags.tag_name AS category_name, tags.tag_text AS tag_text, COUNT('post_id') AS totalPosts , tags.question_tag as question")
            ->join('tags', 'tags.id', '=', 'post_tag.tag_id')
            ->whereIn('tag_id', $related_tags)
            ->whereIn('post_id', $popular_post_ids)
            ->groupBy('tag_id')
            ->orderByRaw("totalPosts DESC")
            ->take(20)
            ->get();
        // Populate Featured image for tags.
        $related_tags_ranked_final = [];
        foreach ($related_tags_ranked as $key => $tag) {
            $featured_image_post = $this->getPostByCategoryTagName($tag->category_name, $post_type = 'cover_photo');
            $featured_image_post_image = !empty($featured_image_post) ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

            $related_tags_ranked_final[] = [
                'tag_id' => $tag->tag_id,
                'category_name' => $tag->category_name,
                'tag_text' => $tag->tag_text,
                'question' => $tag->question,
                'featured_post_image' => $featured_image_post_image,
                'type' => 'tag'
            ];
        }

//        dd($related_tags_ranked_final);
        /*------ randomize related address. -------*/
        // shuffle($related_addresses);
        // Merge related categories with ranked tags.
        $recommended_topics = array_merge($related_addresses, $related_tags_ranked_final);
        $category_colors = config('constants.CATEGORY_COLORS');
        $total_color_count = count($category_colors);
        // randomize the colors.
        shuffle($category_colors);
        $category_color_count = 0;
        foreach ($recommended_topics as $key => $value) {
            $value = (array)$value;
            if ($value['type'] == 'tag') {
                $value['category_name'] = str_replace('-', ' ', (strtolower($value['category_name'])));
                $value['category_name'] = str_replace(' & ', ' and ', $value['category_name']);
                $value['category_name_url'] = str_slug_ovr($value['category_name']);
                $value['location_url'] = '/tag/' . $value['category_name_url'];
            }

            // Set colors.
            if (empty($value['featured_post_image'])) {
                $category_color_count = $category_color_count == $total_color_count ? 0 : $category_color_count;
                $value['color'] = $category_colors[$category_color_count];
                $category_color_count++;
            }

            $recommended_topics[$key] = $value;
        }
        return $recommended_topics;
    }

    public function placeTopChannel(Request $request)
    {
        $input = $request->all();

        // return response()->json($input, 400);
        $users = [];

        $location = !empty($input['location']) ? str_replace(' and ', ' & ', strtolower($input['location'])) : '';
        $city = !empty($input['city']) ? str_replace(' and ', ' & ', strtolower($input['city'])) : '';
        $state = !empty($input['state']) ? str_replace(' and ', ' & ', strtolower($input['state'])) : '';

        $region = !empty($input['region']) ? str_replace(' and ', ' & ', strtolower($input['region'])) : '';
        $country = !empty($input['country']) ? str_replace(' and ', ' & ', strtolower($input['country'])) : '';
        $continent = !empty($input['continent']) ? str_replace(' and ', ' & ', strtolower($input['continent'])) : '';

        if (empty($location) && empty($city) && empty($state) && empty($region) && empty($country) && empty($continent)) {
            goto return_area;
        } else {
            /*
             * Get the "followers only" posts of user's
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
                $public_or_follower_post =
                    Post::where('privacy_id', 1)
                        ->orderBy('id', 'desc')
                        ->get(['id']);
            }
            $public_or_follower_post_ids = array_pluck($public_or_follower_post, 'id');

            // Prepare the post query.
            $post = Post::whereNull('orginal_post_id');

            /*==================== Here we go ======================*/
            // For params which are in posts table.
            if (!empty($location)) {
                $post->searchByAddress('location', $location);
            }
            if (!empty($city)) {
                $post->searchByAddress('city', $city);
            }
            if (!empty($state)) {
                $post->searchByAddress('state', $state);
            }

            /*
             * For params which are dependent country.
             * Query against country data of X when it is present irrespective of others.
             * where X's priority order is below:
             * 1. region >> 2. country >> 3. continent
             */
            if (!empty($region) || !empty($country) || !empty($continent)) {
                $post->whereIn('country_code', function ($query) use ($region, $country, $continent) {
                    if (!empty($country)) {
                        $query->select('country_code')
                            ->from('countries')
                            ->where('country_name', 'LIKE', $country)
                            ->orWhere('country_code', 'LIKE', $country);
                    } elseif (!empty($region)) {
                        $query->select('country_code')
                            ->from('countries')
                            ->whereIn('region_id', function ($q2) use ($region) {
                                $q2->select('id')
                                    ->from('regions')
                                    ->where('name', 'LIKE', $region)
                                    ->orWhere('slug_name', 'LIKE', $region);
                            });
                    } elseif (!empty($continent)) {
                        $query->select('country_code')
                            ->from('countries')
                            ->where('continent', 'LIKE', $continent);
                    }
                });
            }

            $post = $post->groupBy('created_by')
                ->selectRaw('sum(points) as totalPoints, created_by')
                ->orderBy('totalPoints', 'desc')
                ->whereIn('id', $public_or_follower_post_ids)
                ->get();
            $user_ids = array_pluck($post, 'created_by');
            if (!empty($user_ids)) {
                $user_ids_ordered = implode(',', $user_ids);
                $user = User::whereIn('id', $user_ids)
                    ->orderByRaw("FIELD(id, $user_ids_ordered)");
                // Column selection array.
                $select_columns = [
                    'id',
                    'first_name',
                    'last_name',
                    'username',
                    'profile_image',
                    'cover_image',
                    'about_me'
                ];

                // Eager load data.
                $user->withCount('originalPost', 'follower');
                // Get the paginated result.
                $users = $user->skip($this->offset)->take($this->per_page)->get($select_columns);

                foreach ($users as $user) {
                    if (!empty($user->cover_image)) {
                        $user->cover_image = generate_profile_image_url('profile/cover/' . $user->cover_image);
                    }
                }
            }
        }
        return_area:
        $response = [
            'users' => $users
        ];
        return response()->json($response);
    }

    /*
     * Create related address starting with location from posts.
     */
    protected function create_location_url_from_posts($posts, $country = [], $region = [], $input)
    {
        $related_addresses = [];
        foreach ($posts as $post) {
            $location_url = $post->place_url;
            // Prepare query input for featured post image.
            parse_str($location_url, $query_input);
            // Append place page to url.
            $location_url = '/place?' . $location_url;
            // Get the featured post for the url.
           
            $featured_image_post = $this->getPostForPlace($query_input, $post_type = 'cover_photo');//(17-01-18) commented for error occur.
            
            $featured_image_post_image = !empty($featured_image_post) ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

            $related_addresses[] = [
                'category_name' => $post->location,
                'addr_comp' => 'location',
                'featured_post_image' => $featured_image_post_image,
                'type' => 'addr',
                'location_url' => $location_url
            ];
        }
        return $related_addresses;
    }

    /*
     * Create related address starting with city from posts.
     */
    protected function create_city_url_from_posts($posts, $country = [], $region = [], $input)
    {
        $related_addresses = [];
        foreach ($posts as $post) {
            $location_url = $post->place_url;
            // Prepare query input for featured post image.
            parse_str($location_url, $query_input);
            // Append place page to url.
            $location_url = '/place?' . $location_url;
            // Get the featured post for the url.
            $featured_image_post = $this->getPostForPlace($query_input, $post_type = 'cover_photo');
            $featured_image_post_image = !empty($featured_image_post) ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

            $related_addresses[] = [
                'category_name' => $post->city,
                'addr_comp' => 'city',
                'featured_post_image' => $featured_image_post_image,
                'type' => 'addr',
                'location_url' => $location_url
            ];
        }
        return $related_addresses;
    }

    /*
     * Create related address starting with state from posts.
     */
    protected function create_state_url_from_posts($posts, $country = [], $region = [], $input)
    {
        $related_addresses = [];
        foreach ($posts as $post) {
            $location_url = $post->place_url;
            // Prepare query input for featured post image.
            parse_str($location_url, $query_input);
            // Append place page to url.
            $location_url = '/place?' . $location_url;
            // Get the featured post for the url.
            $featured_image_post = $this->getPostForPlace($query_input, $post_type = 'cover_photo');
            $featured_image_post_image = !empty($featured_image_post) ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

            $related_addresses[] = [
                'category_name' => $post->state,
                'addr_comp' => 'state',
                'featured_post_image' => $featured_image_post_image,
                'type' => 'addr',
                'location_url' => $location_url
            ];
        }
        return $related_addresses;

    }

    /*================================ END Place Page ===============================*/
    /*================= Post Add page index. ====================*/
    public function index()
    {
        $categories = Category::where('parent_id', 0)->get(['id', 'category_name']);
        return view('post.add', compact('categories'));
    }

    public function container($post_type)
    {
        
        $data = [];
        if ($post_type == 'video') {
            $data['post_type'] = 'video';
        } elseif ($post_type == 'article') {
            $data['post_type'] = 'article';
        } elseif ($post_type == 'link') {
            $data['post_type'] = 'link';
        } // Fallback to default photo post..
        else {
            $data['post_type'] = 'photo';
        }
        return view('post.add-container', $data);
    }

    public function generalForm($post_type)
    {
        $data['edit_post'] = false;
        if ($post_type == 'video') {
            $data['post_type'] = 'video';
        }
        elseif ($post_type == 'article') {
            $data['post_type'] = 'article';
        }
        elseif ($post_type == 'link') {
            $data['post_type'] = 'link';
        }
        elseif ($post_type == 'edit-photo') {
            $data['post_type'] = 'photo';
            $data['edit_post'] = true;
        }
        elseif ($post_type == 'edit-video') {
            $data['post_type'] = 'video';
            $data['edit_post'] = true;
        }
        elseif ($post_type == 'edit-article') {
            $data['post_type'] = 'article';
            $data['edit_post'] = true;
        }
        elseif ($post_type == 'edit-link') {
            $data['post_type'] = 'link';
            $data['edit_post'] = true;
        }
        // Fallback to default photo post..
        else {
            $data['post_type'] = 'photo';
        }
        return view('post.add-general', $data);
    }

    public function advanceForm()
    {
        return view('post.add-advance');
    }

    public function contentForm()
    {
        return view('post.add-content');
    }

    public function socialForm()
    {
        return view('post.add-social');
    }

    public function statusForm()
    {
        return view('post.add-status');
    }
    
    public function questionForm()
    {
        return view('post.add-question');
    }

    public function storeImagePost(Request $request)
    {
        $response = [];
        $input = $request->all();
        
        $hash_tag_pattern='';
        if(empty($input['caption']))
        {
            $input['caption']='';
        }
        $caption_tags='';

//         return response()->json($input, 400);

        $rules = [
            // 'caption' => 'required|max:255',
            'title' => 'required|max:255',
            /*'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:categories,id'*/
        ];
        // Condition for sub category..
        /*if ($input['sub_category_id']) {
            $rules['sub_category_id'] = 'required|exists:categories,id';
        }*/

        $messages = [
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'sub_category_id.required' => 'The subcategory field is required.',
            'sub_category_id.exists' => 'The selected subcategory is invalid.',
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $path = public_path() . '/uploads/post/';
        // Create new post..
        $post = new Post;
        $post->created_by = Auth::user()->id;

        $post->caption = !empty($input['caption']) ? $input['caption'] : '';
        $post->title = $input['title'];

        $post->category_id = !empty($input['category_id']) ? $input['category_id'] : 0;
        $post->sub_category_id = !empty($input['sub_category_id']) ? $input['sub_category_id'] : 0;

        $post->video = '';
        $post->embed_code = '';

        $post->short_description = !empty($input['short_description']) ? strip_tags($input['short_description']) : '';
        $post->content = !empty($input['content']) ? $input['content'] : '';
        // For Link type posts
        if (!empty($input['link'])) {
            $post->external_link = formatSourceUrl($input['link']);
        }
        else if (!empty($input['source'])) {
            $post->external_link = formatSourceUrl($input['source']);
        }

        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        if (!empty($input['allow_comment']) && $input['allow_comment'] == "true") {
            $post->allow_comment = 1;
        } else {
            $post->allow_comment = 0;
        }

        if (!empty($input['allow_share']) && $input['allow_share'] == "true") {
            $post->allow_share = 1;
        } else {
            $post->allow_share = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = !empty($input['post_type']) ? $input['post_type'] : 1;
        // Save image from external resource..
        if (!empty($input['image_url'])) {
            $post->image_url = $input['image_url'];
            // Part image url..
            $image_url_parts = pathinfo($post->image_url);
            $lowered_extension = strtolower($image_url_parts['extension']);

            if (
                !empty($image_url_parts['extension']) &&
                in_array($lowered_extension, $this->known_image_extensions)
            ) {
                $image_ext = $lowered_extension;
            }
            else {
                $image_ext = getImageExtensionFromUrl($post->image_url);

            }
            $image_ext = cleanFileExtension($image_ext);
            

            $original_name = $image_url_parts['filename'];
            $save_name = generateFileName($original_name) . '.' . $image_ext;

            if ($image_ext == 'gif' || $image_ext == 'GIF') {
                $saveGifImage = $this->saveGifImage($input['image_url'], $save_name);
                if ($saveGifImage == 'failed') {
                    $response['errors'] = ['Sorry! unable to post. <br> Please try with different url or upload a file.'];
                    return response()->json($response, 422);
                }
                /*copy($input['image_url'], $path . $save_name);
                copy($input['image_url'], $path . 'thumbs/' . $save_name);*/
            } else {
                $saveImage = $this->savePostImage($post->image_url, $isUrl = true, $save_name);
                if ($saveImage == 'failed') {
                    try {
                        copy($input['image_url'], $path . $save_name);
                        copy($input['image_url'], $path . 'thumbs/' . $save_name);
                    } catch (Exception $e) {
                        $parsed_url = parse_url($input['image_url']);
                        if ($parsed_url['scheme'] == 'https') {
                            $save_name = $input['image_url'];
                        } else {
                            $response['errors'] = ['Sorry! unable to post. Please try with different url or upload a file.'];
                            return response()->json($response, 422);
                        }
                    }
                }
            }
            // Fill Post array
            $post->image = $save_name;
            $post->source = !empty($input['source']) ? formatSourceUrl($input['source']) : '';
        } // Save uploaded image..
        elseif ($request->file('image')) {
            $image = $request->file('image');
            $original_name = $image->getClientOriginalName();
            $image_ext = $image->getClientOriginalExtension();
            $image_ext = cleanFileExtension($image_ext);
            $save_name = generateFileName($original_name) . '.' . $image_ext;
            // Save the image to storage..
            $this->savePostImage($image, $isUrl = false, $save_name);
            // Fill Post array
            $post->image = $save_name;
            $post->image_url = '';
            $post->source = '';
        }
        // Fill source if already not filled.
        if (empty($post->source)) {
            if (!empty($input['link'])) {
                $post->source = get_domain(formatSourceUrl($input['link']));
            } elseif (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } elseif (!empty($input['source'])) {
                $post->source = get_domain(formatSourceUrl($input['source']));
            }
        }
        // Check for malformed url.
        if (filter_var($post->source, FILTER_VALIDATE_URL) === FALSE) {
            $post->source = '';
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();
        $lastInsertID = $post->id;

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        if (!empty($input['caption'])) {
            $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        }

        $post_tags = [];
        if (!empty($input['tag'])) {
            $post_tags = explode(',', $input['tag']);
        }
        // Merge two types f tags and remove duplicates..
        if (!empty($caption_tags[1])) {
            $post_tags = array_merge($post_tags, $caption_tags[1]);
        }
        $post_tags = array_unique($post_tags, SORT_REGULAR);
        /*-------- Create tags -------*/
        $tag_sync_id = $this->saveTags($post_tags);
        $post->tags()->attach($tag_sync_id);
        /*------ END Tags ------*/

        // Add Existing Collection //
        if (!empty ($input['collection'])) {
            $post->collections()->sync($request->input('collection'));
        }

        // Post published directly  to the twitter
        if (isset($input['twitter_connect']) && $input['twitter_connect'] == 'true') {
            $this->publishedPostToTwitter($lastInsertID);
        }

        if (isset($input['facebook_connect']) && $input['facebook_connect'] == 'true') {

            $this->publishedPostToFacebook($lastInsertID);
        }

        $response['msg'] = 'Image successfully posted.';
        $response['hash_tag_pattern']=$hash_tag_pattern;
        $response['caption']=$input['caption'];
        $response['caption_tags']=$caption_tags;
        $response['post_tags']=$post_tags;
        $response['inputs']=$input;
        

        return response()->json($response, 201);
    }

    public function storeVideoPost(Request $request)
    {
        $response = [];
        $input = $request->all();

//       return response()->json($input, 400);
        $rules = [
            // 'caption' => 'required|max:255',
            'title' => 'required|max:255',
            /*'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:categories,id',
            'location' => 'required',
            'lat' => 'requierd',
            'lon' => 'requierd',*/
        ];
        // Condition for sub category..
        /*if ($input['sub_category_id']) {
            $rules['sub_category_id'] = 'required|exists:categories,id';
        }*/

        if (!empty($input['video'])) {
            $rules['video'] = 'max:10240';
        }

        $messages = [
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'sub_category_id.required' => 'The subcategory field is required.',
            'sub_category_id.exists' => 'The selected subcategory is invalid.',
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $time = time();
        $path = public_path() . '/uploads/video/';
        // Create new post..
        $post = new Post;
        $post->created_by = Auth::user()->id;

        $post->caption = !empty($input['caption']) ? $input['caption'] : '';
        $post->title = $input['title'];

        $post->category_id = !empty($input['category_id']) ? $input['category_id'] : 0;
        $post->sub_category_id = !empty($input['sub_category_id']) ? $input['sub_category_id'] : 0;

        $post->image = '';
        $post->image_url = '';

        $post->short_description = !empty($input['short_description']) ? strip_tags($input['short_description']) : '';
        $post->content = !empty($input['content']) ? $input['content'] : '';
        // For Link type posts
        if (!empty($input['link'])) {
            $post->external_link = formatSourceUrl($input['link']);
        }
        else if (!empty($input['source'])) {
            $post->external_link = formatSourceUrl($input['source']);
        }
        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        if (!empty($input['allow_comment']) && $input['allow_comment'] == "true") {
            $post->allow_comment = 1;
        } else {
            $post->allow_comment = 0;
        }

        if (!empty($input['allow_share']) && $input['allow_share'] == "true") {
            $post->allow_share = 1;
        } else {
            $post->allow_share = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = !empty($input['post_type']) ? $input['post_type'] : 2;
        // Save image from external resource..
        if (!empty($input['embed_code'])) {
            $post->embed_code = $input['parsed_embed_code'];
            $post->video = '';

            if (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } else {
                $post->source = formatSourceUrl(get_domain($input['embed_code']));
            }
        } // Save uploaded video..
        elseif ($request->file('video')) {
            $video = $request->file('video');
            $original_name = $video->getClientOriginalName();
            $video_ext = $video->getClientOriginalExtension();
            $save_file_name = generateFileName($original_name);
            $save_name = $save_file_name . '.' . $video_ext;

            $video->move($path, $save_name);

            // Fill Post array
            $post->video = $save_name;
            $post->embed_code = '';
            $post->source = '';
            try {
                // Save video thumbnail.
                $ffmpeg = FFMpeg::create();
                $video = $ffmpeg->open($path . $save_name);
                $video_poster = $save_file_name . '.jpg';
                // Get video duration.
                $ffprobe = FFProbe::create();
                $duration = $ffprobe
                    ->format($path . $save_name)// extracts file informations
                    ->get('duration');
                $cutAt = $this->getVideoCutTime($duration);
                // Extract frame.
                $video->frame(TimeCode::fromSeconds($cutAt))
                    ->save($path . 'thumbnail/' . $video_poster);

                /* Upload file to aws s3 */
                move_to_s3('/video/thumbnail/' . $video_poster, $path . 'thumbnail/' . $video_poster);
                // Fill post array.
                $post->video_poster = $video_poster;

            } catch (Exception $e) {
                $post->video_poster = '';
            }
            /* Upload file to aws s3 */
            move_to_s3('/video/' . $save_name, $path . $save_name);
        }
        // Fill source if already not filled.
        if (empty($post->source)) {
            if (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } elseif (!empty($input['source'])) {
                $post->source = formatSourceUrl($input['source']);
            }
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();
        $lastInsertID = $post->id;

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        if (!empty($input['caption'])) {
            $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        }

        $post_tags = [];
        if (!empty($input['tag'])) {
            $post_tags = explode(',', $input['tag']);
        }
        // Merge two types f tags and remove duplicates..
        if (!empty($caption_tags[1])) {
            $post_tags = array_merge($post_tags, $caption_tags[1]);
        }
        $post_tags = array_unique($post_tags, SORT_REGULAR);
        /*-------- Create tags -------*/
        $tag_sync_id = $this->saveTags($post_tags);
        $post->tags()->attach($tag_sync_id);
        /*------ END Tags ------*/

        // Add Existing Collection //
        if (!empty ($input['collection'])) {
            $post->collections()->sync($request->input('collection'));
        }

        // Post published directly  to the twitter
        if (isset($input['twitter_connect']) && $input['twitter_connect'] == true) {
            $this->publishedPostToTwitter($lastInsertID);
        }

        $response['msg'] = 'Video successfully posted.';
        return response()->json($response, 201);
    }

    public function storeArticlePost(Request $request)
    {
        $response = [];
        $input = $request->all();
        // return response()->json($input, 422);

        $rules = [
            // 'caption' => 'required|max:255',
            'title' => 'required|max:255',
            /*'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:categories,id',*/
            'content' => 'required',
            /*'location' => 'required',
            'lat' => 'requierd',
            'lon' => 'requierd',*/
        ];
        // Condition for sub category..
        /*if ($input['sub_category_id']) {
            $rules['sub_category_id'] = 'required|exists:categories,id';
        }*/

        $messages = [
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'sub_category_id.required' => 'The subcategory field is required.',
            'sub_category_id.exists' => 'The selected subcategory is invalid.',
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $path = public_path() . '/uploads/post/';
        // Create new post..
        $post = new Post;
        $post->created_by = Auth::user()->id;

        $post->caption = !empty($input['caption']) ? $input['caption'] : '';
        $post->title = $input['title'];

        $post->category_id = !empty($input['category_id']) ? $input['category_id'] : 0;
        $post->sub_category_id = !empty($input['sub_category_id']) ? $input['sub_category_id'] : 0;

        $post->video = '';
        $post->embed_code = '';

        $post->short_description = !empty($input['short_description']) ? strip_tags($input['short_description']) : '';

        if (!empty($input['content'])) {
            $content = $input['content'];
            // Remove new lines..
            $content = trim(preg_replace('/\s+/', ' ', $content));

            // Move photos to s3.
            $photo_ids = [];
            $pattern = '/< *img[^>]*src *= *["\']?([^"\']*)|< *img[^>]*src *= *["\']?([^"\']*)" data-photo-id="(\w+)"/';
            $content = preg_replace_callback($pattern, function($matches) use($path, &$photo_ids) {

                /*$photo_ids = $matches;
                return $matches[0];*/

                // For local image.
                if (strpos($matches[1], 'uploads/post/') !== false) {
                    $save_name = basename($matches[1]);
                }
                else {
                    // Download files to local.
                    $image_src = formatSourceUrl($matches[1]);
                    if (!empty($image_src)) {
                        $download_data = download_image_to_local($image_src, $path);
                        if ($download_data['status'] == 'success') {
                            $save_name = $download_data['save_name'];
                        }
                    }
                }
                try {
                    if(!empty($save_name)) {
                        move_to_s3('/post/article/' . $save_name, $path . $save_name);
                        $new_src = Storage::url('post/article/' . $save_name);
                    }
                    else {
                        throw new Exception('The variable $save_name is undefined.');
                    }

                } catch (\Exception $e) {
                    $new_src = '/assets/img/post-placeholder.png';
                }


                if (!empty($matches[2])) {
                    array_push($photo_ids, $matches[2]);
                    // Remove data-photo-id
                    $matches[0] = str_replace($matches[2], '', $matches[0]);
                }

                return str_replace($matches[1], $new_src, $matches[0]);
            }, $content);

//            return response($photo_ids, 422);

            // Delete DB rows from photos.
            if (!empty($photo_ids)) {
                Photo::whereIn('id', $photo_ids)->delete();
            }

            /* For Video tag */
            $video_tag_pattern = '/< *video.*data-video-id="(\w+)"/';
            $video_ids = [];

            $content = preg_replace_callback($video_tag_pattern, function($matches) use(&$video_ids) {
                if (!empty($matches[1])) {
                    array_push($video_ids, $matches[1]);
                }
                return $matches[0];
            }, $content);

            $response['video_ids'] = $video_ids;

            // Delete DB rows from videos.
            if (!empty($video_ids)) {
                Video::whereIn('id', $video_ids)->delete();
            }

            // Remove class and id from html..
//            $content = preg_replace('/data-photo-id=".*?"|id=".*?"|class=".*?"/', '', $content);
            $content = preg_replace(
                [
                    '/data-photo-id=".*?"/i',
                    '/data-video-id=".*?"/i',
//                    '/class=".*?"/i',
                    '/class="(?=(.(?!fr-))*?>).*?"/i',
                    '/id=".*?"/i',
                    '/srcset=".*?"/i',
                    '/height=".*?"/i',
                    '/width=".*?"/i',
                    '/sizes=".*?"/i',
                    '/onclick=".*?"/i'
                ],
                [
                    '', '', '', '', '', ''
                ], $content);

            $post->content = $content;
        } else {
            $post->content = '';
        }

        // Store the source
        if (!empty($input['link'])) {
            $input['link'] = formatSourceUrl($input['link']);
            $post->source = formatSourceUrl(get_domain($input['link']));
        }
        elseif (!empty($input['source'])) {
            $post->source = formatSourceUrl($input['source']);
        }
        else {
            $post->source = '';
        }

        // For Link type posts
        if (!empty($input['link'])) {
            $post->external_link = formatSourceUrl($input['link']);
        }
        else if (!empty($input['source'])) {
            $post->external_link = formatSourceUrl($input['source']);
        }

        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        if (!empty($input['allow_comment']) && $input['allow_comment'] == "true") {
            $post->allow_comment = 1;
        } else {
            $post->allow_comment = 0;
        }

        if (!empty($input['allow_share']) && $input['allow_share'] == "true") {
            $post->allow_share = 1;
        } else {
            $post->allow_share = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = 3;
        // Save image from external resource..
        if (!empty($input['image_url'])) {
            $post->image_url = $input['image_url'];
            // Part image url..
            $image_url_parts = pathinfo($post->image_url);
            $lowered_extension = strtolower($image_url_parts['extension']);

            if (
                !empty($image_url_parts['extension']) &&
                in_array($lowered_extension, $this->known_image_extensions)
            ) {
                $image_ext = $lowered_extension;
            }
            else {
                $image_ext = getImageExtensionFromUrl($post->image_url);
            }
            $image_ext = cleanFileExtension($image_ext);
            $original_name = $image_url_parts['filename'];
            $save_name = generateFileName($original_name) . '.' . $image_ext;

            if ($image_ext == 'gif' || $image_ext == 'GIF') {
                $saveGifImage = $this->saveGifImage($input['image_url'], $save_name);
                if ($saveGifImage == 'failed') {
                    $response['errors'] = ['Sorry! unable to post. Please try with different url or upload a file.'];
                    return response()->json($response, 422);
                }
            } else {
                // Save the image to storage..
                $saveImage = $this->savePostImage($post->image_url, $isUrl = true, $save_name);
                if ($saveImage == 'failed') {
                    try {
                        copy($input['image_url'], $path . $save_name);
                        copy($input['image_url'], $path . 'thumbs/' . $save_name);
                        /* Upload file to aws s3 */
                        move_to_s3('/post/' . $save_name, $path . $save_name);
                        move_to_s3('/post/thumbs/' . $save_name, $path . 'thumbs/' . $save_name);
                    } catch (Exception $e) {
                        $save_name = $input['image_url'];
                    }
                }
            }
            // Fill Post array
            $post->image = $save_name;
        } // Save uploaded image..
        elseif ($request->file('image')) {
            $image = $request->file('image');
            $original_name = $image->getClientOriginalName();
            $image_ext = $image->getClientOriginalExtension();
            $image_ext = cleanFileExtension($image_ext);
            $save_name = generateFileName($original_name) . '.' . $image_ext;

            // Save the image to storage..
            $this->savePostImage($image, $isUrl = false, $save_name);
            // Fill Post array
            $post->image = $save_name;
            $post->image_url = '';
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();
        $lastInsertID = $post->id;

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        if (!empty($input['caption'])) {
            $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        }

        $post_tags = [];
        if (!empty($input['tag'])) {
            $post_tags = explode(',', $input['tag']);
        }
        // Merge two types of tags and remove duplicates..
        if (!empty($caption_tags[1])) {
            $post_tags = array_merge($post_tags, $caption_tags[1]);
        }
        $post_tags = array_unique($post_tags, SORT_REGULAR);
        /*-------- Create tags -------*/
        $tag_sync_id = $this->saveTags($post_tags);
        $post->tags()->attach($tag_sync_id);
        /*------ END Tags ------*/

        // Add Existing Collection //
        if (!empty ($input['collection'])) {
            $post->collections()->sync($request->input('collection'));
        }

        // Post published directly  to the twitter
        if (isset($input['twitter_connect']) && $input['twitter_connect'] == true) {
            $this->publishedPostToTwitter($lastInsertID);
        }

        $response['msg'] = 'Article successfully posted.';
        return response()->json($response, 201);
    }

    public function storeStatusPost(Request $request)
    {
        $response = [];
        $input = $request->all();

        // return response()->json($input, 422);

        $rules = [
            // 'caption' => 'required|max:255',
            'privacy_id' => 'required|exists:privacies,id'
        ];

        $messages = [
            'privacy_id.exists' => 'The selected privacy is invalid.'
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $time = time();
        $path = public_path() . '/uploads/post/';
        // Create new post..
        $post = new Post;
        $post->created_by = Auth::user()->id;

        $post->caption = $input['caption'];
        // $post->title = '';
        $post->category_id = 0;
        $post->sub_category_id = 0;
        $post->allow_comment = 1;
        $post->allow_share = 0;

        // Initialize image or video.
        $post->image = '';
        $post->image_url = '';
        $post->video = '';
        $post->embed_code = '';

        $post->short_description = '';
        $post->content = '';

        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        if (!empty($input['allow_comment']) && $input['allow_comment'] == "true") {
            $post->allow_comment = 1;
        } else {
            $post->allow_comment = 0;
        }

        if (!empty($input['allow_share']) && $input['allow_share'] == "true") {
            $post->allow_share = 1;
        } else {
            $post->allow_share = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = 5;

        /*-------- File operations --------*/
        // For upload url
        if ($input['file_type'] == 'URL') {
            // Save image from external resource..
            if ($input['upload_url_type'] == 'image' && !empty($input['upload_url'])) {
                $post->image_url = $input['upload_url'];
                // Partition image url..
                $image_url_parts = pathinfo($post->image_url);
                $lowered_extension = strtolower($image_url_parts['extension']);

                if (
                    !empty($image_url_parts['extension']) &&
                    in_array($lowered_extension, $this->known_image_extensions)
                ) {
                    $image_ext = $lowered_extension;
                }
                else {
                    $image_ext = getImageExtensionFromUrl($post->image_url);
                }
                $image_ext = cleanFileExtension($image_ext);
                $original_name = $image_url_parts['filename'];
                $save_name = generateFileName($original_name) . '.' . $image_ext;

                if ($image_ext == 'gif' || $image_ext == 'GIF') {
                    $saveGifImage = $this->saveGifImage($input['upload_url'], $save_name);
                    if ($saveGifImage == 'failed') {
                        $response['errors'] = ['Sorry! unable to post. Please try with different url or upload a file.'];
                        return response()->json($response, 422);
                    }
                } else {
                    // Save the image to storage..
                    $this->savePostImage($post->image_url, $isUrl = true, $save_name);
                }
                // Fill Post array
                $post->image = $save_name;
                $post->source = !empty($input['source']) ? formatSourceUrl($input['source']) : '';
            } // Save video from external resource..
            else if ($input['upload_url_type'] == 'video' && !empty($input['embed_code'])) {
                $post->embed_code = $input['parsed_embed_code'];
                $post->video = '';

                if (!empty($input['source_domain'])) {
                    $post->source = formatSourceUrl($input['source_domain']);
                } else {
                    $post->source = formatSourceUrl(get_domain($input['embed_code']));
                }
            }
        } // For uploaded files.
        else {
            // Save uploaded image..
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $original_name = $image->getClientOriginalName();
                $image_ext = $image->getClientOriginalExtension();
                $image_ext = cleanFileExtension($image_ext);
                $save_name = generateFileName($original_name) . '.' . $image_ext;
                // Save the image to storage..
                $this->savePostImage($image, $isUrl = false, $save_name);
                // Fill Post array
                $post->image = $save_name;
                $post->image_url = '';
                $post->source = '';
            } // Save uploaded video..
            elseif ($request->hasFile('video')) {
                $path = public_path() . '/uploads/video/';
                $video = $request->file('video');
                $original_name = $video->getClientOriginalName();
                $video_ext = $video->getClientOriginalExtension();
                $save_file_name = generateFileName($original_name);
                $save_name = $save_file_name . '.' . $video_ext;

                $video->move($path, $save_name);

                // Fill Post array
                $post->video = $save_name;
                $post->embed_code = '';
                $post->source = '';
                try {
                    // Save video thumbnail.
                    $ffmpeg = FFMpeg::create();
                    $video = $ffmpeg->open($path . $save_name);
                    $video_poster = $save_file_name . '.jpg';
                    // Get video duration.
                    $ffprobe = FFProbe::create();
                    $duration = $ffprobe
                        ->format($path . $save_name)// extracts file informations
                        ->get('duration');
                    $cutAt = $this->getVideoCutTime($duration);
                    // Extract frame.
                    $video->frame(TimeCode::fromSeconds($cutAt))
                        ->save($path . 'thumbnail/' . $video_poster);
                    
                    /* Upload file to aws s3 */
                    move_to_s3('/video/thumbnail/' . $video_poster, $path . 'thumbnail/' . $video_poster);
                    // Fill post array.
                    $post->video_poster = $video_poster;

                } catch (Exception $e) {
                    $post->video_poster = '';
                }
                /* Upload file to aws s3 */
                move_to_s3('/video/' . $save_name, $path . $save_name);
            }
        }
        // Fill source if already not filled.
        if (empty($post->source)) {
            if (!empty($input['link'])) {
                $post->source = get_domain(formatSourceUrl($input['link']));
            } elseif (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } elseif (!empty($input['source'])) {
                $post->source = get_domain(formatSourceUrl($input['source']));
            }
        }
        // Check for malformed url.
        if (filter_var($post->source, FILTER_VALIDATE_URL) === FALSE) {
            $post->source = '';
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        if (!empty($input['caption'])) {
            $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        }

        $post_tags = [];
        
        if (!empty($input['tag'])) {
            //$post_tags = explode(',', strtolower($input['tag']));
            $post_tags = explode(',', $input['tag']);
        }
        // Merge two types of tags and remove duplicates..
        if (!empty($caption_tags)) {
            $post_tags = array_unique(array_merge($post_tags, $caption_tags[1]), SORT_REGULAR);
        }
        /*-------- Create tags -------*/
        $tag_sync_id = $this->saveTags($post_tags);
        $post->tags()->attach($tag_sync_id);
        /*------ END Tags ------*/

        // Add Existing Collection //
        /*--- No need in status ---*/
        /*if(!empty ($input['collection']))
        {
            $post->collections()->sync($request->input('collection'));
        }*/
        $response['msg'] = 'Status successfully posted.';
        $response['input'] = $input;
       
        return response()->json($response, 201);
    }


    public function storeQuestionPost(Request $request)
    {
        $response = [];
        $input = $request->all();

        // return response()->json($input, 422);

        $rules = [
            // 'caption' => 'required|max:255',
            'privacy_id' => 'required|exists:privacies,id'
        ];

        $messages = [
            'privacy_id.exists' => 'The selected privacy is invalid.'
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $time = time();
        $path = public_path() . '/uploads/post/';
        // Create new post..
        $post = new Post;
        $post->created_by = Auth::user()->id;

        $post->caption = $input['caption'];
        // $post->title = '';
        $post->category_id =!empty($input['category_id']) ? $input['category_id'] : 0;
        $post->sub_category_id = !empty($input['sub_category_id']) ? $input['sub_category_id'] : 0;
        $post->allow_comment = 1;
        $post->allow_share = 1;

        // Initialize image or video.
        $post->image = '';
        $post->image_url = '';
        $post->video = '';
        $post->embed_code = '';

        $post->short_description = !empty($input['short_description']) ? $input['short_description'] : '';
        $post->content = '';

        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        

        if (!empty($input['ask_anonymous']) && $input['ask_anonymous'] == "true") {
            $post->ask_anonymous = 1;
        } else {
            $post->ask_anonymous = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = 6;

        /*-------- File operations --------*/
        // For upload url
        if ($input['file_type'] == 'URL') {
            // Save image from external resource..
            if ($input['upload_url_type'] == 'image' && !empty($input['upload_url'])) {
                $post->image_url = $input['upload_url'];
                // Partition image url..
                $image_url_parts = pathinfo($post->image_url);
                $lowered_extension = strtolower($image_url_parts['extension']);

                if (
                    !empty($image_url_parts['extension']) &&
                    in_array($lowered_extension, $this->known_image_extensions)
                ) {
                    $image_ext = $lowered_extension;
                }
                else {
                    $image_ext = getImageExtensionFromUrl($post->image_url);
                }
                $image_ext = cleanFileExtension($image_ext);
                $original_name = $image_url_parts['filename'];
                $save_name = generateFileName($original_name) . '.' . $image_ext;

                if ($image_ext == 'gif' || $image_ext == 'GIF') {
                    $saveGifImage = $this->saveGifImage($input['upload_url'], $save_name);
                    if ($saveGifImage == 'failed') {
                        $response['errors'] = ['Sorry! unable to post. Please try with different url or upload a file.'];
                        return response()->json($response, 422);
                    }
                } else {
                    // Save the image to storage..
                    $this->savePostImage($post->image_url, $isUrl = true, $save_name);
                }
                // Fill Post array
                $post->image = $save_name;
                $post->source = !empty($input['source']) ? formatSourceUrl($input['source']) : '';
            } // Save video from external resource..
            else if ($input['upload_url_type'] == 'video' && !empty($input['embed_code'])) {
                $post->embed_code = $input['parsed_embed_code'];
                $post->video = '';

                if (!empty($input['source_domain'])) {
                    $post->source = formatSourceUrl($input['source_domain']);
                } else {
                    $post->source = formatSourceUrl(get_domain($input['embed_code']));
                }
            }
        } // For uploaded files.
        else {
            // Save uploaded image..
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $original_name = $image->getClientOriginalName();
                $image_ext = $image->getClientOriginalExtension();
                $image_ext = cleanFileExtension($image_ext);
                $save_name = generateFileName($original_name) . '.' . $image_ext;
                // Save the image to storage..
                $this->savePostImage($image, $isUrl = false, $save_name);
                // Fill Post array
                $post->image = $save_name;
                $post->image_url = '';
                $post->source = '';
            } // Save uploaded video..
            elseif ($request->hasFile('video')) {
                $path = public_path() . '/uploads/video/';
                $video = $request->file('video');
                $original_name = $video->getClientOriginalName();
                $video_ext = $video->getClientOriginalExtension();
                $save_file_name = generateFileName($original_name);
                $save_name = $save_file_name . '.' . $video_ext;

                $video->move($path, $save_name);

                // Fill Post array
                $post->video = $save_name;
                $post->embed_code = '';
                $post->source = '';
                try {
                    // Save video thumbnail.
                    $ffmpeg = FFMpeg::create();
                    $video = $ffmpeg->open($path . $save_name);
                    $video_poster = $save_file_name . '.jpg';
                    // Get video duration.
                    $ffprobe = FFProbe::create();
                    $duration = $ffprobe
                        ->format($path . $save_name)// extracts file informations
                        ->get('duration');
                    $cutAt = $this->getVideoCutTime($duration);
                    // Extract frame.
                    $video->frame(TimeCode::fromSeconds($cutAt))
                        ->save($path . 'thumbnail/' . $video_poster);
                    
                    /* Upload file to aws s3 */
                    move_to_s3('/video/thumbnail/' . $video_poster, $path . 'thumbnail/' . $video_poster);
                    // Fill post array.
                    $post->video_poster = $video_poster;

                } catch (Exception $e) {
                    $post->video_poster = '';
                }
                /* Upload file to aws s3 */
                move_to_s3('/video/' . $save_name, $path . $save_name);
            }
        }
        // Fill source if already not filled.
        if (empty($post->source)) {
            if (!empty($input['link'])) {
                $post->source = get_domain(formatSourceUrl($input['link']));
            } elseif (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } elseif (!empty($input['source'])) {
                $post->source = get_domain(formatSourceUrl($input['source']));
            }
        }
        // Check for malformed url.
        if (filter_var($post->source, FILTER_VALIDATE_URL) === FALSE) {
            $post->source = '';
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/

        /*-----------Tag add change for question type (10-1-17) ---------*/

        /*---------------------comment this section------------------------ */

        // if (!empty($input['caption'])) {
        //     //$hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
        //     //preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        //     $caption_tags[]=$input['caption'];
        // }

        // $post_tags = [];
        
        // if (!empty($input['tag'])) {
        //     $post_tags = explode(',', strtolower($input['tag']));
        // }
        // // Merge two types of tags and remove duplicates..
        // if (!empty($caption_tags)) {
        //     $post_tags = array_unique(array_merge($post_tags, $caption_tags), SORT_REGULAR);
        // }
        // /*-------- Create tags -------*/
        // $tag_sync_id = $this->saveTags($post_tags);
        // $post->tags()->attach($tag_sync_id);
        
        /*--------------------comment this section----------------- */

            if (!empty($input['caption'])) {
                //$hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
                //preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
                $caption_tags=$input['caption'];
                $caption_tags = preg_replace('/\s+/', '-', $caption_tags);
                $caption_tags = preg_replace('/[^A-Za-z0-9\-]/', '', $caption_tags);
                $caption_tags = preg_replace('/-{2,}/', '-', $caption_tags);
                $caption_tags = rtrim($caption_tags, '-');
                $caption_tags = ltrim($caption_tags, '-');


                $oldTags = Tag::where('tag_name', strtolower($caption_tags))->first();

                if (!empty($oldTags)) 
                {
                    /******** Add this code convert the tag into question tag start (16-02-18) **********/
                    $oldTags->question_tag=$input['caption'];
                    $oldTags->question_tag_created_at=date("Y-m-d H:i:s", time());
                    $oldTags->save();
                    /******** Add this code convert the tag into question tag end (16-02-18) **********/

                    $tag_id[] = $oldTags->id;
                } 
                else 
                {
                    $question_tag_created_at=date("Y-m-d H:i:s", time());

                    $newTag = new Tag(['tag_name' => strtolower($caption_tags),'tag_text' => preg_replace('/[-]+/i',' ',$caption_tags),'question_tag'=>$input['caption'] ,'question_tag_created_at'=>$question_tag_created_at]);
                    $newTag->save();
                    $tag_id[] = $newTag->id;
                }
                    $post->tags()->attach($tag_id);
                

            }
    
            $post_tags = [];
            
            if (!empty($input['tag'])) {
                $post_tags = explode(',', $input['tag']);
            }
            // Merge two types of tags and remove duplicates..
            if (!empty($post_tags)) {
                $post_tags = array_unique(array_merge($post_tags), SORT_REGULAR);
                
            }
            /*-------- Create tags -------*/
            $tag_sync_id = $this->saveTags($post_tags);
            $tag_sync_id=array_diff($tag_sync_id,$tag_id);
            $post->tags()->attach($tag_sync_id);


        /*-----------Tag add change for question type ---------*/
        /*------ END Tags ------*/

        // Add Existing Collection //
        /*--- No need in status ---*/
        /*if(!empty ($input['collection']))
        {
            $post->collections()->sync($request->input('collection'));
        }*/
        $response['msg'] = 'Question successfully posted.';
        $response['input'] = $input;
        
       
        return response()->json($response, 201);
    }

    public function storeEditedImagePost(Request $request)
    {
        $response = [];
        $input = $request->all();

//         return response()->json($input, 400);

        $rules = [
            // 'caption' => 'required|max:255',
            'title' => 'required|max:255',
            /*'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:categories,id'*/
        ];
        // Condition for sub category..
        /*if ($input['sub_category_id']) {
            $rules['sub_category_id'] = 'required|exists:categories,id';
        }*/

        $messages = [
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'sub_category_id.required' => 'The subcategory field is required.',
            'sub_category_id.exists' => 'The selected subcategory is invalid.',
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $input['id'] = (int) $input['id'];

        $post = Post::find($input['id']);
        if ($post->created_by !== Auth::user()->id) {
            $response['fatal'] = [
                'code' => 'AccessDenied',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }
        if (!empty($post->orginal_post_id)) {
            $response['fatal'] = [
                'code' => 'BadRequest',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }

        $path = public_path() . '/uploads/post/';

        $post->caption = !empty($input['caption']) ? $input['caption'] : '';
        $post->title = $input['title'];

        $post->category_id = !empty($input['category_id']) ? $input['category_id'] : 0;
        $post->sub_category_id = !empty($input['sub_category_id']) && !empty($post->category_id) ? $input['sub_category_id'] : 0;

        $post->video = '';
        $post->embed_code = '';

        $post->short_description = !empty($input['short_description']) ? strip_tags($input['short_description']) : '';
        $post->content = !empty($input['content']) ? $input['content'] : '';
        // For Link type posts
        if (!empty($input['link'])) {
            $post->external_link = formatSourceUrl($input['link']);
        }
        else if (!empty($input['source'])) {
            $post->external_link = formatSourceUrl($input['source']);
        }

        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        if (!empty($input['allow_comment']) && $input['allow_comment'] == "true") {
            $post->allow_comment = 1;
        } else {
            $post->allow_comment = 0;
        }

        if (!empty($input['allow_share']) && $input['allow_share'] == "true") {
            $post->allow_share = 1;
        } else {
            $post->allow_share = 0;
        }

        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = !empty($input['post_type']) ? $input['post_type'] : 1;

        // Remove old file.
        if ($input['isFileEdited'] == 'true') {
            if (!empty($post->image)) {
                Storage::delete(['/post/' . $post->image, '/post/thumbs/' . $post->image]);
            }
            if (!empty($post->video)) {
                Storage::delete(['/video/' . $post->video, '/video/thumbnail/' . $post->video]);
            }
            $post->image_url = '';
            $post->image = '';
            $post->embed_code = '';
            $post->video = '';
            $post->source = '';
        }

        $isNewFile = false;
        // Save image from external resource..
        if (!empty($input['image_url']) && $post->image_url != $input['image_url']) {
            $isNewFile = true;
            $post->image_url = $input['image_url'];
            // Part image url..
            $image_url_parts = pathinfo($post->image_url);
            $lowered_extension = strtolower($image_url_parts['extension']);

            if (
                !empty($image_url_parts['extension']) &&
                in_array($lowered_extension, $this->known_image_extensions)
            ) {
                $image_ext = $lowered_extension;
            }
            else {
                $image_ext = getImageExtensionFromUrl($post->image_url);
            }
            $image_ext = cleanFileExtension($image_ext);

            $original_name = $image_url_parts['filename'];
            $save_name = generateFileName($original_name) . '.' . $image_ext;

            if ($image_ext == 'gif' || $image_ext == 'GIF') {
                $saveGifImage = $this->saveGifImage($input['image_url'], $save_name);
                if ($saveGifImage == 'failed') {
                    $response['errors'] = ['Sorry! unable to post. <br> Please try with different url or upload a file.'];
                    return response()->json($response, 422);
                }
                /*copy($input['image_url'], $path . $save_name);
                copy($input['image_url'], $path . 'thumbs/' . $save_name);*/
            } else {
                $saveImage = $this->savePostImage($post->image_url, $isUrl = true, $save_name);
                if ($saveImage == 'failed') {
                    try {
                        copy($input['image_url'], $path . $save_name);
                        copy($input['image_url'], $path . 'thumbs/' . $save_name);
                    } catch (Exception $e) {
                        $parsed_url = parse_url($input['image_url']);
                        if ($parsed_url['scheme'] == 'https') {
                            $save_name = $input['image_url'];
                        } else {
                            $response['errors'] = ['Sorry! unable to post. Please try with different url or upload a file.'];
                            return response()->json($response, 422);
                        }
                    }
                }
            }
            // Fill Post array
            $post->image = $save_name;
            $post->source = !empty($input['source']) ? formatSourceUrl($input['source']) : '';
        } // Save uploaded image..
        elseif ($request->file('image')) {
            $image = $request->file('image');
            $original_name = $image->getClientOriginalName();
            $image_ext = $image->getClientOriginalExtension();
            $image_ext = cleanFileExtension($image_ext);
            $save_name = generateFileName($original_name) . '.' . $image_ext;
            // Save the image to storage..
            $this->savePostImage($image, $isUrl = false, $save_name);
            // Fill Post array
            $post->image = $save_name;
            $post->image_url = '';
            $post->source = '';
        }
        // Fill source if already not filled.
        if (empty($post->source) || !$isNewFile) {
            if (!empty($input['link'])) {
                $post->source = get_domain(formatSourceUrl($input['link']));
            } elseif (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } elseif (!empty($input['source'])) {
                $post->source = get_domain(formatSourceUrl($input['source']));
            }
        }
        // Check for malformed url.
        if (filter_var($post->source, FILTER_VALIDATE_URL) === FALSE) {
            $post->source = '';
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        if (!empty($input['caption'])) {
            $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        }

        $post_tags = [];
        if (!empty($input['tag'])) {
            $post_tags = explode(',', $input['tag']);
        }
        // Merge two types f tags and remove duplicates..
        if (!empty($caption_tags[1])) {
            $post_tags = array_merge($post_tags, $caption_tags[1]);
        }
        $post_tags = array_unique($post_tags, SORT_REGULAR);
        /*-------- Create tags -------*/
        $tag_sync_id = $this->saveTags($post_tags);
        $post->tags()->sync($tag_sync_id);
        /*------ END Tags ------*/

        // Add Existing Collection //
        if (!empty ($input['collection'])) {
            $post->collections()->sync($request->input('collection'));
        }

        // Update all child posts.
        $this->updateChildPosts($post);

        $response['msg'] = 'Post successfully edited.';
        return response()->json($response, 201);
    }

    public function storeEditedVideoPost(Request $request)
    {
        $response = [];
        $input = $request->all();

//        return response()->json($input, 400);

        $rules = [
            // 'caption' => 'required|max:255',
            'title' => 'required|max:255',
            /*'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:categories,id',
            'location' => 'required',
            'lat' => 'requierd',
            'lon' => 'requierd',*/
        ];
        // Condition for sub category..
        /*if ($input['sub_category_id']) {
            $rules['sub_category_id'] = 'required|exists:categories,id';
        }*/

        if (!empty($input['video'])) {
            $rules['video'] = 'max:10240';
        }

        $messages = [
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'sub_category_id.required' => 'The subcategory field is required.',
            'sub_category_id.exists' => 'The selected subcategory is invalid.',
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $input['id'] = (int) $input['id'];

        $post = Post::find($input['id']);
        if ($post->created_by !== Auth::user()->id) {
            $response['fatal'] = [
                'code' => 'AccessDenied',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }
        if (!empty($post->orginal_post_id)) {
            $response['fatal'] = [
                'code' => 'BadRequest',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }

        $path = public_path() . '/uploads/video/';

        $post->caption = !empty($input['caption']) ? $input['caption'] : '';
        $post->title = $input['title'];

        $post->category_id = !empty($input['category_id']) ? $input['category_id'] : 0;
        $post->sub_category_id = !empty($input['sub_category_id']) && !empty($post->category_id) ? $input['sub_category_id'] : 0;

        $post->image = '';
        $post->image_url = '';

        $post->short_description = !empty($input['short_description']) ? strip_tags($input['short_description']) : '';
        $post->content = !empty($input['content']) ? $input['content'] : '';
        // For Link type posts
        if (!empty($input['link'])) {
            $post->external_link = formatSourceUrl($input['link']);
        }
        else if (!empty($input['source'])) {
            $post->external_link = formatSourceUrl($input['source']);
        }
        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        if (!empty($input['allow_comment']) && $input['allow_comment'] == "true") {
            $post->allow_comment = 1;
        } else {
            $post->allow_comment = 0;
        }

        if (!empty($input['allow_share']) && $input['allow_share'] == "true") {
            $post->allow_share = 1;
        } else {
            $post->allow_share = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = !empty($input['post_type']) ? $input['post_type'] : 2;
        
        // Remove old file.
        if ($input['isFileEdited'] == 'true') {
            if (!empty($post->image)) {
                Storage::delete(['/post/' . $post->image, '/post/thumbs/' . $post->image]);
            }
            if (!empty($post->video)) {
                Storage::delete(['/video/' . $post->video, '/video/thumbnail/' . $post->video]);
            }
            $post->image_url = '';
            $post->image = '';
            $post->embed_code = '';
            $post->video = '';
            $post->source = '';
        }

        // Save image from external resource..
        if (!empty($input['embed_code'])) {
            $post->embed_code = $input['parsed_embed_code'];
            $post->video = '';

            if (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } else {
                $post->source = formatSourceUrl(get_domain($input['embed_code']));
            }
        } // Save uploaded video..
        elseif ($request->file('video')) {
            $video = $request->file('video');
            $original_name = $video->getClientOriginalName();
            $video_ext = $video->getClientOriginalExtension();
            $save_file_name = generateFileName($original_name);
            $save_name = $save_file_name . '.' . $video_ext;

            $video->move($path, $save_name);

            // Fill Post array
            $post->video = $save_name;
            $post->embed_code = '';
            $post->source = '';
            try {
                // Save video thumbnail.
                $ffmpeg = FFMpeg::create();
                $video = $ffmpeg->open($path . $save_name);
                $video_poster = $save_file_name . '.jpg';
                // Get video duration.
                $ffprobe = FFProbe::create();
                $duration = $ffprobe
                    ->format($path . $save_name)// extracts file informations
                    ->get('duration');
                $cutAt = $this->getVideoCutTime($duration);
                // Extract frame.
                $video->frame(TimeCode::fromSeconds($cutAt))
                    ->save($path . 'thumbnail/' . $video_poster);

                /* Upload file to aws s3 */
                move_to_s3('/video/thumbnail/' . $video_poster, $path . 'thumbnail/' . $video_poster);
                // Fill post array.
                $post->video_poster = $video_poster;

            } catch (Exception $e) {
                $post->video_poster = '';
            }
            /* Upload file to aws s3 */
            move_to_s3('/video/' . $save_name, $path . $save_name);
        }
        // Fill source if already not filled.
        if (empty($post->source)) {
            if (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } elseif (!empty($input['source'])) {
                $post->source = formatSourceUrl($input['source']);
            }
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        if (!empty($input['caption'])) {
            $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        }

        $post_tags = [];
        if (!empty($input['tag'])) {
            $post_tags = explode(',', $input['tag']);
        }
        // Merge two types f tags and remove duplicates..
        if (!empty($caption_tags[1])) {
            $post_tags = array_merge($post_tags, $caption_tags[1]);
        }
        $post_tags = array_unique($post_tags, SORT_REGULAR);
        /*-------- Create tags -------*/
        $tag_sync_id = $this->saveTags($post_tags);
        $post->tags()->sync($tag_sync_id);
        /*------ END Tags ------*/

        // Add Existing Collection //
        if (!empty ($input['collection'])) {
            $post->collections()->sync($request->input('collection'));
        }

        // Post published directly  to the twitter
        /*if (isset($input['twitter_connect']) && $input['twitter_connect'] == true) {
            $this->publishedPostToTwitter($lastInsertID);
        }*/

        // Update all child posts.
        $this->updateChildPosts($post);

        $response['msg'] = 'Video successfully edited.';
        return response()->json($response, 201);
    }

    public function storeEditedArticlePost(Request $request)
    {
        $response = [];
        $input = $request->all();
        // return response()->json($input, 422);

        $rules = [
            // 'caption' => 'required|max:255',
            'title' => 'required|max:255',
            /*'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:categories,id',*/
            'content' => 'required',
            /*'location' => 'required',
            'lat' => 'requierd',
            'lon' => 'requierd',*/
        ];
        // Condition for sub category..
        /*if ($input['sub_category_id']) {
            $rules['sub_category_id'] = 'required|exists:categories,id';
        }*/

        $messages = [
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'sub_category_id.required' => 'The subcategory field is required.',
            'sub_category_id.exists' => 'The selected subcategory is invalid.',
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $input['id'] = (int) $input['id'];

        $post = Post::find($input['id']);
        if ($post->created_by !== Auth::user()->id) {
            $response['fatal'] = [
                'code' => 'AccessDenied',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }
        if (!empty($post->orginal_post_id)) {
            $response['fatal'] = [
                'code' => 'BadRequest',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }

        $path = public_path() . '/uploads/post/';

        $post->caption = !empty($input['caption']) ? $input['caption'] : '';
        $post->title = $input['title'];

        $post->category_id = !empty($input['category_id']) ? $input['category_id'] : 0;
        $post->sub_category_id = !empty($input['sub_category_id']) && !empty($post->category_id) ? $input['sub_category_id'] : 0;

        /*$post->video = '';
        $post->embed_code = '';*/

        $post->short_description = !empty($input['short_description']) ? strip_tags($input['short_description']) : '';

        if (!empty($input['content'])) {
            $content = $input['content'];
            // Remove new lines..
            $content = trim(preg_replace('/\s+/', ' ', $content));

            // Move photos to s3.
            $photo_ids = [];
            $pattern = '/< *img[^>]*src *= *["\']?([^"\']*)" data-photo-id="(\w+)"/';
            $content = preg_replace_callback($pattern, function($matches) use($path, &$photo_ids) {

                $save_name = basename($matches[1]);
                move_to_s3('/post/article/' . $save_name, $path . $save_name);
                $new_src = Storage::url('post/article/' . $save_name);

                array_push($photo_ids, $matches[2]);
                // Remove data-photo-id
                $matches[0] = str_replace($matches[2], '', $matches[0]);
                return str_replace($matches[1], $new_src, $matches[0]);
            }, $content);

//            return response($content, 422);

            // Delete DB rows from photos.
            if (!empty($photo_ids)) {
                Photo::whereIn('id', $photo_ids)->delete();
            }

            // Remove class and id from html..
//            $content = preg_replace('/data-photo-id=".*?"|id=".*?"|class=".*?"/', '', $content);
            $content = preg_replace(
                [
                    '/data-photo-id=".*?"/i',
//                    '/class=".*?"/i',
                    '/class="(?=(.(?!fr-))*?>).*?"/i',
                    '/id=".*?"/i',
                    '/srcset=".*?"/i',
                    '/height=".*?"/i',
                    '/width=".*?"/i',
                    '/sizes=".*?"/i',
                    '/onclick=".*?"/i'
                ],
                [
                    '', '', '', '', '', ''
                ], $content);

            $post->content = $content;
        } else {
            $post->content = '';
        }

        // Store the source
        /*if (!empty($input['source_domain'])) {
            $post->source = formatSourceUrl($input['source_domain']);
        }
        else */
        if (!empty($input['link'])) {
            $input['link'] = formatSourceUrl($input['link']);
            $post->source = formatSourceUrl(get_domain($input['link']));
        }
        if (!empty($input['source'])) {
            $post->source = formatSourceUrl($input['source']);
        }
        else {
            $post->source = '';
        }

        // For Link type posts
        if (!empty($input['link'])) {
            $post->external_link = formatSourceUrl($input['link']);
        }
        else if (!empty($input['source'])) {
            $post->external_link = formatSourceUrl($input['source']);
        }

        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        if (!empty($input['allow_comment']) && $input['allow_comment'] == "true") {
            $post->allow_comment = 1;
        } else {
            $post->allow_comment = 0;
        }

        if (!empty($input['allow_share']) && $input['allow_share'] == "true") {
            $post->allow_share = 1;
        } else {
            $post->allow_share = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = 3;

        // Remove old file.
        if ($input['isFileEdited'] == 'true') {
            if (!empty($post->image)) {
                Storage::delete(['/post/' . $post->image, '/post/thumbs/' . $post->image]);
            }
            if (!empty($post->video)) {
                Storage::delete(['/video/' . $post->video, '/video/thumbnail/' . $post->video]);
            }
            $post->image_url = '';
            $post->image = '';
            $post->embed_code = '';
            $post->video = '';
            $post->source = '';
        }
        // Save image from external resource..
        if (!empty($input['image_url'])) {
            $post->image_url = $input['image_url'];
            // Part image url..
            $image_url_parts = pathinfo($post->image_url);
            $lowered_extension = strtolower($image_url_parts['extension']);

            if (
                !empty($image_url_parts['extension']) &&
                in_array($lowered_extension, $this->known_image_extensions)
            ) {
                $image_ext = $lowered_extension;
            }
            else {
                $image_ext = getImageExtensionFromUrl($post->image_url);
            }
            $image_ext = cleanFileExtension($image_ext);

            $original_name = $image_url_parts['filename'];
            $save_name = generateFileName($original_name) . '.' . $image_ext;

            if ($image_ext == 'gif' || $image_ext == 'GIF') {
                $saveGifImage = $this->saveGifImage($input['image_url'], $save_name);
                if ($saveGifImage == 'failed') {
                    $response['errors'] = ['Sorry! unable to post. Please try with different url or upload a file.'];
                    return response()->json($response, 422);
                }
            } else {
                // Save the image to storage..
                $saveImage = $this->savePostImage($post->image_url, $isUrl = true, $save_name);
                if ($saveImage == 'failed') {
                    try {
                        copy($input['image_url'], $path . $save_name);
                        copy($input['image_url'], $path . 'thumbs/' . $save_name);
                        /* Upload file to aws s3 */
                        move_to_s3('/post/' . $save_name, $path . $save_name);
                        move_to_s3('/post/thumbs/' . $save_name, $path . 'thumbs/' . $save_name);
                    } catch (Exception $e) {
                        $save_name = $input['image_url'];
                    }
                }
            }
            // Fill Post array
            $post->image = $save_name;
        } // Save uploaded image..
        elseif ($request->file('image')) {
            $image = $request->file('image');
            $original_name = $image->getClientOriginalName();
            $image_ext = $image->getClientOriginalExtension();
            $image_ext = cleanFileExtension($image_ext);
            $save_name = generateFileName($original_name) . '.' . $image_ext;

            // Save the image to storage..
            $this->savePostImage($image, $isUrl = false, $save_name);
            // Fill Post array
            $post->image = $save_name;
            $post->image_url = '';
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        if (!empty($input['caption'])) {
            $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        }

        $post_tags = [];
        if (!empty($input['tag'])) {
            $post_tags = explode(',', $input['tag']);
        }
        // Merge two types of tags and remove duplicates..
        if (!empty($caption_tags[1])) {
            $post_tags = array_merge($post_tags, $caption_tags[1]);
        }
        $post_tags = array_unique($post_tags, SORT_REGULAR);
        /*-------- Create tags -------*/
        $tag_sync_id = $this->saveTags($post_tags);
        $post->tags()->sync($tag_sync_id);
        /*------ END Tags ------*/

        // Add Existing Collection //
        if (!empty ($input['collection'])) {
            $post->collections()->sync($request->input('collection'));
        }

        // Update all child posts.
        $this->updateChildPosts($post);

        $response['msg'] = 'Article successfully edited.';
        return response()->json($response, 201);
    }

    public function storeEditedStatusPost(Request $request)
    {
        $response = [];
        $input = $request->all();

        // return response()->json($input, 422);

        $rules = [
            // 'caption' => 'required|max:255',
            'privacy_id' => 'required|exists:privacies,id'
        ];

        $messages = [
            'privacy_id.exists' => 'The selected privacy is invalid.'
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $input['id'] = (int) $input['id'];

        $post = Post::find($input['id']);
        if ($post->created_by !== Auth::user()->id) {
            $response['fatal'] = [
                'code' => 'AccessDenied',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }
        if (!empty($post->orginal_post_id)) {
            $response['fatal'] = [
                'code' => 'BadRequest',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }

        $path = public_path() . '/uploads/post/';

        $post->caption = $input['caption'];
        // $post->title = '';
        $post->category_id = 0;
        $post->sub_category_id = 0;
        $post->allow_comment = 1;
        $post->allow_share = 0;

        $post->short_description = '';
        $post->content = '';

        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

        if (!empty($input['allow_comment']) && $input['allow_comment'] == "true") {
            $post->allow_comment = 1;
        } else {
            $post->allow_comment = 0;
        }

        if (!empty($input['allow_share']) && $input['allow_share'] == "true") {
            $post->allow_share = 1;
        } else {
            $post->allow_share = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = 5;

        // Remove old file.
        if ($input['isFileEdited'] == 'true') {
            if (!empty($post->image)) {
                Storage::delete(['/post/' . $post->image, '/post/thumbs/' . $post->image]);
            }
            if (!empty($post->video)) {
                Storage::delete(['/video/' . $post->video, '/video/thumbnail/' . $post->video]);
            }
            $post->image_url = '';
            $post->image = '';
            $post->embed_code = '';
            $post->video = '';
            $post->source = '';
        }      

        /*-------- File operations --------*/
        // For upload url
        if ($input['file_type'] == 'URL') {
            // Save image from external resource..
            if ($input['upload_url_type'] == 'image' && !empty($input['upload_url'])) {
                $post->image_url = $input['upload_url'];
                // Partition image url..
                $image_url_parts = pathinfo($post->image_url);
                $lowered_extension = strtolower($image_url_parts['extension']);

                if (
                    !empty($image_url_parts['extension']) &&
                    in_array($lowered_extension, $this->known_image_extensions)
                ) {
                    $image_ext = $lowered_extension;
                }
                else {
                    $image_ext = getImageExtensionFromUrl($post->image_url);
                }

                $image_ext = cleanFileExtension($image_ext);
                $original_name = $image_url_parts['filename'];
                $save_name = generateFileName($original_name) . '.' . $image_ext;

                if ($image_ext == 'gif' || $image_ext == 'GIF') {
                    $saveGifImage = $this->saveGifImage($input['upload_url'], $save_name);
                    if ($saveGifImage == 'failed') {
                        $response['errors'] = ['Sorry! unable to post. Please try with different url or upload a file.'];
                        return response()->json($response, 422);
                    }
                } else {
                    // Save the image to storage..
                    $this->savePostImage($post->image_url, $isUrl = true, $save_name);
                }
                // Fill Post array
                $post->image = $save_name;
                $post->source = !empty($input['source']) ? formatSourceUrl($input['source']) : '';
            } // Save video from external resource..
            else if ($input['upload_url_type'] == 'video' && !empty($input['embed_code'])) {
                $post->embed_code = $input['parsed_embed_code'];
                $post->video = '';

                if (!empty($input['source_domain'])) {
                    $post->source = formatSourceUrl($input['source_domain']);
                } else {
                    $post->source = formatSourceUrl(get_domain($input['embed_code']));
                }
            }
        } // For uploaded files.
        else {
            // Save uploaded image..
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $original_name = $image->getClientOriginalName();
                $image_ext = $image->getClientOriginalExtension();
                $image_ext = cleanFileExtension($image_ext);
                $save_name = generateFileName($original_name) . '.' . $image_ext;
                // Save the image to storage..
                $this->savePostImage($image, $isUrl = false, $save_name);
                // Fill Post array
                $post->image = $save_name;
                $post->image_url = '';
                $post->source = '';
            } // Save uploaded video..
            elseif ($request->hasFile('video')) {
                $path = public_path() . '/uploads/video/';
                $video = $request->file('video');
                $original_name = $video->getClientOriginalName();
                $video_ext = $video->getClientOriginalExtension();
                $save_file_name = generateFileName($original_name);
                $save_name = $save_file_name . '.' . $video_ext;

                $video->move($path, $save_name);

                // Fill Post array
                $post->video = $save_name;
                $post->embed_code = '';
                $post->source = '';
                try {
                    // Save video thumbnail.
                    $ffmpeg = FFMpeg::create();
                    $video = $ffmpeg->open($path . $save_name);
                    $video_poster = $save_file_name . '.jpg';
                    // Get video duration.
                    $ffprobe = FFProbe::create();
                    $duration = $ffprobe
                        ->format($path . $save_name)// extracts file informations
                        ->get('duration');
                    $cutAt = $this->getVideoCutTime($duration);
                    // Extract frame.
                    $video->frame(TimeCode::fromSeconds($cutAt))
                        ->save($path . 'thumbnail/' . $video_poster);

                    /* Upload file to aws s3 */
                    move_to_s3('/video/thumbnail/' . $video_poster, $path . 'thumbnail/' . $video_poster);
                    // Fill post array.
                    $post->video_poster = $video_poster;

                } catch (Exception $e) {
                    $post->video_poster = '';
                }
                /* Upload file to aws s3 */
                move_to_s3('/video/' . $save_name, $path . $save_name);
            }
        }
        // Fill source if already not filled.
        if (empty($post->source)) {
            if (!empty($input['link'])) {
                $post->source = get_domain(formatSourceUrl($input['link']));
            } elseif (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } elseif (!empty($input['source'])) {
                $post->source = get_domain(formatSourceUrl($input['source']));
            }
        }
        // Check for malformed url.
        if (filter_var($post->source, FILTER_VALIDATE_URL) === FALSE) {
            $post->source = '';
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        if (!empty($input['caption'])) {
            $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
        }

        $post_tags = [];
        /*--- Uncomment this section for allow tag in status edit(1-12-17) ---*/
        if (!empty($input['tag'])) {
            //$post_tags = explode(',', strtolower($input['tag']));  

            $post_tags = explode(',',$input['tag']); 

        }
        // Merge two types of tags and remove duplicates..
        if (!empty($caption_tags)) {
            $post_tags = array_unique(array_merge($post_tags, $caption_tags[1]), SORT_REGULAR);
        }
        /*-------- Create tags -------*/
        $tag_sync_id = $this->saveTags($post_tags);
        $post->tags()->sync($tag_sync_id);
        /*------ END Tags ------*/

        // Update all child posts.
        $this->updateChildPosts($post);

        $response['msg'] = 'Status successfully posted.';
       
        return response()->json($response, 201);
    }
   
    public function storeEditedQuestionPost(Request $request)
    {
        $response = [];
        $input = $request->all();
      
        // return response()->json($input, 422);

        $rules = [
            // 'caption' => 'required|max:255',
            'privacy_id' => 'required|exists:privacies,id'
        ];

        $messages = [
            'privacy_id.exists' => 'The selected privacy is invalid.'
        ];

        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }

        $input['id'] = (int) $input['id'];

        $post = Post::find($input['id']);
        if ($post->created_by !== Auth::user()->id) {
            $response['fatal'] = [
                'code' => 'AccessDenied',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }
        if (!empty($post->orginal_post_id)) {
            $response['fatal'] = [
                'code' => 'BadRequest',
                'message' => 'Sorry! unable to process your request.'
            ];
            return response()->json($response, 400);
        }

        $path = public_path() . '/uploads/post/';

        $post->caption = $input['caption'];
        // $post->title = '';
        $post->category_id =!empty($input['category_id']) ? $input['category_id'] : 0;
        $post->sub_category_id = !empty($input['sub_category_id']) && !empty($post->category_id) ? $input['sub_category_id'] : 0;
        $post->allow_comment = 1;
        $post->allow_share = 1;

        $post->short_description = !empty($input['short_description']) ? strip_tags($input['short_description']) : '';
        $post->content = '';

        $post->post_date = time();

        $post->location = '';
        $post->city = '';
        $post->state = '';
        $post->country_code = '';
        $post->lat = '';
        $post->lon = '';
        // Populate location data.
        if (!empty($input['location'])) {
            $post->location = $input['location'];
            $post->city = !empty($input['city']) ? $input['city'] : '';
            $post->state = !empty($input['state']) ? $input['state'] : '';
            $post->country_code = !empty($input['country_code']) ? $input['country_code'] : '';
            if (!empty($input['lat']) && !empty($input['lon'])) {
                $post->lat = $input['lat'];
                $post->lon = $input['lon'];
            }
        }

       

        if (!empty($input['ask_anonymous']) && $input['ask_anonymous'] == "true") {
            $post->ask_anonymous = 1;
        } else {
            $post->ask_anonymous = 0;
        }
        $post->privacy_id = !empty($input['privacy_id']) ? $input['privacy_id'] : 1;

        $post->post_type = 6;

        // Remove old file.
        if ($input['isFileEdited'] == 'true') {
            if (!empty($post->image)) {
                Storage::delete(['/post/' . $post->image, '/post/thumbs/' . $post->image]);
            }
            if (!empty($post->video)) {
                Storage::delete(['/video/' . $post->video, '/video/thumbnail/' . $post->video]);
            }
            $post->image_url = '';
            $post->image = '';
            $post->embed_code = '';
            $post->video = '';
            $post->source = '';
        }      

        /*-------- File operations --------*/
        // For upload url
        if ($input['file_type'] == 'URL') {
            // Save image from external resource..
            if ($input['upload_url_type'] == 'image' && !empty($input['upload_url'])) {
                $post->image_url = $input['upload_url'];
                // Partition image url..
                $image_url_parts = pathinfo($post->image_url);
                $lowered_extension = strtolower($image_url_parts['extension']);

                if (
                    !empty($image_url_parts['extension']) &&
                    in_array($lowered_extension, $this->known_image_extensions)
                ) {
                    $image_ext = $lowered_extension;
                }
                else {
                    $image_ext = getImageExtensionFromUrl($post->image_url);
                }

                $image_ext = cleanFileExtension($image_ext);
                $original_name = $image_url_parts['filename'];
                $save_name = generateFileName($original_name) . '.' . $image_ext;

                if ($image_ext == 'gif' || $image_ext == 'GIF') {
                    $saveGifImage = $this->saveGifImage($input['upload_url'], $save_name);
                    if ($saveGifImage == 'failed') {
                        $response['errors'] = ['Sorry! unable to post. Please try with different url or upload a file.'];
                        return response()->json($response, 422);
                    }
                } else {
                    // Save the image to storage..
                    $this->savePostImage($post->image_url, $isUrl = true, $save_name);
                }
                // Fill Post array
                $post->image = $save_name;
                $post->source = !empty($input['source']) ? formatSourceUrl($input['source']) : '';
            } // Save video from external resource..
            else if ($input['upload_url_type'] == 'video' && !empty($input['embed_code'])) {
                $post->embed_code = $input['parsed_embed_code'];
                $post->video = '';

                if (!empty($input['source_domain'])) {
                    $post->source = formatSourceUrl($input['source_domain']);
                } else {
                    $post->source = formatSourceUrl(get_domain($input['embed_code']));
                }
            }
        } // For uploaded files.
        else {
            // Save uploaded image..
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $original_name = $image->getClientOriginalName();
                $image_ext = $image->getClientOriginalExtension();
                $image_ext = cleanFileExtension($image_ext);
                $save_name = generateFileName($original_name) . '.' . $image_ext;
                // Save the image to storage..
                $this->savePostImage($image, $isUrl = false, $save_name);
                // Fill Post array
                $post->image = $save_name;
                $post->image_url = '';
                $post->source = '';
            } // Save uploaded video..
            elseif ($request->hasFile('video')) {
                $path = public_path() . '/uploads/video/';
                $video = $request->file('video');
                $original_name = $video->getClientOriginalName();
                $video_ext = $video->getClientOriginalExtension();
                $save_file_name = generateFileName($original_name);
                $save_name = $save_file_name . '.' . $video_ext;

                $video->move($path, $save_name);

                // Fill Post array
                $post->video = $save_name;
                $post->embed_code = '';
                $post->source = '';
                try {
                    // Save video thumbnail.
                    $ffmpeg = FFMpeg::create();
                    $video = $ffmpeg->open($path . $save_name);
                    $video_poster = $save_file_name . '.jpg';
                    // Get video duration.
                    $ffprobe = FFProbe::create();
                    $duration = $ffprobe
                        ->format($path . $save_name)// extracts file informations
                        ->get('duration');
                    $cutAt = $this->getVideoCutTime($duration);
                    // Extract frame.
                    $video->frame(TimeCode::fromSeconds($cutAt))
                        ->save($path . 'thumbnail/' . $video_poster);

                    /* Upload file to aws s3 */
                    move_to_s3('/video/thumbnail/' . $video_poster, $path . 'thumbnail/' . $video_poster);
                    // Fill post array.
                    $post->video_poster = $video_poster;

                } catch (Exception $e) {
                    $post->video_poster = '';
                }
                /* Upload file to aws s3 */
                move_to_s3('/video/' . $save_name, $path . $save_name);
            }
        }
        // Fill source if already not filled.
        if (empty($post->source)) {
            if (!empty($input['link'])) {
                $post->source = get_domain(formatSourceUrl($input['link']));
            } elseif (!empty($input['source_domain'])) {
                $post->source = formatSourceUrl($input['source_domain']);
            } elseif (!empty($input['source'])) {
                $post->source = get_domain(formatSourceUrl($input['source']));
            }
        }
        // Check for malformed url.
        if (filter_var($post->source, FILTER_VALIDATE_URL) === FALSE) {
            $post->source = '';
        }
        // Assign place url to the post.
        $post->place_url = location_url($post, false);
        // Save to places.
        $this->storePlace($post->place_url);

        $post->save();

        // Store feature photo details.
        if  (!empty($post->image)) {
            $this->storeFeaturePhotoDetail($post);
        }

        /*------ Tags ------*/
        /*-----------Tag edit change for question type (11-1-17) ---------*/

        /*---------------------comment this section------------------------ */
            // if (!empty($input['caption'])) {
            //     //$hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
            //    // preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);
            //    $caption_tags[]=$input['caption'];
            // }

            // $post_tags = [];
            // /*--- Uncomment this section for allow tag in status edit(1-12-17) ---*/
            // if (!empty($input['tag'])) {
            //     $post_tags = explode(',', strtolower($input['tag']));  
            // }
            // // Merge two types of tags and remove duplicates..
            // if (!empty($caption_tags)) {
            //     $post_tags = array_unique(array_merge($post_tags, $caption_tags), SORT_REGULAR);
            // }
            // /*-------- Create tags -------*/
            // $tag_sync_id = $this->saveTags($post_tags);
            // $post->tags()->sync($tag_sync_id);
        /*--------------------comment this section----------------- */
                $old_tag_column = ['tags.id', 'tag_name','question_tag','question_tag_created_at'];
                $post_old_tag = Post::where('id', $input['id'])
                        ->with(
                            [
                                'tags' => function ($query) use ($old_tag_column) {
                                    $query->addSelect($old_tag_column)->where('question_tag','<>','');
                                } ]
                                )
                                ->first();

                $post_old_question_tag=$post_old_tag->tags[0];

            if (!empty($input['caption'])) {
                //$hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
               // preg_match_all($hash_tag_pattern, strtolower($input['caption']), $caption_tags);

                


               $caption_tags=$input['caption'];
               $caption_tags = preg_replace('/\s+/', '-', $caption_tags);
               $caption_tags = preg_replace('/[^A-Za-z0-9\-]/', '', $caption_tags);
               $caption_tags = preg_replace('/-{2,}/', '-', $caption_tags);
               $caption_tags = rtrim($caption_tags, '-');
               $caption_tags = ltrim($caption_tags, '-');


               Tag::where('id', $post_old_question_tag->id)->update(['tag_name' => strtolower($caption_tags),'question_tag'=>$input['caption'] ]);
               $tag_id[] = $post_old_question_tag->id;
             
            }

            $post_tags = [];
            /*--- Uncomment this section for allow tag in status edit(1-12-17) ---*/
            if (!empty($input['tag'])) {
                $post_tags = explode(',', $input['tag']);  
            }
            // Merge two types of tags and remove duplicates..
            if (!empty($post_tags)) {
                $old_tag_converted_name=preg_replace('/-/', ' ', $post_old_question_tag->tag_name);
                $post_tags = array_unique(array_merge($post_tags), SORT_REGULAR);
                $post_tags = array_diff($post_tags,[$old_tag_converted_name]);
            }
            /*-------- Create tags -------*/
            $tag_sync_id = $this->saveTags($post_tags);
            $tag_sync_id=array_merge($tag_sync_id,$tag_id);
            $post->tags()->sync($tag_sync_id);

        /*-----------Tag add change for question type ---------*/
        /*------ END Tags ------*/

        // Update all child posts.
        $this->updateChildPosts($post);

        $response['msg'] = 'Question successfully posted.';
        $response['post_old_question_tag']=$post_old_question_tag;
        $response['post_tags']=$post_tags;
         return response()->json($response, 201);
    }

    /**
     * Save collection..
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCollectionFromPost(Request $request)
    {
        $response = [];
        $input = $request->all();

        if (!empty($input['collection_name'])) {
            $input['collection_name'] = strtolower($input['collection_name']);
        }
        $rules = ['collection_name' => 'required|max:255|unique:collections'];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $response['errors'] = $validator->getMessageBag()->toArray();
            return response()->json($response, 422);
        }
        // Save collection to database..
        $input['user_id'] = Auth::user()->id;
        $input['status'] = 'Y';
        $collection = new Collection($input);
        $collection->save($input);
        $response['collection'] = [
            'id' => $collection->id,
            'collection_name' => $collection->collection_name
        ];
        $response['msg'] = 'Collection successfully created.';
        return response()->json($response, 201);
    }

    /**
     * Update all child posts' data.
     *
     * @param \Illuminate\Database\Eloquent\Collection $post
     */
    protected function updateChildPosts($post) {
        // Update all child posts.
        $childPosts = Post::where('orginal_post_id', $post->id)->get();
        foreach ($childPosts as $childPost) {
            $childPostOldData = $childPost->toArray();
            // Fill with new original post's data.
            $childPost->fill($post->toArray());
            // Revert back own data for child post.
            $childPost->id = $childPostOldData['id'];
            $childPost->created_by = $childPostOldData['created_by'];
            $childPost->parent_post_user_id = $childPostOldData['parent_post_user_id'];
            $childPost->caption = $childPostOldData['caption'];
            $childPost->post_date = $childPostOldData['post_date'];
            $childPost->privacy_id = $childPostOldData['privacy_id'];
            $childPost->orginal_post_id = $childPostOldData['orginal_post_id'];

            // Reset points, upvotes, downvotes.
            $childPost->points = 0;
            $childPost->upvotes = 0;
            $childPost->downvotes = 0;

            $childPost->save();
            // Synchronize the tags to new shared post.
            $tag_attach_ids = [];
            foreach ($post->tags as $tag) {
                $tag_attach_ids[] = $tag->id;
            }
            $childPost->tags()->sync($tag_attach_ids);

        }
    }

    /**
     * Save photo to storage..
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(Request $request)
    {
        $response = [];
        $rules = [
            'image' => 'mimes:jpg,jpeg,png,gif|max:10000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->get('image') as $message) {
                $message .= $message . '<br>';
            }
            $message = rtrim($message, '<br>');
            $response['error'] = $message;
            return response()->json($response, 422);
        } else {
            $image = $request->file('image');
            $original_name = $image->getClientOriginalName();
            $image_ext = $image->getClientOriginalExtension();
            $image_ext = cleanFileExtension($image_ext);
            $save_name = generateFileName($original_name) . '.' . $image_ext;

            // Save the image to storage..
            $this->savePostImage($image, $isUrl = false, $save_name);
            $photo_data = [
                'original_name' => $original_name,
                'save_name' => $save_name,
                'schedule_remove' => 'Y'
            ];
            // Save the photo in DB.
            $photo = new Photo($photo_data);
            $photo->save();
            // Prepare and send response.
            $link = generate_post_image_url('post/' . $save_name);
            $response = [
                'photo-id' => $photo->id,
                'link' => $link
            ];
            return response()->json($response, 201);
        }
    }

    /**
     * Save video to storage..
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadVideo(Request $request)
    {
        $response = [];
        $rules = [
            'video' => 'required|max:20000',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = '';
            foreach ($errors->get('video') as $message) {
                $message .= $message . '<br>';
            }
            $message = rtrim($message, '<br>');
            $response['error'] = $message;
            return response()->json($response, 422);
        } else {
            $path = public_path() . '/uploads/video/';

            $video = $request->file('video');
            $original_name = $video->getClientOriginalName();
            $video_ext = $video->getClientOriginalExtension();
            $save_file_name = generateFileName($original_name);
            $save_name = $save_file_name . '.' . $video_ext;

            $video->move($path, $save_name);

            $video_data = [
                'original_name' => $original_name,
                'save_name' => $save_name,
                'schedule_remove' => 'Y'
            ];
            // Save the photo in DB.
            $video = new Video($video_data);
            $video->save();
            // Prepare and send response.

            /* Upload file to aws s3 */
            move_to_s3('/video/' . $save_name, $path . $save_name);
            $link = generate_post_video_url('video/' . $video->save_name);
            $response = [
                'video-id' => $video->id,
                'link' => $link
            ];

            return response()->json($response, 201);
        }
    }

    /**
     * Function to save tags.
     */
    function saveTags($post_tags)
    {
        $already_added = [];

        $tag_sync_id = [];
        if (!empty($post_tags)) {
            foreach ($post_tags as $key => $tag) {
                // Remove excess dash.
                $tagText=$tag;
                $tag= preg_replace('!\s+!', '-', $tag);//modify tag space to "-"
                $tag=preg_replace('/[^A-Za-z0-9\-]/', '', $tag); 
                $tag = preg_replace('/-{2,}/', '-', $tag);
                $tag = rtrim($tag, '-');
                $tag = ltrim($tag, '-');

                if (in_array($tag, $already_added)) {
                    continue;
                }

                $oldTags = Tag::where('tag_name', strtolower($tag))->first();
                if (!empty($oldTags)) {
                    $tag_id = $oldTags->id;
                    $tag_sync_id[] = $tag_id;
                } else {
                    $newTag = new Tag(['tag_name' => strtolower($tag),'tag_text'=>$tagText]);
                    $newTag->save();
                    $tag_sync_id[] = $newTag->id;
                }

                // Add to already added list.
                $already_added[] = $tag;
            }
        }
        return $tag_sync_id;
    }

    /**
     * Function to save image on server..
     */
    protected function savePostImage($image, $isUrl = false, $save_name)
    {
        $path = public_path() . '/uploads/post/';
        if ($isUrl) {
            $link = formatSourceUrl($image);
            $domain = formatSourceUrl(get_domain($link));

            $width = $this->image_width;
            // $height = $this->image_height;
            $height = null;

            // guzzle configuration..
            $options = [
                'language' => 'en',
                'image_min_bytes' => 4500,
                'image_max_bytes' => 5242880,
                'image_min_width' => 120,
                'image_min_height' => 120,
                'image_fetch_best' => true,
                'image_fetch_all' => false,
                /** @see http://guzzle.readthedocs.org/en/latest/clients.html#request-options */
                'browser' => [
                    'timeout' => 60,
                    'connect_timeout' => 30,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36',
                        // 'Referer' => 'https://www.google.com/',
                        'Referer' => $domain,
                    ],
                ]
            ];

            // Get response using guzzle..
            $guzzle = new GuzzleClient();
            try {
                $guzzle_response = $guzzle->get($link, $options);
                $guzzle_image = $guzzle_response->getBody()->getContents();

            }
            catch (Exception $e) {
                copy($image, $path . $save_name);
                copy($image, $path . 'thumbs/' . $save_name);

                move_to_s3('/post/' . $save_name, $path . $save_name);
                move_to_s3('/post/thumbs/' . $save_name, $path . 'thumbs/' . $save_name);
                //return 'failed';
            }

           
            // return $guzzle_response;
            /* ----------------------------------------------------------------------- */
            try {
                $image_make = Image::make($guzzle_image);
            } 
            catch (\Intervention\Image\Exception\NotReadableException $e) {

                return 'failed';
            } 
            catch (Exception $e) {
                return 'failed';
            }

            $image_make->resize($width, $height, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });

            /*-- insert resized image centered into background --*/
            // $background->insert($image_make, 'center');
            /*
             * Save to storage
             */
            // $background->save($path . $save_name);
            $image_make->save($path . $save_name);
            /*--- Save thumbnail image ---*/
            $thumb_width = 480;
            // $thumb_height = 240;
            $thumb_height = null;
            // $background = Image::canvas($thumb_width, $thumb_height);
            $image_make->resize($thumb_width, $thumb_height, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });
            // $background->insert($image_make, 'center');
            /* Save thumbnail image to storage */
            // $background->save($path . 'thumbs/' . $save_name, 100);
            /* get file size */
            $quality = 100;
            $size = $image_make->filesize();
            if ($size && $size > 500000) {
                $quality = 60;
            }
            $image_make->save($path . 'thumbs/' . $save_name, $quality);
        } else {

            $image_ext = $image->getClientOriginalExtension();
            if ($image_ext == 'gif' || $image_ext == 'GIF') {
                $image->move($path, $save_name);
                copy($path . $save_name, $path . 'thumbs/' . $save_name);
            } else {
                $width = $this->image_width;
                // $height = $this->image_height;
                $height = null;
                /*--create new image with transparent background color --*/
                // $background = Image::canvas($width, $height);
                /*but keep aspect-ratio and do not size up,
                so smaller sizes don't stretch*/
                $image_make = Image::make($image->getRealPath())->orientate();
                $image_make->resize($width, $height, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                /*-- insert resized image centered into background --*/
                // $background->insert($image_make, 'center');
                // Save to storage
                // $background->save($path . $save_name);
                $image_make->save($path . $save_name);
                /*--- Save thumbnail image ---*/
                $thumb_width = 480;
                // $thumb_height = 240;
                $thumb_height = null;
                // $background = Image::canvas($thumb_width, $thumb_height);
                $image_make->resize($thumb_width, $thumb_height, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
                // $background->insert($image_make, 'center');
                // Save thumbnail image to storage
                // $background->save($path . 'thumbs/' . $save_name, 100);
                /* get file size */
                $quality = 100;
                $size = $image_make->filesize();
                if ($size && $size > 500000) {
                    $quality = 60;
                }
                $image_make->save($path . 'thumbs/' . $save_name, $quality);
            }
        }

        /* Upload file to aws s3 */
       
        move_to_s3('/post/' . $save_name, $path . $save_name);
        move_to_s3('/post/thumbs/' . $save_name, $path . 'thumbs/' . $save_name);

        return 'success';
    }

    protected function saveGifImage($link, $save_name)
    {
        $domain = formatSourceUrl(get_domain($link));
        $path = $path = public_path() . '/uploads/post/';

        // guzzle configuration..
        $options = [
            'language' => 'en',
            'image_min_bytes' => 4500,
            'image_max_bytes' => 5242880,
            'image_min_width' => 120,
            'image_min_height' => 120,
            'image_fetch_best' => true,
            'image_fetch_all' => false,
            /** @see http://guzzle.readthedocs.org/en/latest/clients.html#request-options */
            'browser' => [
                'timeout' => 60,
                'connect_timeout' => 30,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.100 Safari/537.36',
                    // 'Referer' => 'https://www.google.com/',
                    'Referer' => $domain,
                ],
            ],
            'sink' => $path . $save_name
        ];

        // Get response using guzzle..
        $guzzle = new GuzzleClient();
        try {
            // Get the file and save to server.
            $guzzle_response = $guzzle->get($link, $options);
            copy($path . $save_name, $path . 'thumbs/' . $save_name);
            /* Upload file to aws s3 */
            move_to_s3('/post/' . $save_name, $path . $save_name);
            move_to_s3('/post/thumbs/' . $save_name, $path . 'thumbs/' . $save_name);
        } catch (Exception $e) {
            return 'failed';
        }
        return 'success';
    }

    protected function storePlace($place_url)
    {
        $data = [
            'place_url' => $place_url
        ];

        $exploded = explode('&', rawurldecode($place_url));
        // dd($exploded);
        foreach ($exploded as $key => $place) {
            if ($key > 2) {
                break;
            }
            $data['place_level_' . ($key + 1)] = preg_replace('/.+=/', '', $place);
        }

        Place::firstOrCreate($data);
    }

    protected function getVideoCutTime($duration = 0)
    {
        if ($duration > 20) {
            $at = 10;
        } elseif ($duration > 11) {
            $at = 8;
        } elseif ($duration > 8) {
            $at = 5;
        } elseif ($duration > 5) {
            $at = 3;
        } else {
            $at = 1;
        }
        return $at;
    }

    //  Twitter authenthication
    public function connectTwitter()
    {
        $consumer_key = config('services.twitter.consumer_key');
        $consumer_key_secret = config('services.twitter.consumer_key_secret');
        $callback_url = config('services.twitter.post_twitter_callback_url');

        // create TwitterOAuth object
        $twitteroauth = new TwitterOAuth($consumer_key, $consumer_key_secret);

        // request token of application
        $request_token = $twitteroauth->oauth(
            'oauth/request_token', [
                'oauth_callback' => $callback_url
            ]
        );

        // throw exception if something gone wrong
        if ($twitteroauth->getLastHttpCode() != 200) {
            throw new Exception('There was a problem performing this request');
        }

        // save token of application to session

        Session::put('oauth_token', $request_token['oauth_token']);
        Session::put('oauth_token_secret', $request_token['oauth_token_secret']);

        // generate the URL to make request to authorize our application
        $url = $twitteroauth->url(
            'oauth/authorize', [
                'oauth_token' => $request_token['oauth_token']
            ]
        );

        return redirect($url);
    }

    public function twitterCallbackUrl()
    {

        $consumerkey = config('services.twitter.consumer_key');
        $consumer_secret = config('services.twitter.consumer_key_secret');
        $oauth_verifier = $_GET['oauth_verifier'];


        $oauth_token = Session::get('oauth_token');
        $token_secret = Session::get('oauth_token_secret');


        $conn = new TwitterOAuth($consumerkey, $consumer_secret, $oauth_token, $token_secret);
        $token = $conn->oauth("oauth/access_token", array("oauth_verifier" => $oauth_verifier));


        $access_token = $token['oauth_token'];  // access token
        $access_token_secret = $token['oauth_token_secret']; // access token secret 

        if (Auth::check()) {
            $user_id = Auth::user()->id;
            $user = User::where(['id' => $user_id])->first();
            $user->twitter_token = $oauth_token;
            $user->twitter_access_token = $access_token;
            $user->twitter_access_tokensecret = $access_token_secret;
            $user->save();
        }

        return view('post/twitterCallbackUrl');
    }

    public function commentBoxTemplate()
    {
        return view('tpl.post.comment_box');
    }

    public function discussionTemplate()
    {
        return view('tpl.post.discussion');
    }

    public function publishedPostToFacebook($post_id)
    {

        $category_collumns = ['id', 'category_name'];
        $subCategory_collumns = ['id', 'category_name'];

        $post = Post::where('id', $post_id)
            ->with([
                'category' => function ($query) use ($category_collumns) {
                    $query->addSelect(array('id', 'category_name'));
                },
                'subCategory' => function ($query) use ($subCategory_collumns) {
                    $query->addSelect(array('id', 'category_name'));
                }])
            ->first();
        $embedVideoInfo = getEmbedVideoInfo($post->embed_code);
        $post->embed_code_type = $embedVideoInfo['type'];
        $post->videoid = $embedVideoInfo['videoid'];

        // Create post url..
        $category_name = '';
        if (!empty($post->category->category_name)) {
            $category_name = $post->category->category_name;
        }
        $subcategory_name = '';
        $subCategory = $post->subCategory;
        if (!empty($subCategory)) {
            $subcategory_name = $post->subCategory->category_name;
        }
        $post_url_args = [
            'id' => $post->id,
            'caption' => $post->caption,
            'title' => $post->title,
            'post_type' => $post->post_type,
            'category_name' => $category_name,
            'subcategory_name' => $subcategory_name
        ];

        $post_url = post_url($post_url_args);
        $post->post_url = $post_url;

        $user = User::where(['id' => Auth::user()->id])
            ->select(['facebook_token', 'facebook_access_token'])
            ->first();

        //$facebook_token = $user->facebook_token;
        //$facebook_access_token = $user->facebook_access_token;

        $facebook_token = "418345148496822";
        $facebook_access_token = "EAAIXIdYsmJwBAOdlTKlKlLATnd9DmF19nj5GneRA3wOvtDfHo2rkWDAOf5ZBaRl1onqnQTQ2ZAFdEbMrF5jqldSfkvxSGVXr0pHQIZB86pwqkZA5JLwr1bshZAZAH8GOLMZA6EPjokQDZBoVvpwZB2hnWZA8NGCDHomREZBN6eng46PmwZDZD";


        $feeds = array();
        $feeds['access_token'] = $facebook_access_token;


        if ($post->post_url != '') {
            $feeds['link'] = $post->post_url;
        }

        if ($post->post_type == 5) {  // :: For status post ::
            $feeds['name'] = $post->caption;
        } else {
            if ($post->title != '') {
                $feeds['name'] = $post->title;
            } else {
                $feeds['name'] = $post->caption;
            }
        }


        if ($post->post_type == 3) {   // :: For article post ::

            if ($post->short_description == '') {
                $originalString = $post->content;
                //$content = $originalString.replace(/(<([^>]+)>)/ig,"");

                $feeds['description'] = $originalString . substr(0, 100);
            } else if ($post->short_description != '') {
                $feeds['description'] = $post->short_description;
            } else {
                $feeds['description'] = "  ";
            }

        } else {

            if ($post->short_description != '') {
                $feeds['description'] = $post->short_description;
            } else {
                $feeds['description'] = "  ";
            }
        }

        if ($post->post_type == 1 || $post->post_type == 3 || $post->post_type == 4) {
            if ($post->image != '') {
                $feeds['picture'] = asset('uploads/post/' . $post->image);
            }
        } else if ($post->post_type == 2) {   // For video post ..

            if ($post->embed_code != '') {    // for embed code
                if ($post->embed_code_type == 'youtube') {

                    $picture = 'https://img.youtube.com/vi/' . $post->videoid . '/0.jpg';
                    $feeds['picture'] = $picture;

                } else if ($post->embed_code_type == 'dailymotion') {

                    $thumbnail = 'http://www.dailymotion.com/thumbnail/video/' . $post->videoid;
                    $feeds['picture'] = $thumbnail;

                } else if ($post->embed_code_type == 'vimeo') {
                    $thumbnail = 'https://i.vimeocdn.com/video/' . $post->videoid . '_640.jpg';
                    $feeds['picture'] = $thumbnail;
                }
            } else if ($post->video != '') { // html5

                $video_poster = asset('uploads/video/thumbnail/' . $post->video_poster);
                $feeds['picture'] = $video_poster;

            }
        } else if ($post->post_type == 5) {  // for status post

            if ($post->image != '') {
                $feeds['picture'] = asset('uploads/post/' . $post->image);
            } else {

                if ($post->embed_code != '') {
                    if ($post->embed_code_type == 'youtube') {

                        $pic = 'https://img.youtube.com/vi/' . $post->videoid . '/0.jpg';
                        $feeds['picture'] = $pic;

                    } else if ($post->embed_code_type == 'dailymotion') {

                        $thumbnail = 'http://www.dailymotion.com/thumbnail/video/' . $post->videoid;
                        $feeds['picture'] = $thumbnail;

                    } else if ($post->embed_code_type == 'vimeo') {
                        $thumbnail = 'https://i.vimeocdn.com/video/' . $post->videoid . '_640.jpg';
                        $feeds['picture'] = $thumbnail;
                    }
                } else if ($post->video != '') { // html5

                    $video_poster = asset('uploads/video/thumbnail/' . $post->video_poster);
                    $feeds['picture'] = $video_poster;
                }
            }
        }

        $url = 'https://graph.facebook.com/' . $facebook_token . '/feed';

        $ch = curl_init();


        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $feeds);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
    }

    public function publishedPostToTwitter($post_id)
    {

        $consumerkey = config('services.twitter.consumer_key');
        $consumer_secret = config('services.twitter.consumer_key_secret');
        //$callback_url =  config('services.twitter.callback_url');

        /*
        $oauth_verifier = Session::get('oauth_verifier');
        $oauth_token = Session::get('oauth_token');
        $token_secret = Session::get('oauth_token_secret');
        
        
        $conn = new TwitterOAuth($consumerkey, $consumer_secret, $oauth_token, $token_secret);
        $token = $conn->oauth("oauth/access_token", array("oauth_verifier" => $oauth_verifier));
        */

        $user = User::where(['id' => Auth::user()->id])
            ->select(['twitter_access_token', 'twitter_access_tokensecret'])
            ->first();

        $access_token = $user->twitter_access_token;    // access token

        $access_token_secret = $user->twitter_access_tokensecret; // access token secret 


        $category_collumns = ['id', 'category_name'];
        $subCategory_collumns = ['id', 'category_name'];

        $post = Post::where('id', $post_id)
            ->with([
                'category' => function ($query) use ($category_collumns) {
                    $query->addSelect(array('id', 'category_name'));
                },
                'subCategory' => function ($query) use ($subCategory_collumns) {
                    $query->addSelect(array('id', 'category_name'));
                }])
            ->first();

        // Set embed url info.
        if (!empty($post->embed_code)) {
            $embedVideoInfo = getEmbedVideoInfo($post->embed_code);
            $post->embed_code_type = $embedVideoInfo['type'];
            $post->videoid = $embedVideoInfo['videoid'];
        }

        $category_name = '';
        if (!empty($post->category)) {
            $category_name = $post->category->category_name;
        }

        $subcategory_name = '';
        if (!empty($post->subCategory)) {
            $subcategory_name = $post->subCategory->category_name;
        }

        $post_url_args = [
            'id' => $post->id,
            'caption' => $post->caption,
            'title' => $post->title,
            'post_type' => $post->post_type,
            'category_name' => $category_name,
            'subcategory_name' => $subcategory_name
        ];

        $post_url = post_url($post_url_args);
        $post->post_url = $post_url;

        $parameters = array();

        $connection = new TwitterOAuth($consumerkey, $consumer_secret, $access_token, $access_token_secret);
        if ($post->post_type == 5) {
            $title = $post->caption;
        } else {
            if ($post->title != '') {
                $title = $post->title;
            } else {
                $title = $post->caption;
            }
        }
        $media = '';
        if ($post->post_type == 1 || $post->post_type == 3 || $post->post_type == 4) {

            if (check_file_exists($post->image, 'image_post')) {
                $media = asset('uploads/post/thumbs/' . $post->image);
            } else {
                $media = '';
            }

        } else if ($post->post_type == 2) {

            if ($post->embed_code != '') {    // for embed code
                if ($post->embed_code_type == 'youtube') {

                    $media = 'https://img.youtube.com/vi/' . $post->videoid . '/0.jpg';

                } else if ($post->embed_code_type == 'dailymotion') {

                    $media = 'http://www.dailymotion.com/thumbnail/video/' . $post->videoid;

                } else if ($post->embed_code_type == 'vimeo') {
                    $media = 'https://i.vimeocdn.com/video/' . $post->videoid . '_640.jpg';
                }
            } else if ($post->video != '') { // html5

                if (check_file_exists($post->video_poster, 'video_post')) {
                    $media = asset('uploads/video/thumbnail/' . $post->video_poster);
                } else {
                    $media = '';
                }
            }
        } else if ($post->post_type == 5) {
            if ($post->image != '') {
                if (check_file_exists($post->image, 'image_post')) {
                    $media = asset('uploads/post/thumbs/' . $post->image);
                } else {
                    $media = "";
                }
            } else {
                if ($post->embed_code != '') {    // for embed code
                    if ($post->embed_code_type == 'youtube') {

                        $media = 'https://img.youtube.com/vi/' . $post->videoid . '/0.jpg';

                    } else if ($post->embed_code_type == 'dailymotion') {

                        $media = 'http://www.dailymotion.com/thumbnail/video/' . $post->videoid;

                    } else if ($post->embed_code_type == 'vimeo') {
                        $media = 'https://i.vimeocdn.com/video/' . $post->videoid . '_640.jpg';
                    }
                } else if ($post->video != '') { // html5
                    if (check_file_exists($post->video_poster, 'video_post')) {
                        $media = asset('uploads/video/thumbnail/' . $post->video_poster);
                    } else {
                        $media = '';
                    }
                }
            }
        }


        $parameters['status'] = $title . ' ' . $post->post_url;


        if ($media != '') {
            $arr = explode("/", $media);
            $original_name = end($arr);
            $image_ext = pathinfo($original_name, PATHINFO_EXTENSION);

            if ($image_ext == "gif") {  // convert gif to jpeg.

                $save_name = generateFileName($original_name) . '.' . $image_ext;
                $upload_filename = $media; // distination file
                $converted_filename = public_path() . '/uploads/post/' . $save_name;


                if ($image_ext == "gif") $new_pic = imagecreatefromgif($upload_filename);
                // if ($image_ext=="png") $new_pic = imagecreatefrompng($upload_filename);


                $w = imagesx($new_pic);
                $h = imagesy($new_pic);


                $white = imagecreatetruecolor($w, $h);


                $bg = imagecolorallocate($white, 255, 255, 255);
                imagefill($white, 0, 0, $bg);

                imagecopy($white, $new_pic, 0, 0, 0, 0, $w, $h);

                $new_pic = $white;

                imagejpeg($new_pic, $converted_filename);
                imagedestroy($new_pic);

                $media_file = asset('uploads/post/' . $save_name);
                $del = 1;
            } else {
                $media_file = $media;
                $del = 0;
            }

            $getMedia = $connection->upload('media/upload', ['media' => $media_file]);
            $parameters['media_ids'] = $getMedia->media_id_string;
        }

        $result = $connection->post('statuses/update', $parameters);

        if ($del == 1) {
            unlink($converted_filename);
        }

        //Session::forget('oauth_verifier');
        //Session::forget('oauth_token');
        //Session::forget('oauth_token_secret');

    }

    public function resizeImage()
    {
        // $local = '/opt/lampp/htdocs/swolk/public/uploads/post/thumbs/';
        $server = '/var/www/swolk/public/uploads/post/thumbs/';
        $images = glob($server . '*.{jpg,png,JPG,JPEG,PNG}', GLOB_BRACE);
        foreach ($images as $image) {
            // echo $image . '<br>';
            $image_make = Image::make($image);
            $image_make->save($image, 60);
        }
    }

    public function populatePlaces()
    {
        $posts = Post::all(['id', 'place_url']);
        foreach ($posts as $post) {

            if (empty($post->place_url)) {
                continue;
            }

            $data = [
                'place_url' => $post->place_url
            ];

            $exploded = explode('&', rawurldecode($post->place_url));
            // dd($exploded);
            foreach ($exploded as $key => $place) {
                if ($key > 2) {
                    break;
                }
                $data['place_level_' . ($key + 1)] = preg_replace('/.+=/', '', $place);
            }

            \App\Models\Place::firstOrCreate($data);
        }
    }

    public function autoMoveS3()
    {
        // return 'Please enable feature.';

        $page = 1;
        $per_page = 100;
        $offset = ($page - 1) * $per_page;

        /* For image posts */
        /*$path = public_path() . '/uploads/post/';
        $posts = Post::where('post_type', '<>', 2)->orderBy('id', 'desc')->skip($offset)->take($per_page)->get(['id', 'image']);
        foreach ($posts as $post) {
            if (!empty($post->image)) {
                // Upload file to aws s3
                move_to_s3('/post/' . $post->image, $path . $post->image);
                move_to_s3('/post/thumbs/' . $post->image, $path . 'thumbs/' . $post->image);
            }
        }*/

        /* For image posts */
        $path = public_path() . '/uploads/video/';
        $posts = Post::where('post_type', '=', 2)->where('video', '<>', '')
                ->orderBy('id', 'desc')->skip($offset)->take($per_page)
                ->get(['id', 'video', 'video_poster']);
        foreach ($posts as $post) {
            if (!empty($post->video)) {
                // Upload file to aws s3
                move_to_s3('/video/' . $post->video, $path . $post->video);
            }
            if (!empty($post->video_poster)) {
                // Upload file to aws s3
                move_to_s3('/video/thumbnail/' . $post->video_poster, $path . 'thumbnail/' . $post->video_poster);
            }
        }

        /* For user */
        /*$path = public_path() . '/uploads/profile/';
        $cover_image_path = public_path() . '/uploads/profile/cover/';
        $users = User::where('id', '<>', 18)->orderBy('id', 'desc')->skip($offset)->take($per_page)->get(['id', 'cover_image']);
        foreach ($users as $user) {
            if (!empty($user->profile_image)) {
                move_to_s3('/profile/' . $user->profile_image, $path . $user->profile_image);
                move_to_s3('/profile/thumbs/' . $user->profile_image, $path . 'thumbs/' . $user->profile_image);
            }
            if (!empty($user->cover_image)) {
                move_to_s3('/profile/cover/' . $user->cover_image, $cover_image_path . $user->cover_image);
            }
        }*/

        /*$s3MoveFails = \App\Models\S3MoveFail::all(['id', 'local_path']);
        foreach ($s3MoveFails as $fail) {
            if (!empty($fail->local_path)) {
                $file_name = basename($fail->local_path);

                if (\Storage::disk('s3')->exists('post/' . $file_name)) {
                    $fail->delete();
                }
                elseif (\File::exists($fail->local_path)){
                    //
                }
                else {
                    Post::where('image', $file_name)->delete();
                    $fail->delete();
                }
            }
        }*/

    }

    /**
     * Store feature photo details.
     *
     * @param object $post
     * @return string
     */
    private function storeFeaturePhotoDetail($post)
    {
        if (empty($post)) {
            return 'failed';
        }

        $main_image_url = generate_post_image_url('post/' . $post->image);
        $thumb_image_url = generate_post_image_url('post/thumbs/' . $post->image);

        $image_make = Image::make($main_image_url);

        // Get main image height and width.
        $main_image_width = $image_make->width();
        $main_image_height = $image_make->height();
        // Get image blob.
        $blob_width = 42;
        $blob_height = null;
        $image_make->resize($blob_width, $blob_height, function ($c) {
            $c->aspectRatio();
            $c->upsize();
        });

        // encode image as data-url
        $data_url = (string) $image_make->encode('data-url', 20);

        // Get thumb image height and width.
        $image_make = Image::make($thumb_image_url);
        $thumb_image_width = $image_make->width();
        $thumb_image_height = $image_make->height();

        FeaturePhotoDetail::create([
            'post_id' => $post->id,
            'thumb_width' => $thumb_image_width,
            'thumb_height' => $thumb_image_height,
            'width' => $main_image_width,
            'height' => $main_image_height,
            'data_url' => $data_url
        ]);

        return 'success';
    }


    public function autoSaveFeature() {

        /*$page = 0;
        $per_page = 400;
        $offset = ($page - 1) * $per_page;

        $posts = Post::where('post_type', '<>', 2)->skip($offset)->take($per_page)->get(['id', 'image']);

        foreach ($posts as $post) {
            if (!empty($post->image))
                $this->storeFeaturePhotoDetail($post);
        }*/

    }
/****************(06-03-18)************************** */


public function searchTagJson(Request $request)
{
    // if (! $request->has('q')) {
    //     $response = [
    //         'error_message' => "Invalid request. Missing the 'q' parameter",
    //         'results' => [],
    //         'status' => 'INVALID_REQUEST'
    //     ];
    //     return response()->json($response, 400);
    // }
    $input = $request->all();
    $q = $input['q'];

    $inputSearchText=$q;

    /*************(19-04-18) start****************/

    // $q= preg_replace('!\s+!', '-', $q);//modify tag space to "-"
    // $q=preg_replace('/[^A-Za-z0-9\-]/', '', $q); 
    // $q = preg_replace('/-{2,}/', '-', $q);
    // $q = rtrim($q, '-');
    // $q = ltrim($q, '-');

    $q=preg_replace('/[^A-Za-z0-9]/', ' ', $q);
    //print($inputSearchText);
    //print($q);

    /*************(19-04-18) start****************/





    if (empty($q)) {
        $results = [
        
            'searchTags' => [],
           
        ];
        $response = [
            'results' => $results,
            'status' => 'ZERO_RESULT'
        ];
        return response()->json($response);
    }
    //$query_arr = preg_split('/[\ \,]+/', $q);
    // Remove special characters.
    //$special_char = ['&'];
    // foreach ($query_arr as $key => $value) {
    //     if (in_array($value, $special_char)) {
    //         array_splice($query_arr, $key, 1);
    //     }
    // }
    // $post_search_arr = $query_arr;

   // array_unshift($post_search_arr, $q);
   $page = $input['page'];
   $per_page = 10;
   $offset = ($page - 1) * $per_page;

    $tagTabData = $this->getTag($inputSearchText, $q, $offset, $per_page);

    $results = [
        
        'searchTags' => $tagTabData['tags'],
        'q' => $tagTabData['q'],
        'inputSearchText'=>$inputSearchText,
       
    ];

    // Send the response.
    $response = [
        'results' => $results,
        'status' => 'OK'
    ];
    return response()->json($response);


}


protected function getTag($inputSearchText,$q, $offset, $per_page)
{
   // $glue = count($query_arr) > 1 ? '|' : '';
   // $query = implode($glue, $query_arr);

    /*******(02-04-18) ********/
        /*********(19-04-18)*********/
            //$query_arr = preg_split('/[\ \,\-]+/', $q);//task update (28-03-18)
            // $glue = count($query_arr) > 1 ? '|' : '';
            // $tag_query = implode($glue, $query_arr);

        

            $query_arr = preg_split('/[\ ]+/', $q);
            $result_query_arr= array_filter($query_arr,function($v){ return strlen($v) > 2; });

            $glue = count($result_query_arr) > 1 ? '|' : '';
            $tag_query = implode($glue, $result_query_arr);
            if(count($result_query_arr) > 0)
                $tag_query = implode($glue, $result_query_arr);
            else
                $tag_query = $inputSearchText;
           
        /*********(19-04-18)*********/

    /*******(02-04-18) ********/

    //$q = preg_replace('#[ -]+#', '-',  $q);(19-04-18)
  
    // Initialize
//     $totalTags = 0;
//    // $totalTagsCount = Tag::where('tag_name','like', $q.'%')->count(); (19-04-18)
//    $totalTagsCount = Tag::where('tag_name','like', $inputSearchText.'%')->count();

//     if (! empty($totalTagsCount)) {
//         $totalTags = $totalTagsCount;
//     }

//     $category_sql = '';
//     if(strlen($inputSearchText)>2)
//     {
//        // $category = Category::where('category_name','like', '%'.$q.'%')->first(); (19-04-18)
//        $category = Category::where('category_name','like', '%'.$inputSearchText.'%')->first();
//     }
//     else
//     {
//        // $category = Category::where('category_name','like', $q.'%')->first(); (19-04-18)
//        $category = Category::where('category_name','like', $inputSearchText.'%')->first();
//     }

//     if ($category !== null) {
//         $category_sql = '(SELECT COUNT(*) FROM  `posts` WHERE `id` NOT IN (SELECT `posts`.`id` FROM `posts` INNER JOIN `post_tag` ON `posts`.`id` = `post_tag`.`post_id` WHERE `post_tag`.`tag_id` = `tags`.`id`) AND (`posts`.`category_id` = (SELECT `id` FROM `categories` WHERE `category_name` REGEXP `tags`.`tag_name` OR `category_name_slug` LIKE `tags`.`tag_name` LIMIT 1) OR `posts`.`sub_category_id` = (SELECT `id` FROM `categories` WHERE `category_name` REGEXP `tags`.`tag_name` OR `category_name_slug` LIKE `tags`.`tag_name` LIMIT 1)) AND orginal_post_id is null) + ';
//     }

    // Check whether following the tag.
    if (Auth::check()) {
        $follow_sql = '(select count(*) from `tag_user` where `tag_user`.`tag_id` = `tags`.`id` and `tag_user`.`user_id` = '. Auth::user()->id . ') as isFollow';
    }
    else {
        $follow_sql = '0 as isFollow ';
    }

    if(strlen($inputSearchText)>2)
    {
        $tag1 = Tag::selectRaw('`id`, `tag_name`,`tag_text`,`question_tag` as question, (SELECT count(*) FROM `posts` INNER JOIN `post_tag` ON `posts`.`id` = `post_tag`.`post_id` WHERE `post_tag`.`tag_id` = `tags`.`id` AND orginal_post_id is null) as `posts_count`, (select count(*) from `users` inner join `tag_user` on `users`.`id` = `tag_user`.`user_id` where `tag_user`.`tag_id` = `tags`.`id` and `users`.`deleted_at` is null) as `users_count`, ' . $follow_sql)
        ->where('tag_text',$inputSearchText)->get();


        $tag2 = Tag::selectRaw('`id`, `tag_name`,`tag_text`,`question_tag` as question, (SELECT count(*) FROM `posts` INNER JOIN `post_tag` ON `posts`.`id` = `post_tag`.`post_id` WHERE `post_tag`.`tag_id` = `tags`.`id` AND orginal_post_id is null) as `posts_count`, (select count(*) from `users` inner join `tag_user` on `users`.`id` = `tag_user`.`user_id` where `tag_user`.`tag_id` = `tags`.`id` and `users`.`deleted_at` is null) as `users_count`, ' . $follow_sql)
                
                // ->whereRaw('(`tag_name` REGEXP ? OR `tag_name` = ? OR `tag_name` = ?)', [
                //     $query,
                //     $q,
                //     str_slug_ovr($q)
                // ])
                //->where('tag_name','like', '%'.$q.'%')

                /********(19-04-18)*******/
                    // ->whereRaw('(`tag_name` REGEXP ? OR `tag_name` = ? OR `tag_name` = ?)', [
                    //     $tag_query,
                    //     $q,
                    //     str_slug_ovr($q)
                    // ])
                    // ->orderByRaw(" posts_count desc, users_count desc")


                    ->whereRaw('( `tag_text` REGEXP ?)', [
                     
                        $tag_query,
                      
                    ])
                  
                    ->orderByRaw(" posts_count desc, users_count desc")
                    ->get()
                /******(19-04-18)*******/    
        ;

        $tag=$tag1->merge($tag2)->unique();
    }

    else
    {
        $tag1 = Tag::selectRaw('`id`, `tag_name`,`tag_text`,`question_tag` as question, (SELECT count(*) FROM `posts` INNER JOIN `post_tag` ON `posts`.`id` = `post_tag`.`post_id` WHERE `post_tag`.`tag_id` = `tags`.`id` AND orginal_post_id is null) as `posts_count`, (select count(*) from `users` inner join `tag_user` on `users`.`id` = `tag_user`.`user_id` where `tag_user`.`tag_id` = `tags`.`id` and `users`.`deleted_at` is null) as `users_count`, ' . $follow_sql)
        ->where('tag_text',$inputSearchText)->get();

        $tag2 = Tag::selectRaw('`id`, `tag_name`,`tag_text`,`question_tag` as question, (SELECT count(*) FROM `posts` INNER JOIN `post_tag` ON `posts`.`id` = `post_tag`.`post_id` WHERE `post_tag`.`tag_id` = `tags`.`id` AND orginal_post_id is null) as `posts_count`, (select count(*) from `users` inner join `tag_user` on `users`.`id` = `tag_user`.`user_id` where `tag_user`.`tag_id` = `tags`.`id` and `users`.`deleted_at` is null) as `users_count`, ' . $follow_sql)
                
                // ->whereRaw('(`tag_name` REGEXP ? OR `tag_name` = ? OR `tag_name` = ?)', [
                //     $query,
                //     $q,
                //     str_slug_ovr($q)
                // ])
               // ->where('tag_name','like', $q.'%')

             /********(19-04-18)*******/  
                //    ->whereRaw('(`tag_name` REGEXP ? OR `tag_name` = ? OR `tag_name` = ?)', [
                //     $tag_query,
                //     $q,
                //     str_slug_ovr($q)
                // ])

                // ->whereRaw('(`tag_text` REGEXP ?)', [
        
                //     $tag_query,
                  
                // ])
                ->where('tag_name','like', $inputSearchText.'%')
                ->get()
            /********(19-04-18)*******/     
        ;
        $tag=$tag1->merge($tag2)->unique();
    }

    // \DB::connection()->enableQueryLog();
    // Paginate the results
    //$offset=0;
   /// $tags = $tag->skip($offset)->take(10)->get();

   // $tags = $tag->skip($offset)->take($per_page)->get();(19-04-18)
        $tag=$tag->toArray();
     
        
      $tags =  array_slice($tag, $offset, $per_page);

      $tags=collect($tags);

    

    // Check if category is present in tags.
    // if ($category !== null  && $offset == 0 ) {
    //     $isPresent = false;
    //     foreach($tags as $tag) {
    //         print($category);
    //         if(
    //             strtolower($category->category_name) == strtolower($tag->tag_name) ||
    //             str_slug_ovr($category->category_name) == $tag->tag_name
    //         ) {
    //             $isPresent = true;
    //             break;
    //         }
    //     }
    //     // Fetch category if not present in tags.
    //     if(!$isPresent) {
    //         $category_tag = Category::selectRaw('`id`, `category_name` as tag_name, `category_name` as tag_text,  (SELECT COUNT(*) FROM `posts` WHERE (`posts`.`category_id` = ' . $category->id . ' OR `posts`.`sub_category_id` = ' . $category->id . '))  as `posts_count`, 0  as `users_count`, 0 as isFollow ')
    //         ->where('id', $category->id)->first();
    //         if ($category_tag !== null) {
    //             $tags->push($category_tag);
    //         }
    //     }
    // }
    
    /*$query = \DB::getQueryLog();
    dd($query);*/

    // Prepare the return data.
    $return_data = [
       // 'totalTags' => $totalTags,
        'tags' => $tags,
       // 'q'=>$q, (19-04-18)
       'q'=>$inputSearchText,

    ];
    return $return_data;
}
    public function searchTagJson1(Request $request)
    {
        if (! $request->has('q')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'q' parameter",
				'results' => [],
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
        }
        $input = $request->all();
		$q = $input['q'];
		if (empty($q)) {
			$response = [
				'results' => [],
				'status' => 'ZERO_RESULT'
			];
			return response()->json($response);
        }
        $query_arr = preg_split('/[\ \,]+/', $q);
        // Remove special characters.
        $special_char = ['&'];
        foreach ($query_arr as $key => $value) {
            if (in_array($value, $special_char)) {
                array_splice($query_arr, $key, 1);
            }
        }
        $post_search_arr = $query_arr;

        array_unshift($post_search_arr, $q);
        $tagTabData = $this->getTag($query_arr, $q);

        $results = [
            
            'searchTags' => $tagTabData['tags'],
           
		];

		// Send the response.
		$response = [
            'results' => $results,
			'status' => 'OK'
		];
		return response()->json($response);


    }


    protected function getTag1($query_arr, $q)
    {
        $glue = count($query_arr) > 1 ? '|' : '';
        $query = implode($glue, $query_arr);

        // Initialize
        $totalTags = 0;
        $totalTagsCount = DB::select('SELECT COUNT(*) as total from `tags` WHERE `tag_name` REGEXP ?', [$query]);
        if (! empty($totalTagsCount)) {
            $totalTags = $totalTagsCount[0]->total;
        }

        $category_sql = '';
        $category = Category::whereRaw('`category_name` REGEXP ?', [$query])->first();
        if ($category !== null) {
            $category_sql = '(SELECT COUNT(*) FROM  `posts` WHERE `id` NOT IN (SELECT `posts`.`id` FROM `posts` INNER JOIN `post_tag` ON `posts`.`id` = `post_tag`.`post_id` WHERE `post_tag`.`tag_id` = `tags`.`id`) AND (`posts`.`category_id` = (SELECT `id` FROM `categories` WHERE `category_name` REGEXP `tags`.`tag_name` OR `category_name_slug` LIKE `tags`.`tag_name` LIMIT 1) OR `posts`.`sub_category_id` = (SELECT `id` FROM `categories` WHERE `category_name` REGEXP `tags`.`tag_name` OR `category_name_slug` LIKE `tags`.`tag_name` LIMIT 1)) AND orginal_post_id is null) + ';
        }

        // Check whether following the tag.
        if (Auth::check()) {
            $follow_sql = '(select count(*) from `tag_user` where `tag_user`.`tag_id` = `tags`.`id` and `tag_user`.`user_id` = '. Auth::user()->id . ') as isFollow';
        }
        else {
            $follow_sql = '0 as isFollow ';
        }

        $tag = Tag::selectRaw('`id`, `tag_name`,`tag_text`,`question_tag` as question,' . $category_sql . ' (SELECT count(*) FROM `posts` INNER JOIN `post_tag` ON `posts`.`id` = `post_tag`.`post_id` WHERE `post_tag`.`tag_id` = `tags`.`id` AND orginal_post_id is null) as `posts_count`, (select count(*) from `users` inner join `tag_user` on `users`.`id` = `tag_user`.`user_id` where `tag_user`.`tag_id` = `tags`.`id` and `users`.`deleted_at` is null) as `users_count`, ' . $follow_sql)
                ->whereRaw('(`tag_name` REGEXP ? OR `tag_name` = ? OR `tag_name` = ?)', [
                    $query,
                    $q,
                    str_slug_ovr($q)
                ])
                ->orderByRaw("CASE  WHEN `tag_name` REGEXP '[[:<:]]" . $query . "[[:>:]]' THEN 1 WHEN `tag_name` LIKE '" . $query . "%' THEN 2 WHEN `tag_name` LIKE '%" . $query . "' THEN 4 ELSE 3 END, posts_count desc, users_count desc")
        ;

        // \DB::connection()->enableQueryLog();
        // Paginate the results
        $offset=0;
       /// $tags = $tag->skip($offset)->take(10)->get();

        $tags = $tag->get();

        // Check if category is present in tags.
        if ($category !== null ) {
            $isPresent = false;
            foreach($tags as $tag) {
                if(
                    strtolower($category->category_name) == strtolower($tag->tag_name) ||
                    str_slug_ovr($category->category_name) == $tag->tag_name
                ) {
                    $isPresent = true;
                    break;
                }
            }
            // Fetch category if not present in tags.
            if(!$isPresent) {
                $category_tag = Category::selectRaw('`id`, `category_name` as tag_name,  (SELECT COUNT(*) FROM `posts` WHERE (`posts`.`category_id` = ' . $category->id . ' OR `posts`.`sub_category_id` = ' . $category->id . '))  as `posts_count`, 0  as `users_count`, 0 as isFollow ')
                ->where('id', $category->id)->first();
                if ($category_tag !== null) {
                    $tags->push($category_tag);
                }
            }
        }
        
        /*$query = \DB::getQueryLog();
        dd($query);*/

        // Prepare the return data.
        $return_data = [
            'totalTags' => $totalTags,
            'tags' => $tags
        ];
        return $return_data;
    }



}
