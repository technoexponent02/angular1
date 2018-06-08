<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use Session;

use App\Models\Category;
use App\Models\Country;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Follower;
use Carbon\Carbon;

use Illuminate\Http\Request;

class FeedController extends Controller
{

    protected $per_page;
    protected $offset;

    protected $allowed_post_types;
    protected $filter_post_type;

    /**
     * FeedController constructor
     */
    public function __construct(Request $request)
    {
        /*--- build the pagination logic ---*/
        $this->per_page = config('constants.PER_PAGE');
        $this->per_page = 10;
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        
        return view('feed.index');
    }

    /**
     * Method to get random recommendation
     */
    public function getRandomRecommendation()
    {

        $logged_in_user_id = Auth::user()->id;
        $category = array();
        $cat = array('category', 'tag', 'location');
        $count = [];
        $no_data_found = false;
        $randomCategory = null;
        $getResult = [];

        $currentDateTime = Carbon::now()->toDateTimeString();
        $prevMonthDateTime = Carbon::now()->addDays(-30);

        //Check For Post Availability based on user followed category
        $sql_category_check = "SELECT `CF`.`category_id` as `id` FROM `category_follower` as `CF` INNER JOIN `posts` as `p` WHERE (`p`.`category_id`=`CF`.`category_id` OR `p`.`sub_category_id`=`CF`.`category_id`) AND `p`.`created_by` NOT IN (SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `p`.`created_by` != '$logged_in_user_id' AND `p`.`id` IN(SELECT `p1`.`id` FROM `posts` as `p1` INNER JOIN `activity_post` as `ap` ON `p1`.`id`=`ap`.`post_id` WHERE `ap`.`activity_id` IN(1,3,4,5,6,8,9,10,11) AND `ap`.`created_at` >= '$prevMonthDateTime' AND `ap`.`created_at` <= '$currentDateTime') AND `CF`.`follower_id`='$logged_in_user_id' AND `p`.`orginal_post_id` IS NULL GROUP BY `CF`.`category_id`";

        $get_category_post = DB::select($sql_category_check);

        $get_category_post_count = count($get_category_post);


        //Check For Post Availability based on user followed tags
        $sql_tag_check = "SELECT `pt`.`tag_id` as `id` FROM `tag_user` as `tu` INNER JOIN `post_tag` as `pt` ON `tu`.`tag_id`=`pt`.`tag_id` INNER JOIN `posts` as `p` ON `pt`.`post_id`=`p`.`id` WHERE `p`.`created_by` NOT IN(SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `p`.`created_by` != '$logged_in_user_id' AND `tu`.`user_id`='$logged_in_user_id' AND `p`.`id` IN(SELECT `p1`.`id` FROM `posts` as `p1` INNER JOIN `activity_post` as `ap` ON `p1`.`id`=`ap`.`post_id` WHERE `ap`.`activity_id` IN(1,3,4,5,6,8,9,10,11) AND `ap`.`created_at` >= '$prevMonthDateTime' AND `ap`.`created_at` <= '$currentDateTime') AND `p`.`orginal_post_id` IS NULL GROUP BY `pt`.`tag_id`";

        $get_tag_post = DB::select($sql_tag_check);

        $get_tag_post_count = count($get_tag_post);

        //Check for post availability based on user followed location
        $sql_location_check = "SELECT `pf`.`location`, `pf`.`city`, `pf`.`state`, `pf`.`country`, `c`.`country_code`, `pf`.`place_url`, `p`.`title`, `p`.`created_by`,`p`.`place_url` FROM `place_follower` as `pf` LEFT JOIN `countries` as `c` ON `pf`.`country`=`c`.`country_name` INNER JOIN `posts` as `p` ON `pf`.`place_url`=`p`.`place_url` WHERE `pf`.`user_id`='$logged_in_user_id' AND `p`.`created_by` != '$logged_in_user_id' AND `p`.`created_by` NOT IN (SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `p`.`orginal_post_id` IS NULL";


        $get_location_post = DB::select($sql_location_check);

        $get_location_post_count = count($get_location_post);

        if ($get_category_post_count > 0) {
            $count[] = 0;
        }
        if ($get_tag_post_count > 0) {
            $count[] = 1;
        }
        if ($get_location_post_count > 0) {
            $count[] = 2;
        }

        if (!empty($count)) {
            shuffle($count);
            $index = $count[0];
          
        } else {
            $no_data_found = true;
        }
$i=0;
       do{
       
         //$index = $count[$i];

        if(array_key_exists($i,$count))
        {
            $index = $count[$i];
        }
        else
        {
            $no_data_found = true;
        }


        if (!$no_data_found) {
            switch ($cat[$index]) {
                case 'category':
                    shuffle($get_category_post);
                    $randomCategory = DB::table('categories')->select('categories.category_name', 'categories.id')->where('categories.id', '=', '' . $get_category_post[0]->id . '')->first();
                    $randomCategoryId = $randomCategory->id;
                    $getResult = $this->getRandomUser($randomCategoryId, 50);
                    break;

                case 'tag':
                    shuffle($get_tag_post);
                   // $randomCategory = DB::table('tags')->select('tags.tag_name as category_name', 'tags.id')->where('tags.id', '=', '' . $get_tag_post[0]->id . '')->first();
                   $randomCategory = DB::table('tags')->select('tags.tag_text as category_name', 'tags.id')->where('tags.id', '=', '' . $get_tag_post[0]->id . '')->first();
                    $tagId = $randomCategory->id;
                    $getResult = $this->getRandomUserPostOnTags($tagId, 50);
                    break;

                case 'location':

                    /**
                     * Try to fetch user recommendation from user followed location
                     */
                    $sql_location = "SELECT `pf`.`location`, `pf`.`city`, `pf`.`state`, `pf`.`country`, `c`.`country_code`, `pf`.`place_url`,`pf`.`region`  FROM `place_follower` as `pf` LEFT JOIN `countries` as `c` ON `pf`.`country`=`c`.`country_name` WHERE `pf`.`user_id`='$logged_in_user_id'";
                    $location = DB::select($sql_location);

                    if (!empty($location)) {
                        shuffle($location);

                        foreach ($location as $key => $loc) {
                            $condition = [];

                            if (!empty($loc->location) && $loc->location!='' ) {
                                $condition[] = "`p`.`location`='" . $loc->location . "'";  //for get result if location filed fetch is null
                            }

                            if (!empty($loc->city)) {
                                $condition[] = "`p`.`city`='" . $loc->city . "'";
                            }

                            if (!empty($loc->state)) {
                                $condition[] = "`p`.`state`='" . $loc->state . "'";
                            }

                            if (!empty($loc->country_code)) {
                                $condition[] = "`p`.`country_code`='" . $loc->country_code . "'";
                            }

                            if(!empty($loc->place_url)){
                                $condition[] = "`p`.`place_url`='".$loc->place_url."'"; 
                            }

                            $condition = implode(' AND ', $condition);

                            if($condition)
                            {
                                 $sql_posts = "SELECT `p`.`created_by`, count(DISTINCT(`p`.`id`)) as `totalPosts`, count(`p`.`points`) as `totalPoints`, `users`.`first_name`,`users`.`last_name`, `users`.`username`, `users`.`profile_image`, `users`.`cover_image`, `users`.`about_me`, `users`.`cover_image` FROM `posts` as `p` INNER JOIN `users` as `users` ON `p`.`created_by`=`users`.`id` WHERE " . $condition . " AND `p`.`created_by`!='$logged_in_user_id' AND `p`.`created_by` NOT IN (SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `p`.`orginal_post_id` IS NULL GROUP BY `p`.`created_by` HAVING `totalPosts` > '0' ORDER BY `totalPoints`,`totalPosts` DESC LIMIT 0,50";
                            }
                            else
                            {
                                $sql_posts = "SELECT `p`.`created_by`, count(DISTINCT(`p`.`id`)) as `totalPosts`, count(`p`.`points`) as `totalPoints`, `users`.`first_name`,`users`.`last_name`, `users`.`username`, `users`.`profile_image`, `users`.`cover_image`, `users`.`about_me`, `users`.`cover_image` FROM `posts` as `p` INNER JOIN `users` as `users` ON `p`.`created_by`=`users`.`id` WHERE  `p`.`created_by`!='$logged_in_user_id' AND `p`.`created_by` NOT IN (SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `p`.`orginal_post_id` IS NULL GROUP BY `p`.`created_by` HAVING `totalPosts` > '0' ORDER BY `totalPoints`,`totalPosts` DESC LIMIT 0,50";
                            }
                            $postResult = DB::select($sql_posts);

                            if (!empty($postResult)) {
                                $getResult = $postResult;
                                $randomCategory = $loc;
                                break;
                            }
                        }
                    }
                    break;
                   
            }
        } else {
            $randomCategory = '';
            $getResult = array();
        }
        $i++;
    }while(empty($getResult) && $i<=3);

        //Select random top 10 from top 50 users
        if (!empty($getResult)) {
            shuffle($getResult);
            $getResult = array_slice($getResult, 0, 10);

            //Get total post and total followers
            foreach ($getResult as $key => $result) {

                $sql = "SELECT (SELECT COUNT(`posts`.`id`) FROM `posts` WHERE `posts`.`created_by`='" . $getResult[$key]->created_by . "') as `totalPost`, (SELECT COUNT(`followers`.`follower_id`) FROM `followers` WHERE `followers`.`user_id`='" . $getResult[$key]->created_by . "') as `totalFollowers`";
                $info = DB::select($sql);
                $new_info = array('allPostCount' => $info[0]->totalPost, 'totalFollowers' => $info[0]->totalFollowers);

                //Fetch all posts uder category and tags if recommendation is from category/tags
                //Skip this step if recommendation is from location
                if (!empty($randomCategory->category_name)) {
                    $sql_count_totalposts_under_category_tags = "SELECT DISTINCT(`p`.`id`) FROM `posts` as `p` WHERE (`p`.`category_id` IN (SELECT `id` FROM `categories` WHERE `categories`.`category_name`='$randomCategory->category_name') OR `sub_category_id` IN (SELECT `id` FROM `categories` WHERE `categories`.`category_name`='$randomCategory->category_name') OR `p`.`id` IN (SELECT DISTINCT(`pt`.`post_id`) FROM `post_tag` as `pt` INNER JOIN `posts` as `p` ON `pt`.`post_id`=`p`.`id` INNER JOIN `tags` as `t` ON `t`.`id`=`pt`.`tag_id` WHERE `t`.`tag_name`='$randomCategory->category_name'  AND `p`.`created_by`='" . $getResult[$key]->created_by . "' AND `p`.`orginal_post_id` IS NULL) ) AND `p`.`created_by`='" . $getResult[$key]->created_by . "' AND `p`.`orginal_post_id` IS NULL ORDER BY `id`  DESC";
                    $getResult[$key]->totalPosts = count(DB::select($sql_count_totalposts_under_category_tags));
                }

                if (!empty($getResult[$key]->profile_image)) {
                    $getResult[$key]->profile_image = generate_profile_image_url('profile/thumbs/' . $getResult[$key]->profile_image);
                }
                if (!empty($getResult[$key]->cover_image)) {
                    $getResult[$key]->cover_image = generate_profile_image_url('profile/cover/' . $getResult[$key]->cover_image);
                }

                $getResult[$key] = (object)array_merge((array)$getResult[$key], $new_info);
            }

        }


        $data = [
            'randomCategory' => $randomCategory,
            'getResult' => $getResult,
            'get_category_post_count'=>$get_category_post_count,
            'get_tag_post_count'=>$get_tag_post_count,
            'get_location_post_count'=>$get_location_post_count,
            'index'=>$index,
            'i'=>$i
        ];


        return $data;
    }

