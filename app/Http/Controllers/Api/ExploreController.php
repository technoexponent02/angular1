<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client as GuzzleClient;

use DB;
use Auth;
use Session;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Postview;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Tag;

class ExploreController extends Controller {

	protected $per_page;
    protected $offset;

    protected $allowed_post_types;
    protected $filter_post_type;

    protected $position;

    protected $clientIp;

    /**
     * ExploreController constructor.
     * @param Request $request
     */
	public function __construct(Request $request)
	{
		/*--- build the pagination logic ---*/
		$this->per_page = config('constants.PER_PAGE');
		$this->per_page = 24;
        // Calculation for pagination
        $page = 1;
        if(!empty($request->input('page'))) {
            $page = $request->input('page');
        }
        $this->page = $page;
        $this->offset = ($page - 1) * $this->per_page;

        // Set client ip.
        $this->clientIp = $request->ip();

        /*------ Set post type for filtering posts ------*/
        $this->filter_post_type = 0;
        $this->allowed_post_types = config('constants.ALLOWED_POST_TYPES');
        if ($request->has('card_post_type') && in_array($request->input('card_post_type'), $this->allowed_post_types)) {
			$this->filter_post_type = $request->input('card_post_type');
		}
		/*----- Set position ------*/
		$this->position = [];
		if ($request->has('position')) {
			$this->position = $request->input('position');
		}
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('explore.index');
	}

	/**
     * Test Function
     */
    public function test()
    {
        return view('explore.test');
    }
	
	public function category()
	{
		return view('explore.category');
	}
	
	public function followingTopics()
	{
		return view('explore.following-topics');
	}

