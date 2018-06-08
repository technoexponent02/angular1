<?php 
namespace App\Http\Controllers\Api;

use App\Models\SearchKeyword;
use DB;
use Auth;
use Mail;
use Embed\Embed;
use Embed\Http\CurlDispatcher;
use Readability\Readability;
use GuzzleHttp\Client as GuzzleClient;

use App\Events\ScriptProgressed;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Photo;
use App\Models\Privacy;
use App\Models\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller {

    private $custom_user_agent;
    private $request;

    public function __construct(Request $request)
    {
        $this->custom_user_agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.98 Safari/537.36';

        $this->request = $request;
    }

    public function tag(Request $request)
	{
		$query = Tag::where('status', 'Y')->where('tag_name', '!=', '')
					->orderBy('updated_at', 'desc');
		if($request->has('q')) {
			$q = $request->input('q');
			$query->where('tag_name', 'like', '%' . $q . '%');
		}
		$tags = $query->limit(100)->lists('tag_name');
		return response()->json($tags);
	}

	public function privacy()
	{
		$privacies = Privacy::get(['id', 'privacy_name']);
		return response()->json($privacies);
	}

	public function userCollection()
	{
		$collections = Auth::user()->collection()
									->where('status', 'Y')
									->orderBy('collection_name', 'asc')
									->get(['id', 'collection_name']);
		return response()->json($collections);
	}

    /**
     * Extract external page information from user link.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function getExternalPageInfo(Request $request)
	{
		$response = [];
		if (! $request->has('link')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'link' parameter",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}
		$link = formatSourceUrl($request->input('link'));
		$domain = get_domain($link);
		$domain = formatSourceUrl($domain);
		$response['domain'] = $domain;

        // Initialize progress.
        $progress = 5;

		// Broadcast script progress event.
        $this->broadCastScriptProgress($progress);

		// Load external url:
		try {
            $dispatcher = new CurlDispatcher([
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => '',
                CURLOPT_AUTOREFERER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => $this->custom_user_agent,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ]);

            try {
                $info = Embed::create($link, [], $dispatcher);
            }
            catch(\Exception $e) {
                $info = Embed::create($link);
            }

            // Broadcast script progress event embed_http.
            $progress += 10;
            $this->broadCastScriptProgress($progress);

			$image = $info->image;

			// Remove query string params..
			if ($posOfQueryStr = strpos($image, '?')) {
				$imageWithoutQuery = substr($image, 0, $posOfQueryStr);
               if (isValidUrlByHttpRequest($imageWithoutQuery)) {
                   $image = $imageWithoutQuery;
               }
			}

			/*======== Download file to local if served over http ==========*/
            $local_img_src = '';
            $formattedImageUrl = formatSourceUrl($image);
            if (strpos($formattedImageUrl, 'https://') === FALSE) {
                $path = public_path() . '/uploads/post/';
                $download_data = download_image_to_local($image, $path);
                if ($download_data['status'] == 'success') {
                    $photo_data = [
                        'original_name' => $download_data['original_name'],
                        'save_name' => $download_data['save_name'],
                        'schedule_remove' => 'Y'
                    ];
                    // Save the photo in DB.
                    $photo = new Photo($photo_data);
                    $photo->save();

                    $local_img_src = '/uploads/post/' . $download_data['save_name'];
                }
            }

            /*========= For profiling ==========*/
            // Broadcast script progress event for embed_image_save.
            $progress += 5;
            $this->broadCastScriptProgress($progress);

            /*==================================================================*/

			$response = [
				'title' => $info->title,
				'type' => $info->type,
				'image' => $image,
                'local_img_src' => $local_img_src,
				'code' => $info->code,
				'providerName' => $info->providerName,
				'providerUrl' => $info->providerUrl,
				'domain' => $domain,
				

			];

			$noArticle = ['photo', 'image', 'video'];

			if ($request->has('article') && !in_array($info->type, $noArticle)) {

                //*=============== Try mercury =======================*//
                $result = getResultFromMercuryWebParser($link);

                // Broadcast script progress event mercury_http.
                $progress += 10;
                $this->broadCastScriptProgress($progress);

                $emptyContent = [
                    '<body class="sponsored-template"></body>'
                ];

                if (!empty($result->content) && !in_array($result->content, $emptyContent)) {
                    $articleText = $result->content;
                }
                //*=============== Try custom algorithm with readability =======================*//
                else {
                    $articleText = $this->getArticleByReadability($link, $domain);
                }

                // Broadcast script progress event mercury_http. // up to 40
                $progress += 10;
                $this->broadCastScriptProgress($progress);

//                dd($articleText);

                if (!empty($articleText)) {
                    $articleText = $this->cleanArticleTextAndSaveImage($articleText, $domain);
                    $response['description'] = $articleText;
                    // Prepare Live Preview limited description.
                    $limited_article = get_limited_article($articleText);
                    $response['lp_desc'] = $limited_article['content'];
                    $response['time_needed'] = $limited_article['time_needed'];
                }
				else {
					$response['description'] = '';
				}
			}
			else {
				$response['description'] = $info->description;
			}
		} catch(\Exception $e) {
		    $response = [
		    	'error' => 'INVALID_REQUEST',
		    	'link' => $link,
		    	 'message' => $e->getMessage()
		    ];
		    return response()->json($response, 400);
		}

		return response()->json($response);
	}

	public function getDomainName(Request $request)
	{
		$response = [];
		if (! $request->has('url')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'url' parameter",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}

		$url = $request->input('url');
		$url = formatSourceUrl(get_domain(formatSourceUrl($url)));
		$response['domain'] = $url;
		return response()->json($response);
	}

	public function saveImageToLocal(Request $request)
    {
        $response['status'] = 'FAILED';
        if (! $request->has('link')) {
            $response = [
                'error_message' => "Invalid request. Missing the 'link' parameter",
                'status' => 'INVALID_REQUEST'
            ];
            return response()->json($response, 400);
        }
        $link = formatSourceUrl($request->input('link'));

        $path = public_path() . '/uploads/post/';
        $download_data = download_image_to_local($link, $path);
        if ($download_data['status'] == 'success') {
            $photo_data = [
                'original_name' => $download_data['original_name'],
                'save_name' => $download_data['save_name'],
                'schedule_remove' => 'Y'
            ];
            // Save the photo in DB.
            $photo = new Photo($photo_data);
            $photo->save();

            $new_src = '/uploads/post/' . $download_data['save_name'];
            $response['status'] = 'SUCCESS';
            $response['new_src'] = $new_src;
        }
        return response()->json($response);
    }

	public function tagFollowUnfollow(Request $request)
	{
		$response = [
			'status' => 0
		];

		$user_id = Auth::user()->id;
		
		if (! $request->has('name')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'name' parameter",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}
		$name = $request->input('name');
		$name = strtolower($name);
		// Get category case by searching insensitive category.
		$category = Category::searchByName($name)->first(['id', 'category_name']);
		// Get tag and post_id related to the tag.

		//$tag = Tag::where('tag_name', $name)->orWhere('tag_name', str_slug($name))->first(['id']); (25-04-18)

		$tag_name= preg_replace('!\s+!', '-', $name);//modify tag space to "-"
        $tag_name=preg_replace('/[^A-Za-z0-9\-]/', '', $tag_name); 
        $tag_name = preg_replace('/-{2,}/', '-', $tag_name);
        $tag_name = rtrim($tag_name, '-');
        $tag_name = ltrim($tag_name, '-');


         $tag_name = str_slug_ovr($tag_name);

		$tag = Tag::where('tag_name', $tag_name)->orWhere('tag_name', str_slug($tag_name))->first(['id']);


		if ($category !== null && $tag !== null) {
			$category_follower = DB::table('category_follower')
									->where('category_id', $category->id)
									->where('follower_id', $user_id);
			$category_follower_data = $category_follower->get(['id']);

			$tag_user = DB::table('tag_user')
									->where('tag_id', $tag->id)
									->where('user_id', $user_id);
			$tag_user_data = $tag_user->get(['id']);
			// Exists one of table..so remove from both irrespective of existence.
			if (!empty($category_follower_data) || !empty($tag_user_data)) {
				// Remove category follower.
				$category_follower->delete();
				// Remove tag follower.
				$tag_user->delete();
			}
			// Exists in none of the table.
			else {
				// Make category follower.
				$category_follower_insert = [
					'category_id' => $category->id,
			    	'follower_id' => $user_id
			    ];
				DB::table('category_follower')->insert($category_follower_insert);
				// Make tag follower.
				$tag_user_insert = [
					'tag_id' => $tag->id,
			    	'user_id' => $user_id
			    ];
				DB::table('tag_user')->insert($tag_user_insert);
				$response['status'] = 1;
			}
		}
		// Tag is of type category.
		elseif ($category !== null) {
			$category_follower = DB::table('category_follower')
									->where('category_id', $category->id)
									->where('follower_id', $user_id);
			$category_follower_data = $category_follower->get(['id']);
			if (!empty($category_follower_data)) {
				// Remove category follower.
				$category_follower->delete();
			}
			else {
				// Make category follower.
				$category_follower_insert = [
					'category_id' => $category->id,
			    	'follower_id' => $user_id
			    ];
				DB::table('category_follower')->insert($category_follower_insert);

				// Create new tag.
                $tag_name = $category->category_name;
                // Remove excess dash.
                $tag_name = preg_replace('/-{2,}/', '-', $tag_name);
                $tag_name = rtrim($tag_name, '-');
                $tag_name = rtrim($tag_name, ' ');
                $tag_name = ltrim($tag_name, '-');
                $tag_name = ltrim($tag_name, ' ');

                $tag_name = str_slug_ovr($tag_name);

               
                $tag_text=rtrim($name,' ');
                $tag_text=ltrim($name,' ');
                if(substr($tag_text, -1)=='?')
                {
                	$question_tag=$name;
                }
                else
                {
                	$question_tag='';
                }


                $newTag = new Tag(['tag_name' => strtolower($tag_name),'tag_text'=>ucwords($tag_text),'question_tag'=>ucwords($question_tag)]);
                $newTag->save();

                // Make tag follower.
                $tag_user_insert = [
                    'tag_id' => $newTag->id,
                    'user_id' => $user_id
                ];
                DB::table('tag_user')->insert($tag_user_insert);

				$response['status'] = 1;
			}
		}
		// Tag is of type tags(actual).
		elseif ($tag !== null) {
			$tag_user = DB::table('tag_user')
									->where('tag_id', $tag->id)
									->where('user_id', $user_id);
			$tag_user_data = $tag_user->get(['id']);
			if (!empty($tag_user_data)) {
				// Remove tag follower.
				$tag_user->delete();
			}
			else {
				// Make tag follower.
				$tag_user_insert = [
					'tag_id' => $tag->id,
			    	'user_id' => $user_id
			    ];
				DB::table('tag_user')->insert($tag_user_insert);
				$response['status'] = 1;
			}
		}
		else {
			/*$response = [
				'error_message' => "Invalid request. The tag name $name does not exists.",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);*/
			/**********(25-04-18)************/
			// $name = str_slug($name);
			// $tag = new Tag(['tag_name' => $name]);
   		    //  $tag->save();
   		    /**********(25-04-18)************/
   		     $tag_name= preg_replace('!\s+!', '-', $name);//modify tag space to "-"
             $tag_name=preg_replace('/[^A-Za-z0-9\-]/', '', $tag_name); 
             $tag_name = preg_replace('/-{2,}/', '-', $tag_name);
             $tag_name = rtrim($tag_name, '-');
             $tag_name = ltrim($tag_name, '-');

          
             $tag_text=rtrim($name,' ');
             $tag_text=ltrim($name,' ');
             if(substr($tag_text, -1)=='?')
             {
               	$question_tag=$name;
             }
             else
             {
               	$question_tag='';
             }


            $tag = new Tag(['tag_name' => $tag_name,'tag_text'=>ucwords($tag_text),'question_tag'=>ucwords($question_tag)]);
   		    $tag->save();


            // Make tag follower.
			$tag_user_insert = [
				'tag_id' => $tag->id,
		    	'user_id' => $user_id
		    ];
			DB::table('tag_user')->insert($tag_user_insert);
			$response['status'] = 1;
		}
		return response()->json($response);
	}

	public function allCatFollowUnfollow(Request $request)
	{
		$response = [
			'status' => 0
		];

		$user_id = Auth::user()->id;
		if (! $request->has('name')) {
			$response = [
				'error_message' => "Invalid request. Missing the 'name' parameter",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}
		$name = $request->input('name');
		$name = strtolower($name);
		$oldFollowStatus = $request->input('followStatus');
		// check if 'all'
		if($name != 'all') {
			$response = [
				'error_message' => "Invalid request. Value of 'name' must be 'all'",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}
		// Get only main catgeory.
		$categories = Category::where('parent_id', 0)->get(['id']);
		foreach ($categories as $category) {
			$name = strtolower($category->category_name);
			// Get tag and post_id related to the tag.
			$tag = Tag::where('tag_name', $name)->orWhere('tag_name', str_slug($name))->first(['id']);
			if ($category !== null && $tag !== null) {
				$category_follower = DB::table('category_follower')
										->where('category_id', $category->id)
										->where('follower_id', $user_id);
				$category_follower_data = $category_follower->get(['id']);

				$tag_user = DB::table('tag_user')
										->where('tag_id', $tag->id)
										->where('user_id', $user_id);
				$tag_user_data = $tag_user->get(['id']);
				// Exists one of table..so remove from both irrespected of existance.
				if ($oldFollowStatus == 1 && !empty($category_follower_data) || !empty($tag_user_data)) {
					// Remove category follower.
					$category_follower->delete();
					// Remove tag follower.
					$tag_user->delete();
				}
				// Exists in none of the table.
				else {
					// Make category follower.
					$category_follower_insert = [
						'category_id' => $category->id,
				    	'follower_id' => $user_id
				    ];
					DB::table('category_follower')->insert($category_follower_insert);
					// Make tag follower.
					$tag_user_insert = [
						'tag_id' => $tag->id,
				    	'user_id' => $user_id
				    ];
					DB::table('tag_user')->insert($tag_user_insert);
					$response['status'] = 1;
				}
			}
			// Tag is of type category.
			elseif ($category !== null) {
				$category_follower = DB::table('category_follower')
										->where('category_id', $category->id)
										->where('follower_id', $user_id);
				$category_follower_data = $category_follower->get(['id']);
				if ($oldFollowStatus == 1 && !empty($category_follower_data)) {
					// Remove category follower.
					$category_follower->delete();
				}
				else {
					// Make category follower.
					$category_follower_insert = [
						'category_id' => $category->id,
				    	'follower_id' => $user_id
				    ];
					DB::table('category_follower')->insert($category_follower_insert);
					$response['status'] = 1;
				}
			}
			// Tag is of type tags(actual).
			elseif ($tag !== null) {
				$tag_user = DB::table('tag_user')
										->where('tag_id', $tag->id)
										->where('user_id', $user_id);
				$tag_user_data = $tag_user->get(['id']);
				if ($oldFollowStatus == 1 && !empty($tag_user_data)) {
					// Remove tag follower.
					$tag_user->delete();
				}
				else {
					// Make tag follower.
					$tag_user_insert = [
						'tag_id' => $tag->id,
				    	'user_id' => $user_id
				    ];
					DB::table('tag_user')->insert($tag_user_insert);
					$response['status'] = 1;
				}
			}
			else {
				/*$response = [
					'error_message' => "Invalid request. The tag name $name does not exists.",
					'status' => 'INVALID_REQUEST'
				];
				return response()->json($response, 400);*/
				$name = str_slug($name);
				$tag = new Tag(['tag_name' => $name]);
	            $tag->save();
	            // Make tag follower.
				$tag_user_insert = [
					'tag_id' => $tag->id,
			    	'user_id' => $user_id
			    ];
				DB::table('tag_user')->insert($tag_user_insert);
				$response['status'] = 1;
			}
		}
		return response()->json($response);
	}

	public function placeFollowUnfollow(Request $request)
	{
		$response = [
			'status' => 0
		];

		$user_id = Auth::user()->id;
		$input = $request->all();
		// Throw error message if no params provided.
		if (empty($input)) {
			$response = [
				'error_message' => "Invalid request. Atleast one parameter required.",
				'status' => 'INVALID_REQUEST'
			];
			return response()->json($response, 400);
		}
		$place_follower = DB::table('place_follower')->where('user_id', $user_id);

		// Check if place_url provided.
		if (!empty($input['place_url'])) {
			$place_url = $input['place_url'];

	        $input = explode('&', rawurldecode($place_url));
	        foreach ($input as $key => $value) {
	            preg_match_all('/(.+)=(.*)/', $input[$key], $matches);
	            if (!empty($matches[1][0]) && !empty($matches[2][0])) {
	                $text = $matches[1][0];
	                $place_follower_insert[$text] = $matches[2][0];
	            }
	        }
			goto place_url_area;
		}

		// Allowed params.
		$allowed_addr_comps = [
			'location',
			'city',
			'state',
			'country',
			'region',
			'continent'
		];
		foreach ($input as $param => $value) {
			$param = strtolower($param);
			if (in_array($param, $allowed_addr_comps)) {
				$value = rawurldecode($value);
				// $place_follower->where($param, 'LIKE', $value);
				// new place follower insert array
				$place_follower_insert[$param] = $value;
			}
		}
		// Generate place_url.
		$url = '';
		if(
            !empty($place_follower_insert['location']) &&
            (empty($place_follower_insert['city']) || $place_follower_insert['location'] != $place_follower_insert['city']) &&                
            (empty($place_follower_insert['city']) || $place_follower_insert['location'] != $place_follower_insert['state'])
        ) {
			$url = 'location=' . rawurlencode($place_follower_insert['location']) . '&';
		}
		if (!empty($place_follower_insert['city'])) {
			$url .= 'city=' . rawurlencode($place_follower_insert['city']) . '&';
		}
		if (!empty($place_follower_insert['state'])) {
			$url .= 'state=' . rawurlencode($place_follower_insert['state']) . '&';
		}
		if (!empty($place_follower_insert['country'])) {
			$url .= 'country=' . rawurlencode($place_follower_insert['country']) . '&';
		}
		if (!empty($place_follower_insert['region'])) {
			$url .= 'region=' . rawurlencode($place_follower_insert['region']) . '&';
		}
		if (!empty($place_follower_insert['continent'])) {
			$url .= 'continent=' . rawurlencode($place_follower_insert['continent']) . '&';
		}
		$place_url = rtrim($url, '&');

		place_url_area:

		// Convert to lowercase.
		$place_url = strtolower($place_url);
		// Fetch place follower.
		$place_follower_id = $place_follower->where('place_url', $place_url)->first(['id']);
		if (!empty($place_follower_id)) {
			// Remove place follower.
			$place_follower->delete();
		}
		else {
			$place_follower_insert['place_url'] = $place_url;
			// Assign the user to insert array.
			$place_follower_insert['user_id'] = $user_id;
			// Make place follower.
			DB::table('place_follower')->insert($place_follower_insert);
			$response['status'] = 1;
		}
		return response()->json($response);
	}

    public function topSearchJson()
    {
        $hour = 24;
        $search_keywords = SearchKeyword::select(DB::raw('id, COUNT(id) AS totalHits, keyword'))
            ->sinceHoursAgo($hour)
            ->groupBy('keyword')
            ->orderByRaw('totalHits desc, created_at desc')
            ->limit(10)
            ->get(['keyword']);
        return $search_keywords;

    }

    private function getArticleByReadability($link, $domain)
    {
        $articleText = null;

        // guzzle configuration..
        $options = [
            'http_errors' => false,
            'language' => 'en',
            'image_min_bytes' => 4500,
            'image_max_bytes' => 5242880,
            'image_min_width' => 120,
            'image_min_height' => 120,
            'image_fetch_best' => true,
            'image_fetch_all' => false,
            /** @see http://guzzle.readthedocs.io/en/latest/request-options.html */
            'browser' => [
                'timeout' => 60,
                'connect_timeout' => 30,
                'headers' => [
                    'User-Agent' => $this->custom_user_agent,
                    // 'Referer' => 'https://www.google.com/',
                    'Referer' => $domain,
                ],
            ]
        ];
        // Get response using guzzle..
        $guzzle = new GuzzleClient();
        $guzzle_response = $guzzle->get($link, $options);
        $status_code = $guzzle_response->getStatusCode();

        if ($status_code == 200) {
            $html = $guzzle_response->getBody();
        }
        else {
            // Turn php error/warnings to exceptions.
            set_error_handler("exception_error_handler");
            $html = file_get_contents($link);
            restore_error_handler();
        }

        //$response['guzzle_html'] = $html;
        $readability = new Readability($html, $link);
        // or without Tidy
        // $readability = new Readability($html, $link, 'libxml', false);
        $result = $readability->init();
        // Try again without query param if failed.
        if(!$result) {
            if ($posOfQueryStr = strpos($link, '?')) {
                $linkWithoutParam = substr($link, 0, $posOfQueryStr);

                // Again..
                $guzzle_response = $guzzle->get($linkWithoutParam, $options);
                $html = $guzzle_response->getBody();

                $readability = new Readability($html, $linkWithoutParam);

                $result = $readability->init();
            }
        }
        if ($result) {
            $articleText = $readability->getContent()->innerHTML;
        }
        return $articleText;
    }

    /**
     * clean article text and save image to local.
     *
     * @param $articleText
     * @return mixed|string
     */
    private function cleanArticleTextAndSaveImage($articleText, $domain)
    {
        $img_ext_array = ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'wbmp', 'xpm'];
        $articleText = str_replace('&amp;', '&', $articleText);
        $articleText = htmlspecialchars_decode($articleText);

        // For img only with data-src attribute.
        $pattern = '/< *img[^>]*data-src *= *["\']?([^"\']*)/';
        $articleText = preg_replace_callback($pattern, function($matches) use($domain) {
            // dump(filter_var($matches[1], FILTER_VALIDATE_URL));
            // dd($matches);
            $matches[0] = str_replace('data-src', 'src', $matches[0]);
            if (filter_var($matches[1], FILTER_VALIDATE_URL) === FALSE) {
                $new_url = $domain . '/' . ltrim($matches[1], '/');
                return str_replace($matches[1], $new_url, $matches[0]);
            }
            else {
                return $matches[0];
            }
        }, $articleText);

        // For img with both src and data-src attribute.
        $pattern = '/< *img[^>]*src *= *["\']?([^"\']*)["\'].*data-src-tablet *= *["\']?([^"\']*)|< *img[^>]*src *= *["\']?([^"\']*)["\'].*data-src *= *["\']?([^"\']*)|< *img[^>]*src *= *["\']?([^"\']*)["\'].*data-src-desktop *= *["\']?([^"\']*)|< *img[^>]*src *= *["\']?([^"\']*)["\'].*data-src-mobile *= *["\']?([^"\']*)/';
        $articleText = preg_replace_callback($pattern, function($matches) use($domain, $img_ext_array) {
            $new_src = $matches[1];
            $ext = pathinfo($matches[2], PATHINFO_EXTENSION);
            if (in_array($ext, $img_ext_array)) {
                $new_src = $matches[2];
                if (filter_var($matches[2], FILTER_VALIDATE_URL) === FALSE) {
                    $new_src = $domain . '/' . ltrim($matches[2], '/');
                }
            }
            return str_replace($matches[1], $new_src, $matches[0]);
        }, $articleText);
        //*-----------------------*//
        $path = public_path() . '/uploads/post/';


        $pattern = '/< *img[^>]*src *= *["\']?([^"\']*)/';

        /*========= Count total images to be saved ==========*/
        $totalImageCount = 1;
        preg_match_all($pattern, $articleText, $matches);
        if (!empty($matches[0])) {
            $totalImageCount = count($matches[0]);
        }

        // Each save progress.
        $each_save_progress = round((60 / $totalImageCount), 2);

        $articleText = preg_replace_callback($pattern, function($matches) use($domain, $path, $each_save_progress) {

            static $progress = 40;

            // For mercury merging srcset.
            $matched_src = $matches[1];
            if ($posOfQueryStr = strpos($matched_src, ',%20')) {
                $matched_src = substr($matched_src, 0, $posOfQueryStr);
                $matched_src = cleanImageLink($matched_src);
            }

            if (filter_var($matched_src, FILTER_VALIDATE_URL) === FALSE) {
                $new_url = $domain . '/' . ltrim($matched_src, '/');
                // str_replace($matched_src, $new_url, $matches[0]);
                $image_src = $new_url;
            }
            else {
                $image_src = $matched_src;
            }

            // Download files to local.
            $new_src = '/assets/img/post-placeholder.png';
            if (!empty($image_src)) {
                $download_data = download_image_to_local($image_src, $path);
                if ($download_data['status'] == 'success') {
                    $photo_data = [
                        'original_name' => $download_data['original_name'],
                        'save_name' => $download_data['save_name'],
                        'schedule_remove' => 'Y'
                    ];
                    // Save the photo in DB.
                    $photo = new Photo($photo_data);
                    $photo->save();

                    $new_src = '/uploads/post/' . $download_data['save_name'];
                    // For cron job.
                    $new_src .= '" data-photo-id="' . $photo->id;

                    // Broadcast script progress event mercury_http.
                    $progress += $each_save_progress;
                    $this->broadCastScriptProgress($progress);
                }
            }

            return str_replace($matches[1], $new_src, $matches[0]);
        }, $articleText);

        // Remove needsclick|<noscript>
        $articleText = preg_replace(
            array(
                '@needsclick@siu',
                '/class=".*?"/i',
                '/id=".*?"/i',
                '/srcset=".*?"/i',
                '/height=".*?"/i',
                '/width=".*?"/i',
                '/sizes=".*?"/i',
                '/onclick=".*?"/i',
                '/readability=".*?"/i',
                // Remove invisible content
                '@<script[^>]*?.*?</script>@siu',
                '@<object[^>]*?.*?</object>@siu',
                '@<embed[^>]*?.*?</embed>@siu',
                '@<applet[^>]*?.*?</applet>@siu',
                '@<noframes[^>]*?.*?</noframes>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
                '@<noembed[^>]*?.*?</noembed>@siu'
            ),
            array(
                '', '', '', '', '', '', '', ' ', ' ', ' ', ' ', ' ', ' '
            ),
            $articleText );

        // Removing more than one white-space
        $articleText = preg_replace('/\s\s+/', ' ', $articleText);

        return $articleText;
    }

    /**
     * Broadcast script progress event.
     *
     * @param $progress
     * @return bool
     */
    private function broadCastScriptProgress($progress) {
        if (! $this->request->has('uuid')) {
            return false;
        }

        $channel = $this->request->input('uuid');
        // Broadcast ScriptProgressed event.
        $event_data = [
            'progress' => $progress
        ];
        event(new ScriptProgressed($channel, $event_data));
        return true;
	}
	public function loginapi(Request $request)
    {
		if (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
            $request['email'] .= '@technoexponent.com';
        }
        
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
			
		
        $cookieExpiration = 24 * 60 * 30;

        $credentials = $request->only('email', 'password');
        $credentials['email_verified'] = 1;
        //$credentials['status'] = 1;
        //$credentials['sign_up_via'] = 'email';
    
        /*----- Set Cookie Expiration time ----*/
        if($request->has('remember')){
            Session::put('cookie_expiration', $cookieExpiration);
        }
     
        if (Auth::attempt($credentials, $request->has('remember'))) {
		 
			
           $response = ['msg'=>'success'];
           
		}
		else
		{
			
			$response = ['msg'=>'error'];
		}
		
        return response()->json($response);
        //$loginFailedMessage = $this->getFailedLoginMessage();

        // $response = "ererere";
        //return $response;

        // return redirect('explore')
        //         ->with('flash_notification.level', 'alert-danger')
        //         ->with('flash_notification.message', $loginFailedMessage)
        //         ->withInput($request->only('email', 'remember'));
	}
	public function forgotPasswordApi(Request $request)
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
			$response = ['msg'=>'success'];
        }
        else {
           // return redirect(url('password/forgot'))->with('errors', 'Internal error');   //(17-11-17) 
		   $response = ['msg'=>'error'];

		}
		return response()->json($response);
	}

}
