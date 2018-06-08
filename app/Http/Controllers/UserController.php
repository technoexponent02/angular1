<?php

namespace App\Http\Controllers;

use Mail;
use Auth;
use Validator;
use DB;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Userview;
use App\Models\Follower;
use App\Models\State;
use App\Models\Country;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Collection;
use App\Models\Privacy;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller {

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

    
    public function forgetPassword()
    {
        return view('auth.password');
    }

    public function forgotPasswordProcess(Request $request)
    {
        $input_data=$request->all();
        $this->validate($request, [
           'email' => 'required|email',
        ]);
        $user = User::where('email',$input_data['email'])->first();

        $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);

        if(count($user))
        {
            $token = base64_encode($user->id.'####'.$randomString);            

            $data  = [
                'token' => $token,
                'profile_pic' => $user->thumb_image_url ? $user->thumb_image_url : url("assets/img/swolk-icon.png"),
                'fullname'     => $user->first_name." ".$user->last_name
            ];

            Mail::send('emails.password', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)->subject('Your Password Reset Link');
            });
            
            $user->reset_password_token = $randomString;
            $user->save();
            //return redirect(url('password/forgot'))->with('status', 'We have emailed your password reset link!'); //(17-11-17)
            return redirect(url('explore'))->with('status', 'We have emailed your password reset link!');
        }
        else {
           // return redirect(url('password/forgot'))->with('errors', 'Internal error');   //(17-11-17) 
           return redirect(url('explore'))->with('errors', 'Internal error'); 

        }
        
    }
        
    public function resetPassword($token = '')
    {
        if(!Auth::check()) {
            $info_user = base64_decode($token);
            $info_user_arr = explode('####',$info_user);
                
            $user_id = $info_user_arr[0];
            $user = User::find($user_id); 
            
            if ($user !== null) {
                if($user->reset_password_token == $info_user_arr[1]) {
                    return view('auth.reset')->with('token', $token);
                }
                else {
                    return view('errors.404');
                }
            }
            else {
                return view('errors.404');
            }    
        }
        else {
            return redirect(url('users/dashboard'));
        }
    }

    public function resetPassProcess(Request $request)
    {
        $input_data=$request->all();

        $this->validate($request, [
           'password' => 'required|alpha_num|min:8|max:25|confirmed',
        ]);

        if (!$input_data['token']) {
            return  redirect(url('password/reset/'.$input_data['token']))->with('errors', 'Invalid token error.');
        }
        if ($input_data['password'] != $input_data['password_confirmation']) {
            return  redirect(url('password/reset/'.$input_data['token']))->with('error_msg', 'Password  confirmation password does not matched.');
        }
        $info_user = base64_decode($input_data['token']);
        $info_user_arr = explode('####',$info_user);
        
        $user_id = $info_user_arr[0];
        $update=0;
        $user = User::find($user_id);
        //dump($input_data);exit;
        if($user !== null) {
            /*$user->password = bcrypt($input_data['password']);*/
            $user->password = $input_data['password'];
            $user->reset_password_token = "";
            $update=$user->save();
        }
        if($update==1){
            return  redirect(url('/'))->with('status', 'Password successfully changed!');
        }
    }

    /**
     * Get list of random tags that the user following.
     */
    public function tags()
    {
        $tag_columns = ['tag_name','tag_text','tags.id','question_tag'];

        // Recent tags by user.
        $post_tag_data = DB::table('post_tag')
            ->whereIn('post_id', function($query){
                $query->select('id')
                    ->from('posts')
                    ->where('created_by', Auth::user()->id);
            })
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get(['id', 'tag_id']);

        $user_latest_tag_ids = [];
        foreach ($post_tag_data as $value) {
            $user_latest_tag_ids[] = $value->tag_id;
        }

        $recent_tags = Tag::whereIn('id', $user_latest_tag_ids)->get($tag_columns);
        // Format text properly.
        /*foreach ($recent_tags as $key => $tag) {
            $name = slug_ovr_rev($tag->tag_name);
            $tag->tag_name = ucwords($name[1]);
        }*/

        $following_tags = Auth::user()->tags()
                                        ->whereNotIn('tags.id', $user_latest_tag_ids)
                                        ->inRandomOrder()
                                        ->distinct()
                                        ->take(30)
                                        ->get($tag_columns);
        // Format text properly.
        /*foreach ($following_tags as $key => $tag) {
            $name = slug_ovr_rev($tag->tag_name);
            $tag->tag_name = ucwords($name[1]);
        }*/

        $response = [
            'recent_tags' => $recent_tags,
            'following_tags' => $following_tags,
        ];
        return response()->json($response);
    }

    /**
     * Get list of random tags that the user following.
     */
    public function followingTags()
    {
        $logged_in_user_id = Auth::user()->id; 
        $tag_columns = ['tag_name','tag_text','tags.id', 'question_tag','tag_user.created_at'];
        $following_tags = Auth::user()->tags()
                                        /*->inRandomOrder()*/
                                        ->distinct()
                                        // ->orderBy('tag_user.created_at', 'desc')
                                        ->get($tag_columns);

        $category_columns = ['category_name', 'category_name_slug','categories.id', /*'parent_id',*/ 'category_follower.created_at'];
        $following_categories = Auth::user()->categories()
                                        ->distinct()
                                        ->get($category_columns);
                                       
        // Merge two types of tags.
        $merged_tags = $following_categories->merge($following_tags);
        // Modify.
        $pattern = '/-(&-)?/i';
        foreach ($merged_tags as $key => $tag) {
            if (!empty($tag->tag_name)) {
                //$merged_tags[$key]->tag_name = preg_replace($pattern, ' ', $merged_tags[$key]->tag_name);
                $last_visted_ts = DB::table('tag_user')->select('last_visited')->where([
                    ['tag_id', '=', ''.$tag->id.''],
                    ['user_id', '=', ''.$logged_in_user_id.'']
                ])->first()->last_visited;
                
                // $merged_tags[$key]->value = str_slug_ovr($tag->tag_name);
                $merged_tags[$key]->type = 'tag';
            }
            elseif (!empty($tag->category_name)) {
                $merged_tags[$key]->tag_name = $tag->category_name;
                $merged_tags[$key]->category_name_slug = $tag->category_name_slug;
                $last_visted_ts = DB::table('category_follower')->select('last_visited')->where([
                    ['category_id', '=', ''.$tag->id.''],
                    ['follower_id', '=', ''.$logged_in_user_id.'']
                ])->first()->last_visited;
                // $merged_tags[$key]->value = str_slug_ovr($tag->category_name);
                unset($tag->category_name);
                $merged_tags[$key]->type = 'category';
            }
            $total_new_post_count = $this->getLatestPostByCategoryTagName($merged_tags[$key]->tag_name, $last_visted_ts);
            //Get The Feature Image of that cover photo

            $featured_image_post='';//(01-03-18)
            //$featured_image_post = $this->getPostByCategoryTagName($merged_tags[$key]->tag_name, $post_type = 'cover_photo');(01-03-18)

            $featured_image_post_image = !empty($featured_image_post->image)  ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

            $merged_tags[$key]->last_visited    = $last_visted_ts;
            $merged_tags[$key]->new_post_count  = $total_new_post_count['totalPost'];
            $merged_tags[$key]->featured_image  = $featured_image_post_image;    
        }
        
        // Take only unique names.
        $merged_tags = $merged_tags->unique(function ($item) {
            return str_slug_ovr($item['tag_name']);
        });

        /*------- Get followed place --------*/
        $place_followers = DB::table('place_follower')->where('user_id', Auth::user()->id)->get();
        // dd($place_followers);
        $place_tags = [];
        foreach ($place_followers as $place_follower) {
            $new_post_count = Post::where('place_url', $place_follower->place_url)
                                    ->where('created_by', '<>', Auth::user()->id)
                                    ->where('created_at', '>=', $place_follower->last_visited)
                                    ->count();
            $place_name = '';
            if (!empty($place_follower->location)) {
                $place_name = $place_follower->location;
            }
            elseif (!empty($place_follower->city)) {
                $place_name = $place_follower->city;
            }
            elseif (!empty($place_follower->state)) {
                $place_name = $place_follower->state;
            }
            elseif (!empty($place_follower->country)) {
                $place_name = $place_follower->country;
            }
            elseif (!empty($place_follower->region)) {
                $place_name = $place_follower->region;
            }
            elseif (!empty($place_follower->continent)) {
                $place_name = $place_follower->continent;
            }
            $place_name = str_replace('-', ' ', $place_name);
            $place_name = str_replace('-and-', ' & ', $place_name);

            // Prepare query input for featured post image.
            parse_str($place_follower->place_url, $query_input);
             // Get the featured post for the url.
             
             $featured_image_post='';//(01-03-18)
           // $featured_image_post = $this->getPostForPlace($query_input, $post_type = 'cover_photo');
            $featured_image_post_image = !empty($featured_image_post->image) ? generate_post_image_url('post/thumbs/' . $featured_image_post->image) : '';

            $place_tags[] = [
                'tag_name' 		  => $place_name,
                'place_url' 	  => $place_follower->place_url,
                'new_post_count'  => $new_post_count,
                'type' 			  => 'place',
                'created_at' 	  => $place_follower->created_at,
				'featured_image'  => $featured_image_post_image
            ];
        }

        // Sort order 1.type -> 2.follow time.
        $merged_tags = $merged_tags->toArray();
        $merged_tags = array_merge($merged_tags, $place_tags);
        usort($merged_tags, function($item1, $item2) {
            // Sort based on follow time when same type tag.
            return $item1['created_at'] < $item2['created_at'] ? -1 : 1;
        });

        $response = [
            'following_tags' => $merged_tags
        ];
        
        return response()->json($response);
    }

    protected function getLatestPostByCategoryTagName($name, $timestamp)
    {
        $totalPost = 0;
        $posts = [];
        $name = strtolower($name);
        $last_visited = $timestamp;
        $new_post_count = 0;
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
                    ->orWhere(function($query) {
                        $query->where('privacy_id', 2)
                                ->whereIn('id', function($query) {
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

            /*----------- For category/tag -----------*/
            $tag_post_id = [];
            if ($tag !== null) {
                $post_tag = DB::table('post_tag')->where('tag_id', $tag->id)->get(['post_id']);
                $tag_post_id = array_pluck($post_tag, 'post_id');
            }

            /*==================== Here we go ======================*/
            // Get posts for category & tag.
            if ($category !== null && !empty($tag_post_id)) {
                $post->where(function($query) use ($category, $tag_post_id) {
                    $query->where('category_id', $category->id)
                            ->orWhere('sub_category_id', $category->id)
                            ->orWhereIn('id', $tag_post_id);
                });
            }
            // Get posts for category
            elseif ($category !== null) {
                $post->where(function($query) use ($category) {
                    $query->where('category_id', $category->id)
                            ->orWhere('sub_category_id', $category->id);
                });
            }
            // Get posts for tag.
            elseif (!empty($tag_post_id)) {
                $final_post_ids = array_intersect($public_or_follower_post_ids, $tag_post_id);
                $post->whereIn('id', $final_post_ids);
            }
            else {
                goto return_area;
            }
            /*------------------ END For category/tag ------------------*/
            // Condition based on post types...
            $post->whereIn('privacy_id', [1,2])
                ->orderBy('created_at', 'desc')
                ->whereIn('id', $public_or_follower_post_ids);
            $new_post = $post;
            $new_post_count = $new_post->where('created_at', '>=', $last_visited)->where('created_by', '!=', Auth::user()->id)->count();    
            // Count total posts..
            //$totalPost = $post->count();
        
        }
        return_area:
        // Prepare the return data.
        $return_data = [
            'totalPost' => $new_post_count,
        ];
        return $return_data;
    }

    protected function getPostByCategoryTagName($name, $post_type) {
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
        if(Auth::check()){
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

        /*============== For location search ================*/
        // Get country by searching insensitive country.
        /*$country = Country::where('country_name', 'like', $name)->first(['country_code']);

        $location_post = Post::where(function($query) use ($name, $country) {
                                    $query->where('location', 'like', $name)
                                        ->orWhere('city', 'like', $name)
                                        ->orWhere('state', 'like', $name)
                                    ;
                                    if ($country !== null) {
                                        $query->orWhere('country_code', 'like', $country->country_code);
                                    }
                                })
                                ->get(['id']);
        $location_post_ids = array_pluck($location_post ,'id');*/
//        dd($location_post_ids);
//        dd($location_post ? $location_post->toArray() : null);
        /*============== END for location search ================*/

        if ($tag !== null || $category !== null/* || $location_post !== null*/) {
            /* 
             * Get the public and "followers only" posts of user's
             * whom the logged in user following.
             */
            if(Auth::check()){
                $public_or_follower_post = 
                    Post::where('privacy_id', 1)
                        ->orWhere(function($query) {
                            $query->where('privacy_id', 2)
                                    ->whereIn('id', function($query) {
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
            /*if ($this->filter_post_type > 0) {
                $post->where('post_type', $this->filter_post_type);
            }*/

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
                $post->where(function($query) use ($category, $tag_post_id) {
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
            }
            // Get posts for category
            elseif ($category !== null) {
                $post->where(function($query) use ($category) {
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
            }
            // Get posts for tag.
            elseif (!empty($tag_post_id)) {
                if ($post_type !== 'trending' && $post_type !== 'popular') {
                    $final_post_ids = array_intersect($public_or_follower_post_ids, $tag_post_id);
                    $post->whereIn('id', $final_post_ids);
                }
            }
            else {
                $unique_follower = array_unique($tag_followers);
                $totalFollower = count($unique_follower);
                goto return_area;
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
                if ($category !== null && !empty($tag_post_id)) {
                    $activity_post_id = Post::where(function($query) use ($category, $tag_post_id) {
                        $query->where('category_id', $category->id)
                                ->orWhere('sub_category_id', $category->id)
                                ->orWhereIn('id', $tag_post_id);
                    })
                    ->get(['id']);
                }
                // Get activity_post_id for category
                elseif ($category !== null) {
                    $activity_post_id = Post::where(function($query) use ($category) {
                        $query->where('category_id', $category->id)
                                ->orWhere('sub_category_id', $category->id);
                    })
                    ->get(['id']);
                }
                // Get activity_post_id for tag.
                elseif (!empty($tag_post_id)) {
                    $activity_post_id = Post::whereIn('id', $tag_post_id)->get(['id']);
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
                    // determine point based on activity.
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
                        $point = 1;
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
                                    // ->orderBy('post_id')
                // Get activity_post_id for category & tag.
                $activity_post_id = [];
                if ($category !== null && !empty($tag_post_id)) {
                    $activity_post_id = Post::where(function($query) use ($category, $tag_post_id) {
                        $query->where('category_id', $category->id)
                                ->orWhere('sub_category_id', $category->id)
                                ->orWhereIn('id', $tag_post_id);
                    })
                    ->get(['id']);
                }
                // Get activity_post_id for category
                elseif ($category !== null) {
                    $activity_post_id = Post::where(function($query) use ($category) {
                        $query->where('category_id', $category->id)
                                ->orWhere('sub_category_id', $category->id);
                    })
                    ->get(['id']);
                }
                // Get activity_post_id for tag.
                elseif (!empty($tag_post_id)) {
                    $activity_post_id = Post::whereIn('id', $tag_post_id)->get(['id']);
                }
                // Add post_id to query activity_post.
                if (!empty($activity_post_id)) {
                    // Get the post creators..
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
                    $point = 0;
                    // determine point based on activity.
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
                        $point = 1;
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
                }
                else {
                    goto return_area;
                }
            }

            // For cover photo.
            if ($post_type === 'cover_photo') {
                // \DB::connection()->enableQueryLog();
                /******block code due to change logic (13-02-18) start  ***********/
                // $post = $post->where('image', '<>', '')
                //                 ->where('privacy_id', 1)
                //                 ->where('points', '>', 0)
                //                 ->sinceDaysAgo(7)
                //                 // ->whereIn('id', $public_or_follower_post_ids)
                //                 ->with('user')
                //                 ->orderBy('points', 'desc')->first();
                // /*$query = \DB::getQueryLog();
                // // $lastQuery = end($query);
                // dd($query);*/
                // return $post;
                  /******block code due to change logic (13-02-18) end  ***********/

                    /******add code due to change logic (13-02-18) start  ***********/

                        $day = 7;
                        $activity_posts = DB::table('activity_post')
                                            ->where('created_at', '>=', Carbon::now()->subDays($day));
                                            // ->orderBy('post_id')
                        // Get activity_post_id for category & tag.
                        $activity_post_id = [];
                        if ($category !== null && !empty($tag_post_id)) {
                            $activity_post_id = Post::where(function($query) use ($category, $tag_post_id) {
                                $query->where('category_id', $category->id)
                                        ->orWhere('sub_category_id', $category->id)
                                        ->orWhereIn('id', $tag_post_id);
                            })
                            ->get(['id']);
                        }
                        // Get activity_post_id for category
                        elseif ($category !== null) {
                            $activity_post_id = Post::where(function($query) use ($category) {
                                $query->where('category_id', $category->id)
                                        ->orWhere('sub_category_id', $category->id);
                            })
                            ->get(['id']);
                        }
                        // Get activity_post_id for tag.
                        elseif (!empty($tag_post_id)) {
                            $activity_post_id = Post::whereIn('id', $tag_post_id)->get(['id']);
                        }
                        // Add post_id to query activity_post.
                        if (!empty($activity_post_id)) {
                            // Get the post creators..
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
                            $point = 0;
                            // determine point based on activity.
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
                                $point = 1;
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


                /******add code due to change logic (13-02-18) start  ***********/
            }
            
            // \DB::connection()->enableQueryLog();
            // Count total posts..
            $totalPost = $post->count();
            
            /*$query = \DB::getQueryLog();
            dd($query);*/

            // Column selection array for eager loaded data.
            $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
            $category_columns = ['id', 'category_name'];
            $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag'];
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
                }
            ])
            ->withCount('comment');
            
            // Get the paginated result.
            $posts = $post->skip($this->offset)->take($this->per_page)->get()->makeVisible('people_here');

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
	
	
	/********************Function To Find Featured Post********************/
	/**
     * Get location page posts data from database.
     *
     * @param $input
     * @param $post_type
     * @return array
     */
    protected function getPostForPlace($input, $post_type) {
        // Initialize variables.
        $tagFollowStatus = 0;
        $category_followers = [];
        $tag_followers = [];
        $totalFollower = 0;
        $totalPost = 0;
        $posts = [];

        $location = !empty($input['location']) ? str_replace(' and ', ' & ', strtolower($input['location'])) : '';
        $city = !empty($input['city']) ? str_replace(' and ', ' & ', strtolower($input['city'])) : '';
        $state = !empty($input['state']) ? str_replace(' and ', ' & ', strtolower($input['state'])) : '';

        $region = !empty($input['region']) ? str_replace(' and ', ' & ', strtolower($input['region'])) : '';
        $country = !empty($input['country']) ? str_replace(' and ', ' & ', strtolower($input['country'])) : '';
        $continent = !empty($input['continent']) ? str_replace(' and ', ' & ', strtolower($input['continent'])) : '';

        if (empty($location) && empty($city) && empty($state) && empty($region) && empty($country) && empty($continent)) {
            goto return_area;
        }
        else {
            /*
             * Get the public and "followers only" posts of users'
             * whom the logged in user following.
             */
            if(Auth::check()){
            $public_or_follower_post =
                Post::where('privacy_id', 1)
                    ->orWhere(function($query) {
                        $query->where('privacy_id', 2)
                            ->whereIn('id', function($query) {
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
            /*if ($this->filter_post_type > 0) {
                $post->where('post_type', $this->filter_post_type);
            }*/

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
                $post->whereIn('privacy_id', [1,2])
                    ->orderBy('created_at', 'desc')
                    ->whereIn('id', $public_or_follower_post_ids);
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
                                    ->where('slug_name', 'LIKE', $region);
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
                    $activity_post_id->whereIn('country_code', function($query) use($region, $country, $continent) {
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
                                        ->where('slug_name', 'LIKE', $region);
                                });
                        }
                        elseif (!empty($continent)) {
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
                        $activity_post_id->orWhereRaw( "(`location` like ? OR `location` LIKE ?)", array( $ld1,  $ld2) );
                    }
                    //*========= END ORed condition for location ==========*//
                }

                $activity_post_id = $activity_post_id->get(['id']);
                /*-------------------------------------------------------------------------*/

                // Add post_id to query activity_post.
                if (!empty($activity_post_id)) {
                    // Get the post creators..
                    $post_users =  Post::whereIn('id', $activity_post_id)->get(['id', 'created_by']);
                    $activity_posts->whereIn('post_id', $activity_post_id)
                        // Remove activity by the post creator.
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
                    // determine point based on activity.
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
                        $point = 1;
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
                    $activity_post_id->whereIn('country_code', function($query) use($region, $country, $continent) {
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
                                        ->where('slug_name', 'LIKE', $region);
                                });
                        }
                        elseif (!empty($continent)) {
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
                        $activity_post_id->orWhereRaw( "(`location` like ? OR `location` LIKE ?)", array( $ld1,  $ld2) );
                    }
                    //*========= END ORed condition for location ==========*//
                }

                $activity_post_id = $activity_post_id->get(['id']);
                /*-------------------------------------------------------------------------*/
                // Add post_id to query activity_post.
                if (!empty($activity_post_id)) {
                    // Get the post creators..
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
                    $point = 0;
                    // determine point based on activity.
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
                        $point = 1;
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
                }
                else {
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

                /******** Code block for change logic in featured post start (13-02-18)**********/

                // if (!empty($region) || !empty($country) || !empty($continent)) {
                //     $post->whereIn('country_code', function($query) use($region, $country, $continent) {
                //         if (!empty($country)) {
                //             $query->select('country_code')
                //                 ->from('countries')
                //                 ->where('country_name', 'LIKE', $country)
                //                 ->orWhere('country_code', 'LIKE', $country);
                //         }
                //         elseif (!empty($region)) {
                //             $query->select('country_code')
                //                 ->from('countries')
                //                 ->whereIn('region_id', function ($q2) use ($region) {
                //                     $q2->select('id')
                //                         ->from('regions')
                //                         ->where('slug_name', 'LIKE', $region);
                //                 });
                //         }                        
                //         elseif (!empty($continent)) {
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

                // return $post;

                /************* Code block for change logic in fetured image(13-02-18)********/

                /************** code for implement new logic()start ****************/

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

                $post_ids_ordered = implode(',', $sorted_activity_post_ids);
                
            
        
            
                $post = $post->where('image', '<>', '')
                    ->where('privacy_id', 1)
                    ->where('points', '>', 0)
                    // ->whereIn('id', $sorted_activity_post_ids)
                // ->sinceDaysAgo(7)
                    // ->whereIn('id', $public_or_follower_post_ids)
                    ->with('user')
                // ->orderBy('points', 'desc');
                ->whereIn('id', $post_ids)->orderByRaw("FIELD(id, $post_ids_ordered)");
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

            // Count total posts..
            $totalPost = $post->count();
            // Column selection array for eager loaded data.
            $user_columns = ['id', 'username', 'first_name', 'last_name', 'profile_image', 'about_me'];
            $category_columns = ['id', 'category_name'];
            $tag_columns = ['tags.id', 'tag_name','tag_text','question_tag'];
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
                }
            ])
                ->withCount('comment');

            // Get the paginated result.
            $posts = $post->skip($this->offset)->take($this->per_page)->get()->makeVisible('people_here');

            $merged_follower = array_merge($tag_followers, $category_followers);
            $unique_follower = array_unique($merged_follower);
            $totalFollower = count($unique_follower);
        }
        return_area:
        
        // Get total numer of follwers following the place.
        // Allowed params.
        $allowed_addr_comps = [
            'location',
            'city',
            'state',
            'country',
            'region',
            'continent'
        ];
        $place_follower = DB::table('place_follower');
        foreach ($input as $param => $value) {
            $param = strtolower($param);
            if (in_array($param, $allowed_addr_comps)) {
                $value = str_slug_ovr($value);
                $place_follower->where($param, 'LIKE', $value);
            }               
        }
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

    
}