    public function getRandomUser($randomCategoryId, $limit)
    {
        $logged_in_user_id = Auth::user()->id;

        /*$sql = "SELECT SUM(`posts`.`points`) as `totalPoints`, `posts`.`created_by`, COUNT(`posts`.`id`) as `totalPosts`,`users`
.`first_name`,`users`.`last_name`, `users`.`username`, `users`.`profile_image`, `users`.`about_me`, `users`
.`cover_image` FROM `posts` INNER JOIN `users` ON `posts`.`created_by`=`users`.`id` WHERE `posts`.`created_by` NOT IN
(SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `posts`.`created_by`!='$logged_in_user_id' AND ( `posts`.`category_id`='$randomCategoryId' OR `posts`.`sub_category_id`='$randomCategoryId' ) AND `posts`.`orginal_post_id` IS NULL GROUP BY `posts`.`created_by` HAVING SUM(`posts`.`points`) > 0 LIMIT 0,
        $limit";*/

        $currentDateTime = Carbon::now()->toDateTimeString();
        $prevMonthDateTime = Carbon::now()->addDays(-30);

        $sql = "SELECT COUNT(`activity_post`.`activity_id`) as `totalActivityOnPOST`, `posts`.`created_by`, count(DISTINCT(`posts`.`id`)) as `totalPosts`, count(`posts`.`points`) as `totalPoints`, `users`.`first_name`,`users`.`last_name`, `users`.`username`, `users`.`profile_image`, `users`.`about_me`, `users`.`cover_image` FROM `posts` INNER JOIN `activity_post` ON `posts`.`id`=`activity_post`.`post_id` INNER JOIN `users` ON `posts`.`created_by`=`users`.`id` WHERE `posts`.`created_by` NOT IN (SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `posts`.`created_by`!='$logged_in_user_id' AND ( `posts`.`category_id`='$randomCategoryId' OR `posts`.`sub_category_id`='$randomCategoryId' ) AND `activity_post`.`activity_id` IN(1,3,4,5,6,8,9,10,11) AND `activity_post`.`created_at` >= '$prevMonthDateTime' AND `activity_post`.`created_at` <= '$currentDateTime' AND `posts`.`orginal_post_id` IS NULL GROUP BY `posts`.`created_by` HAVING `totalActivityOnPOST` > 0 ORDER BY `totalPoints` LIMIT 0, $limit";

        $getResult = DB::select($sql);

        return $getResult;
    }

