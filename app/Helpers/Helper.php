<?php
use App\Models\S3MoveFail;
use Illuminate\Support\Str;
use GuzzleHttp\Client as GuzzleClient;

if (! function_exists('move_to_s3')) {
    function move_to_s3($s3_path, $local_path, $permission = 'public') {
        try {
            // throw new Exception("Test Exception");
            
            /* Move file to aws s3 */
            Storage::put($s3_path, file_get_contents($local_path), $permission);
            try {
                /* Delete from local disk */
                File::Delete($local_path);
            } catch (Exception $e) {
                // dd($e->getMessage());
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            // echo $message . '<br>';
            $s3_dirname = pathinfo($s3_path, PATHINFO_DIRNAME);
            // If there's a file with the local_path, set the reason & s3_dirname
            // If no matching model exists, create one.
            S3MoveFail::updateOrCreate(
                ['local_path' => $local_path],
                ['reason' => $message, 's3_dirname' => $s3_dirname]
            );
        }
    }
}

if (! function_exists('getResultFromMercuryWebParser')) {
    function getResultFromMercuryWebParser($link) {
        $mercury_base_uri = config('services.mercury.base_uri');
        $x_api_key = config('services.mercury.key');

        try {
            $guzzle_client = new GuzzleClient();
            $mercury_response = $guzzle_client->request('GET', $mercury_base_uri, [
                'query' => ['url' => $link],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'x-api-key'     => $x_api_key
                ]
            ]);

            $body = $mercury_response->getBody();
            $result = json_decode($body);
            return $result;
        } catch (Exception $e) {
            return $result = null;
        }
    }
}

if (! function_exists('download_image_to_local')) {
    function download_image_to_local($url, $path) {

        $return_data = [
            'status' => 'failed'
        ];

        $intervention_supported = ['jpeg', 'jpg', 'png'];

        $link = formatSourceUrl($url);
        $domain = formatSourceUrl(get_domain($link));

        // Create file name.
        $url_parts = pathinfo($url);

        if (!empty($url_parts['extension'])) {
            $file_ext = strtolower($url_parts['extension']);
            if ($posOfQueryStr = strpos($file_ext, '&')) {
                $file_ext = substr($file_ext, 0, $posOfQueryStr);
            }
        }
        else {
            $file_ext = getImageExtensionFromUrl($url);
        }
        if ($file_ext == 'unknown') {
            return $return_data;
        }
        $file_ext = cleanFileExtension($file_ext);

        $original_name = !empty($url_parts['filename']) ? $url_parts['filename'] : 'image_file';
        if ($posOfQueryStr = strpos($original_name, '?')) {
            $original_name = substr($original_name, 0, $posOfQueryStr);
        }
        $save_name = generateFileName($original_name) . '.' . $file_ext;
        $original_name .= '.' . $file_ext;

        $width = config('constants.POST_IMAGE_WIDTH');
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
        if (!in_array($file_ext, $intervention_supported)) {
            $options['sink'] = $path . $save_name;
            try {
                $guzzle_response = $guzzle->get($link, $options);
            }
            catch (Exception $e) {
                return $return_data;
            }
        }
        else {
            try {
                $guzzle_response = $guzzle->get($link, $options);
                $guzzle_image = $guzzle_response->getBody()->getContents();
            }
            catch (Exception $e) {
                return $return_data;
            }

            try {
                $image_make = Image::make($guzzle_image);
            } catch (Exception $e) {
                return $return_data;
            }

            $image_make->resize($width, $height, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            });
            $image_make->save($path . $save_name);
        }

        $return_data = [
            'status' => 'success',
            'original_name' => $original_name,
            'save_name' => $save_name
        ];

        return $return_data;
    }
}

if (! function_exists('generate_post_image_url')) {
    function generate_post_image_url($image = '') {
        if (!empty($image)) {
            if (Storage::disk('s3')->exists($image))
                $image = Storage::url($image);
            elseif (File::exists(public_path('uploads/' . $image)))
                $image = '/uploads/' . $image;
            else
                $image = 'assets/img/post-placeholder.png';
        }
        return $image;
    }
}


if (! function_exists('generate_post_video_url')) {
    function generate_post_video_url($video = '') {
        if (!empty($video)) {
            if (Storage::disk('s3')->exists($video))
                $video = Storage::url($video);
            elseif (File::exists(public_path('uploads/' . $video)))
                $video = '/uploads/' . $video;
        }
        return $video;
    }
}

