<?php namespace App\Http\Controllers\Api;

use Storage;
use Validator;
use Auth;
use View;
use Response;
use Session;
use Carbon\Carbon;
use DB;
use Image;
use Hash;
use Mail;
use Facebook;

use App\Events\PostShared;
use App\Events\PostBookMarked;
use App\Events\PostViewed;

use App\Models\User;
use App\Models\Userview;
use App\Models\Post;
use App\Models\Notification;
use App\Models\CommentNotification;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Follower;
use App\Models\State;
use App\Models\Category;
use App\Models\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;

class ProfileController extends Controller
{
    protected $clientIp;

    protected $image_width;
    protected $image_height;

    protected $loadComments;

    protected $per_page;
    protected $offset;

    protected $allowed_post_types;
    protected $filter_post_type;

    //protected $bots_array;
    protected $bots_array = [];
    protected $default_description;

    public function __construct(Request $request)
    {

        $this->bots_array = config('constants.CRAWLER_BOTS');
        $this->default_description = config('constants.DEFAULT_DESCRIPTION');
        if ($request->has('isTest')) {
            dd($this->default_description);
        }
        // Set client ip.
        $this->clientIp = $request->ip();

        $this->image_width = config('constants.POST_IMAGE_WIDTH');
        $this->image_height = config('constants.POST_IMAGE_HEIGHT');

        $this->loadComments = 10;

        /*--- build the pagination logic ---*/
        $this->per_page = config('constants.PER_PAGE');
        // Calculation for pagination.
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
    }


    /**
     * @return View
     */
    public function index()
    {
        return view('profile.index');
    }

    /**
     * @return View
     */
    public function viewUserProfile(Request $request, $username = '')
    {

        $user_agent = $request->header('User-Agent');
        $crawler_bot = false;
        foreach ($this->bots_array as $key => $bot) {
            if (strstr(strtolower($user_agent), $bot)) {
                $crawler_bot = true;
                break;
            }
        }

        if ($crawler_bot) {
          
            if (!empty($username)) {
                $cond = [
                    'username' => $username,
                    'status'=>'1'
                ];
            } else {
                //abort and redirect to 404
                abort(404);
            }
            $user = User::where('username', $username)->first();
            $profileData = $this->getUserProfileData($cond);
          

            /***block code for change logic (17-04-18) */

            // if (empty($profileData['status'])) {
            //     abort(404);
            // }
            // else if ($profileData['status'] == "INVALID_REQUEST") {
            //     abort(404);
            // }
            /***block code for change logic (17-04-18) */

            /***add code for change logic (17-04-18) */
            if (empty($profileData['user'])) {
                abort(404);
            }
            else if ($profileData['user'] == "INVALID_REQUEST") {
                abort(404);
            }
             /***add code for change logic (17-04-18) */

            #... Check to see if about me length is sufficient for crawler or not ...#
            /**Enable default description**/
            $profileData['user']->about_me = strlen($profileData['user']->about_me) > 100 ?
                $profileData['user']->about_me : $this->default_description;

            $recentPosts = $this->loadRecentPosts($user->id, $postCount = 50);
            $userProfileData = array_merge($profileData, $recentPosts);
            
             /***block code for change logic (17-04-18) */
            #... If user profile does not exists then redirect to 404 ...#
            // if (isset($userProfileData['status']) && $userProfileData['status'] = "INVALID_REQUEST") {
            //     //abort and redirect to 404
            //     abort(404);
            // }
             /***block code for change logic (17-04-18) */

           

            return view('profile.profile-seo', $userProfileData);
        }
        else {
            return view('index');
        }
    }

    /**
     * @return array containing user profile data
     */
    public function getUserProfileData($cond = array())
    {
        $data = [];
        $follower_count = 0;

        // Column selection array.
        $follower_columns = ['id', 'user_id', 'follower_id'];
        $collection_columns = ['id', 'collection_name', 'collection_text'];

        if (!empty($cond)) {

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

            if ($user === null) {
                $response = [
                    'error_message' => "Invalid request. The profile does not exists.",
                    'status' => 'INVALID_REQUEST'
                ];
                return $response;
            }

            $follower_count = $user->follower->count();
            $following_count = $user->following->count();
            $collection_count = $user->collection->count();

            $posts = Post::where('created_by', $user->id);

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
                } else {
                    $posts = $posts->whereIn("privacy_id", [1, 2, 3]);
                }
            } else {
                $posts = $posts->whereIn("privacy_id", [1]);
            }

            $post_count = $posts->count();

            $userDataProfileViews = Userview::where(['user_id' => $user->id])->count();