    public function getRandomUserPostOnTags($tagId, $limit)
    {
        $logged_in_user_id = Auth::user()->id;

        /*$sql = "SELECT `post_tag`.`tag_id`,`posts`.`created_by`, `users`.`first_name`, `users`.`last_name`, `users`.`about_me`, `users`.`profile_image`, `users`.`cover_image`, `users`.`username`, SUM(`posts`.`points`) as `totalPoints`, COUNT(`posts`.`id`) as `totalPosts` FROM `post_tag` INNER JOIN `posts` ON `post_tag`.`post_id`=`posts`.`id` INNER JOIN `users` ON `posts`.`created_by`=`users`.`id` WHERE `posts`.`created_by` NOT IN(SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `posts`.`created_by`!='$logged_in_user_id' AND `post_tag`.`tag_id`='$tagId' AND `posts`.`orginal_post_id` IS NULL GROUP BY `posts`.`created_by`  HAVING SUM(`posts`.`points`) > 0 LIMIT 0,$limit";*/

        $currentDateTime = Carbon::now()->toDateTimeString();
        $prevMonthDateTime = Carbon::now()->addDays(-30);

        $sql = "SELECT COUNT(`activity_post`.`activity_id`) as `totalActivityOnPOST`, count(DISTINCT(`posts`.`id`)) as `totalPosts`, count(`posts`.`points`) as `totalPoints`, `post_tag`.`tag_id`,`posts`.`created_by`, `users`.`first_name`, `users`.`last_name`, `users`.`about_me`, `users`.`profile_image`, `users`.`cover_image`, `users`.`username` FROM `post_tag` INNER JOIN `posts` ON `post_tag`.`post_id`=`posts`.`id` INNER JOIN `activity_post` ON `posts`.`id`=`activity_post`.`post_id` INNER JOIN `users` ON `posts`.`created_by`=`users`.`id` WHERE `posts`.`created_by` NOT IN(SELECT `user_id` FROM `followers` WHERE `follower_id`='$logged_in_user_id') AND `posts`.`created_by`!='$logged_in_user_id' AND `post_tag`.`tag_id`='$tagId' AND `activity_post`.`created_at` >= '$prevMonthDateTime' AND `activity_post`.`created_at` <= '$currentDateTime' AND `posts`.`orginal_post_id` IS NULL GROUP BY `posts`.`created_by` HAVING `totalActivityOnPOST` > 0 ORDER BY `totalPoints` LIMIT 0, $limit";

        $getResult = DB::select($sql);

        return $getResult;

    }

