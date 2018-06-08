<?php

namespace App\Http\Controllers\Api;

use Auth;
use DB;
use Session;
use App\Models\Category;
use App\Models\Post;
use App\Models\Place;
use App\Models\Tag;
use App\Models\Comment;
use App\Models\User;
use App\Models\SearchKeyword;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
	protected $page;
	protected $filter_post_type;
	protected $allowed_post_types;

	/**
	 * SearchController constructor
	 */
	public function __construct(Request $request)
	{
        /*------ Set post type for filtering posts ------*/
        $this->filter_post_type = 0;
        $this->allowed_post_types = config('constants.ALLOWED_POST_TYPES');
        if ($request->has('card_post_type') && in_array($request->input('card_post_type'), $this->allowed_post_types)) {
			$this->filter_post_type = $request->input('card_post_type');
		}
	}

	public function searchResult()
	{
		return view('search.search-result');
	}
    
	public function searchJson(Request $request)
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

    /*******(23-04-17) work on new search logic******/

		// $q = $input['q'];
		// if (empty($q)) {
		// 	$response = [
		// 		'results' => [],
		// 		'status' => 'ZERO_RESULT'
		// 	];
		// 	return response()->json($response);
		// }
    
		// $query_arr = preg_split('/[\ \,]+/', $q);
  //       // Remove special characters.
  //       $special_char = ['&'];
  //       foreach ($query_arr as $key => $value) {
  //           if (in_array($value, $special_char)) {
  //               array_splice($query_arr, $key, 1);
  //           }
  //       }

 /*******(23-04-17) work on new search logic******/

		// Calculation for pagination
        $page = 1;
        $per_page = config('constants.PER_PAGE');
        /* For instant search from header */
        if (!empty($input['ref']) && $input['ref'] == 'instant') {
        	$per_page = 10;
        }
        else if(!empty($input['page'])) {
            $page = $input['page'];
        }
        $offset = ($page - 1) * $per_page;

      //  $post_search_arr = $query_arr;   /*******(23-04-17) work on new search logic******/

        // Check category present in actual query.
        // Get category case by searching insensitive category.
        /*$categories = Category::all(['id', 'category_name']);
        $category_in_q  = [];
        foreach ($categories as $category) {
            // if (strpos($q, strtolower($category->category_name)) !== false) {
            $needle = strtolower($category->category_name);
            if ( preg_match("~\b$needle\b~", $q)) {
                $category_in_q[] = $category->category_name;
            }
        }*/

        // $post_search_arr = array_merge($category_in_q, $post_search_arr);
        // Add original query text to query_arr.
      //  array_unshift($post_search_arr, $q);  /*******(23-04-17) work on new search logic******/



        $q = $input['orginal_query'];

        $inputSearchText=$q;

         $q=preg_replace('/[^A-Za-z0-9]/', ' ', $q);

       
        // Fetch data for different tabs.
       // $postTabData = $this->getPost($query_arr, $q, $offset, $per_page);/*******(23-04-17) work on new search logic******/
       
        $channelTabData = $this->getChannel($inputSearchText, $q,  $offset, $per_page);

        $tagTabData = $this->getTag($inputSearchText, $q, $offset, $per_page);

        $locationTabData = $this->getLocation($inputSearchText, $q, $offset, $per_page);

         $postTabData = $this->getPost($inputSearchText, $q, $offset, $per_page);


        $posts = $postTabData['posts'];


        // Record search_keywords entry.
        if ($page == 1) {
            $search_keyword_row = [
               // 'keyword' => $q,
                'keyword' => $input['orginal_query'],
                'user_id' => Auth::user()->id,
                'post_count' => $postTabData['totalPost'],
                'channel_count' => $channelTabData['totalChannelUsers'],
                'tag_count' => $tagTabData['totalTags'],
                'location_count' => $locationTabData['totalPlaces'],
            ];
            $this->recordSearchKeyword($search_keyword_row);
        }


        /*// Add original query text to query_arr.
        array_unshift($query_arr, $q);*/
        // Remove query string less than 1 charcter, to prevent highlight.
        // foreach ($query_arr as $key => $value) {
        //     if (strlen($value) < 2)
        //         array_splice($query_arr, $key, 1);
        // }
         $query_arr = preg_split('/[\ ]+/', $q);
           
            $result_query_arr= array_filter($query_arr,function($v){ return strlen($v) > 2; });
           

            $glue = count($result_query_arr) > 1 ? '|' : '';
            $tag_query = implode($glue, $result_query_arr);
            if(count($result_query_arr) > 0)
                $tag_query = implode($glue, $result_query_arr);
            else
                $tag_query = $inputSearchText;

            if(isAssoc($result_query_arr))
            {
                $result_query_arr = array_values($result_query_arr);
            }

		$results = [
            'tagFollowStatus' => $postTabData['tagFollowStatus'],
			'totalPost' => $postTabData['totalPost'],
			'posts' => $posts,
            'totalChannelUsers' => $channelTabData['totalChannelUsers'],
            'channelUsers' => $channelTabData['channelUsers'],
            'totalTags' => $tagTabData['totalTags'],
            'searchTags' => $tagTabData['tags'],
            'places' => $locationTabData['places'],
            'totalPlaces' => $locationTabData['totalPlaces'],
            'query_arr' => $query_arr,
            'tag_query_arr' => $result_query_arr,
            //'q'=>$q,
            //'post_query'=> $postTabData['post_query'],//(18-12-17)
		];

		// Send the response.
		$response = [
			'results' => $results,
			'status' => 'OK'
		];
		return response()->json($response);
	}

	protected function getPost($inputSearchText,$q, $offset, $per_page)
	{
        // Initialize variables.
    /*******(23-04-18) ********/
         $tagFollowStatus = 0;
        // $name = strtolower($q);
        // $search_tag_name=preg_replace("/[\s]/", "-", $name);//    (18-12-17)
    /*******(23-04-18) ********/     
        // Get category case by searching insensitive category.
    /*******(02-04-18) ********/

       $q =preg_replace('/(?<!^)\W+/', ' ', $inputSearchText);
       if(!ctype_alnum(substr($q,0,1)))
        {
            $q="\\".$q;
        }
        

         $query_arr = preg_split('/[\ \-\,]+/', $q);
            $result_query_arr= array_filter($query_arr,function($v){ return strlen($v) > 2; });

            //print_r($q);
            // print_r($query_arr);
            // print_r($result_query_arr);


            $glue = count($result_query_arr) > 1 ? '|' : '';
            $tag_query = implode($glue, $result_query_arr);

            // for($k=0; $k<count($result_query_arr); $k++)
            // {
               
            //     $tag_query_arr[$k]= '(^|[[:space:]])'.$result_query_arr[$k];
            // }


            // if(count($tag_query_arr) > 0)
            //     $tag_query = implode($glue, $tag_query_arr);
            // else
            //     $tag_query = $tag_query_arr;

    /*******(02-04-18) ********/

      
        $category = Category::searchByName($tag_query)->first(['id']);

        // Get tag and post_id related to the tag.
        $tag = Tag::where('tag_name', $inputSearchText)->orWhere('tag_name', str_slug($inputSearchText))->first();
       // $tag = Tag::where('tag_name', $name)->orWhere('tag_name', str_slug($search_tag_name))->first();  // (18-12-17)

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

		// Prepare the post query.
        $post = Post::whereNull('orginal_post_id');
		if (Auth::check()) {
			// Remove own post.
			// $post->where('created_by', '<>', Auth::user()->id);

			// Follower only posts.
			$post->where(function($query) {
				$query->where('privacy_id', 1)
				->orWhere(function($query) {
					$query->where('privacy_id', 2)
                        ->whereIn('created_by', function($q2) {
                        $q2->select('user_id')
                        ->from('followers')
                        ->where('follower_id', Auth::user()->id);
                    });
				});
			});
		}
		else {
			$post = Post::where('privacy_id', 1);
		}

		// Add filter based on post type.
		if ($this->filter_post_type > 0) {
			$post->where('post_type', $this->filter_post_type);
		}
        // Search the query text.

        /*******(28-03-18) ********/
        /**********(23-04-18)
            // $query_arr = preg_split('/[\ \,\-]+/', $q);//task update (28-03-18)


            // $glue = count($query_arr) > 1 ? '|' : '';
            // $tag_query = implode($glue, $query_arr);
         /********(23-04-18)*************/   
        /*******(28-03-18) ********/


        
		foreach ($result_query_arr as $text) {


			$post->where(function($query) use ($text) {
				// $query->whereRaw(
				// 	"MATCH (`title` , `caption` , `caption`) AGAINST (? IN BOOLEAN MODE) OR `location` REGEXP '(^|[[:space:]])" . $text . "' OR `short_description` REGEXP '(^|[[:space:]])" . $text . "' OR LEFT(content, 200) REGEXP '(^|[[:space:]])" . $text . "'",
				// 	['"' . $text . '*"']
				// );

                $query->whereRaw(
                    "`title` REGEXP '(^|[[:space:]])" . $text . "' OR `caption` REGEXP '(^|[[:space:]])" . $text . "' OR `caption` REGEXP '(^|[[:space:]])" . $text . "'  OR `location` REGEXP '(^|[[:space:]])" . $text . "' OR `short_description` REGEXP '(^|[[:space:]])" . $text . "' OR LEFT(content, 200) REGEXP '(^|[[:space:]])" . $text . "'"
                );


               

				// Search for tag.
				$query->orWhereIn('id', function($q1) use ($text) {
					$q1->select('post_id')
						->from('post_tag')
						->whereIn('tag_id', function($q2) use ($text) {
							$q2->select('id')
								->from('tags')
                                ->whereRaw("tag_text REGEXP '(^|[[:space:]])" . $text ."'");
                                //->where('tag_name', $search_tag_name)//(18-12-17)
                                //->orWhere('tag_name', str_slug($text));
                                // ->whereRaw('( `tag_text` REGEXP ?)', [
                                //     $tag_query
                                  
                                // ]);
					});
				});
                
                // Get category case by searching insensitive category.
                // $category = Category::searchByName($text)->first(['id']);
                $category = Category::searchByNameWildCards($text)->first(['id']);
                
				if ($category !== null) {
                    // dd($category->toArray());
					$query->orWhere(function($q) use ($category) {
						$q->where('category_id', $category->id)
							->orWhere('sub_category_id', $category->id);
					});
				}
			});
		}
		
		// Count total posts..
		$totalPost = $post->count();
		// Column selection array for eager loaded data.
        $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
        $category_columns = ['id', 'category_name'];
        $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
        $featurePhotoDetail_column = ['id', 'post_id', 'thumb_width', 'thumb_height', 'data_url'];
        $region_columns = ['id', 'name', 'slug_name'];

        // Eager load data from relations.
        $post->with([
            'user' => function ($query) use ($user_columns) {
                $query->addSelect($user_columns);
            },
            'category' => function ($query) use ($category_columns) {
                $query->addSelect($category_columns);
            },
            'subCategory' => function ($query)  use ($category_columns) {
                $query->addSelect($category_columns);
            },
            'country',
            'country.region' => function ($query)  use ($region_columns) {
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
        // Order recent posts first.
        $post->orderBy('id', 'desc');

        /*if (!empty($_REQUEST['test'])) {
        	\DB::connection()->enableQueryLog();
        }*/
        

		// Get the paginated result.
        $posts = $post->skip($offset)->take($per_page)->get()->makeVisible('people_here');
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
        
        $post_query=$post->toSql();// (18-12-17)

		/*if (!empty($_REQUEST['test'])) {
			$query = \DB::getQueryLog();
			dd($query);
		}*/

		// Add data to each post.
		$post_count = count($posts);
        for($p = 0; $p < $post_count; $p++) {
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

            if($posts[$p]->parent_post_id!=0){
                $posts[$p]->orginalPostUserName =  $posts[$p]->parentPost->user->first_name;
            }
            // $posts[$p]->totalComments = count($posts[$p]->comment);
			
			// total comments
			$totalComments = Comment::where(['post_id' => $posts[$p]->id])->whereNull('parent_id')->count();
			
            $posts[$p]->totalComments = $totalComments;
            $posts[$p]->totalPostViews = countPostView($posts[$p]->id,$posts[$p]->post_type);
            $totalShare = DB::table('activity_post')
                               //->where(['post_id' => $posts[$p]->id, 'activity_id' => 3]) 
							   ->where(['post_id' => $posts[$p]->id])
							   ->whereIn('activity_id', [3, 4, 5])							   
                               ->select(['id'])
                               ->count();
            $posts[$p]->totalShare = $totalShare;
			$posts[$p]->child_post_id = $posts[$p]->id;
			$posts[$p]->child_post_user_id= $posts[$p]->created_by;
			// Set embed url info.
            if (!empty($posts[$p]->embed_code)) {
                $embedVideoInfo = getEmbedVideoInfo($posts[$p]->embed_code);
                $posts[$p]->embed_code_type = $embedVideoInfo['type'];
                $posts[$p]->videoid = $embedVideoInfo['videoid'];
            }	
			if(Auth::check()){
    			$isBookMark = DB::table('bookmarks')
                                        ->where([
                                            'post_id'=>$posts[$p]->id,
                                            'user_id'=>Auth::user()->id
                                        ])
                                        ->count();
                $isUpvote = DB::table('activity_post')
                                        ->where([
                                            'post_id' => $posts[$p]->id,
                                            'activity_id' => 1,
                                            'user_id'=>Auth::user()->id
                                        ])
                                        ->select(['id'])
                                        ->count();
                $isDownvote = DB::table('activity_post')
                                    ->where([
                                        'post_id' => $posts[$p]->id,
                                        'activity_id' => 2,
                                        'user_id'=>Auth::user()->id
                                    ])
                                    ->select(['id'])
                                    ->count();
            } else {
                $isBookMark = 0;
                $isUpvote = 0;
                $isDownvote = 0;
            }

            $posts[$p]->isBookMark = ($isBookMark!=0) ? 'Y' : 'N';
			$posts[$p]->isUpvote = ($isUpvote!=0) ? 'Y' : 'N';
			$posts[$p]->isDownvote = ($isDownvote!=0) ? 'Y' : 'N';

            // Set unique cardID.
            $posts[$p]->cardID = $posts[$p]->id;

            if (!empty($posts[$p]->image)) {
                $posts[$p]->image = generate_post_image_url('post/' . $posts[$p]->image);
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

        /*************Fetch Distance Between Post Location And End User Location****************/
        $userLocationInfo   = Session::get('userLocationInfo');
        if($userLocationInfo !== null){
            $posts = addPostDistance($userLocationInfo, $posts);
        }
        /*************Fetch Distance Between Post Location And End User Location****************/

		// Prepare the return data.
		$return_data = [
            'tagFollowStatus' => $tagFollowStatus,
			'totalPost' => $totalPost,
            'posts' => $posts,
            'post_query'=>$post_query // (18-12-17)
		];
		return $return_data;
	}

    protected function getChannel($inputSearchText,$q, $offset, $per_page)
    {
        // Column selection array.
        /**************(23-04-18)***************/
       // $query = implode('|', $query_arr);

             $inputSearchText=preg_replace('/[^A-Za-z0-9]/', '', $inputSearchText);
          $query_arr = preg_split('/[\ \,\-]+/', $q);
            $result_query_arr= array_filter($query_arr,function($v){ return strlen($v) > 2; });

            $glue = count($result_query_arr) > 1 ? '|' : '';
            $tag_query = implode($glue, $result_query_arr);
            if(count($result_query_arr) > 0)
                $tag_query = implode($glue, $result_query_arr);
            else
                $tag_query = $inputSearchText;



        /**************(23-04-18)***************/

        // Initialize
        $totalChannelUsers = 0;
        $totalChannelUsersCount = DB::select("SELECT COUNT(*) AS total FROM (SELECT `id`, CONCAT_WS(' ', `username`, `first_name`, `last_name`, `about_me`) as search from `users` HAVING search REGEXP ?) tbl", [$tag_query]);
        if (! empty($totalChannelUsersCount)) {
            $totalChannelUsers = $totalChannelUsersCount[0]->total;
        }

        // Search the query text.
        $user = User::selectRaw("`id`, `first_name`, `last_name`, `username`, `profile_image`, `cover_image`, `about_me`, `occupation`, (select count(*) from `followers` where `followers`.`user_id` = `users`.`id`) as `follower_count`, (select count(*) from `posts` where `posts`.`created_by` = `users`.`id` and `orginal_post_id` is null) as `original_post_count`, CONCAT_WS(' ', `username`, `first_name`, `last_name`, `about_me`, `occupation`) as search");
        // Remove anonymous user
        $user->where('id', '<>', 1);
        
        // Remove logged in user from result.
        /*if (Auth::check()) {
            $user->where('id', '<>', Auth::user()->id);
        }*/
        
        $channelUsers = $user->havingRaw("search REGEXP ?", [$tag_query])
            ->orderByRaw("CASE  WHEN search REGEXP '[[:<:]]" . $tag_query . "[[:>:]]' THEN 1 WHEN search LIKE '" . $tag_query . "%' THEN 2 WHEN search LIKE '%" . $tag_query . "' THEN 4 ELSE 3 END, follower_count desc, original_post_count desc")
            ->skip($offset)->take($per_page)->get();

        foreach ($channelUsers as $user) {
            if (!empty($user->cover_image)) {
                $user->cover_image = generate_profile_image_url('profile/cover/' . $user->cover_image);
            }
        }

        // Prepare the return data.
        $return_data = [
            'totalChannelUsers' => $totalChannelUsers,
            'channelUsers' => $channelUsers
        ];
        return $return_data;
    }

    protected function getTag($inputSearchText,$q, $offset, $per_page)
    {
        /**********(23-04-18)*************/
        // $query_arr = preg_split('/[\ \,\-]+/', $q);//task update (28-03-18)


        // $glue = count($query_arr) > 1 ? '|' : '';
        // $query = implode($glue, $query_arr);

        $query_arr = preg_split('/[\ \,\-]+/', $q);
            $result_query_arr= array_filter($query_arr,function($v){ return strlen($v) > 2; });

            $glue = count($result_query_arr) > 1 ? '|' : '';
            $tag_query = implode($glue, $result_query_arr);
            if(count($result_query_arr) > 0)
                $tag_query = implode($glue, $result_query_arr);
            else
                $tag_query = $inputSearchText;

        /***********(23-04-18)**********/

        // Initialize
        $totalTags = 0;
        $totalTagsCount = DB::select('SELECT COUNT(*) as total from `tags` WHERE `tag_text` REGEXP ?', [$tag_query]);
        if (! empty($totalTagsCount)) {
            $totalTags = $totalTagsCount[0]->total;
        }

        $category_sql = '';
        $category = Category::whereRaw('`category_name` REGEXP ?', [$tag_query])->first();
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
        $tag=$tag->toArray();
     
        
        $tags =  array_slice($tag, $offset, $per_page);

        $tags=collect($tags);

        // Check if category is present in tags.
        if ($category !== null && $offset == 0) {
            $isPresent = false;
            foreach($tags as $tag) {
                if(
                    strtolower($category->category_name) == strtolower($tag['tag_name']) ||
                    str_slug_ovr($category->category_name) == $tag['tag_name']
                ) {
                    $isPresent = true;
                    break;
                }
            }
            // Fetch category if not present in tags.
            if(!$isPresent) {
                $category_tag = Category::selectRaw('`id`, `category_name` as tag_name,`category_name_slug` as tag_text  , (SELECT COUNT(*) FROM `posts` WHERE (`posts`.`category_id` = ' . $category->id . ' OR `posts`.`sub_category_id` = ' . $category->id . '))  as `posts_count`, 0  as `users_count`, 0 as isFollow ')
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

    protected function getLocation($inputSearchText,$q, $offset, $per_page)
    {

         $inputSearchText=preg_replace('/[^A-Za-z0-9]/', '', $inputSearchText);

          $query_arr = preg_split('/[\ \-\,]+/', $q);
            $result_query_arr= array_filter($query_arr,function($v){ return strlen($v) > 2; });

            $glue = count($result_query_arr) > 1 ? '|' : '';
            $tag_query = implode($glue, $result_query_arr);
            if(count($result_query_arr) > 0)
                $tag_query = implode($glue, $result_query_arr);
            else
                $tag_query = $inputSearchText;
            

            

        $sql = '';
        // Prepare the post query for fetching locations.'vzz
        // Search the query text.
        $totalPlaces=0;
        $places='';
        if(count($result_query_arr) > 0)
        {
            foreach ($result_query_arr as $text) {
                $sql .= 'MATCH (`place_level_1`, `place_level_2`, `place_level_3`) AGAINST (\'\"' . $text . '\"\' IN BOOLEAN MODE) AND ';
            }

            $sql = rtrim($sql, 'AND ');

            // Check whether following the tag.
            if (Auth::check()) {
                $follow_sql = '(select count(*) from `place_follower` where `place_follower`.`place_url` LIKE `places`.`place_url` AND user_id = ' . Auth::user()->id . ') as isFollow';
            }
            else {
                $follow_sql = '0 as isFollow ';
            }

            // Count places.
            $totalPlaces = Place::whereRaw($sql)->count();
        

        $place = Place::selectRaw('`id`, `place_level_1`, `place_level_2`, `place_level_3`, `place_url`, (SELECT count(*) FROM `place_follower` WHERE `place_follower`.`place_url` LIKE `places`.`place_url`) as `users_count`, ' . $follow_sql)
            ->whereRaw($sql)
            ->orderByRaw("CASE WHEN LOWER(`place_level_1`) REGEXP '[[:<:]]" . $q . "[[:>:]]' THEN 1  WHEN LOWER(`place_level_2`) REGEXP '[[:<:]]" . $q . "[[:>:]]' THEN 2 WHEN LOWER(`place_level_3`) REGEXP '[[:<:]]" . $q . "[[:>:]]' THEN 3 END, users_count desc");

        // \DB::connection()->enableQueryLog();
        // Get the paginated result.
        $places = $place->skip($offset)->take($per_page)->get();

        // Count post number for each place url.
        foreach ($places as $place) {
            $place->posts_count = $this->countPostForPlace($place->place_url);
        }
    }
        
        /*$query = \DB::getQueryLog();
        dd($places);*/

        // Prepare the return data.
        $return_data = [
            'places' => $places,
            'totalPlaces' => $totalPlaces
        ];
        return $return_data;
    }

    protected function countPostForPlace($place_url)
    {
        // Initialize data.
        $location = '';
        $city = '';
        $state = '';

        $region = '';
        $country = '';
        $continent = '';

        $input = explode('&', rawurldecode($place_url));
        foreach ($input as $key => $value) {
            preg_match_all('/(.+)=(.*)/', $input[$key], $matches);
            if (!empty($matches[1][0]) && !empty($matches[2][0])) {
                $text = $matches[1][0];
                $$text = $matches[2][0];
            }
        }

        // Initialize.
        $totalPost = 0;
        if (empty($location) && empty($city) && empty($state) && empty($region) && empty($country) && empty($continent)) {
            return $totalPost;
        }
        else {
            // Prepare the post query.
            $post = Post::whereNull('orginal_post_id');

            // Follower only posts.
            $post->where(function($query) {
                $query->where('privacy_id', 1)
                ->orWhere(function($query) {
                    $query->where('privacy_id', 2)
                            ->whereIn('created_by', function($q2) {
                                $q2->select('user_id')
                                ->from('followers')
                                ->where('follower_id', Auth::user()->id);
                            });
                });
            });

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
                $post->whereIn('country_code', function($query) use($region, $country, $continent) {
                    if (!empty($country)) {
                        $query->select('country_code')
                            ->from('countries')
                            ->where('country_name', 'LIKE', $country)
                            ->orWhere('country_code', 'LIKE', $country);
                    }
                    elseif (!empty($region)) {
                        $query->select('country_code')
                            ->from('countries')
                            ->whereIn('region_id', function ($q2) use ($region) {
                                $q2->select('id')
                                    ->from('regions')
                                    ->where('name', 'LIKE', $region)
                                    ->orWhere('slug_name', 'LIKE', $region);
                            });
                    }
                    elseif (!empty($continent)) {
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
                        $query->orWhereRaw( "(`location` like ? OR `location` LIKE ?)", array( $ld1,  $ld2) );
                    }
                    //*========= END ORed condition for location ==========*//
                });
            }
            // Count the total number of posts.
            $totalPost = $post->count();
            return $totalPost;
        }
    }

    protected function recordSearchKeyword($search_keyword_row) {
        $hour = 1;
        $combination = [
            'keyword' => $search_keyword_row['keyword'],
            'user_id' => $search_keyword_row['user_id'],
        ];
        $checkSearchKeyword = SearchKeyword::where($combination)->sinceHoursAgo($hour)->first();
        // Insert if not searched within $hour.
	    if($checkSearchKeyword === null){
            SearchKeyword::create($search_keyword_row);
        }
    }
}