if (! function_exists('generate_profile_image_url')) {
    function generate_profile_image_url($image = '') {
        if (!empty($image)) {
            if (Storage::disk('s3')->exists($image))
                $image = Storage::url($image);
            elseif (File::exists(public_path('uploads/' . $image)))
                $image = '/uploads/' . $image;
            else
                $image = '';
        }
        return $image;
    }
}

if (! function_exists('post_url')) {
    /**
     * Generate a post url.
     * @param array $post
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function post_url($post) {
        $id = $post['id'];
        $post_type = $post['post_type'];
        // For status.
        if ($post_type == 5) {
            $caption = $post['caption'];
            // Limit caption to 100 char.
            $caption = substr($caption, 0, 100);
            // Remove # from url
            $caption = str_replace('#', '', $caption);
            $caption = str_slug($caption);
            /*-- /swolk.com/{caption}/{id} --*/
            return url('post/' . $caption . '/' . $id);
        }

        if ($post_type == 6) {
            $caption = $post['caption'];
            $caption = preg_replace('/\s+/', '-', $caption);
            $caption = preg_replace('/[^A-Za-z0-9\-]/', '', $caption);
            $caption = preg_replace('/-{2,}/', '-', $caption);
            $caption = rtrim($caption, '-');
            $caption = ltrim($caption, '-');
            return url('post/' . $caption . '/' . $id);
        }
        
        // For other post types
        $title = str_slug($post['title']);

        $category_url = '';
        $category = $post['category_name'];
        if ($category) {
            $category = str_slug($category);
            $category_url = '/' . $category;
        }
        // Optional subcategory..
        $subcategory_url = '';
        $subcategory = $post['subcategory_name'];
        if ($subcategory) {
            $subcategory = str_slug($subcategory);
            $subcategory_url = '/' . $subcategory;
        }

        /*-- /{category}/{subcategory}/{title} --*/
        return url('post' . $category_url . $subcategory_url . '/' . $title . '/' . $id);
    }
}

/**
 * Get all the months.
 *
 * @return array
 */
function getMonths() {

	return [
		1 => "January",
		2 => "February",
		3 => "March",
		4 => "April",
		5 => "May",
		6 => "June",
		7 => "July",
		8 => "August",
		9 => "September",
		10 => "October",
		11 => "Novemeber",
		12 => "December"
	];
}

/**
 * Return the profile image URL.
 *
 * @param  string $image
 * @return string
 */
function profileImage($profile_image) {
	return ($profile_image) ? url('public/uploads/profile/thumbs/' . $profile_image) : asset('assets/img/default-profile.jpg') ;
}

function get_domain($url)
{
    /*$pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
    return $regs['domain'];
    }
    return false;*/
    // regex can be replaced with parse_url
    preg_match("/^(https|http|ftp):\/\/(.*?)\//", "$url/" , $matches);
    $parts = explode(".", $matches[2]);
    $tld = array_pop($parts);
    $host = array_pop($parts);
    if ( strlen($tld) == 2 && strlen($host) <= 3 ) {
        $tld = "$host.$tld";
        $host = array_pop($parts);
    }

    $parts = array(
        'protocol' => $matches[1],
        'subdomain' => implode(".", $parts),
        'domain' => "$host.$tld",
        'host'=>$host,'tld'=>$tld
    );
    $subdomain_url = '';
    if (!empty($parts['subdomain'])) {
        $subdomain_url = $parts['subdomain'] . '.';
    }
    return $parts['protocol'] . '://' . $subdomain_url . $parts['domain'];
}

function formatSourceUrl($url)
{
    // $url = parse_url($url);
    // $url['host'] = strtolower($url['host']);
   $url = ltrim($url, '//');
    $url=strtolower($url);//(27-02-18) for convert the url in lower case..

    
	if (preg_match("@^https?://@", $url))
		return $url;
    return 'http://' . $url; 
   // return $url['scheme']."://".$url['host'].$url['path']."?".$url['query'];
}

function generateFileName($original_name)
{
	return time() . str_random(10) . '.' . str_slug(substr($original_name, 0, 75));
}