    public function feedsJson(Request $request)
    {
        
        $logged_in_user_id = Auth::user()->id;
        $response = [];
        if (!$request->has('name')) {
            $response = [
                'error_message' => "Invalid request. Missing the 'name' parameter",
                'status' => 'INVALID_REQUEST'
            ];
            return response()->json($response, 400);
        }
        $input = $request->all();
        
        // return response()->json($input, 400);

        // Initialize.
        $posts = [];
        $totalPost = [];

        
        $name = $input['name'];
        // Set tab type.
        $tabType = 'fx';
        if (!empty($input['type'])) {
            $tabType = $input['type'];
        }

        // For fixed tabs..
        if ($tabType == 'fx') {
            if ($name == 'Following') {
                $followedPeoplePost = $this->getFollowedPeoplePost();
                $posts = $followedPeoplePost['posts'];
                $totalPost = $followedPeoplePost['totalPost'];
            } elseif ($name == 'For You') {
                $favouriteTopicPost = $this->getFavouriteTopicPost();
                $posts = $favouriteTopicPost['posts'];
                $totalPost = $favouriteTopicPost['totalPost'];
            } elseif ($name == 'Nearby') {
                $nearByPost = $this->getNearByPost();
            }
        } // For dynamic follwing 'place' tag tabs..
        else if ($tabType == 'place') {
            $place_url = $input['place_url'];
            // Update last visited.
            DB::table('place_follower')->where('place_url', $place_url)
                ->where('user_id', $logged_in_user_id)
                ->update(['last_visited' => Carbon::now()]);
            // Retrieve posts.
            $postByPlaceUrl = $this->getPostByPlaceUrl($place_url);
            $posts = $postByPlaceUrl['posts'];
            $totalPost = $postByPlaceUrl['totalPost'];
        } // For dynamic follwing 'real' tag or category tag tabs..
        else if ($tabType == 'ft') {

            $time = Carbon::now();

            //Update last visited timestamp for category or tags or both
            $category = DB::table('categories')->join('category_follower', 'categories.id', '=', 'category_follower.category_id')->select('categories.category_name', 'categories.id')->where([
                ['categories.category_name', '=', '' . $name . ''],
                ['category_follower.follower_id', '=', '' . $logged_in_user_id . '']
            ])->first();
            $tags = DB::table('tags')->join('tag_user', 'tags.id', '=', 'tag_user.tag_id')->select('tags.tag_name','tags.tag_text', 'tags.id')->where([
                ['tags.tag_name', '=', '' . $name . ''],
                ['tag_user.user_id', '=', '' . $logged_in_user_id . '']
            ])->first();

            if ($category !== null) {
                DB::table('category_follower')->where([
                    ['follower_id', '=', '' . $logged_in_user_id . ''],
                    ['category_id', '=', '' . $category->id . '']
                ])->update(['last_visited' => $time]);
            }

            if ($tags !== null) {
                DB::table('tag_user')->where([
                    ['user_id', '=', '' . $logged_in_user_id . ''],
                    ['tag_id', '=', '' . $tags->id . '']
                ])->update(['last_visited' => $time]);
            }

            $postByCategoryTagName = $this->getPostByCategoryTagName($name);
            $posts = $postByCategoryTagName['posts'];
            $totalPost = $postByCategoryTagName['totalPost'];
            
        }


      


        /*============== Modify post data ================*/
        // dd($posts->toArray());
        // dump($posts->toArray());
        $post_count = count($posts);
        $final_posts = [];
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

                $sql = "select count(*) as totalComments from `comments` where post_id ='" . $orginalPost->id . "'  AND parent_id is null";
                $comment = DB::select($sql);

                $final_posts[$p]['totalComments'] = $comment[0]->totalComments;

                $final_posts[$p]['totalPostViews'] = countPostView($orginalPost->id, $orginalPost->post_type);
                $totalShare = DB::table('activity_post')
                    /* ->where([
                         'post_id' => $orginalPost->id,
                         'activity_id' => 3
                     ])
                     */
                    ->where(['post_id' => $orginalPost->id])
                    ->whereIn('activity_id', [3, 4, 5])
                    ->select(['id'])
                    ->count();

                $final_posts[$p]['totalShare'] = $totalShare;
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
                $isBookMark = DB::table('bookmarks')
                    ->where([
                        'post_id' => $posts[$p]->orginalPost->id,
                        'user_id' => Auth::user()->id
                    ])
                    ->count();
                $final_posts[$p]['isBookMark'] = ($isBookMark != 0) ? 'Y' : 'N';

                $isUpvote = DB::table('activity_post')
                    ->where([
                        'post_id' => $orginalPost->id,
                        'activity_id' => 1,
                        'user_id' => Auth::user()->id
                    ])
                    ->select(['id'])
                    ->count();
                $final_posts[$p]['isUpvote'] = ($isUpvote != 0) ? 'Y' : 'N';

                $isDownvote = DB::table('activity_post')
                    ->where([
                        'post_id' => $orginalPost->id,
                        'activity_id' => 2,
                        'user_id' => Auth::user()->id
                    ])
                    ->select(['id'])
                    ->count();
                $final_posts[$p]['isDownvote'] = ($isDownvote != 0) ? 'Y' : 'N';
                $final_posts[$p]['child_post_created_at'] = $current_post->created_at->format('Y-m-d H:i:s');
            }
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
                    //->where(['post_id' => $posts[$p]->id, 'activity_id' => 3])
                    ->where(['post_id' => $posts[$p]->id])
                    ->whereIn('activity_id', [3, 4, 5])
                    ->select(['id'])
                    ->count();
                $posts[$p]->totalShare = $totalShare;
                $posts[$p]->child_post_id = $posts[$p]->id;
                $posts[$p]->child_post_user_id = $posts[$p]->created_by;
                // Set to final post.
                $final_posts[$p] = $posts[$p];
                $final_posts[$p]['cardID'] = $posts[$p]->id;
                // Set embed url info.
                if (!empty($final_posts[$p]->embed_code)) {
                    $embedVideoInfo = getEmbedVideoInfo($final_posts[$p]->embed_code);
                    $final_posts[$p]->embed_code_type = $embedVideoInfo['type'];
                    $final_posts[$p]->videoid = $embedVideoInfo['videoid'];
                }
                $isBookMark = DB::table('bookmarks')
                    ->where([
                        'post_id' => $posts[$p]->id,
                        'user_id' => Auth::user()->id
                    ])
                    ->count();
                $final_posts[$p]->isBookMark = ($isBookMark != 0) ? 'Y' : 'N';

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