            $data = [
                'user' => $user,
                'total_post' => $post_count,
                'userDataProfileViews' => $userDataProfileViews,
                'follower_count' => $follower_count,
                'following_count' => $following_count
            ];

        }
        return $data;
    }

    /**
     * @return array containing all the recent user posts
     */
    public function loadRecentPosts($profileID = '', $postCount = '')
    {
        // Initialize data.
        $posts = [];
        $total_posts = 0;
        $privacyWishTotalPost = 0;

        if ($profileID == '') {
            return [];
        }
        if ($postCount == '') {
            $postCount = 50;
        }

        // Set post_view_type
        $post_view_type = 'recent';

        $posts = Post::where('created_by', $profileID);
        //	->removeReported(Auth::user()->id);


        $posts = $posts->whereIn("privacy_id", [1]);


        $privacyWishTotalPost = $posts->count();

        // Condition based on post types.
        if ($post_view_type === 'recent') {
            $posts->orderBy('created_at', 'desc');
        }

        $total_posts = $posts->count();


        // Collumn selection array
        $user_collumns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
        $category_collumns = ['id', 'category_name'];
        $subCategory_collumns = ['id', 'category_name'];
        $tag_collumns = ['tags.id', 'tag_name','tag_text'];
        $region_columns = ['id', 'name', 'slug_name'];
        $featurePhotoDetail_column = ['id', 'post_id', 'thumb_width', 'thumb_height', 'data_url'];

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
            ->take($postCount)->get()->makeVisible('people_here');

        /*if (!empty($_REQUEST['test'])) {
            $query = \DB::getQueryLog();
            // $lastQuery = end($query);
            dd($query);
        }*/

        $post_count = count($posts);
        $final_posts = [];
        for ($p = 0; $p < $post_count; $p++) {
            $parent_post_user_id = $posts[$p]->parent_post_user_id;
            $current_post = null;
            // For shared post.
            if ($posts[$p]->orginal_post_id > 0 && $posts[$p]->orginalPost->id > 0) {
                $current_post = $posts[$p];

                $orginalPost = $posts[$p]->orginalPost->makeVisible('people_here');
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

                $final_posts[$p]['cardID'] = $current_post->id;
                // Set embed url info.
                if (!empty($final_posts[$p]['embed_code'])) {
                    $embedVideoInfo = getEmbedVideoInfo($final_posts[$p]['embed_code']);
                    $final_posts[$p]['embed_code_type'] = $embedVideoInfo['type'];
                    $final_posts[$p]['videoid'] = $embedVideoInfo['videoid'];
                }

                $final_posts[$p]['child_post_created_at'] = $current_post->created_at->format('Y-m-d H:i:s');

            } // For post which is not shared (original).
            else {
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

        return_area:
        // Prepare the return data.
        $data = [
            'allPosts' => $final_posts,
            'total_posts' => $total_posts,
            'privacyWishTotalPost' => $privacyWishTotalPost
        ];
        return $data;
    }

    /**
     * @param object $user_details
     * @return string $about_me
     */
    public function generateAboutMeMeta($user_details)
    {
        $about_me = [];
        $top_tag_category = [];
        if (!empty($user_details)) {

            /*** Get Top Category or tag used by profile user to post ***/
            $sql_get_top_cat_tag = "SELECT * FROM ((SELECT COUNT(`p`.`id`) as `total_posts`,`p`.`category_id` as `id`,`c`.`category_name` as `name`, 
'category' as `type` FROM `posts` as `p` INNER JOIN `categories` as `c` ON `p`.`category_id`=`c`.`id` WHERE `p`.`category_id` > 0 AND `p`.`created_by`='$user_details->id' GROUP BY `p`.`category_id` ORDER BY `total_posts` DESC LIMIT 3) UNION
 (SELECT COUNT(`pt`.`id`) as `total_posts`, `pt`.`tag_id` as `id`,`t`.`tag_name` as `name`,`t`.`tag_text` as `tag_text`,'tag' as `type` FROM `post_tag` as `pt` INNER JOIN `tags` as `t` ON `pt`.`tag_id`=`t`.`id` INNER JOIN `posts` as `p` ON `pt`.`post_id`=`p`.`id` WHERE `p`.`created_by`='$user_details->id' GROUP BY `pt`.`tag_id` ORDER BY `total_posts` DESC LIMIT 3)) tbl ORDER BY `total_posts` DESC LIMIT 3";
            $result_get_top_cat_tag = DB::select($sql_get_top_cat_tag);
            foreach ($result_get_top_cat_tag as $key => $result) {
                $top_tag_category[] = $result->name;
            }

            /*** Prepare about me ***/
            $about_me[] = $user_details->first_name . " " . $user_details->last_name;

            if (!empty($user_details->occupation)) {
                $about_me[] = $user_details->occupation;
            }
            if (!empty($user_details->about_me)) {
                $about_me[] = $user_details->about_me;
            }
            if (!empty($top_tag_category)) {
                $about_me[] = "most share topics are " . implode(', ', $top_tag_category);
            }

            $about_me = implode(', ', $about_me);

        }
        return $about_me;
    }

    /**
     * Show the profile edit modal form
     *
     * @return Response JSON
     */
    public function editProfile()
    {

        return view('profile.edit');
    }

    public function editMyAccount()
    {
        return view('profile.myaccount');
    }

    public function editPassword()
    {
        return view('profile.changepassword');
    }

    /**
     * Return List of all the countries
     *
     * @return Response JSON
     */
    public function getAllInfo()
    {
        //$user       = User::findOrFail(Auth::id());
        $user = Auth::user();
        $countries = Country::all();
        $states = State::where(['country_id' => $user->country_id])->get();
        $data = [
            'user' => $user,
            'countries' => $countries,
            'states' => $states
        ];

        if (!empty($user->profile_image)) {
            $user->profile_image = generate_profile_image_url('profile/thumbs/' . $user->profile_image);
        }
        return response()->json($data);
    }

    /**
     * Return List of all the states in repect to a country
     *
     * @return Response JSON
     */
    public function getAllStates(Request $request)
    {
        $country_id = $request->input('country_id');
        $states = State::where(['country_id' => $country_id])->get();
        $data = [
            'states' => $states
        ];
        return response()->json($data);
    }

    public function saveUserData(Request $request)
    {
        $userInput = $request->all();
        $userInput['website'] = formatSourceUrl($userInput['website']);

        preg_match('/.*.facebook.com\/(.*)/i', $userInput['profile_facebook'], $matches);
        if (!empty($matches)) {
            $userInput['profile_facebook'] = $matches[1];
        }
        $userInput['profile_facebook'] = 'https://www.facebook.com/' . $userInput['profile_facebook'];

        preg_match('/.*.twitter.com\/(.*)/i', $userInput['profile_facebook'], $matches);
        if (!empty($matches)) {
            $userInput['profile_facebook'] = $matches[1];
        }
        $userInput['profile_twitter'] = 'https://twitter.com/' . $userInput['profile_twitter'];

        // Don't Allow User to update username for now
        if (isset($userInput['username'])) {
            unset($userInput['username']);
        }

        $response = DB::table('users')->where('id', Auth::id())->update($userInput);
        $data = [
            'response' => $response
        ];
        return response()->json($data);
    }

    /**
     * Uploads and save user profile picture
     *
     * @return Response JSON
     */
    public function saveProfilePicture(Request $request)
    {

        $fullPath = '';
        //$allowed_file_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $allowed_file_extensions = ['jpg', 'jpeg', 'png'];
        $error_message = '';
        $has_error = 0;
        $size = 0;
        if ($request->file('file')) {
            $image = $request->file('file');
            $image_ext = $image->getClientOriginalExtension();
            $image_ext = strtolower(trim($image_ext));

            if (in_array($image_ext, $allowed_file_extensions)) {
                $original_name = $image->getClientOriginalName();
                $original_name = str_replace(' ', '-', substr($original_name, 0, 75));
                $save_name = time() . str_random(10) . '.' . $original_name;

                //Store Path For both thumbs and orginal picture
                $path = public_path() . '/uploads/profile/';
                $thumb_path = public_path() . '/uploads/profile/thumbs/';

                if ($image_ext == 'gif') {
                    $image->move($path, $save_name);
                    copy($path . $save_name, $thumb_path . $save_name);

                    /* Upload file to aws s3 */
                    move_to_s3('/profile/' . $save_name, $path . $save_name);
                    move_to_s3('/profile/thumbs/' . $save_name, $thumb_path . $save_name);

                    if (!empty(Auth::user()->profile_image)) {
                        Storage::delete(['/profile/' . Auth::user()->profile_image, '/profile/thumbs/' . Auth::user()->profile_image]);
                    }
                    Auth::user()->profile_image = $save_name;
                    Auth::user()->save();
                } else {
                    $image_make = Image::make($image->getRealPath());
                    $size = $image_make->filesize();
                    $size_mb = floor($size / pow(1024, 2));
                    if ($size_mb <= 5) {
                        $width = $this->image_width;
                        $height = null;
                        $image_make = Image::make($image->getRealPath())->orientate();
                        $image_make->resize($width, $height, function ($c) {
                            $c->aspectRatio();
                            $c->upsize();
                        });
                        $image_make->save($path . $save_name);

                        /*--- Save thumbnail image ---*/
                        $thumb_width = 200;
                        $thumb_height = null;
                        $image_make->resize($thumb_width, $thumb_height, function ($c) {
                            $c->aspectRatio();
                            $c->upsize();
                        });
                        $quality = 100;
                        $size = $image_make->filesize();
                        if ($size && $size > 500000) {
                            $quality = 90;
                        }
                        $image_make->save($thumb_path . $save_name, $quality);

                        /* Upload file to aws s3 */
                        move_to_s3('/profile/' . $save_name, $path . $save_name);
                        move_to_s3('/profile/thumbs/' . $save_name, $thumb_path . $save_name);

                        if (!empty(Auth::user()->profile_image)) {
                            Storage::delete(['/profile/' . Auth::user()->profile_image, '/profile/thumbs/' . Auth::user()->profile_image]);
                        }

                        Auth::user()->profile_image = $save_name;
                        Auth::user()->save();
                    } else {
                        $error_message = 'Images only less than 5 mb are allowed';
                        $has_error = 1;
                    }
                }


            } else {
                $error_message = 'Only Jpg,Jpeg and png images are allowed';
                $has_error = 1;
            }
        }
        $profile_image = generate_profile_image_url('profile/thumbs/' . Auth::user()->profile_image);
        $data = [
            'profile_image' => $profile_image,
            'size' => $size,
            'has_error' => $has_error,
            'error_msg' => $error_message
        ];

        return response()->json($data);
    }

    /**
     * Function to check username and email are unique or not
     *
     * @return Response JSON
     */

    public function checkUnqiueUsernameEmail(Request $request)
    {
        $data = [];
        $value = $request->input('value');
        $type = $request->input('type');

        $info = DB::table('users')->where([
            [$type, '=', $value],
            ['id', '!=', Auth::id()]
        ])->value('id');
        $data = [
            'is_unique' => empty($info) ? true : false,
            'type' => $type
        ];
        return response()->json($data);
    }

    /**
     * Function to store user location in session
     *
     * @return Response JSON
     */
    public function saveUserLocation(Request $request)
    {
        $locationInfo = $request->all();
        $latitudeFrom = $locationInfo['lat'];
        $longitudeFrom = $locationInfo['lon'];
        $data = [];
        $data = [
            'lat' => $latitudeFrom,
            'lon' => $longitudeFrom,
            'city' => $locationInfo['city'],
            'state' => $locationInfo['state'],
            'country_code' => $locationInfo['country_code']
        ];
        //Save user location in session
        Session::put('userLocationInfo', $data);

        return response()->json($data);
    }

    /**
     * Function to update password
     *
     * @return Response JSON
     */

    public function changePassword(Request $request)
    {
        $data = [];
        $has_error = 0;
        $old_password_mismatch = '';
        $new_password_mismatch = '';

        $current_password = $request->input('current_password');
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');

        if ($new_password != $confirm_password) {
            $has_error = 1;
            $new_password_mismatch = "New Password & Confirm Password Does not match";
        }
        if (!Hash::check($current_password, Auth::user()->password)) {
            // The current passwords mismatch...
            $has_error = 1;
            $old_password_mismatch = "Old Password Does not match";
        }

        if ($has_error == 0) {
            $new_password = Hash::make($new_password);
            //dd($new_password);
            DB::table("users")->where('id', Auth::id())->update(['password' => $new_password]);
        }

        $data = [
            'has_error' => $has_error,
            'old_password_mismatch' => $old_password_mismatch,
            'new_password_mismatch' => $new_password_mismatch
        ];

        return response()->json($data);
    }

    /**
     * Update the profile.
     *
     * @param  Request $request
     * @return Response
     */
    public function postProfile(Request $request)
    {
        $user = Auth::user();
        //dd($request->all());

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'month' => 'sometimes|numeric|between:1,12',
            'day' => 'sometimes|numeric|between:1,31',
            'year' => 'sometimes|numeric|max:2016',
            'username' => 'required|min:2|max:15|unique:users,username,' . $user->id,
            'country' => 'required|numeric|exists:countries,id',
            'state' => 'required|required_with:country|numeric|exists:states,id,country_id,' . $request->input('country'),
            'city' => 'required|max:100',
            'zipcode' => 'required|numeric',

            'address' => 'sometimes',
            'about_me' => 'sometimes',
            'description' => 'sometimes',
            'occupation' => 'sometimes|max:255',
            'website' => 'sometimes|url|max:255',
            'profile_linkedin' => 'sometimes|url|max:255',
            'profile_facebook' => 'sometimes|url|max:255',

        ]);

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->username = $request->input('username');
            $user->profile_image = $request->input('file_name');
            $user->address = $request->input('address');
            $user->about_me = $request->input('about_me');
            $user->description = $request->input('description');
            $user->occupation = $request->input('occupation');
            $user->website = $request->input('website');
            $user->profile_linkedin = $request->input('profile_linkedin');
            $user->profile_facebook = $request->input('profile_facebook');
            $user->country_id = $request->input('country');
            $user->state_id = $request->input('state');
            $user->city = $request->input('city');
            $user->zipcode = $request->input('zipcode');

            $user->dob = ($request->input('year') && $request->input('month') && $request->input('day')) ? Carbon::createFromDate($request->input('year'), $request->input('month'), $request->input('day')) : NULL;

            $user->save();

            Flash::success('Profile updated successfully.');
        }
        return redirect()->back();
    }

    public function commentTemplate()
    {
        return view('profile.tpl_comment');
    }

    public function showPostDetails(Request $request)
    {
        $post_id = $request->input('post_id');
        $is_briefed = $request->input('is_briefed');
        $child_post_id = $request->input('child_post_id');

        $initiator = $request->has('initiator') ? $request->input('initiator') : '';

        if (Auth::check()) {
            $follower_id = $user_id = Auth::user()->id;
            // Clear notification for the post.
            $notification_cond = [
                'post_id' => $child_post_id,
                'post_user_id' => Auth::user()->id
            ];

            $notification = Notification::where($notification_cond)->update(['status' => 3]);
            // Clear comment notification for the post.
            $comment_notification = CommentNotification::where('notified_user_id', Auth::user()->id)
                ->whereIn('comment_id', function ($query) use ($child_post_id) {
                    $query->select('id')
                        ->from('comments')
                        ->where('post_id', $child_post_id);
                })
                ->update(['status' => 3]);
        }

        $category_collumn = ['id', 'category_name'];
        $subCategory_collumn = ['id', 'category_name'];
        $tags = ['tags.id','tag_name','tag_text','question_tag','question_tag_created_at'];
        $user_collumns = ['id', 'username', 'first_name', 'last_name', 'about_me', 'description', 'sex', 'city', 'profile_image', 'cover_image', 'occupation', 'points'];

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

        $postCaptions = $post->caption;

        $post->caption = hash_tag_url($post->caption);
        $limited_article = get_limited_article($post->content);
        if (!empty($limited_article)) {
            $post->time_needed = $limited_article['time_needed'];
        }

        if (!empty($post->content)) {
            if ($is_briefed == 1) {
                $limited_article = get_limited_article($post->content);
                $post->content = $limited_article['content'];
            } else {
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

                // Add <br /> after every <img> tag.
                $content = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '$1<br />', $content);

                $post->content = $content;
            }

        }

        // Post activity for image,status and article

        if (Auth::check()) {
            $userID = Auth::user()->id;
        } else {
            $userID = 1; // anonymous user
        }