if (! function_exists('random_number')) {
    function random_number($length) {
        $result = '';

        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        return $result;
    }
}

if (! function_exists('words')) {
    /**
     * Limit the number of words in a string.
     *
     * @param  string  $value
     * @param  int     $words
     * @param  string  $end
     * @return string
     */
    function words($value, $words = 100, $end = '...')
    {
        return Str::words($value, $words, $end);
    }
}

if (! function_exists('generate_id_slug')) {
    function generate_id_slug($value)
    {
        return random_number(8) . $value . random_number(10);
    }
}

if (! function_exists('get_id_from_slug')) {
    function get_id_from_slug($value)
    {
        return substr(substr($value,0, -10), 8);
    }
}

if (! function_exists('get_limited_article')) {
    function get_limited_article($content)
    {
        $return_data = [];
        if (!empty($content)) {
            $content = str_replace('&amp;', '&', $content);
            $content = htmlspecialchars_decode($content);
            $fully_stripped_content = strip_tags($content);
            $stripped_content = strip_tags($content, '<br><p>');
            // $pattern = "/^(<br[^>]*>)*|<\/p>(<br[^>]*>)|(<p><\/p>)|<p><br[^>]*><\/p>|<p>[&nbsp;|\s]*<\/p>/";
            $pattern = "/^(<br[^>]*>)*|<\s*\/p>(<br[^>]*>)|(<\s*p\s*><\s*\/p\s*>)|<\s*p\s*><br[^>]*><\/p>|<\s*p\s*>[&nbsp;|\s]*<\s*\/p\s*>/";
            // "/<\/p><br[^>]*>/"
            $stripped_content = preg_replace($pattern, '', $stripped_content);
            
            // Check if Latin or not..
            $check = preg_match('/[^\\p{Common}\\p{Latin}]/u', $fully_stripped_content);
            if ($check) { // Non Latin
                $total_chars = mb_strlen($fully_stripped_content);
                $return_data['time_needed'] = round($total_chars / 1000);
            }
            else {
                // Calculate total words..
                $total_words = str_word_count($fully_stripped_content, 0);
                $return_data['time_needed'] = round($total_words / 200);
            }

            $content = str_limit($stripped_content, config('constants.CARD_ARTICLE_LIMIT'));
            // $return_data['content'] = '<p>' . $content . '</p>';
            $return_data['content'] = $content;
        }
        return $return_data;
    }
}

if (! function_exists('hash_tag_url')) {
    /**
     * Generate hash tag url from caption.
     *
     * @param string $caption
     * @return mixed|string
     */
    function hash_tag_url($caption = '')
    {
        $hash_tag_pattern = config('constants.HASH_TAG_PATTERN');
        if (!empty($caption)) {
            // Convert new lines to break tag.
            $caption = nl2br($caption);
            $replace = "<a href='/tag/str_slug_ovr($1)'><span>#</span><span>$1</span></a>";
            $caption = preg_replace_callback($hash_tag_pattern, 'callback_hash_tag', $caption);
        }
        return $caption;
    }
}

if (! function_exists('callback_hash_tag')) {
    function callback_hash_tag($matches) {
        $link = preg_replace('/-{2,}/', '-', $matches[1]);
        $link = rtrim($link, '-');
        $link = ltrim($link, '-');

        //$formatted_tag_name = str_ireplace('-&-', ' and ', trim($matches[1]));//changes due to format the caption tag(21-11-17)
       
        //$formatted_tag_name = preg_replace('/-(?=.)/', ' ', $formatted_tag_name);//changes due to format the caption tag(21-11-17)

        return "<a href='/tag/" . $link . "?src=hash'><span>#</span><span>" . $matches[1] . "</span></a>";//changes due to format the caption tag(21-11-17)
       // return "<a href='/tag/" . $link . "?src=hash'><span>#</span><span>" . $formatted_tag_name . "</span></a>";//changes due to format the caption tag(21-11-17)
    }
}