	public function exploreJson(Request $request)
	{
		$response = [];
		if (! $request->has('name')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'name' parameter",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}
		$name = $request->input('name');
		// Set view post type
		$post_type = 'recent';
		if ($request->has('type')) {
			$post_type = $request->input('type');
		}

		// \DB::connection()->enableQueryLog();

		$postByCategoryName = $this->getExplorePosts($name, $post_type);
		
		// dd('died');
		/*$query = \DB::getQueryLog();
		$lastQuery = end($query);
		dd($query);*/

		$posts = $postByCategoryName['posts'];
		// Add data to each post.
		$post_count = count($posts);
        for($p = 0; $p < $post_count; $p++) {
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
			// Add child post user id
			$posts[$p]->child_post_user_id = $posts[$p]->created_by;

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

            if($posts[$p]->parent_post_id!=0){
                $posts[$p]->orginalPostUserName =  $posts[$p]->parentPost->user->first_name;
            }
            $posts[$p]->totalComments = Comment::where(['post_id' => $posts[$p]->id])->whereNull('parent_id')->count();
            // $posts[$p]->totalPostViews = $posts[$p]->postview->sum('views');
            $totalShare = DB::table('activity_post')
                              // ->where(['post_id' => $posts[$p]->id, 'activity_id' => 3])       
							   ->where(['post_id' => $posts[$p]->id])
							   ->whereIn('activity_id', [3, 4, 5])
                               ->select(['id'])
                               ->count();
            $posts[$p]->totalShare = $totalShare;
            $posts[$p]->child_post_id = $posts[$p]->id;
            $posts[$p]->totalPostViews = countPostView($posts[$p]->id,$posts[$p]->post_type);
            // Set embed url info.
            if (!empty($posts[$p]->embed_code)) {
                $embedVideoInfo = getEmbedVideoInfo($posts[$p]->embed_code);
                $posts[$p]->embed_code_type = $embedVideoInfo['type'];
                $posts[$p]->videoid = $embedVideoInfo['videoid'];
			}

			if (Auth::check()) {
			 	$isBookMark = DB::table('bookmarks')
                                    ->where([
                                        'post_id'=>$posts[$p]->id,
                                        'user_id'=>Auth::user()->id
                                    ])
									->count();
			}else{
				$isBookMark = 0;
			}
									
             $posts[$p]->isBookMark = ($isBookMark!=0) ? 'Y' : 'N';
			 if (Auth::check()) {

				 $isUpvote = DB::table('activity_post')
                                    ->where([
                                        'post_id' => $posts[$p]->id,
                                        'activity_id' => 1,
                                        'user_id'=>Auth::user()->id
                                    ])
                                    ->select(['id'])
									->count();
			 }else{
				$isUpvote = 0;
			 }

			$posts[$p]->isUpvote = ($isUpvote!=0) ? 'Y' : 'N';

			if (Auth::check()) {

				$isDownvote = DB::table('activity_post')
								->where([
									'post_id' => $posts[$p]->id,
									'activity_id' => 2,
									'user_id'=>Auth::user()->id
								])
								->select(['id'])
								->count();
			}else{
				$isDownvote = 0;
			}
			$posts[$p]->isDownvote = ($isDownvote!=0) ? 'Y' : 'N';

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
			$featured_cover_image_post = $this->getExplorePosts($name, $post_type);
			if (!empty($featured_cover_image_post->image)) {
                $featured_cover_image_post->image = generate_post_image_url('post/' . $featured_cover_image_post->image);
            }
		}
		
        /*************Fetch Distance Between Post Location And End User Location****************/
        $userLocationInfo   = Session::get('userLocationInfo');
        if($userLocationInfo !== null && $request->input('userLocationSaved') == "true"){
			$posts 				= addPostDistance($userLocationInfo, $posts);

			

        }
		/*************Fetch Distance Between Post Location And End User Location****************/
		
	

		$response = [
			'followStatus' => $postByCategoryName['followStatus'],
			'totalPost' => $postByCategoryName['totalPost'],
			'posts' => $posts,
			'featured_image_post' => $featured_cover_image_post,
			//'tranding_tags'=>$tranding_tag_details

		];
		return response()->json($response);
	}



	protected function getExplorePosts($name = 'all', $post_type) {
		// Initialize variables.
		$totalPost = 0;
		$followStatus = 0;
		$posts = [];

		$post_tag = $category = null;
		$post_tag_ids = [];
		$category_ids = [];		
		// Get category for particular category. 
		if ($name != 'all') {
			$name = strtolower($name);
			// Get post_tag ids
			$post_tag= DB::table('post_tag')->whereIn('tag_id',  function ($query) use($name) {
				$query->select('id')
					->from('tags')
					->where('tag_name', $name)->orWhere('tag_name', str_slug($name));
			})
			->get(['post_id']);

			// Get category case by searching insensitive category.
			$category = Category::searchByName($name)->get(['id']);
		}
		else {
				$category_tag_names = '';
				$all_categories = Category::where('parent_id', 0)->get(['category_name_slug']);
				foreach ($all_categories as $category) {
					$category_tag_names .= "[[:<:]]$category->category_name_slug[[:>:]]|";
				}
				$category_tag_names = rtrim($category_tag_names, '|');
				
				// Get post_tag ids for all tags which are also category.
				$post_tag = DB::table('post_tag')->whereIn('tag_id',  function ($query) use($category_tag_names) {
					$query->select('id')
						->from('tags')
						->whereRaw("`tag_name` REGEXP '$category_tag_names'");
				})
				->get(['post_id']);

				/*if (!empty($_REQUEST['myTest'])) {
				dd($category_tag_names);
				}*/

				// Get only main catgeory if selected all.
				$category = Category::where('parent_id', 0)->get(['id']);
		}

		// Set the post_tag post_ids.
		if ($post_tag !== null) {
			$post_tag_ids = array_pluck($post_tag, 'post_id');
			// dd($post_tag_ids);
		}

		// Set the category ids.
		if ($category !== null) {
			$category_ids = array_pluck($category, 'id');
		}
		$category_follower_count=0;
		if ($name != 'all' && $category !== null) {

			if(Auth::user())
			{
			$category_follower_count = DB::table('category_follower')
									->whereIn('category_id', $category_ids)
									->where('follower_id', Auth::user()->id)
									->count();
			}
			

		}
		else {
			$category_follower_count = 1;
			foreach ($category_ids as $id) {
				if(Auth::user())
				{
					$category_follower_count = DB::table('category_follower')
										->where('category_id', $id)
										->where('follower_id', Auth::user()->id)
										->count();
					if ($category_follower_count < 1) {
						$category_follower_count = 0;
						break;
					}
				}
			}
		}
		
		if ($category_follower_count > 0) {
			$followStatus = 1;
		}

		if (!empty($category_ids) || !empty($post_tag_ids)) {
			/* 
			 * Get the public and "followers only" posts of user's
			 * whom the logged in user following.
			 */
			if (Auth::check()) {
// //changes(10-11-17) PS
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
				// if ($post_type === 'location') {

				// 	$public_or_follower_post =
				// 	Post::where('privacy_id', 1)
				// 		->orWhere(function ($query) {
				// 			$query->where('privacy_id', 2)
				// 				->whereIn('id', function ($query) {
				// 					$query->select('id')
				// 						->from('posts')
				// 						->whereIn('created_by', function ($q2) {
				// 							$q2->select('user_id')
				// 								->from('followers')
				// 								->where('follower_id', Auth::user()->id);
				// 						})
				// 						->orWhere('created_by', Auth::user()->id);
				// 				});
				// 		})
				// 		->where('points','>','0')
				// 		->orderBy('id', 'desc')
				// 		->get(['id']);


				// }
				// else{
				// 	$public_or_follower_post =
				// 	    Post::where('privacy_id', 1)
				// 	        ->orWhere(function ($query) {
				// 	            $query->where('privacy_id', 2)
				// 	                ->whereIn('id', function ($query) {
				// 	                    $query->select('id')
				// 	                        ->from('posts')
				// 	                        ->whereIn('created_by', function ($q2) {
				// 	                            $q2->select('user_id')
				// 	                                ->from('followers')
				// 	                                ->where('follower_id', Auth::user()->id);
				// 	                        })
				// 	                        ->orWhere('created_by', Auth::user()->id);
				// 	                });
				// 			})
	
				// 	        ->orderBy('id', 'desc')
				// 	        ->get(['id']);

				// }
				
		// //changes(10-11-17) PS

            } else {

                $public_or_follower_post =
                    Post::where('privacy_id', 1)
                        ->orderBy('id', 'desc')
                        ->get(['id']);
            }

			$public_or_follower_post_ids = array_pluck($public_or_follower_post, 'id');
			
			// Prepare the post query.
			$post = Post::whereNull('orginal_post_id');
			if (!empty($category_ids) && !empty($post_tag_ids)) {
				$post->where(function($query) use($category_ids, $post_tag_ids) {
					$query->whereIn('category_id', $category_ids)
						->orWhereIn('id', $post_tag_ids);
				});
			}
			else if (!empty($category_ids)) {
				$post->whereIn('category_id', $category_ids);
			}
			else if (!empty($post_tag_ids)) {
				$post->whereIn('id', $post_tag_ids);
			}

			// Add filter based on post type.
			if ($this->filter_post_type > 0) {
				$post->where('post_type', $this->filter_post_type);
			}
			
			/*------------------ END For category/tag ------------------*/
			// Condition based on post types.
			if ($post_type === 'recent') {
				$post->whereIn('privacy_id', [1,2])
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
				// Get activity_post_id for category and category as tag.
				if (!empty($category_ids) && !empty($post_tag_ids)) {
					$activity_post_id = Post::where(function($query) use ($category_ids, $post_tag_ids) {
						$query->whereIn('category_id', $category_ids)
							->orWhereIn('id', $post_tag_ids);
					})
					->get(['id']);
				}
				// Get activity_post_id for category
				else if (!empty($category_ids)) {
					$activity_post_id = Post::where(function($query) use ($category_ids) {
						$query->whereIn('category_id', $category_ids);
					})
					->get(['id']);
				}
				// Get activity_post_id for  category as tag.
				else if (!empty($post_tag_ids)) {
					$activity_post_id = Post::where(function($query) use ($post_tag_ids) {
						$query->whereIn('id', $post_tag_ids);
					})
					->get(['id']);
				}
				
				// Add post_id to query activity_post.
				if (!empty($activity_post_id)) {
					// Get the post creaters..
					$post_users =  Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
					$activity_posts->whereIn('post_id', $activity_post_id)
						// Remove activity by the post creater.
						->whereNotIn('id', function($query) use ($post_users) {
							$query->select('id')
							->from('activity_post');
							foreach ($post_users as $post_user) {
								$query->orWhere(function($query) use ($post_user) {
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
					// determine point based on actibity.
					$point = 0;
					if ($activity_post->activity_id == 1) {
						$point = 2;
						$activityPostSort[$activity_post->post_id]['upvote'] += 1;
					}
					elseif ($activity_post->activity_id == 2) {
						$point = -2;
					}
					elseif ($activity_post->activity_id == 3) {
						$point = 10;
						$activityPostSort[$activity_post->post_id]['share'] += 1;
					}
					elseif ($activity_post->activity_id == 4) {
						$point = 10;
					}
					elseif ($activity_post->activity_id == 5) {
						$point = 10;
					}
					elseif ($activity_post->activity_id == 8) {
						$point = 2;
					}
					elseif ($activity_post->activity_id == 9) {
						$point = 2;
					}
					elseif ($activity_post->activity_id == 10) {
						$point = 2;
					}
					elseif ($activity_post->activity_id == 11) {
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

				// dd($activityPostSort);

				$sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
				// dd($post_ids);
				// Take  intersection..
				$post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);
				if (!empty($post_ids)) {
					// $post_ids_ordered = implode(',', $post_ids);
					$post_ids_ordered = implode(',', $sorted_activity_post_ids);

					$post->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
				}
				else {
					goto return_area;
				}
			}
			elseif ($post_type === 'popular') {
				$post->where('points', '>', 0);
				$day = 7;
				$activity_posts = DB::table('activity_post')
									->where('created_at', '>=', Carbon::now()->subDays($day));
				// Get activity_post_id for category & tag.
				$activity_post_id = [];
				// Get activity_post_id for category and category as tag.
				if (!empty($category_ids) && !empty($post_tag_ids)) {
					$activity_post_id = Post::where(function($query) use ($category_ids, $post_tag_ids) {
						$query->whereIn('category_id', $category_ids)
							->orWhereIn('id', $post_tag_ids);
					})
					->get(['id']);
				}
				// Get activity_post_id for category
				else if (!empty($category_ids)) {
					$activity_post_id = Post::where(function($query) use ($category_ids) {
						$query->whereIn('category_id', $category_ids);
					})
					->get(['id']);
				}
				// Get activity_post_id for  category as tag.
				else if (!empty($post_tag_ids)) {
					$activity_post_id = Post::where(function($query) use ($post_tag_ids) {
						$query->whereIn('id', $post_tag_ids);
					})
					->get(['id']);
				}
				
				// Add post_id to query activity_post.
				if (!empty($activity_post_id)) {
					// Get the post creaters..
					$post_users =  Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
					$activity_posts->whereIn('post_id', $activity_post_id)
									// Remove activity by the post creater.
									->whereNotIn('id', function($query) use ($post_users) {
									    $query->select('id')
									    ->from('activity_post');
									    foreach ($post_users as $post_user) {
									    	$query->orWhere(function($query) use ($post_user) {
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
					// determine point based on actibity.
					$point = 0;
					if ($activity_post->activity_id == 1) {
						$point = 2;
						$activityPostSort[$activity_post->post_id]['upvote'] += 1;
					}
					elseif ($activity_post->activity_id == 2) {
						$point = -2;
					}
					elseif ($activity_post->activity_id == 3) {
						$point = 10;
						$activityPostSort[$activity_post->post_id]['share'] += 1;
					}
					elseif ($activity_post->activity_id == 4) {
						$point = 10;
					}
					elseif ($activity_post->activity_id == 5) {
						$point = 10;
					}
					elseif ($activity_post->activity_id == 8) {
						$point = 2;
					}
					elseif ($activity_post->activity_id == 9) {
						$point = 2;
					}
					elseif ($activity_post->activity_id == 10) {
						$point = 2;
					}
					elseif ($activity_post->activity_id == 11) {
						$point = 2;
					}

					$activityPostSort[$activity_post->post_id]['point'] += $point;
					$activityPostSort[$activity_post->post_id]['post_id'] = $activity_post->post_id;


				


				}
				/* ===============================================================
				 * Sort the array in followig order:
				 * 1. point, 2. upvote, 3. share, 4. latest
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
				}
				else {
					goto return_area;
				}
			}
			if ($post_type === 'location') {
// 				$post->where('points', '>=', 0);// //changes(10-11-17) PS
				
// 				// $this->clientIp = $this->clientIp == '127.0.0.1' ? '115.187.63.83' : $this->clientIp;
				
// 				$api_link = 'https://ipinfo.io/' . $this->clientIp;
// 				$guzzle = new GuzzleClient();
// 				$response_body = $guzzle->get($api_link, [
//                     'verify' => false
//                 ])->getBody();
// 				$ipinfo_obj = json_decode($response_body);
// 				// Query country.
// 				// what if we find no country from ip
// 				if (!empty($ipinfo_obj->country)) {
// 					$day = 7;
// 					$allowedCreationTime = Carbon::now()->subDays($day);
// 					// Query based on 
					
// 					$post->where('country_code', $ipinfo_obj->country)
					
// 							/*->where('created_at', '>=', $allowedCreationTime)*/;
// 					/*--- Sort posts based on activity, posts must be less that 7 days old ---*/

// 					$activity_posts = [];//changes(10-11-17) PS
					
// //changes(10-11-17) PS					
// 					 $activity_posts = DB::table('activity_post');
// 					// 					// Get activities of atmost 7 days posts.
// 					// 					->whereIn('post_id', function($query) use ($allowedCreationTime) {
// 					// 						$query->select("id")
// 					// 								->from('posts')
// 					// 								->where('created_at', '>=', $allowedCreationTime);
// 					// 					});
// 					// // Get the post creaters..
// 					 $post_users =  Post::where('created_at', '>=', $allowedCreationTime)
// 					 					->get(['id', 'created_by']);
// 					 // Remove activity by the post creater.

// 					$activity_posts->whereNotIn('id', function($query) use ($post_users) {
// 					    $query->select('id')
// 					    ->from('activity_post');
// 					    foreach ($post_users as $post_user) {
// 					    	$query->orWhere(function($query) use ($post_user) {
// 								    $query->where('user_id', $post_user->created_by)
// 								    		->where('post_id', $post_user->id);
// 							});
// 					    }
// 					});
// 					$activity_posts = $activity_posts->get(['activity_id', 'post_id']);
					
// //changes(10-11-17) PS					

// 					$activityPostSort = [];

// 				if(!empty($activity_posts))
// 				{
// 					foreach ($activity_posts as $key => $activity_post) {
// 						// Initialize.
// 						if (empty($activityPostSort[$activity_post->post_id]['point'])) {
// 							$activityPostSort[$activity_post->post_id]['point'] = 0;
// 						}
// 						if (empty($activityPostSort[$activity_post->post_id]['upvote'])) {
// 							$activityPostSort[$activity_post->post_id]['upvote'] = 0;
// 						}
// 						if (empty($activityPostSort[$activity_post->post_id]['share'])) {
// 							$activityPostSort[$activity_post->post_id]['share'] = 0;
// 						}
// 						// determine point based on activity.
// 						$point = 0;
// 						if ($activity_post->activity_id == 1) {
// 							$point = 2;
// 							$activityPostSort[$activity_post->post_id]['upvote'] += 1;
// 						}
// 						elseif ($activity_post->activity_id == 2) {
// 							$point = -2;
// 						}
// 						elseif ($activity_post->activity_id == 3) {
// 							$point = 10;
// 							$activityPostSort[$activity_post->post_id]['share'] += 1;
// 						}
// 						elseif ($activity_post->activity_id == 4) {
// 							$point = 10;
// 						}
// 						elseif ($activity_post->activity_id == 5) {
// 							$point = 10;
// 						}
// 						elseif ($activity_post->activity_id == 8) {
// 							$point = 2;
// 						}
// 						elseif ($activity_post->activity_id == 9) {
// 							$point = 2;
// 						}
// 						elseif ($activity_post->activity_id == 10) {
// 							$point = 2;
// 						}
// 						elseif ($activity_post->activity_id == 11) {
// 							$point = 2;
// 						}
						
// 						$activityPostSort[$activity_post->post_id]['point'] += $point;
// 						$activityPostSort[$activity_post->post_id]['post_id'] = $activity_post->post_id;
						

// 							/*****get distance for sorting (13-11-17)**************/
// 								$userLocationInfo   = Session::get('userLocationInfo');
// 								if($userLocationInfo !== null ){
// 									$postInformation=Post::where('id',  $activity_post->post_id)
// 									->first();
// 									$userLatitude       = $userLocationInfo['lat'];
// 									$userLongitude      = $userLocationInfo['lon'];
// 									$latitudeTo  = floatval($postInformation->lat);
// 									$longitudeTo = floatval($postInformation->lon); 
									
// 									$distance1 = haversineGreatCircleDistance($userLatitude, $userLongitude, $latitudeTo, $longitudeTo);
// 									$activityPostSort[$activity_post->post_id]['distance1'] = $distance1;
									
// 								}
								
// 							/*******get distance for sorting(13-11-17)******/	
// 					}
// 				}
// 			//	print_r($activityPostSort);
			
// 					/* ===============================================================
// 					 * Sort the array in following order:
// 					 * 1.distance 2. point, 3. up vote, 4. share
// 					================================================================ */
// 					usort($activityPostSort, function ($item1, $item2) {
// 						if ($item1['distance1'] == $item2['distance1']) {
// 							if ($item1['point'] == $item2['point']) {
// 									if ($item1['upvote'] == $item2['upvote']) {
// 										if ($item1['share'] == $item2['share']) {
// 											// Sort by latest post.
// 											return $item2['post_id'] < $item1['post_id'] ? -1 : 1;
// 										}
// 										return $item2['share'] < $item1['share'] ? -1 : 1;
// 									}
// 									return $item2['upvote'] < $item1['upvote'] ? -1 : 1;
// 								}
// 							return $item2['point'] < $item1['point'] ? -1 : 1;
// 						}
// 						return $item2['distance1'] > $item1['distance1'] ? -1 : 1;
						
// 					});

// 					/*if (!empty($_REQUEST['test'])) {
// 						dd($activityPostSort);
// 					}*/

// 					$sorted_activity_post_ids = array_pluck($activityPostSort, 'post_id');
// 					// dd($post_ids);
// 					// Take  intersection..
// 					$post_ids = array_intersect($public_or_follower_post_ids, $sorted_activity_post_ids);
// 					// Allow 0 point posts also.
// 					// $post_ids = $public_or_follower_post_ids;
// 					if (!empty($post_ids)) {
// 						// $post_ids_ordered = implode(',', $post_ids);
// 						$post_ids_ordered = implode(',', $sorted_activity_post_ids);

// 						$post->whereIn('id', $post_ids)
// 							// ->orderByRaw("FIELD(id, $post_ids_ordered)")
// 							->orderByRaw("IF(FIELD(id,$post_ids_ordered)=0,1,0) ,FIELD(id,$post_ids_ordered)")
// 							;
// 					}

// 					/*===================================================================*/
// 					// Order posts based on lat and lon
// 					/*if (!empty($this->position['lat']) && !empty($this->position['lng'])) {
// 						$lat = $this->position['lat'];
// 						$lon = $this->position['lng'];
// 						$post->orderByRaw("3956 * 2 * ASIN(SQRT( POWER(SIN(($lat - abs(lat)) *  pi()/180 / 2), 2) + COS($lat * pi()/180) * COS(abs(lat) * pi()/180) * POWER(SIN(($lon - lon) * pi()/180 / 2), 2) )) ");
// 					}
// 					else {
// 						$post->orderBY('id', 'desc');
// 					}*/
// 					/*==================================================================*/
// 				}
// 				else {
// 					goto return_area;
// 				}





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
									  )";
						}

						if (!empty($category_ids) && !empty($post_tag_ids)) {
							$sql=$sql." AND ( category_id IN ('".implode("','",$category_ids)."') OR  id IN ('".implode("','",$post_tag_ids)."') )";
						}
						else if (!empty($category_ids)) {
							$sql=$sql." AND category_id IN ('".implode("','",$category_ids)."' )";
						}
						else if (!empty($post_tag_ids)) {
							$sql=$sql." AND id IN ('".implode("','",$post_tag_ids)."' )";
						}


						$sql=$sql." AND 
						`points`>=0
						   
							".($this->filter_post_type > 0 ? " AND `post_type`='$this->filter_post_type' " : "")." GROUP BY `id` ORDER BY  `distance` ASC,  `created_at` DESC "; //remove `points` DESC,



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
				/********** block the previous code for change the logic for feture-image start (13-02-18)***********/
						// $post = $post->where('image', '<>', '')
						// 				->where('privacy_id', 1)
						// 				->where('points', '>', 0)
						// 				->sinceDaysAgo(7)
						// 				// ->whereIn('id', $public_or_follower_post_ids)
						// 				->with('user')
						// 				->orderBy('points', 'desc')->first();
						// return $post;
				/********** block the previous code for change the logic for feture-image start (13-02-18)***********/

				/********* Code for new logic (13-02-18) start  *****************************/
					
						$day = 7;
						$activity_posts = DB::table('activity_post')
											->where('created_at', '>=', Carbon::now()->subDays($day));
						// Get activity_post_id for category & tag.
						$activity_post_id = [];
						// Get activity_post_id for category and category as tag.
						if (!empty($category_ids) && !empty($post_tag_ids)) {
							$activity_post_id = Post::where(function($query) use ($category_ids, $post_tag_ids) {
								$query->whereIn('category_id', $category_ids)
									->orWhereIn('id', $post_tag_ids);
							})
							->get(['id']);
						}
						// Get activity_post_id for category
						else if (!empty($category_ids)) {
							$activity_post_id = Post::where(function($query) use ($category_ids) {
								$query->whereIn('category_id', $category_ids);
							})
							->get(['id']);
						}
						// Get activity_post_id for  category as tag.
						else if (!empty($post_tag_ids)) {
							$activity_post_id = Post::where(function($query) use ($post_tag_ids) {
								$query->whereIn('id', $post_tag_ids);
							})
							->get(['id']);
						}
						
						// Add post_id to query activity_post.
						if (!empty($activity_post_id)) {
							// Get the post creaters..
							$post_users =  Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
							$activity_posts->whereIn('post_id', $activity_post_id)
											// Remove activity by the post creater.
											->whereNotIn('id', function($query) use ($post_users) {
												$query->select('id')
												->from('activity_post');
												foreach ($post_users as $post_user) {
													$query->orWhere(function($query) use ($post_user) {
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
							// determine point based on actibity.
							$point = 0;
							if ($activity_post->activity_id == 1) {
								$point = 2;
								$activityPostSort[$activity_post->post_id]['upvote'] += 1;
							}
							elseif ($activity_post->activity_id == 2) {
								$point = -2;
							}
							elseif ($activity_post->activity_id == 3) {
								$point = 10;
								$activityPostSort[$activity_post->post_id]['share'] += 1;
							}
							elseif ($activity_post->activity_id == 4) {
								$point = 10;
							}
							elseif ($activity_post->activity_id == 5) {
								$point = 10;
							}
							elseif ($activity_post->activity_id == 8) {
								$point = 2;
							}
							elseif ($activity_post->activity_id == 9) {
								$point = 2;
							}
							elseif ($activity_post->activity_id == 10) {
								$point = 2;
							}
							elseif ($activity_post->activity_id == 11) {
								$point = 2;
							}

							$activityPostSort[$activity_post->post_id]['point'] += $point;
							$activityPostSort[$activity_post->post_id]['post_id'] = $activity_post->post_id;


				


						}
						/* ===============================================================
						* Sort the array in followig order:
						* 1. point, 2. upvote, 3. share, 4. latest
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
							
				/********* Code for new logic (13-02-18) end  *****************************/
			}
			
			if(!empty($post))
			{

					// Count total posts..
					$totalPost = $post->count();
					// Column selection array for eager loaded data.
					// Column selection array
					$user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
					$category_columns = ['id', 'category_name'];
					$subCategory_columns = ['id', 'category_name'];
					$tag_columns = ['tags.id', 'tag_name','tag_text','question_tag','question_tag_created_at'];
					$region_columns = ['id', 'name', 'slug_name'];
					$featurePhotoDetail_column = ['id', 'post_id', 'thumb_width', 'thumb_height', 'data_url'];
					// Eager load data from relations.
					$post->with([
						'user' => function ($query) use ($user_columns) {
							$query->addSelect($user_columns);
						},
						'category' => function ($query) use ($category_columns) {
							$query->addSelect(array('id', 'category_name'));
						},
						'subCategory' => function ($query)  use ($subCategory_columns) {
							$query->addSelect(array('id', 'category_name'));
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
					]);

					// \DB::connection()->enableQueryLog();

					// Get the paginated result.
					$posts = $post->skip($this->offset)->take($this->per_page)
									->get()->makeVisible('people_here');

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

			/*$query = \DB::getQueryLog();
			// $lastQuery = end($query);
			dd($query);*/
		}
		return_area:
		// Prepare the return data.
		$return_data = [
			'followStatus' => $followStatus,
			'totalPost' => $totalPost,
			'posts' => $posts
		];
		return $return_data;
	}

	public function catTopChannel(Request $request)
	{
		$response = [];
		$users = [];
		$followStatus = 0; //(26-02-18) update
		if (! $request->has('name')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'name' parameter",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}
		$name = $request->input('name');
		$name = strtolower($name);
		
		// Initialize data
		$post_tag_ids = [];
		$category_ids = [];
		// Get category for particular category. 
		if ($name != 'all') {
			$name = strtolower($name);
			// Get post_tag ids
			$post_tag= DB::table('post_tag')->whereIn('tag_id',  function ($query) use($name) {
				$query->select('id')
					->from('tags')
					->where('tag_name', $name)->orWhere('tag_name', str_slug($name));
			})
			->get(['post_id']);

			// Get category case by searching insensitive category.
			$category = Category::searchByName($name)->get(['id']);
		}
		else {
			$category_tag_names = '';
			$all_categories = Category::all(['category_name_slug']);
			foreach ($all_categories as $category) {
				$category_tag_names .= "$category->category_name_slug|";
			}
			$category_tag_names = rtrim($category_tag_names, '|');
			
			// Get post_tag ids for all tags which are also category.
			$post_tag= DB::table('post_tag')->whereIn('tag_id',  function ($query) use($category_tag_names) {
				$query->select('id')
					->from('tags')
					->whereRaw("`tag_name` REGEXP '$category_tag_names'");
			})
			->get(['post_id']);

			// Get only main catgeory if selected all.
			$category = Category::where('parent_id', 0)->get(['id']);
		}

		// Set the post_tag post_ids.
		if ($post_tag !== null) {
			$post_tag_ids = array_pluck($post_tag, 'post_id');
			// dd($post_tag_ids);
		}

		// Set the category ids.
		if ($category !== null) {
			$category_ids = array_pluck($category, 'id');
		}


		/***************(26-02-18)************************/

		$category_follower_count=0;
		if ($name != 'all' && $category !== null) {

			if(Auth::user())
			{
			$category_follower_count = DB::table('category_follower')
									->whereIn('category_id', $category_ids)
									->where('follower_id', Auth::user()->id)
									->count();
			}
			

		}
		else {
			$category_follower_count = 1;
			foreach ($category_ids as $id) {
				if(Auth::user())
				{
					$category_follower_count = DB::table('category_follower')
										->where('category_id', $id)
										->where('follower_id', Auth::user()->id)
										->count();
					if ($category_follower_count < 1) {
						$category_follower_count = 0;
						break;
					}
				}
			}
		}
		
		if ($category_follower_count > 0) {
			$followStatus = 1;
		}

		/***************(26-02-18)************************/




		if (!empty($category_ids)) {
			// Prepare the post query.
			$post = Post::whereNull('orginal_post_id')->where('post_type', '<>', 5);
			if (!empty($category_ids) && !empty($post_tag_ids)) {
				$post->where(function($query) use($category_ids, $post_tag_ids) {
					$query->whereIn('category_id', $category_ids)
						->orWhereIn('id', $post_tag_ids);
				});
			}
			else if (!empty($category_ids)) {
				$post->whereIn('category_id', $category_ids);
			}
			else if (!empty($post_tag_ids)) {
				$post->whereIn('id', $post_tag_ids);
			}

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
				// Collumn selection array.
				$select_collumns = [
					'id',
					'username',
					'first_name',
					'last_name',
					'profile_image',
					'cover_image',
					'about_me'
				];
				
				// Eager load data.
                $user->withCount('originalPost', 'follower');
				// Get the paginated result.
				$users = $user->skip($this->offset)->take($this->per_page)->get($select_collumns);

                foreach ($users as $user) {
                    if (!empty($user->cover_image)) {
                        $user->cover_image = generate_profile_image_url('profile/cover/' . $user->cover_image);
                    }
                }
			}			
		}
		$response = [
			'users' => $users,
			'followStatus'=>$followStatus
		];
		return response()->json($response);
	}

	/**
	 *
	 */
	protected function getMainCategoriesWithCover() {
		$main_categories = [];
		
		$main_categories = Category::where('parent_id', 0)
									->orderBy('category_name')
									->get(['id', 'category_name']);

		if ($main_categories !== null) {
			$category_colors = config('constants.CATEGORY_COLORS');
			$total_color_count = count($category_colors);
			// randomize the colors.
			shuffle($category_colors);
			$category_color_count = 0;
			foreach ($main_categories as $key => $category) {
				$name = strtolower($category->category_name);
				$main_categories[$key]->value = $name;
				$post_type = 'cover_photo';
				$category_featured_post = $this->getExplorePosts($name, $post_type);
				if ($category_featured_post !== null) {
					$main_categories[$key]->featured_post_image = generate_post_image_url('post/thumbs/' . $category_featured_post->image);
				}
				else {
					$main_categories[$key]->featured_post_image = '';
					// Set colors.
					$category_color_count = $category_color_count == $total_color_count ? 0 : $category_color_count;
					$main_categories[$key]->color = $category_colors[$category_color_count];
					$category_color_count++;
				}
			}
		}
		return $main_categories;
	}


	protected function todaysTrendingTopics()
	{
			$date = new \DateTime();
			$date->modify('-24 hours');
			$formatted_date = $date->format('Y-m-d H:i:s');
		
	
			$tranding_tags= DB::table('post_tag')
			->whereIn('post_id', function($query) use ($formatted_date) {
				$query->select('id')
					->from('posts')
					->where('created_at', '>',$formatted_date)
					->whereNull('orginal_post_id');
			})
			
			->get(['tag_id']);
			$trnd_tags=array();
	
			$trnding_tag_details=[];
	
			foreach($tranding_tags as $tranding_tags)
			{
				array_push($trnd_tags,$tranding_tags->tag_id);
			}
	
			$t=array_count_values($trnd_tags);
			//print_r($t);
			arsort($t);
			$tranding_tag_details=[];
			$count=0;
			foreach($t as $k=>$v)
			{
				$tag_details=Tag::find($k);

				$match_category=Category::where('category_name_slug',$tag_details->tag_name)->count();

				if(!$match_category)
				{
					array_push($tranding_tag_details,$tag_details);
					$count++;
				}
				if($count>10)
					break;
				
			}
			return $tranding_tag_details;
			
	
		
	}


	public function savepost()
	{
		return view('savepost.index');
	}
	public function nearby()
	{
		return view('nearby.index');
	}
	public function followingTopicGrid()
	{
		return view('explore.topics');
	}
	public function fetchNearbyPostJson(Request $request){

		$totalPost = 0;
        $final_posts = $posts = [];
        $locationInfo   = $request->input('location');

        if(
            !empty($locationInfo) &&
            !empty($locationInfo['lat']) &&
            !empty($locationInfo['lon']) &&
            !empty($locationInfo['country_code']) &&
            !empty($locationInfo['city']) &&
            !empty($locationInfo['state'])

        ){
        	 $latitudeFrom 	= $locationInfo['lat'];
        	 $longitudeFrom	= $locationInfo['lon'];
        	 $country_code  = $locationInfo['country_code'];
             $data = [
                'lat' => $latitudeFrom,
                'lon' => $longitudeFrom,
                'city' => $locationInfo['city'],
                'state' => $locationInfo['state'],
                'country_code' => $country_code
             ];
            //Save user location in session
            Session::put('userLocationInfo', $data);
        }
        else {
        	$userLocationInfo   = Session::get('userLocationInfo');
            if (
                !empty($userLocationInfo) &&
                !empty($userLocationInfo['lon']) &&
                !empty($userLocationInfo['country_code'])
            ) {
                $latitudeFrom 		= $userLocationInfo['lat'];
                $longitudeFrom		= $userLocationInfo['lon'];
                $country_code  		= $userLocationInfo['country_code'];
            }
        	else {
                $response = [
                    'totalPost' => 0,
                    'posts' => []
                ];
                return response()->json($response);
            }
        }

        $activity_id_list   = [1,3,4,5,8,9,10,11];
        $currentDateTime 	= Carbon::now()->toDateTimeString();
        $dayBeforeDateTime  = Carbon::now()->addDays(-1);

        $lastActivityDate   = Carbon::now()->addDays(-3); 

        //DB::connection()->enableQueryLog();

		//$sql = "SELECT `id` FROM `posts` WHERE `posts`.`country_code`='".$locationInfo['country_code']."' AND (`posts`.`privacy_id`='1' OR (`posts`.`privacy_id`='2' AND `posts`.`created_by` IN (SELECT `user_id` FROM `followers` WHERE `follower_id`='".Auth::user()->id."'))) AND (`posts`.`orginal_post_id` IS NULL AND  `posts`.`created_by` != '".Auth::user()->id."') AND ((`posts`.`created_at` >= '$dayBeforeDateTime' AND `posts`.`created_at`<'$currentDateTime')) ORDER BY `posts`.`created_at` DESC LIMIT ".$this->offset.",".$this->per_page;

		
		// previous logic before (9-11-17) select all post where (country is current country of the login user) and (privacy of the post is equal or the login user follow the creator of the post .) and (the post is not posted by login user self) and  ( user have an activity within activities like 1,3,4,5,8,9,10,11  and this activity done within last 3 days  or the post is created yesterday and post type equals to given post type )


		//$sql = "SELECT `posts`.`id`, `posts`.`created_at`, ( 6371 * acos( cos( radians(".$latitudeFrom.") ) * cos( radians( `posts`.`lat` ) ) * cos( radians(  `posts`.`lon` ) - radians(".$longitudeFrom.") ) + sin( radians(".$latitudeFrom.") ) * sin( radians( `posts`.`lat` ) ) ) ) AS distance,  COUNT(`activity_post`.`id`) as `totalActivity` FROM `posts` LEFT JOIN `activity_post` ON `posts`.`id`=`activity_post`.`post_id` WHERE (`posts`.`country_code`='".$country_code."' AND `posts`.`lat`!='' AND  `posts`.`lon`!='') AND (`posts`.`privacy_id`='1' OR (`posts`.`privacy_id`='2' AND `posts`.`created_by` IN (SELECT `user_id` FROM `followers` WHERE `follower_id`='".Auth::user()->id."'))) AND (`posts`.`orginal_post_id` IS NULL AND `posts`.`created_by` != '".Auth::user()->id."') AND ((`activity_post`.`user_id`!=`posts`.`created_by` AND `activity_post`.`activity_id` IN(".implode(',',$activity_id_list).") AND `activity_post`.`updated_at` >= '$lastActivityDate') OR (`posts`.`created_at` >= '$dayBeforeDateTime' AND `posts`.`created_at`<'$currentDateTime')) ".($this->filter_post_type > 0 ? " AND `posts`.`post_type`='$this->filter_post_type' " : "")." GROUP BY `posts`.`id` ORDER BY `distance` ASC, `posts`.`points` DESC, `posts`.`created_at` DESC LIMIT ".$this->offset.",".$this->per_page;


		//(9-10-17) 
		//changes:
		//logic  remove :

		// 1. Post that created yesterday OR
		// 2. Post with activities within 3 days
		// In addition, add this :

		// 1. Post with 0 or more points (if negative we will skip it)


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
				 
			AND 
				`points`>=0
				   
					".($this->filter_post_type > 0 ? " AND `post_type`='$this->filter_post_type' " : "")." GROUP BY `id` ORDER BY `distance` ASC, `points` DESC, `created_at` DESC LIMIT ".$this->offset.",".$this->per_page;
		


        $nearBylocation = DB::select($sql);


        /*$query = DB::getQueryLog();
        $lastQuery = end($query);
        dump($lastQuery);*/

        /*$nearBylocation = DB::table('posts')
            ->where('country_code', $locationInfo['country_code'])
            ->skip($this->offset)
            ->take($this->per_page)
            ->orderBy('created_at', 'DESC')
            ->get();*/

        $searchNearby = array_pluck($nearBylocation, 'id');

        $arrayImplode = implode(",", $searchNearby);

        // \DB::connection()->enableQueryLog();

        if(!empty($searchNearby)) {

            $post = Post::whereIn('id', $searchNearby);

            // Add filter based on post type.
            /*if ($this->filter_post_type > 0) {
                $post->where('post_type', $this->filter_post_type);
            }*/

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
                },
                'orginalPost.featurePhotoDetail' => function ($query) use ($featurePhotoDetail_column) {
                    $query->addSelect($featurePhotoDetail_column);
                },
            ])
            ->withCount('comment');

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

            // dd($posts);

            //$query = \DB::getQueryLog();
            // $lastQuery = end($query);
            //dd($query);
            /*============== Modify post data ================*/
            $post_count = count($posts);
            $final_posts = [];
            for ($p = 0; $p < $post_count; $p++) {
                $parent_post_user_id = $posts[$p]->parent_post_user_id;
                $current_post = null;

                $original_caption = $posts[$p]->caption;

                //Calculate distance between post loaction and users current location
                $latitudeTo     = $posts[$p]->lat;
                $longitudeTo    = $posts[$p]->lon;
                $posts[$p]->distance = haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);/**/
                //$posts[$p]->distance = number_format($nearBylocation[$p]->distance, 3, '.', '');

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

                $totalShare = DB::table('activity_post')
                    ->where(['post_id' => $posts[$p]->id])
                    ->whereIn('activity_id', [3, 4, 5])
                    ->select(['id'])
                    ->count();
                $posts[$p]->totalShare = $totalShare;


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
                }

                if (!empty($final_posts[$p]->video)) {
                    $final_posts[$p]->video = generate_post_video_url('video/' . $final_posts[$p]->video);
                }
                if (!empty($final_posts[$p]->video_poster)) {
                    $final_posts[$p]->video_poster = generate_post_video_url('video/thumbnail/' . $final_posts[$p]->video_poster);
                }

                if (!empty($final_posts[$p]->feature_photo_detail)) {
                    $percentage = ($final_posts[$p]->feature_photo_detail->thumb_height /
                            $final_posts[$p]->feature_photo_detail->thumb_width) * 100;
                    $final_posts[$p]->feature_photo_detail->percentage = round($percentage, 2);
                }
            }
        }
        $response = [
            'totalPost' => $totalPost,
            'posts' => $final_posts
        ];
        return response()->json($response);
	}

	

}