/**************** change show post logic (22-12-17) ******************/  
        /********block the previous code***********/
                // if ($initiator != 'share_popup') {
                //     if ($post->post_type == 1 || $post->post_type == 5) { // view post for image and status
                //         $activity_id = 8; //  activity ID 8 ::  View
                //         $flag = $this->viewPostProcess($activity_id, $post_id, $userID);

                //         if ($post_id != $child_post_id && $flag == 1) {
                //             $flag = $this->viewPostProcess($activity_id, $child_post_id, $userID);
                //         }
                //     } else if ($post->post_type == 3) {  // view post for article
                //         $activity_id = 9; //  activity ID 9 ::  Read
                //         $flag = $this->viewPostProcess($activity_id, $post_id, $userID);

                //         if ($post_id != $child_post_id && $flag == 1) {
                //             $flag = $this->viewPostProcess($activity_id, $child_post_id, $userID);
                //         }
                //     }

                //     //Take Record for all type of post views for analytics purpose
                //     //Take record for child post and parent post both following 1hour rule
                //     //Only For Non Logged In User
                //     $activityId = 8; //for post view
                //     $activityViewStatus = $this->recordPostView($activityId, $post_id, $userID);
                //     if ($post_id != $child_post_id) {
                //         $activityViewStatus = $this->recordPostView($activityId, $child_post_id, $userID);
                //     }
                // }
        /********block the previous code***********/

        /**************New Code*******************/
                   if ($initiator != 'share_popup') {
                        if ($post->post_type == 1 || $post->post_type == 5 || $post->post_type == 2 || $post->post_type == 4 || $post->post_type == 6) { // view post for image and status
                            $activity_id = 8; //  activity ID 8 ::  View
                            $flag = $this->viewPostProcessNew($activity_id, $post_id, $userID);

                            if ($post_id != $child_post_id && $flag == 1) {
                                $flag = $this->viewPostProcessNew($activity_id, $child_post_id, $userID);
                            }
                        } else if ($post->post_type == 3) {  // view post for article
                            $activity_id = 9; //  activity ID 9 ::  Read
                            $flag = $this->viewPostProcessNEW($activity_id, $post_id, $userID);

                            if ($post_id != $child_post_id && $flag == 1) {
                                $flag = $this->viewPostProcessNEW($activity_id, $child_post_id, $userID);
                            }

                            $activity_id = 8; // again record with activity ID 8 ::  view
                            $flag = $this->viewPostProcessNEW($activity_id, $post_id, $userID);

                            if ($post_id != $child_post_id && $flag == 1) {
                                $flag = $this->viewPostProcessNEW($activity_id, $child_post_id, $userID);
                            }

                        }

                        //Take Record for all type of post views for analytics purpose
                        //Take record for child post and parent post both following 1hour rule
                        //Only For Non Logged In User
                        $activityId = 8; //for post view
                        $activityViewStatus = $this->recordPostView($activityId, $post_id, $userID);
                        if ($post_id != $child_post_id) {
                            $activityViewStatus = $this->recordPostView($activityId, $child_post_id, $userID);
                        }
                    }
        
        /**************New Code*******************/