if (! function_exists('location_url')) {
    /**
     * Generate hash tag url from caption.
     * @param array $post
     * @return mixed|string
     */
    function location_url($post, $addPlaceToUrl = true)
    {
        $url = '';
        if(!empty($post)) {
        // dd($post);
            $url = '';
            if(
                !empty($post->location) &&
                $post->location != $post->city &&                
                $post->location != $post->state
            ) {
                if (empty($post->country)) {
                    if(!empty($post->country)) {
                        $url .= check_url_for_location($post->location, false);
                    }
                    else {
                        $url .= check_url_for_location($post->location, true);
                    }
                }
                else {
                    $country_name = $post->country->country_name;
                    $region_name = !empty($post->country->region) ? $post->country->region->name : '';
                    $region_slug_name = !empty($post->country->region) ? $post->country->region->slug_name : '';
                    if (
                        $post->location != $country_name && 
                        $post->location != $region_name &&
                        $post->location != $region_slug_name
                    ) {
                        $url .= 'location=' . rawurlencode($post->location) . '&';
                    }
                }
                
            }
            if(!empty($post->city)) {
                $url .= 'city=' . rawurlencode($post->city) . '&';
            }
            if(!empty($post->state)) {
                $url .= 'state=' . rawurlencode($post->state) . '&';
            }
            if(!empty($post->country)) {
                $url .= 'country=' . rawurlencode($post->country->country_name) . '&';
                if(!empty($post->country->region)) {
                    $url .= 'region=' . rawurlencode($post->country->region->name) . '&';
                }
            }
            if(!empty($post->country->continent)) {
                $url .= 'continent=' . rawurlencode($post->country->continent) . '&';
            }

            $url = rtrim($url, '&');
            if(!empty($url) && $addPlaceToUrl) {
                $url = '/place?' . $url;
            }
        }
        return $url;
    }
}

if (! function_exists('countPostView')) {
    /**
     * Get  views/read/plays etc. count for a post.
     *
     * @param $post_id
     * @param $post_type
     * @param string $type
     * @return mixed
     */
    function countPostView($post_id, $post_type, $type = '') {
        $activity_post = DB::table('activity_post')->where( 'post_id', $post_id);
        // For image or status post
        if ($post_type == 1 || $post_type == 5) {
            if ($type == 'viewed') {
                $activity_post->where('activity_id', 8);
            }
            else {
                $activity_post->where('activity_id', 14);
            }            
        }
        else if ($post_type == 2) {
            $activity_post->where('activity_id', 11);
        }
        else if ($post_type == 3) {
            $activity_post->where('activity_id', 9);
        }
        else if ($post_type == 4) {
            $activity_post->where('activity_id', 10);
        }
        // Count total views/read/plays etc.
        $totalPostViews = $activity_post->count();
        return $totalPostViews;
    }
}

if (! function_exists('check_url_for_location')) {
    function check_url_for_location($location, $push_extra_data = false) {
        $formatted_continent = str_replace('-',' ', $location);
        $country = App\Models\Country::where('country_name', 'LIKE', $location)
                                        ->orWhere('country_name_slug', 'LIKE', $location)
                                        ->orWhere('continent', 'LIKE', $location)
                                        ->orWhere('continent', 'LIKE', $formatted_continent)
                                        ->first();
        if (!empty($country)) {
            if ($country->country_name == $location) {
                $url = 'country=' . rawurlencode($country->country_name) . '&';
                if ($push_extra_data) {
                    $url .= 'continent=' . rawurlencode($country->continent);
                }
                return $url;
            }
            else {
                return 'continent=' . rawurlencode($country->continent) . '&';
            }
        }
        else {
            $region = App\Models\Region::where('name', 'LIKE', $location)
                                        ->orWhere('slug_name', 'LIKE', str_slug_ovr($location))
                                        ->first();
            if (!empty($region)) {
                $url = 'region=' . rawurlencode($region->name) . '&';
                if ($push_extra_data) {
                    $url .= 'continent=' . rawurlencode($region->continent);
                }
                return $url;
            }
        }
        return 'location=' . rawurlencode($location) . '&';
    }
}