        $userLocationInfo = Session::get('userLocationInfo');
        $userLatitude = '';
        $userLongitude = '';
        if ($userLocationInfo !== null && !empty($input['userLocationSaved']) && $input['userLocationSaved'] == "true") {
            $userLatitude = $userLocationInfo['lat'];
            $userLongitude = $userLocationInfo['lon'];
            foreach ($final_posts as $key => $postInfo) {
                $final_posts[$key] = (object)$final_posts[$key];
                if ($final_posts[$key]->lat != '' && $final_posts[$key]->lon != '' && $userLatitude != '' && $userLongitude != '') {
                    $latitudeTo = floatval($final_posts[$key]->lat);
                    $longitudeTo = floatval($final_posts[$key]->lon);
                    $distance = haversineGreatCircleDistance($userLatitude, $userLongitude, $latitudeTo, $longitudeTo);
                } else {
                    $distance = null;
                }
                $final_posts[$key]->distance = $distance;
            }/**/
            //$final_posts = addPostDistance($userLocationInfo, $final_posts);
        }
        

        $response = [
            'totalPost' => $totalPost,
            'posts' => $final_posts,
            'lat' => $userLatitude,
            'lon' => $userLongitude,
            
           
        ];

        
        return response()->json($response);
    }

    protected function getFollowedPeoplePost()
    {
        $post = Post::whereIn('privacy_id', [1, 2])
            ->whereIn('created_by', function ($query) {
                $query->select('user_id')
                    ->from('followers')
                    ->where('follower_id', Auth::user()->id);
            });
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
        $region_columns = ['id', 'name', 'slug_name'];

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
            'country',
            'country.region' => function ($query) use ($region_columns) {
                $query->addSelect($region_columns);
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
            'orginalPost.country',
            'orginalPost.country.region' => function ($query) use ($region_columns) {
                $query->addSelect($region_columns);
            },
            'orginalPost.tags' => function ($query) use ($tag_columns) {
                $query->addSelect($tag_columns);
            },
            'orginalPost.featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                $query->addSelect($featurePhotoDetail_column);
            },
        ])
            ->withCount('comment');
        // Order recent posts first.
        $post->orderBy('id', 'desc');

        // \DB::connection()->enableQueryLog();

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

        /*$query = \DB::getQueryLog();
        dd($query);*/

        // dd($posts ? $posts->toArray() : $posts);

        // Prepare the return data.
        $return_data = [
            'totalPost' => $totalPost,
            'posts' => $posts
        ];
        return $return_data;
    }

    protected function getFavouriteTopicPost()
    {
        $day = 7;

        $post = Post::sinceDaysAgo($day)
            ->whereNull('orginal_post_id')
            // Remove self posts
            ->where('created_by', '<>', Auth::user()->id)
            // Show public or follower only posts of followed users.
            ->where(function ($query) {
                $query->where('privacy_id', 1)
                    ->orWhere(function ($query) {
                        $query->where('privacy_id', 2)
                            ->whereIn('id', function ($query) {
                                $query->select('id')
                                    ->from('posts')
                                    ->whereIn('created_by', function ($q2) {
                                        $q2->select('user_id')
                                            ->from('followers')
                                            ->where('follower_id', Auth::user()->id);
                                    });
                            });
                    });
            });
        // Add filter based on post type.
        if ($this->filter_post_type > 0) {
            $post->where('post_type', $this->filter_post_type);
        }

        // Column selection array for eager loaded data.
        $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
        $category_columns = ['id', 'category_name'];
        $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
        $featurePhotoDetail_column = ['id', 'post_id', 'thumb_width', 'thumb_height', 'data_url'];

        /* ===============================================================================================
         * Order posts based on following formula.
         * SORT POINT =
         * [1 + (point / post age in minute) ] X
         * [1 - (post age in minute / 10080) ] X
         * [1 + (interest rate x 0.5) ] X
         * [1 + (location follow x 0.5) ]
         *
         * If the current user already view / read / accessed / play the post then
         * SORT POINT = SORT POINT / 2
         *
         * =============================================================================================*/
        $post->selectRaw('`posts`.*,
        (TIMESTAMPDIFF(MINUTE,
            `posts`.`created_at`,
            UTC_TIMESTAMP)+1) ageInMin,
        TIMESTAMPDIFF(DAY,
            `posts`.`created_at`,
            UTC_TIMESTAMP) ageInDay,
        (
            (
                SELECT
                    COUNT(*)
                FROM
                    `category_follower`
                WHERE
                    `follower_id` = ' . Auth::user()->id . '
                    AND `category_follower`.`category_id` IN (`posts`.`category_id` , `posts`.`sub_category_id`)
            ) + 
            (
                SELECT 
                    COUNT(*)
                FROM
                    `tag_user`
                WHERE
                    `user_id` = ' . Auth::user()->id . '
                        AND `tag_id` IN (
                            SELECT 
                                `tag_id`
                            FROM
                                `post_tag`
                            WHERE
                                `post_id` = `posts`.`id`
                        )
                        AND 
                            CASE WHEN `posts`.`post_type` = 5
                                    THEN TRUE
                                ELSE
                                    `tag_id` NOT IN (SELECT 
                                        `tags`.`id`
                                    FROM
                                        `tags`
                                    JOIN
                                        `categories` ON REPLACE(REPLACE(`tag_name`, "-and-", " & "), "-", " ") = `categories`.`category_name`)
                            END
            )
            ) AS interest_count,
            (
                SELECT 
                    COUNT(*)
                FROM
                    `place_follower`
                WHERE 
                    `user_id` = ' . Auth::user()->id . '
                    AND `posts`.`place_url` <> ""
                    AND (
                        `posts`.`place_url` LIKE CONCAT("%", `place_follower`.`place_url`, "%")
                    )
            ) AS place_follow_count,
            (
                SELECT 
                    IF(COUNT(*) > 0, 1, 0)
                FROM
                    `activity_post`
                WHERE 
                    `user_id` = ' . Auth::user()->id . '
                    AND  `post_id` = `posts`.`id`
                    AND `activity_id` IN (8,9,10,11)
            ) AS user_activity,
            (
                (
                    (1 + (`posts`.`points` / (SELECT ageInMin))) * 
                    (1 - ((SELECT ageInMin) / 10080)) *
                    (1 + (SELECT interest_count) * 0.5) *
                    (1 + (SELECT place_follow_count) * 0.5)
                )
            ) AS sort_point'
        )
            ->havingRaw('(interest_count + place_follow_count) > 0')
            ->orderByRaw('sort_point desc');
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
            'tags' => function ($query) use ($tag_columns) {
                $query->addSelect($tag_columns);
            },
            'featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                $query->addSelect($featurePhotoDetail_column);
            }
        ])
        ->withCount('comment');

        /*if (!empty($_REQUEST['test'])) {
            \DB::connection()->enableQueryLog();
        }*/

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

        // Count total posts..
        $totalPost = $posts->count();

        /*if (!empty($_REQUEST['test'])) {
            $query = \DB::getQueryLog();
            $main = $query[0]['query'];
            $bindings = $query[0]['bindings'];
            echo '<pre>' . $main . '</pre>';
            dd($bindings);
            dd($query);
        }*/

        // Prepare the return data.
        $return_data = [
            'totalPost' => $totalPost,
            'posts' => $posts
        ];
        return $return_data;
    }

    protected function getNearByPost()
    {

    }

    protected function getPostByCategoryTagName($name)
    {
        $totalPost = 0;
        $posts = [];
        $name = strtolower($name);
        // Get category case by searching insensitive category.
        $category = Category::searchByName($name)->first(['id']);

        // Get tag and post_id related to the tag.
        $tag = Tag::where('tag_name', $name)->orWhere('tag_name', str_slug($name))->first();

        if ($tag !== null || $category !== null) {
            /*
             * Get the public and "followers only" posts of user's
             * whom the logged in user following.
             */
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
            $public_or_follower_post_ids = array_pluck($public_or_follower_post, 'id');

            // Prepare the post query.
            $post = Post::whereNull('orginal_post_id');

            // Add filter based on post type.
            if ($this->filter_post_type > 0) {
                $post->where('post_type', $this->filter_post_type);
            }

            /*----------- For category/tag -----------*/
            $tag_post_id = [];
            if ($tag !== null) {
                $post_tag = DB::table('post_tag')->where('tag_id', $tag->id)->get(['post_id']);
                $tag_post_id = array_pluck($post_tag, 'post_id');
            }

            /*==================== Here we go ======================*/
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
            } // Get posts for tag.
            elseif (!empty($tag_post_id)) {
                $final_post_ids = array_intersect($public_or_follower_post_ids, $tag_post_id);
                $post->whereIn('id', $final_post_ids);
            } else {
                goto return_area;
            }
            /*------------------ END For category/tag ------------------*/
            // Condition based on post types...
            $post->whereIn('privacy_id', [1, 2])
                ->orderBy('created_at', 'desc')
                ->whereIn('id', $public_or_follower_post_ids);

            // Count total posts..
            $totalPost = $post->count();

            // Column selection array for eager loaded data.
            $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
            $category_columns = ['id', 'category_name'];
            $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
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
                'tags' => function ($query) use ($tag_columns) {
                    $query->addSelect($tag_columns);
                },
                'featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                    $query->addSelect($featurePhotoDetail_column);
                }
            ])
            ->withCount('comment');;

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
            
        }
        return_area:
        // Prepare the return data.
        $return_data = [
            'totalPost' => $totalPost,
            'posts' => $posts
        ];
        return $return_data;
    }

    protected function getPostByPlaceUrl($place_url)
    {
        $totalPost = 0;
        $posts = [];

        parse_str($place_url, $input);

        $location = !empty($input['location']) ? $input['location'] : '';
        $city = !empty($input['city']) ? $input['city'] : '';
        $state = !empty($input['state']) ? $input['state'] : '';

        $region = !empty($input['region']) ? $input['region'] : '';
        $country = !empty($input['country']) ? $input['country'] : '';
        $continent = !empty($input['continent']) ? $input['continent'] : '';

        // Prepare the post query.
        $post = Post::whereNull('orginal_post_id');
        /* 
         * Get the public and "followers only" posts of user's
         * whom the logged in user following.
         */
        $post->where(function ($query) {
            $query->where('privacy_id', 1)
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
                });
        });

        // Add filter based on post type.
        if ($this->filter_post_type > 0) {
            $post->where('post_type', $this->filter_post_type);
        }

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

        // Order post by recent..
        $post->orderBy('created_at', 'desc');

        // Count total posts..
        $totalPost = $post->count();

        // Column selection array for eager loaded data.
        $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
        $category_columns = ['id', 'category_name'];
        $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
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
            'tags' => function ($query) use ($tag_columns) {
                $query->addSelect($tag_columns);
            },
            'featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                $query->addSelect($featurePhotoDetail_column);
            }
        ])
            ->withCount('comment');;

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
        // Prepare the return data.
        $return_data = [
            'totalPost' => $totalPost,
            'posts' => $posts
        ];
        return $return_data;
    }
}