/**************** change show post logic (22-12-17) ******************/


        $postTotalComment = Comment::where('post_id', $post_id)->get()->count();
        // get total share post ...
        // totalShare=normalShare+totalFBshare+totalTwittershare
        $post->totalShare = DB::table('activity_post')
            ->where(['post_id' => $post->id])
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
            ->where(['post_id' => $post->id, 'activity_id' => 4])
            ->select(['id'])
            ->count();
        // get total twitter shared
        $post->totalTwittershare = DB::table('activity_post')
            ->where(['post_id' => $post->id, 'activity_id' => 5])
            ->select(['id'])
            ->count();

        // Create category url and post url..
        $category_name = '';
        if (!empty($post->category)) {
            $category_name = $post->category->category_name;
            $post->category->category_name_url = str_slug_ovr($category_name);
        }

        $subcategory_name = '';
        if (!empty($post->subCategory->category_name)) {
            $subcategory_name = $post->subCategory->category_name;
            $post->subCategory->subcategory_name_url = str_slug_ovr($subcategory_name);
        }

        // create post url         
        $post_url_args = [
            'id' => (int)$child_post_id,
            'caption' => $postCaptions,
            'title' => $post->title,
            'post_type' => $post->post_type,
            'category_name' => $category_name,
            'subcategory_name' => $subcategory_name
        ];

        $post_url = post_url($post_url_args);
        $post->post_url = $post_url;

        // Set unique cardID.
        $post->cardID = $post->id;

        if (!empty($post->image)) {
            if ($initiator == 'share_popup') {
                $post->image = generate_post_image_url('post/thumbs/' . $post->image);
            } else {
                $post->image = generate_post_image_url('post/' . $post->image);
            }

        }

        if (!empty($post->video)) {
            $post->video = generate_post_video_url('video/' . $post->video);
        }
        if (!empty($post->video_poster)) {
            $post->video_poster = generate_post_video_url('video/thumbnail/' . $post->video_poster);
        }

        // For not showing edit post link.
        if ($post_id != $child_post_id) {
            $post->orginal_post_id = $post_id;
        }

        // fetch comment sections ...
        $post->postTotalComment = $postTotalComment;
        $post->postParentComment = Comment::where([
            'post_id' => $post_id,
            'parent_id' => 0])
            ->get()->count();
        $post->child_post_id = $child_post_id;
        // It is execute when post is shared ....
        if ($child_post_id > 0 && $post_id != $child_post_id) {
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

        // Replace place_url if saved as undefined.
        if (!empty($post->place_url) && $post->place_url == 'undefined') {
            $post->place_url = '';
        }

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

        // Set embed url info.
        if (!empty($post->embed_code)) {
            $embedVideoInfo = getEmbedVideoInfo($post->embed_code);
            $post->embed_code_type = $embedVideoInfo['type'];
            $post->videoid = $embedVideoInfo['videoid'];
        }

        $totalBookMark = DB::table('bookmarks')
            ->where([
                'post_id' => $post->id,
            ])
            ->count();

        $post->totalBookMark = $totalBookMark;

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

        $response = [
            'post' => $post
        ];

        return response()->json($response);
    }

    public function viewPostProcess($activity_id, $post_id, $user_id, $initiator = '')
    {
        
        $flag = 0;
        $post = Post::where(['id' => $post_id])->first();
        $user = User::where(['id' => $post->created_by])->first();

        // \DB::connection()->enableQueryLog();

        if (Auth::check()) {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id
            ];
        } else {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id,
                'ip_address' => $this->clientIp
            ];
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
                        'ip_address' => $this->clientIp
                    ];
                    DB::table('activity_post')->insert($postviewArry);
                    $flag = 1;
                }
            } else {
                $postviewArry = [
                    'activity_id' => $activity_id,
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'ip_address' => $this->clientIp
                ];
                DB::table('activity_post')->insert($postviewArry);
                $flag = 1;
            }
        }

        if ($flag == 1) {
            if ($post->created_by != $user_id) {
                // For Image or status post
               /*  comment for change seen algo  (20-12-17)
               
               if (($post->post_type == 1 || $post->post_type == 5) && $initiator != 'viewPost') {
                    $post_point = $activity_id == 8 ? 2 : 1;
                    $post->points = $post->points + $post_point;
                    $post->save();

                    $user->points = $user->points + 1;
                    $user->save();
                } else if ($post->post_type == 2) {   // Post Type 2 :: Video Post
                    $post->points = $post->points + 2;
                    $post->save();

                    $user->points = $user->points + 1;
                    $user->save();
                } else if ($post->post_type == 3) {    // Post Type 3 :: Article Post
                    $post->points = $post->points + 2;
                    $post->save();

                    $user->points = $user->points + 1;
                    $user->save();

                } else if ($post->post_type == 4) {  // Post Type 4 :: Link Post
                    $post->points = $post->points + 2;
                    $post->save();

                    $user->points = $user->points + 1;
                    $user->save();
                }
                
                */

                /******** Add for change seen algo  **********/


                if ( $post->post_type == 1 || $post->post_type == 5 || $post->post_type == 6 ) {
                    $post_point = $activity_id == 8 ? 2 : 1;
                    $post->points = $post->points + $post_point;
                    $post->save();

                    $user->points = $user->points + 1;
                    $user->save();
                } else if ($post->post_type == 2) {   // Post Type 2 :: Video Post
                    $post_point = $activity_id == 11 ? 2 : 1;
                    $post->points = $post->points + $post_point;
                    $post->save();

                    $user->points = $user->points + 1;
                    $user->save();
                } else if ($post->post_type == 3) {    // Post Type 3 :: Article Post
                    $post_point = $activity_id == 9 ? 2 : 1;
                    $post->points = $post->points + $post_point;
                    $post->save();

                    $user->points = $user->points + 1;
                    $user->save();

                } else if ($post->post_type == 4) {  // Post Type 4 :: Link Post
                    $post_point = $activity_id == 10 ? 2 : 1;
                    $post->points = $post->points + $post_point;
                    $post->save();

                    $user->points = $user->points + 1;
                    $user->save();
                }

                /******** Add for change seen algo  **********/ 


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


    /******* write on (22-12-17) for implement new show Post algo  ***********************/

    public function viewPostProcessNEW($activity_id, $post_id, $user_id, $initiator = '')
    {
        
        $flag = 0;
        $post = Post::where(['id' => $post_id])->first();
        $user = User::where(['id' => $post->created_by])->first();

        // \DB::connection()->enableQueryLog();

        if (Auth::check()) {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id
            ];
        } else {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id,
                'ip_address' => $this->clientIp
            ];
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
                        'ip_address' => $this->clientIp
                    ];
                    DB::table('activity_post')->insert($postviewArry);
                    $flag = 1;
                }
            } else {
                $postviewArry = [
                    'activity_id' => $activity_id,
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'ip_address' => $this->clientIp
                ];
                DB::table('activity_post')->insert($postviewArry);
                $flag = 1;
            }
        }

        if ($flag == 1) {
            if ($post->created_by != $user_id) {
              
                $post_point = 2;
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

/******* write on (22-12-17) for implement new show Post algo  ***********************/





    /**
     * Method to record activity id = 8 for post view for non logged in user
     * Author : Alapan Chatterjee; Date:23-01-2017
     */
    public function recordPostView($activity_id, $post_id, $user_id)
    {
        $flag = 0;
        $post = Post::where(['id' => $post_id])->first();

        // \DB::connection()->enableQueryLog();
        if (Auth::check()) {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id
            ];
        } else {
            $cond = [
                'activity_id' => $activity_id,
                'post_id' => $post_id,
                'user_id' => $user_id,
                'ip_address' => $this->clientIp
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
                        'ip_address' => $this->clientIp
                    ];
                    DB::table('activity_post_analytics')->insert($postviewArry);
                    $flag = 1;
                }
            } else {
                $postviewArry = [
                    'activity_id' => $activity_id,
                    'post_id' => $post_id,
                    'user_id' => $user_id,
                    'ip_address' => $this->clientIp
                ];
                DB::table('activity_post_analytics')->insert($postviewArry);
                $flag = 1;
            }
        }

        return $flag;
    }

    public function viewPost(Request $request)
    {
        $post_id = $request->input('postID');
        $child_post_id = $request->input('childPostID');
        $postType = $request->input('postType');

        if ($postType == 1 ) {
            $activity_id = 14; // image
        } else if ($postType == 2) {
            $activity_id = 11; // play
        } else if ($postType == 4) {
            $activity_id = 10; // link
        } else if ($postType == 5) {
            $activity_id = 14; // status
        }else if($postType == 6) {
            $activity_id = 14; // question
        }

        if (Auth::check()) { // for logged in user
            $user_id = Auth::user()->id;
        } else {
            $user_id = 1; // anonymus user
        }

        $flag = $this->viewPostProcess($activity_id, $post_id, $user_id, $initiator = 'viewPost');


        if ($post_id != $child_post_id && $flag == 1) {  // when it is share post .
            $flag = $this->viewPostProcess($activity_id, $child_post_id, $user_id, $initiator = 'viewPost');
            $postID = $child_post_id;
        } else {
            $postID = $post_id;
        }

        $user_collumns = ['id', 'points'];
        $post = Post::where('id', $post_id)
            ->select(['id', 'created_by', 'points', 'upvotes', 'downvotes', 'post_type'])->first();

        if ($child_post_id > 0 && $post_id != $child_post_id)  // It is execute when post is shared ....
        {
            $childPost = Post::where(['id' => $child_post_id])->select('created_by')->first();

            $whereArr = [$childPost->created_by, $post->created_by];
            $getUsers = User::whereIn('id', $whereArr)
                ->orderByRaw("FIELD(id, $childPost->created_by,$post->created_by)")
                ->select($user_collumns)->get();
        } else {
            $getUsers = User::where('id', $post->created_by)
                ->orderByRaw("FIELD(id, $post->created_by)")
                ->select($user_collumns)->get();
        }

        $post->totalPostViews = countPostView($post_id, $post->post_type);
        $post->getUsers = $getUsers;
        //dd($post);
        $data = [
            'post' => $post
        ];
        return $data;
    }


    public function viewSeenPost(Request $request)
    {
        $post_id = $request->input('postID');
        $child_post_id = $request->input('childPostID');
        $postType = $request->input('postType');

        // if ($postType == 1) {
        //     $activity_id = 14; // image
        // } else if ($postType == 2) {
        //     $activity_id = 11; // play
        // } else if ($postType == 4) {
        //     $activity_id = 10; // link
        // } else if ($postType == 5) {
        //     $activity_id = 14; // status
        // }
        $activity_id=14;

        if (Auth::check()) { // for logged in user
            $user_id = Auth::user()->id;
        } else {
            $user_id = 1; // anonymus user
        }

        $flag = $this->viewPostProcess($activity_id, $post_id, $user_id, $initiator = 'viewPost');


        if ($post_id != $child_post_id && $flag == 1) {  // when it is share post .
            $flag = $this->viewPostProcess($activity_id, $child_post_id, $user_id, $initiator = 'viewPost');
            $postID = $child_post_id;
        } else {
            $postID = $post_id;
        }

        $user_collumns = ['id', 'points'];
        $post = Post::where('id', $post_id)
            ->select(['id', 'created_by', 'points', 'upvotes', 'downvotes', 'post_type'])->first();

        if ($child_post_id > 0 && $post_id != $child_post_id)  // It is execute when post is shared ....
        {
            $childPost = Post::where(['id' => $child_post_id])->select('created_by')->first();

            $whereArr = [$childPost->created_by, $post->created_by];
            $getUsers = User::whereIn('id', $whereArr)
                ->orderByRaw("FIELD(id, $childPost->created_by,$post->created_by)")
                ->select($user_collumns)->get();
        } else {
            $getUsers = User::where('id', $post->created_by)
                ->orderByRaw("FIELD(id, $post->created_by)")
                ->select($user_collumns)->get();
        }

        $post->totalPostViews = countPostView($post_id, $post->post_type);
        $post->getUsers = $getUsers;
        //dd($post);
        $data = [
            'post' => $post
        ];
        return $data;
    }


    public function showLoadMoreComments(Request $request)
    {
        $post_id = $request->input('post_id');
        $offset = $request->input('offsetx');
        $sortType = $request->input('sortType');

        if ($sortType == 1) {
            $orderBy = 'desc';
        } else if ($sortType == 2) {
            $orderBy = 'desc';
        } else {
            $orderBy = 'asc';
        }
        if (Auth::check()) {
            $user_id = Auth::user()->id;
        }

        if ($sortType == 1) {
            // $offset=0;

            //  Checking ::
            // Comment author activities always considering.
            $sqlQuery = "SELECT C.id,sum(IF(A.activity_id = '1', 1, NULL)) as `upvotes`, 
                            sum(IF(A.activity_id = '2', 1, NULL)) as `downvotes`, 
                            (upvotes-downvotes) as 'total_upvotes'
                            FROM comments  AS C 
                            JOIN  activity_comment AS A ON C.id=A.comment_id 
                            WHERE (C.post_id='" . $post_id . "' AND C.parent_id is null)
                           
                            AND ((upvotes-downvotes) > 0 AND C.user_id!=A.user_id)
                            GROUP BY C.id ORDER BY (upvotes-downvotes) desc ,C.created_at desc";
            if ($request->has('showAll')) {
                $limit = Comment::where(['post_id' => $post_id])->whereNull('parent_id')->count();
                $sql = $sqlQuery . " LIMIT $offset,$limit";
            } else {
                $sql = $sqlQuery . " LIMIT $offset,$this->loadComments";
            }

            $getActiviesResult = DB::select($sql);

            foreach ($getActiviesResult as $key => $value) {

                $sql = "SELECT C.*,sum(IF(A.activity_id = '1', 1, NULL)) as `upvotes`, 
                            sum(IF(A.activity_id = '2', 1, NULL)) as `downvotes`, 
                            (upvotes-downvotes) as 'total_upvotes'
                            FROM comments  AS C 
                            JOIN  activity_comment AS A ON C.id=A.comment_id 
                            WHERE C.id='" . $getActiviesResult[$key]->id . "' 
                            GROUP BY C.id ORDER BY (upvotes-downvotes),C.created_at desc";

                $result = DB::select($sql);

                $getActiviesResult[$key]->id = $result[0]->id;
                $getActiviesResult[$key]->post_id = $result[0]->post_id;
                $getActiviesResult[$key]->user_id = $result[0]->user_id;
                $getActiviesResult[$key]->message = nl2br($result[0]->message);
                $getActiviesResult[$key]->upvotes = (int)$result[0]->upvotes;
                $getActiviesResult[$key]->downvotes = (int)$result[0]->downvotes;
                $getActiviesResult[$key]->created_at = $result[0]->created_at;
                $getActiviesResult[$key]->updated_at = $result[0]->updated_at;
                $getActiviesResult[$key]->total_upvotes = $result[0]->total_upvotes;
                $getActiviesResult[$key]->user = User::where(['id' => $result[0]->user_id])->first();
            }


            $allComments = $getActiviesResult;

            $postTotalComment = count(DB::select($sqlQuery));
        } else {

            $query = Comment::where(['post_id' => $post_id])->whereNull('parent_id');
            $query->orderBy('id', $orderBy);
            $query->with('user');
            if ($request->has('showAll')) {
                $limit = Comment::where(['post_id' => $post_id])->whereNull('parent_id')->count();
                $query->skip($offset)->take($limit);
            } else {
                $query->skip($offset)->take($this->loadComments);
            }
            $allComments = $query->get();

            $postTotalComment = Comment::where([
                'post_id' => $post_id
            ])->whereNull('parent_id')->get()->count();
        }


        if (count($allComments) > 0) {
            for ($i = 0; $i < count($allComments); $i++) {
                $count_child = Comment::where(['post_id' => $post_id, 'parent_id' => $allComments[$i]->id])->count();
                $allComments[$i]->count_child = $count_child;
                $allComments[$i]->child_comment = [];
                $allComments[$i]->show = 'true';
                $allComments[$i]->textarea = "message" . $allComments[$i]->id;
                $allComments[$i]->message = nl2br($allComments[$i]->message);

                //To check comment upvote and downvote
                if (Auth::check()) {
                    $isUpvote = DB::table('activity_comment')
                        ->where([
                            'comment_id' => $allComments[$i]->id,
                            'activity_id' => 1,
                            'user_id' => Auth::user()->id
                        ])
                        ->select(['id'])
                        ->count();
                    $allComments[$i]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';

                    $isDownvote = DB::table('activity_comment')
                        ->where([
                            'comment_id' => $allComments[$i]->id,
                            'activity_id' => 2,
                            'user_id' => Auth::user()->id
                        ])
                        ->select(['id'])
                        ->count();
                    $allComments[$i]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';
                } else {

                    $isUpvote = 0;
                    $allComments[$i]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';
                    $isDownvote = 0;
                    $allComments[$i]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';

                }
            }
        }

        $postParentComment = Comment::where(['post_id' => $post_id])->whereNull('parent_id')->get()->count();


        $data = [
            'allComments' => $allComments,
            'postTotalComment' => $postTotalComment,
            'postParentComment' => $postParentComment

        ];

        return $data;


    }

    public function loadChildComments(Request $request)
    {
        $commentId = $request->input('commentId');
        $post_id = $request->input('post_id');
        $activeItem = $request->input('activeItem');

        //\DB::connection()->enableQueryLog();

        //if($activeItem==1){
        $allComments = Comment::where(['post_id' => $post_id, 'parent_id' => $commentId])->orderBy('created_at', 'DESC')->with('user')->get();
        //} else
        //{
        //	$allComments = Comment::where(['post_id' => $post_id, 'parent_id' => $commentId])->with('user')->get();
        //}
        //$query = \DB::getQueryLog();
        // $lastQuery = end($query);
        //dd($query);

        $count = count($allComments);

        for ($k = 0; $k < $count; $k++) {
            $count_child = Comment::where(['post_id' => $post_id, 'parent_id' => $allComments[$k]->id])->count();
            $allComments[$k]->count_child = $count_child;
            $allComments[$k]->child_comment = [];
            $allComments[$k]->show = "true";

            //To check comment upvote and downvote
            if (Auth::check()) {
                $isUpvote = DB::table('activity_comment')
                    ->where([
                        'comment_id' => $allComments[$k]->id,
                        'activity_id' => 1,
                        'user_id' => Auth::user()->id
                    ])
                    ->select(['id'])
                    ->count();
                $allComments[$k]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';

                $isDownvote = DB::table('activity_comment')
                    ->where([
                        'comment_id' => $allComments[$k]->id,
                        'activity_id' => 2,
                        'user_id' => Auth::user()->id
                    ])
                    ->select(['id'])
                    ->count();
                $allComments[$k]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';
            } else {

                $isUpvote = 0;
                $allComments[$i]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';
                $isDownvote = 0;
                $allComments[$i]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';

            }
        }

        $data = [
            'allComments' => $allComments
        ];

        return $data;
    }

    /**
     * Share post by other user.
     *
     * @param Request $request
     */
    public function shareThisPost(Request $request)
    {
        $post_id = $request->input('post_id');
        $caption = $request->input('caption');
        $privacy_id = json_decode($request->input('privacy_id'));
        $profile_id = $request->input('profile_id');
        $childPostId = $request->input('childPostId');
        $user_id = Auth::user()->id;

        $activity_id = 3;

        $post = Post::where('id', $post_id)->first();
        // Replicate the post.
        $sharePost = $post->replicate();

        $sharePost->created_by = $user_id;
        $sharePost->parent_post_user_id = $profile_id;
        $sharePost->caption = $caption;
        $sharePost->post_date = time();
        $sharePost->privacy_id = !empty($privacy_id->id) ? $privacy_id->id : 1;
        $sharePost->orginal_post_id = ($post->orginal_post_id != 0) ? $post->orginal_post_id : $post_id;
        $sharePost->ask_anonymous =0;// (16-1-17) when any one share a post make as ask anonymously false
        // Reset points, upvotes, downvotes.
        $sharePost->points = 0;
        $sharePost->upvotes = 0;
        $sharePost->downvotes = 0;

        $sharePost->save();
        // Attach the tags to new shared post.
        $tag_attach_ids = [];
        foreach ($post->tags as $tag) {
            $tag_attach_ids[] = $tag->id;
        }
        $sharePost->tags()->attach($tag_attach_ids);

        // Notification  send to child post user id 
        if ($childPostId > 0 && $childPostId != $post_id) {
            $childPost = Post::where(['id' => $childPostId])->first();

            $activity_childPost = [
                'post_id' => $childPost->id,
                'user_id' => $user_id,
                'activity_id' => $activity_id,
            ];
            DB::table('activity_post')->insert($activity_childPost);

            if ($childPost->created_by != $user_id) {

                $childPost->points = ($childPost->points + 10);
                $childPost->save();

                $notification = [
                    'post_id' => $childPost->id,
                    'user_id' => $user_id,
                    'activity_id' => $activity_id,
                    'post_user_id' => $childPost->created_by,
                    'status' => 1
                ];
                DB::table('notifications')->insert($notification);
            }
        }

        if ($post->created_by != $user_id) {    // Notification always send original post user id

            $notification = [
                'post_id' => $post_id,
                'user_id' => $user_id,
                'activity_id' => $activity_id,
                'post_user_id' => $post->created_by,
                'status' => 1
            ];

            DB::table('notifications')->insert($notification);

            // Immediate post user get 10 points ..
            $post->points = $post->points + 10;
            $post->save();

        }

        $activity_post = [
            'post_id' => $post_id,
            'user_id' => $user_id,
            'activity_id' => $activity_id,
        ];
        DB::table('activity_post')->insert($activity_post);

        // Broadcast post shared event.
        event(new PostShared($post_id));
    }

    //  share post in social  sites

    public function sharedPostInSocialNetworkingForFacebook(Request $request)
    {
        $post_id = $request->input('post_id');
        $child_post_id = $request->input('child_post_id');
        $activityType = $request->input('activityType');

        if (Auth::check()) {
            $user_id = Auth::user()->id;
        } else {
            $user_id = 1; // anonymus user
        }

        // activity_id =4  facebook
        // activity_id =5  twitter

        $post = Post::where('id', $post_id)->first();

        // create activity
        $activity = [
            'post_id' => $post->id,
            'user_id' => $user_id,
            'activity_id' => $activityType,
        ];
        DB::table('activity_post')->insert($activity);


        if ($post->created_by != $user_id) {  // original post get points and notification

            $post->points = $post->points + 10;
            $post->save();


            // send notification
            $notification = [
                'post_id' => $post_id,
                'user_id' => $user_id,
                'activity_id' => $activityType,
                'post_user_id' => $post->created_by,
                'status' => 1
            ];
            DB::table('notifications')->insert($notification);
        }

        if ($post_id != $child_post_id) { //  child post

            $child_post = Post::where('id', $child_post_id)->first();

            // create child activity
            $child_activity = [
                'post_id' => $child_post->id,
                'user_id' => $user_id,
                'activity_id' => $activityType,
            ];
            DB::table('activity_post')->insert($child_activity);

            if ($child_post->created_by != $user_id) {
                $child_post->points = $child_post->points + 10;
                $child_post->save();
            }
        }

        // Broadcast post shared event.
        event(new PostShared($post_id));
    }

    ///  facebook ...

    public function followUnfollow(Request $request)
    {
        $user_id = $request->input('user_id');
        $follower_id = Auth::user()->id;

        $arr = [
            'user_id' => $user_id,
            'follower_id' => $follower_id
        ];

        $getFollower = DB::table('followers')->where($arr)->first();

        if ($getFollower != null) {
            $follow = [
                'user_id' => $user_id,
                'follower_id' => $follower_id
            ];

            DB::table('followers')->where($follow)->delete();
            $following = 0;
            $getFollower = $getFollower->id;
        } else {
            $follow = [
                'user_id' => $user_id,
                'follower_id' => $follower_id
            ];
            $insertGetId = DB::table('followers')->insertGetId($follow);


            $following = 1;

            $user_collumns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];

            $getFollower = Follower::where(['id' => $insertGetId])
                ->with([
                    'followed_by' => function ($query) use ($user_collumns) {
                        $query->addSelect($user_collumns);
                    }
                ])->first();

        }

        $data = [
            "following" => $following,
            "follower" => $getFollower
        ];

        return $data;

    }

    public function profileTabData(Request $request)
    {
        $type = $request->input('type');
        $user_id = $request->input('user_id');
        $follwer_collumns = ['id', 'user_id', 'follower_id'];
        $user_collumns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
        $follower = null;

        switch ($type) {
            case 'follower':
                $follower = Follower::where('user_id', $user_id)
                    ->with([
                        'followed_by' => function ($query) use ($user_collumns) {
                            $query->addSelect($user_collumns);
                        }
                    ])
                    ->get($follwer_collumns);
                $data = [
                    "follower" => $follower
                ];
                // Clear follower notifications.
                Follower::where('user_id', $user_id)->update(['status' => 3]);
                break;

            case 'following':

                $following = Follower::where('follower_id', $user_id)
                    ->with([
                        'following_by' => function ($query) use ($user_collumns) {
                            $query->addSelect($user_collumns);
                        }
                    ])
                    ->get($follwer_collumns);

                $data = [
                    "following" => $following
                ];
                break;

            case 'categories' :

                $categories = Category::where('parent_id', 0)->with('childCat')->get();

                $data = [
                    "categories" => $categories
                ];
                break;

            default :

                die('Invalid Request..........');

                break;

        }
        return $data;
    }

    public function deleteMyPost(request $request)
    {
        $post_id = $request->input('post_id');

        $post = Post::find($post_id);
        // delete files for original post.
        if ($post->orginal_post_id === null) {
            if (!empty($post->image)) {
                Storage::delete(['/post/' . $post->image, '/post/thumbs/' . $post->image]);
            }
            if (!empty($post->video)) {
                Storage::delete(['/video/' . $post->video, '/video/thumbnail/' . $post->video]);
            }
        }

        $post->delete();

        return ['success' => 1];
    }

    public function doPostReport(request $request)
    {
        $post_id = $request->input('post_id');
        $report_id = $request->input('report_id');
        $user_id = Auth::user()->id;

        $post_report = [
            'post_id' => $post_id,
            'report_id' => $report_id,
            'user_id' => $user_id
        ];

        $getData = DB::table('post_report')->where($post_report)->first();

        if ($getData !== null) {
            DB::table('post_report')->where($post_report)->delete();
        } else {
            // Remove all previous reports for the post by the user.
            DB::table('post_report')->where([
                'post_id' => $post_id,
                'user_id' => $user_id
            ])->delete();

            DB::table('post_report')->insert($post_report);

            $post_column = ['id', 'title', 'caption', 'created_by'];
            $user_columns = ['id', 'username', 'first_name', 'last_name'];
            $post = Post::where('id', $post_id)
                ->with([
                    'user' => function ($query) use ($user_columns) {
                        $query->addSelect($user_columns);
                    }
                ])
                ->first($post_column);


            $report = Report::find($report_id);

            $email_data = [
                'post' => $post,
                'report' => $report
            ];

            /* Send email to admin */
            $to = 'swolk.com@gmail.com';
//            $to = 'tuhin@technoexponent.com';

            Mail::send('emails.report.post', $email_data, function ($m) use ($to) {
                $m->from('admin@swolk.com', 'Swolk');

                $m->to($to)->subject('Post Reported');
            });
        }

    }

    public function doCommentReport(request $request)
    {
        $comment_id = $request->input('commentId');
        $report_id = $request->input('reportId');
        $user_id = Auth::user()->id;

        $comment_report = [
            'comment_id' => $comment_id,
            'report_id' => $report_id,
            'user_id' => $user_id
        ];

        $getData = DB::table('comment_report')->where($comment_report)->first();

        if ($getData !== null) {
            DB::table('comment_report')->where($comment_report)->delete();
        } else {
            // Remove all previous reports for the post by the user.
            DB::table('comment_report')->where([
                'comment_id' => $comment_id,
                'user_id' => $user_id
            ])->delete();

            DB::table('comment_report')->insert($comment_report);

            $comment_column = ['id', 'post_id', 'user_id', 'message'];
            $post_column = ['id', 'title', 'caption', 'created_by'];
            $user_columns = ['id', 'username', 'first_name', 'last_name'];
            $comment = Comment::where('id', $comment_id)
                ->with([
                    'post' => function ($query) use ($post_column) {
                        $query->addSelect($post_column);
                    },
                    'user' => function ($query) use ($user_columns) {
                        $query->addSelect($user_columns);
                    }
                ])
                ->first($comment_column);

            $report = Report::find($report_id);

            $email_data = [
                'comment' => $comment,
                'report' => $report
            ];

            /* Send email to admin */
            $to = 'swolk.com@gmail.com';
//            $to = 'tuhin@technoexponent.com';

            Mail::send('emails.report.comment', $email_data, function ($m) use ($to) {
                $m->from('admin@swolk.com', 'Swolk');

                $m->to($to)->subject('Comment Reported');
            });
        }

    }

    public function bookmark(request $request)
    {
        $postID = $request->input('postID');
        $user_id = Auth::user()->id;

        $dataArray = [
            'post_id' => $postID,
            'user_id' => $user_id
        ];

        $isBookMark = DB::table('bookmarks')
            ->where($dataArray)
            ->select(['id'])
            ->count();


        if ($isBookMark > 0) {
            DB::table('bookmarks')
                ->where($dataArray)
                ->delete();
        } else {

            DB::table('bookmarks')
                ->insert($dataArray);
        }

        // Broadcast bookmark event.
        event(new PostBookMarked($postID));
    }

    public function followerTemplate()
    {

        return view('tpl.profile.tpl_follower');
    }

    public function followingTemplate()
    {

        return view('tpl.profile.tpl_following');
    }

    public function postsTemplate()
    {
        return view('tpl.profile.tpl_posts');
    }

    public function followcategories()
    {
        return view('tpl.profile.tpl_categories');
    }

    public function collections()
    {
        return view('tpl.profile.tpl_collections');
    }

    public function postcardmodal()
    {
        return view('tpl.profile.tpl_postcardmodal');
    }

    public function exploreTab()
    {
        return view('tpl.profile.tpl_explore_tab');
    }

    public function deletePostModal()
    {
        return view('tpl.profile.tpl_deletePostModal');
    }

    public function reportPostModal()
    {
        return view('tpl.profile.tpl_reportPostModal');
    }

    public function reportCommentModal()
    {
        return view('tpl.profile.tpl_reportCommentModal');
    }

    public function commentDeleteModal()
    {
        return view('tpl.profile.tpl_commentDeleteModal');
    }

    public function promptSinginBox()
    {
        return view('tpl.profile.tpl_promptSinginBox');
    }

    public function savepostJson(Request $request)
    {
        $totalPost = 0;
        $posts = [];

        // \DB::connection()->enableQueryLog();
        $user_id = Auth::user()->id;
        if ($this->filter_post_type > 0) {
            $arr = [
                'posts.post_type' => $this->filter_post_type,
                'bookmarks.user_id' => $user_id];
        } else {
            $arr = ['bookmarks.user_id' => $user_id];
        }

        $bookMarkQuery = DB::table('bookmarks')
            ->join('posts', 'posts.id', '=', 'bookmarks.post_id')
            ->where($arr)
            ->skip($this->offset)
            ->take($this->per_page)
            ->orderBy('bookmarks.created_at', 'DESC')
            ->get();

        $searchBookMark = array_pluck($bookMarkQuery, 'post_id');


        if (count($searchBookMark) > 0) {

            $arrayImplode = implode(",", $searchBookMark);
            // \DB::connection()->enableQueryLog();

            $post = Post::whereIn('id', $searchBookMark);
            // Add filter based on post type.
            if ($this->filter_post_type > 0) {
                $post->where('post_type', $this->filter_post_type);
            }

            // Count total posts..
            $totalPost = $post->count();
            // Column selection array for eager loaded data.
            $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
            $category_columns = ['id', 'category_name'];
            $subCategory_columns = ['id', 'category_name'];
            $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
            $featurePhotoDetail_column = ['id', 'post_id', 'thumb_width', 'thumb_height', 'data_url'];

            // Eager load data from relations.
            $post->with([
                'user' => function ($query) use ($user_columns) {
                    $query->addSelect($user_columns);
                },
                'category' => function ($query) use ($category_columns) {
                    $query->addSelect(array('id', 'category_name'));
                },
                'subCategory' => function ($query) use ($subCategory_columns) {
                    $query->addSelect(array('id', 'category_name'));
                },
                'tags' => function ($query) use ($tag_columns) {
                    $query->addSelect($tag_columns);
                },
                'featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                    $query->addSelect($featurePhotoDetail_column);
                },

                'parentPostUser' => function ($query) use ($user_columns) {
                    $query->addSelect($user_columns);
                },
                'orginalPost.user' => function ($query) use ($user_columns) {
                    $query->addSelect($user_columns);
                },
                'orginalPost.category' => function ($query) use ($category_columns) {
                    $query->addSelect($category_columns);
                },
                'orginalPost.subCategory' => function ($query) use ($category_columns) {
                    $query->addSelect($category_columns);
                },
                'orginalPost.tags' => function ($query) use ($tag_columns) {
                    $query->addSelect($tag_columns);
                }
            ])->withCount('comment');
            $post->orderByRaw("FIELD(id, $arrayImplode)");
            // Get the paginated result.
            // $posts = $post->skip($this->offset)->take($this->per_page)->get();

            $posts = $post->get()->makeVisible('people_here');
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


        } else {
            $posts = [];
        }
        /*============== Modify post data ================*/
        $post_count = count($posts);

        $final_posts = [];
        for ($p = 0; $p < $post_count; $p++) {
            $parent_post_user_id = $posts[$p]->parent_post_user_id;
            $current_post = null;

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
            // total comments
            $totalComments = Comment::where(['post_id' => $posts[$p]->id])->whereNull('parent_id')->count();


            $posts[$p]->totalComments = $totalComments;
            $posts[$p]->totalPostViews = countPostView($posts[$p]->id, $posts[$p]->post_type);
            $totalShare = DB::table('activity_post')
                ->where(['post_id' => $posts[$p]->id, 'activity_id' => 3])
                ->select(['id'])
                ->count();
            $posts[$p]->totalShare = $totalShare;
            $posts[$p]->child_post_id = $posts[$p]->id;
            $posts[$p]->child_post_user_id = $posts[$p]->created_by;

            $isBookMark = DB::table('bookmarks')
                ->where([
                    'post_id' => $posts[$p]->id,
                    'user_id' => Auth::user()->id
                ])
                ->count();
            $posts[$p]->isBookMark = ($isBookMark != 0) ? 'Y' : 'N';

            $isUpvote = DB::table('activity_post')
                ->where([
                    'post_id' => $posts[$p]->id,
                    'activity_id' => 1,
                    'user_id' => Auth::user()->id
                ])
                ->select(['id'])
                ->count();
            $posts[$p]->isUpvote = ($isUpvote != 0) ? 'Y' : 'N';

            $isDownvote = DB::table('activity_post')
                ->where([
                    'post_id' => $posts[$p]->id,
                    'activity_id' => 2,
                    'user_id' => Auth::user()->id
                ])
                ->select(['id'])
                ->count();
            $posts[$p]->isDownvote = ($isDownvote != 0) ? 'Y' : 'N';

            // Set to final post.
            $final_posts[$p] = $posts[$p];
            $final_posts[$p]['cardID'] = $posts[$p]->id;
            // Set embed url info.
            if (!empty($final_posts[$p]->embed_code)) {
                $embedVideoInfo = getEmbedVideoInfo($final_posts[$p]->embed_code);
                $final_posts[$p]->embed_code_type = $embedVideoInfo['type'];
                $final_posts[$p]->videoid = $embedVideoInfo['videoid'];
            }

            if (!empty($final_posts[$p]->image)) {
                $final_posts[$p]->image = generate_post_image_url('post/thumbs/' . $final_posts[$p]->image);
            } else if (!empty($final_posts[$p]['image'])) {
                $final_posts[$p]['image'] = generate_post_image_url('post/thumbs/' . $final_posts[$p]['image']);
            }

            if (!empty($final_posts[$p]->video)) {
                $final_posts[$p]->video = generate_post_image_url('video/' . $final_posts[$p]->video);
            }
            if (!empty($final_posts[$p]->video_poster)) {
                $final_posts[$p]->video_poster = generate_post_image_url('video/thumbnail/' . $final_posts[$p]->video_poster);
            }

            if (!empty($final_posts[$p]->feature_photo_detail)) {
                $percentage = ($final_posts[$p]->feature_photo_detail->thumb_height /
                        $final_posts[$p]->feature_photo_detail->thumb_width) * 100;
                $final_posts[$p]->feature_photo_detail->percentage = round($percentage, 2);
            }
        }

        /*************Fetch Distance Between Post Location And End User Location****************/
        $userLocationInfo = Session::get('userLocationInfo');
        if ($userLocationInfo !== null && $request->input('userLocationSaved') == "true") {
            $final_posts = addPostDistance($userLocationInfo, $final_posts);
        }/**/
        /*************Fetch Distance Between Post Location And End User Location****************/

        $response = [
            'totalPost' => $totalPost,
            'posts' => $final_posts
        ];
        return response()->json($response);
    }

    public function inviteFriend()
    {
        return view('inviteFriend.index');
    }

    public function sendFeedback()
    {
        return view('feedback.index');
    }

    public function twitterConnect($id = '')
    {
        $consumer_key = config('services.twitter.consumer_key');
        $consumer_key_secret = config('services.twitter.consumer_key_secret');
        $callback_url = config('services.twitter.callback_url');

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
            throw new \Exception('There was a problem performing this request');
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

    public function twitter_oauth(Request $request)
    {
        $consumerkey = config('services.twitter.consumer_key');
        $consumer_secret = config('services.twitter.consumer_key_secret');

        $oauth_verifier = $_GET['oauth_verifier'];
        Session::put('oauth_verifier', $oauth_verifier);

        if (Auth::check()) {

            $oauth_token = Session::get('oauth_token');
            $oauth_token_secret = Session::get('oauth_token_secret');

            $conn = new TwitterOAuth($consumerkey, $consumer_secret, $oauth_token, $oauth_token_secret);

            $token = $conn->oauth("oauth/access_token", array("oauth_verifier" => $oauth_verifier));
            $access_token = $token['oauth_token'];  // access token
            $access_token_secret = $token['oauth_token_secret']; // access token secret

            $user_id = Auth::user()->id;
            $user = User::where(['id' => $user_id])->first();
            $user->twitter_token = $oauth_token;
            $user->twitter_access_token = $access_token;
            $user->twitter_access_tokensecret = $access_token_secret;
            $user->save();
        }

        return view('profile/twitterConnect');
    }

    public function postToTwitter(Request $request)
    {

        $consumerkey = config('services.twitter.consumer_key');
        $consumer_secret = config('services.twitter.consumer_key_secret');
        $callback_url = config('services.twitter.callback_url');
        $post_id = $request->input('post_id');
        $child_post_id = $request->input('child_post_id');

        $del = 0; //  for 1  need to be deleted.

        if (Auth::check()) {
            $user = User::where(['id' => Auth::user()->id])
                ->select(['twitter_access_token', 'twitter_access_tokensecret'])
                ->first();

            $access_token = $user->twitter_access_token;    // access token

            $access_token_secret = $user->twitter_access_tokensecret; // access token secret
        } else {
            // Get access token and access token secret from twitter  for anonymous user

            $oauth_verifier = Session::get('oauth_verifier');
            $oauth_token = Session::get('oauth_token');
            $token_secret = Session::get('oauth_token_secret');

            $conn = new TwitterOAuth($consumerkey, $consumer_secret, $oauth_token, $token_secret);
            $token = $conn->oauth("oauth/access_token", array("oauth_verifier" => $oauth_verifier));


            $access_token = $token['oauth_token'];  // access token
            $access_token_secret = $token['oauth_token_secret']; // access token secret
        }

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
            $media = generate_post_image_url('post/thumbs/' . $post->image);
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
                    $media = generate_post_image_url('video/thumbnail/' . $post->video_poster);
                } else {
                    $media = '';
                }
            }
        } else if ($post->post_type == 5) {
            if (!empty($post->image)) {
                $media = generate_post_image_url('post/thumbs/' . $post->image);
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

        $parameters['status'] = strip_tags($title) . ' ' . $post->post_url;

        if ($media != '') {
            $arr = explode("/", $media);
            $original_name = end($arr);
            $image_ext = pathinfo($original_name, PATHINFO_EXTENSION);

            if ($image_ext == "gif") {  // convert gif to jpeg.

                $save_name = generateFileName($original_name) . '.' . $image_ext;
                $upload_filename = $media; // destination file
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
                $del = 0;
                $media_file = $media;
            }

            $getMedia = $connection->upload('media/upload', ['media' => $media_file]);

            $parameters['media_ids'] = $getMedia->media_id_string;
        }

        $result = $connection->post('statuses/update', $parameters);

        if ($connection->getLastHttpCode() == 200) {
            $activityType = 5; // shared post to twitter ..
            $this->sharedPostInSocialNetworking($post->id, $child_post_id, $activityType);
        }

        if ($del == 1) {
            unlink($converted_filename);
        }

        Session::forget('oauth_verifier');
        Session::forget('oauth_token');
        Session::forget('oauth_token_secret');

        // Broadcast post shared event.
        event(new PostShared($post_id));
    }

    public function sharedPostInSocialNetworking($post_id, $child_post_id, $activityType)
    {
        // $post_id = $request->input('post_id');
        //$child_post_id = $request->input('child_post_id');
        //$activityType = $request->input('activityType');

        if (Auth::check()) {
            $user_id = Auth::user()->id;
        } else {
            $user_id = 1; // anonymus user
        }

        // activity_id =4  facebook
        // activity_id =5  twitter

        $post = Post::where('id', $post_id)->first();

        // create activity
        $activity = [
            'post_id' => $post->id,
            'user_id' => $user_id,
            'activity_id' => $activityType,
        ];
        DB::table('activity_post')->insert($activity);


        if ($post->created_by != $user_id) {  // original post get points and notification

            $post->points = $post->points + 10;
            $post->save();


            // send notification
            $notification = [
                'post_id' => $post_id,
                'user_id' => $user_id,
                'activity_id' => $activityType,
                'post_user_id' => $post->created_by,
                'status' => 1
            ];
            DB::table('notifications')->insert($notification);
        }

        if ($post_id != $child_post_id) { //  child post

            $child_post = Post::where('id', $child_post_id)->first();

            // create child activity
            $child_activity = [
                'post_id' => $child_post->id,
                'user_id' => $user_id,
                'activity_id' => $activityType,
            ];
            DB::table('activity_post')->insert($child_activity);

            if ($child_post->created_by != $user_id) {
                $child_post->points = $child_post->points + 10;
                $child_post->save();
            }
        }
    }
    /*
    public function twitter_oauth(Request $request)
    {       
        $consumerkey=config('services.twitter.consumer_key');
        $consumer_secret=config('services.twitter.consumer_key_secret');
        $callback_url =  config('services.twitter.callback_url');

        $oauth_verifier = $_GET['oauth_verifier'];
        $oauth_token = Session::get('oauth_token');
        $token_secret = Session::get('oauth_token_secret');


        $conn = new TwitterOAuth($consumerkey, $consumer_secret, $oauth_token, $token_secret);
        $token = $conn->oauth("oauth/access_token", array("oauth_verifier" => $oauth_verifier));


        $access_token =	 $token['oauth_token'];	 // access token
        $access_token_secret = $token['oauth_token_secret']; // access token secret

        $post_id= Session::get('post_id');

        $category_collumns = ['id', 'category_name'];
        $subCategory_collumns = ['id', 'category_name'];

        $post=Post::where('id',$post_id)
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


$content = $connection->get("account/verify_credentials");



        if($post->post_type==5) {
            $title = $post->caption;
        } else {
            if($post->title!=''){
                $title = $post->title;
            } else {
                $title = $post->caption;
            }
        }
        $media = '';
        if($post->post_type==1 || $post->post_type==3 || $post->post_type==4) {

            if(check_file_exists($post->image,'image_post')){
                $media = asset('uploads/post/'.$post->image);
            } else {
                $media = '';
            }

        } else  if($post->post_type==2) {

            if($post->embed_code!='') {    // for embed code
                if($post->embed_code_type =='youtube'){

                    $media='https://img.youtube.com/vi/'.$post->videoid.'/0.jpg';

                } else if($post->embed_code_type =='dailymotion'){

                    $media='http://www.dailymotion.com/thumbnail/video/'.$post->videoid;

                } else if($post->embed_code_type =='vimeo') {
                    $media='https://i.vimeocdn.com/video/'.$post->videoid.'_640.jpg';
                }
            } else  if($post->video!='') { // html5

                if(check_file_exists($post->video_poster,'video_post')){
                    $media = asset('uploads/video/thumbnail/'.$post->video_poster);
                } else {
                    $media = '';
                }
            }
        } else if($post->post_type == 5) {
            if($post->image != ''){
                if(check_file_exists($post->image,'image_post')){
                    $media = asset('uploads/post/'.$post->image);
                } else {
                    $media = "";
                }
            } else  {
                if($post->embed_code != '') {    // for embed code
                    if($post->embed_code_type =='youtube'){

                        $media = 'https://img.youtube.com/vi/'.$post->videoid.'/0.jpg';

                    } else if($post->embed_code_type =='dailymotion'){

                        $media = 'http://www.dailymotion.com/thumbnail/video/'.$post->videoid;

                    } else if($post->embed_code_type =='vimeo') {
                        $media = 'https://i.vimeocdn.com/video/'.$post->videoid.'_640.jpg';
                    }
                } else  if($post->video!='') { // html5
                    if(check_file_exists($post->video_poster,'video_post')){
                        $media = asset('uploads/video/thumbnail/'.$post->video_poster);
                    } else {
                        $media = '';
                    }
                }
            }
        }


        $parameters['status'] = $title.' '.$post->post_url;


        if($media!=''){
            $arr=explode("/", $media);
            $original_name = end($arr);
            $image_ext = pathinfo($original_name, PATHINFO_EXTENSION);

            if ($image_ext == "gif"){  // convert gif to jpeg.

                $save_name = generateFileName($original_name) . '.' . $image_ext;
                $upload_filename=$media; // distination file
                $converted_filename = public_path() . '/uploads/post/'.$save_name;


                if ($image_ext=="gif") $new_pic = imagecreatefromgif($upload_filename);


                $w = imagesx($new_pic);
                $h = imagesy($new_pic);


                $white = imagecreatetruecolor($w, $h);


                $bg = imagecolorallocate($white, 255, 255, 255);
                imagefill($white, 0, 0, $bg);

                imagecopy($white, $new_pic, 0, 0, 0, 0, $w, $h);

                $new_pic = $white;

                imagejpeg($new_pic, $converted_filename);
                imagedestroy($new_pic);

                $media_file=asset('uploads/post/'.$save_name);

            } else {
                $media_file=$media;
            }

            $getMedia = $connection->upload('media/upload', ['media' => $media_file]);
            $parameters['media_ids' ] =  $getMedia->media_id_string;
        }

        $result = $connection->post('statuses/update', $parameters);
        Session::forget('post_id');

        return view('profile/twitterConnect');
    }  */
    // verify twitter access  token ...

    public function accessTokenVerify()
    {
        $consumerkey = config('services.twitter.consumer_key');
        $consumer_secret = config('services.twitter.consumer_key_secret');
        $flag = 0;

        if (Auth::check()) {


            $user_id = Auth::user()->id;
            $user = User::where(['id' => $user_id])
                ->select(['twitter_access_token', 'twitter_access_tokensecret'])
                ->first();

            $connection = new TwitterOAuth($consumerkey, $consumer_secret, $user->twitter_access_token, $user->twitter_access_tokensecret);

            $content = $connection->get("account/verify_credentials");

            if ($connection->getLastHttpCode() == 200) {
                $flag = 1;
            } else {
                $flag = 0;
            }
        }
        return $flag;
    }

    // verify twitter access  token ...
    public function facebookAccessTokenVerify()
    {
        $consumerkey = config('services.twitter.consumer_key');
        $consumer_secret = config('services.twitter.consumer_key_secret');
        $flag = 0;

        if (Auth::check()) {


            $user_id = Auth::user()->id;
            $user = User::where(['id' => $user_id])
                ->select(['twitter_access_token', 'twitter_access_tokensecret'])
                ->first();


            if ($connection->getLastHttpCode() == 200) {
                $flag = 1;
            } else {
                $flag = 0;
            }
        }
        return $flag;
    }

    public function facebookLogin()
    {

        session_start();
        $fb = new Facebook\Facebook([
            'app_id' => '1050589168353691',
            'app_secret' => '88ba6142e6271a336aea5b87b2b84084',
            'default_graph_version' => 'v2.8',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email', 'public_profile']; // Optional permissions
        $loginUrl = $helper->getLoginUrl('https://swolk.com/facebookCallback', $permissions);

        //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
        return redirect($loginUrl);
    }

    public function facebookCallback()
    {
        session_start();
        $fb = new Facebook\Facebook([
            'app_id' => '1050589168353691',
            'app_secret' => '88ba6142e6271a336aea5b87b2b84084',
            'default_graph_version' => 'v2.8',
        ]);

        $helper = $fb->getRedirectLoginHelper();


        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }
        $oAuth2Client = $fb->getOAuth2Client();

        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        //echo '<h3>Metadata</h3>';
        //var_dump($tokenMetadata);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId('1050589168353691');
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }

            echo '<h3>Long-lived</h3>';
            var_dump($accessToken->getValue());
        }

        $accessToken = (string)$accessToken;
        echo $accessToken;
        //$accessToken ="EAACEdEose0cBAL90FzVX3m4WzDTefPZBWIc7bGaCZA9FrKNViZCCfuthGa1tqPvaH8q5VAC1NqMEKJZCPPRbvkNbZCMRXYfLUQtxtTkIavYK77l4fZCZBEz9MB3x6zEkxMbufFbOgnpTUFztpRsVFXsYzDrltV716wlnHoUFuNMCwZDZD";
        $res = $fb->get('/me?fields=id,name', $accessToken);

        $fbUser = $res->getGraphUser();

        print_r($fbUser);

        $user = User::where(['id' => Auth::user()->id])->first();


        $user->facebook_token = $fbUser['id'];
        $user->facebook_access_token = $accessToken;
        $user->save();


    }

    public function updateFacebookCredentails(Request $request)
    {
        $accessToken = $request->input('accessToken');

        $user = User::where(['id' => Auth::user()->id])->first();
        $user->facebook_access_token = $accessToken;
        $user->save();
    }

}