if (! function_exists('place_url_from_array')) {
    function place_url_from_array($input, $toLowerCase = true) {
        // Allowed params.
        $allowed_addr_comps = [
            'location',
            'city',
            'state',
            'country',
            'region',
            'continent'
        ];
        // Generate place_url.
        $place_url = '';
        if(
            !empty($input['location']) &&
            (empty($input['city']) || $input['location'] != $input['city']) &&                
            (empty($input['state']) || $input['location'] != $input['state'])
        ) {
            $place_url = 'location=' . rawurlencode($input['location']) . '&';
        }
        if (!empty($input['city'])) {
            $place_url .= 'city=' . rawurlencode($input['city']) . '&';
        }
        if (!empty($input['state'])) {
            $place_url .= 'state=' . rawurlencode($input['state']) . '&';
        }
        if (!empty($input['country'])) {
            $place_url .= 'country=' . rawurlencode($input['country']) . '&';
        }
        if (!empty($input['region'])) {
            $place_url .= 'region=' . rawurlencode($input['region']) . '&';
        }
        if (!empty($input['continent'])) {
            $place_url .= 'continent=' . rawurlencode($input['continent']) . '&';
        }
        $place_url = rtrim($place_url, '&');
        // Convert to lowercase.
        if ($toLowerCase) {
            $place_url = strtolower($place_url);
        }
        return $place_url;
    }
}

if (! function_exists('str_slug_ovr')) {
    function str_slug_ovr($string) {
        return str_slug(str_replace(' & ',' and ', $string));
    }
}

/**
 * Reverse of str_slug_ovr to query against original.
 * @return array [with two values]
 */
if (! function_exists('slug_ovr_rev')) {
    function slug_ovr_rev($string) {
        $v1 = str_replace('-',' ', $string);
        $v2 = str_replace(' and ',' & ', $v1);
        return [$v1, $v2];
    }
}

/**
 * Function to get information about embed video url.
 * @param string $url
 * @return array
 * @author Tuhin <tuhin@technoexponent.com>
 */
if (! function_exists('getEmbedVideoInfo')) {
    function getEmbedVideoInfo($url)
    {
        // Initialize.
        $info = [
            'type' => 'unsupported',
            'videoid' => ''
        ];
        // Check if the embed url is unsupported.
        if (!preg_match('/youtube|vimeo|dailymotion/i', $url)) {
            return $info;
        }

        $youtubePattern = "/(?:http?s?:\/\/)?(?:www\.)?(?:youtube\.com\/embed\/)\/?(.+)/";
        $vimeoPattern = "/(?:http?s?:\/\/)?(?:www\.)?(?:vimeo\.com\/video)\/?([^?]+)/";
        $dailymotionPattern = "/(?:http?s?:\/\/)?(?:www\.)?(?:dailymotion\.com\/embed\/video\/)\/?([^?_]+)/";

        // Match youtube video.
        preg_match($youtubePattern, $url, $matches);
        if (!empty($matches)) {
            $info = [
                'type' => 'youtube',
                'videoid' => $matches[1]
            ];
        } else {
            // Match vimeo video.
            preg_match($vimeoPattern, $url, $matches);
            if (!empty($matches)) {
                $info = [
                    'type' => 'vimeo',
                    'videoid' => $matches[1]
                ];
            } else {
                // Match dailymotion video.
                preg_match($dailymotionPattern, $url, $matches);
                if (!empty($matches)) {
                    $info = [
                        'type' => 'dailymotion',
                        'videoid' => $matches[1]
                    ];
                }
            }
        }
        return $info;
    }
}

/**
 * Function to Checks whether a file or directory exists
 * @param string $file_name ,$image_type
 * @return boolean
 * @author Anurag Saha <anurag@technoexponent.com>
 */
 if (! function_exists('check_file_exists')) {
        function check_file_exists($file_name,$image_type) {
            $flag=false;

            switch($image_type){
                case 'image_post' :
                    $file_path = public_path() . '/uploads/post/'.$file_name; 
                    break;
                case 'video_post' :
                    $file_path = public_path() . '/uploads/video/thumbnail/'.$file_name; 
                    break;
                default :
                    $file_path = "";
            }

             if (!empty($file_name)) {
                if(file_exists($file_path)){
                    $flag=true; 
                } else {
                    $flag=false; 
                }               
            } else {
               $flag=false; 
            }
            return $flag;
        }
    }


if(! function_exists('haversineGreatCircleDistance')){

    /**
    * Function to calculate distance between two points using haversine great circle distance formula
    * Author : Alapan Chatterjee; Date:24-01-2017
    */
    function haversineGreatCircleDistance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371 )
    {
      // convert from degrees to radians
      $latFrom = deg2rad($latitudeFrom);
      $lonFrom = deg2rad($longitudeFrom);
      $latTo   = deg2rad($latitudeTo);
      $lonTo   = deg2rad($longitudeTo);

      $latDelta = $latTo - $latFrom;
      $lonDelta = $lonTo - $lonFrom;

      $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
      return $angle * $earthRadius;
    }    
}

if( !function_exists('addPostDistance')){
    /**
    * Fetch Distance Between Post Location And End User Location
    * Author : Alapan Chatterjee; Date:27-01-2017
    */
    function addPostDistance($userLocationInfo, $final_posts){
        $userLatitude       = $userLocationInfo['lat'];
        $userLongitude      = $userLocationInfo['lon'];
        foreach($final_posts as $key=>$postInfo){
            $final_posts[$key]  = (object) $final_posts[$key];
            if($final_posts[$key]->lat != '' && $final_posts[$key]->lon != '' && $userLatitude != '' && $userLongitude != '')
            {
               $latitudeTo  = floatval($final_posts[$key]->lat);
               $longitudeTo = floatval($final_posts[$key]->lon);    
                $distance = haversineGreatCircleDistance($userLatitude, $userLongitude, $latitudeTo, $longitudeTo);
            }
            else{
                $distance = null;
            }
            $final_posts[$key]->distance = $distance;
        }
        return $final_posts;
    }
}

if( !function_exists('calculateAgeFromDob') ){
    /**
    * Calculate age of a person from his date of birth
    * Author : Alapan Chatterjee; Date:20-02-2017
    */
    function calculateAgeFromDob($date=''){
        $age = '';
        if($date != ''){
            $dobTimestamp = strtotime($date);
            $ageDifS      = time() - $dobTimestamp;
            $year         = date('Y', $ageDifS) - 1970;
            if($year == 0){
                $dobMonth = date('n', $dobTimestamp);
                $month    = date('n');
                if($month > $dobMonth){
                    $age = ($month - $dobMonth); 
                }
                else{
                    $age = (12 - $dobMonth) + $month;
                }
                if($age==0){
                    return "less than a month";
                }
                $age = $age.( $age > 1 ? ' months' : ' month');  
            }
            else{
                $age = $year.( $year > 1 ? ' years' : ' year');
            }
        }
        return $age;
    }
}

if( !function_exists('thousandsSuffix') ){
    /**
    * Format a number if greater than 10000
    * Author : Alapan Chatterjee; Date:20-02-2017
    */
    function thousandsSuffix($number, $fractionSize=''){
        if($number === null) return null;
        if($number === 0) return 0;
        if($number <= 9999) return $number;

        if($fractionSize == ''){
            $fractionSize = 2;
        }

        $isNegative = $number < 0;
        $suffix     = '';
        $powers     = array(
            'Q' => pow(10, 15),
            'T' => pow(10, 12),
            'B' => pow(10, 9),
            'M' => pow(10, 6),
            'K' => 1000
        ); 

        foreach($powers as $key=>$power){
            if($number >= $power){
                $reduced = $number / $power;
                $reduced = round($reduced, $fractionSize, PHP_ROUND_HALF_UP);
                $suffix  = $key;
                break;
            }
        }

        return ($isNegative == true ? '-' : '').$reduced.$suffix;

    }
}

if(!function_exists('account')){
    function account($username){
        $userProfile= '';
        if($username != ''){
            $userProfile = '/profile/'.$username;
        }
        return $userProfile;
    }
}

if(!function_exists('showLocation')){
    /**
    * Prepare the location name of a post
    * Author : Alapan Chatterjee; Date:22-02-2017
    */
    function showLocation($location=''){
        $formattedLocation = '';
        if($location != ''){
            $charLimit = 14;
            $commaPos  = strpos($location, ',');
            if($commaPos !== false){
                $location = substr($location, 0, $commaPos);
            }
            $formattedLocation = strlen($location) > $charLimit ? substr($location, 0, $charLimit).'..' : $location;
        }
        return $formattedLocation;
    }
}

if(!function_exists('domainFilter')){
    /**
    * Prepare the location url of a domain
    * Author : Alapan Chatterjee; Date:25-02-2017
    */
    function domainFilter($url = ''){
        $domain = '';
        if($url != ''){
             // find & remove protocol (http, ftp, etc.) and get domain
            if(strpos($url, "://") !== false){
                $arr    = explode('/', $url);
                $domain = $arr[2];
            }
            else{
                $arr    = explode('/', $url);
                $domain = $arr[0]; 
            }
            // find & remove port number
            $arr    = explode(':', $domain);
            $domain = $arr[0];
            $domain = str_ireplace('www.', '', $domain);
        }
        return $domain;
    }
}

if (!function_exists('getImageExtensionFromUrl')) {
    /**
     * Get file extension from image url.
     *
     * @param $url
     * @return string
     */
    function getImageExtensionFromUrl($url) {

        if (empty($url)) return 'unknown';
        try {
            $typeInt = exif_imagetype($url);
        }
        catch (Exception $e) {
            return 'unknown';
        }

        switch($typeInt) {
            case IMG_GIF:
                $ext = 'gif';
                break;
            case IMG_JPG:
                $ext = 'jpg';
                break;
            case IMG_JPEG:
                $ext = 'jpeg';
                break;
            case IMG_PNG:
                $ext = 'png';
                break;
            case IMG_WBMP:
                $ext = 'wbmp';
                break;
            case IMG_XPM:
                $ext = 'xpm';
                break;
            default:
                $ext = 'unknown';
        }
        return $ext;
    }
}

if (!function_exists('checkValidUrlByHttpRequest')) {
    /**
     * Check url validity by making an http request.
     *
     * @param $url
     * @return bool
     */
    function isValidUrlByHttpRequest($url) {
        // guzzle configuration..
        $options = [
            'http_errors' => false,
            /** @see http://guzzle.readthedocs.org/en/latest/clients.html#request-options */
            'browser' => [
                'timeout' => 60,
                'connect_timeout' => 30,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36',
                    'Referer' => 'https://www.google.com/',
                ],
            ]
        ];
        // Get response using guzzle..
        $guzzle = new GuzzleClient();
        $guzzle_response = $guzzle->get($url, $options);
        $status_code = $guzzle_response->getStatusCode();
        return $status_code == 200;
    }
}

if (!function_exists('cleanFileExtension')) {
    /**
     * Remove unnecessary character from extension name.
     * @param $file_ext
     * @return string
     */
    function cleanFileExtension($file_ext) {

        if (strpos($file_ext, 'jpeg') !== FALSE)  {
            return $file_ext = 'jpeg';
        }
        else if (strpos($file_ext, 'jpg') !== FALSE)  {
            return $file_ext = 'jpg';
        }
        else if (strpos($file_ext, 'png') !== FALSE)  {
            return $file_ext = 'png';
        }
        else if (strpos($file_ext, 'gif') !== FALSE)  {
            return $file_ext = 'gif';
        }
        else {
            if ($posOfQueryStr = strpos($file_ext, '?')) {
                $file_ext = substr($file_ext, 0, $posOfQueryStr);
            }
            if ($posOfQueryStr = strpos($file_ext, '&')) {
                $file_ext = substr($file_ext, 0, $posOfQueryStr);
            }
        }
        return $file_ext;
    }
}

if (!function_exists('cleanImageLink')) {
    /**
     * Remove unnecessary character from image link.
     * @param $link
     * @return string
     */
    function cleanImageLink($link)
    {
        if (($pos = strrpos($link, 'jpg')) !== FALSE)
            return substr($link, 0, $pos) . 'jpg';
        elseif (($pos = strrpos($link, 'jpeg')) !== FALSE)
            return substr($link, 0, $pos) . 'jpeg';
        elseif (($pos = strrpos($link, 'png')) !== FALSE)
            return substr($link, 0, $pos) . 'png';
        return $link;
    }
}

if(!function_exists('addUserToMailchimp')){

    /**
     * @param array $data
     * @return mixed
     */

    function addUserToMailchimp($data=array()){

        $apiKey    = config('services.mailchimp.apikey');
        $listId    = config('services.mailchimp.listid');

        if(!empty($data)){
            $memberId   = md5(strtolower($data['email']));
            $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);

            $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

            $json = json_encode([
                'email_address' => $data['email'],
                'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
                'merge_fields'  => [
                    'FNAME'     => $data['firstname'],
                    'LNAME'     => $data['lastname']
                ]
            ]);

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

            $curl_response = curl_exec($ch);
            $info          = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $info;

        }

    }
}


if (!function_exists('isAssoc')) {
   
    function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

}


