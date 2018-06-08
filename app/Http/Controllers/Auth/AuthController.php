<?php

namespace App\Http\Controllers\Auth;

use Auth;
use DB;
use Socialite;
use Session;
use App\Models\User;
use App\Mailers\UserMailer;
use Validator;
use Image;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    
    protected $redirectTo = '/';

    private $mailer;
    private $landingPageURL = 'http://swolk.com/beta/landing/';
    protected $image_width;
    protected $image_height;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(UserMailer $mailer)
    {
        $this->image_width = config('constants.POST_IMAGE_WIDTH');
        $this->image_height = config('constants.POST_IMAGE_HEIGHT');
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
        // 
        $this->mailer = $mailer;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    /*protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }*/

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    /*protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }*/

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    /***** (21-11-17) changes due to redirect login page */
    public function getLogin(){
        return redirect('explore');
    }
    /***** (21-11-17) changes due to redirect login page */

    public function postLogin(Request $request) {
      
       
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
         
            
            // Check if user following any other user.
            $user_follower = DB::table('followers')
                                    ->where('follower_id', Auth::user()->id)
                                    ->first(['id']);
            
                                    
            if (!empty($user_follower)) {
                return redirect($this->redirectTo);
                // redirect('explore');
               
            }
            // Check if user following any category.
            $category_follower = DB::table('category_follower')
                                    ->where('follower_id', Auth::user()->id)
                                    ->first(['id']);
            if (!empty($category_follower)) {
               // return redirect($this->redirectTo);
               redirect('explore');
            }
            // Check if user following any tag.
            $tag_user = DB::table('tag_user')
                                    ->where('user_id', Auth::user()->id)
                                    ->first(['id']);
            if (!empty($tag_user)) {
                //return redirect($this->redirectTo);
                redirect('explore');
            }
            // Otherwise redirect to explore page.
            return redirect('explore');
        }
        
        $loginFailedMessage = $this->getFailedLoginMessage();

        

        return redirect('explore')
                ->with('flash_notification.level', 'alert-danger')
                ->with('flash_notification.message', $loginFailedMessage)
                ->withInput($request->only('email', 'remember'));
    }
    

    public function validateRequest($request) {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            //'last_name' => 'required|max:255',
            'username' => 'required|alpha_num|unique:users,username|min:2|max:15',
            //'email' => 'required|unique:users,email|max:255',
            'termsconditions' => 'required'
        ]);
    }

    public function getRegister($media = NULL, Request $request) {

        if (! $request->session()->has('sign_up_incomplete')) {
            //return redirect('login');
            return redirect('explore');
        }

        $user = $request->session()->get('user');
        $user['media'] = $media;

        return view('auth.socialmedia', compact('user'));
    }

    public function postRegister($media = NULL, Request $request) {
        if (! $request->session()->has('sign_up_incomplete')) {
            //return redirect('login');
            return redirect('explore');
        }

        $this->validateRequest($request);
        $user = $this->createUser($media, $request);

        if ($request->session()->has('sign_up_incomplete')) {
            $request->session()->forget('sign_up_incomplete');
            $request->session()->forget('user');
        }

        Auth::login($user);
       // return redirect('auth/social/redirect/explore');  
       return redirect('explore');      
    }

    public function createUser($media = NULL, $request) {
        $session_info       = $request->session()->get('user');
        $user_info          = $request->only('first_name', 'last_name', 'username');
        $user_info['email'] = $session_info['email'];
        $user_info['sign_up_via'] = $session_info['sign_up_via'];

        //Save Profile Picture
        if($request->file('profile') !== null){
            $profileImage = $this->saveProfilePicture($request->file('profile'));
        }
        else{
            /*if($media == "facebook"){

                $picture_name = "fb_profileImage_".time();
            }
            else{
                $picture_name = "twitter_profileImage_".time();
            }*/

            $original_name = basename($session_info['avatar_original']);
            $original_name = str_replace(' ', '-', substr($original_name, 0, 75));
            $picture_name = generateFileName($original_name);

            $path         = public_path() . '/uploads/profile/';
            $thumb_path   = public_path() . '/uploads/profile/thumbs/';
            $profileImage = $this->saveProfilePictureFromSocialSite($session_info['avatar_original'], $picture_name, $path);
            $profileImage = $this->saveProfilePictureFromSocialSite($session_info['avatar'], $picture_name, $thumb_path);
        }

        // Save user
        $user = User::firstOrCreate($user_info);

        if($media == "facebook"){
            $user->facebook_connect         = 'Y';
            $user->facebook_access_token    = $session_info['facebook_access_token'];
            $user->facebook_token           = $session_info['facebook_token'];
        }

        if($media == "twitter"){
            $user->twitter_connect            = 'Y';
            $user->twitter_access_token       = $session_info['twitter_access_token'];
            $user->twitter_access_tokensecret = $session_info['twitter_access_tokensecret']; 
            $user->twitter_token              = $session_info['twitter_token'];
        }
        
        $user->profile_image            = $profileImage;
        $user->email_verified           = '1';
        $user->dob                      = NULL;                
        $user->save();

        //Add User To Mail Chimp subscription list
        $user_data = [
            'email'     => $user->email,
            'status'    => 'subscribed',
            'firstname' => $user->first_name,
            'lastname'  => $user->last_name
        ];

        $info = addUserToMailchimp($user_data);

        if(!empty($user) && $session_info['invitation_code'] != null){

            $uniquecode = $session_info['invitation_code'];
            
            //Update Unqiue code status
            $affected = DB::update("UPDATE `invitation` set `status` = 'Inactive' where  `uniquecode` = ?", [$uniquecode]);
        }

        // Send email
        $this->mailer->welcomeToSwolk($user);

        return $user;
    }

    /**
     * Uploads and save user profile picture
     * 
     * @return Picture name
     */
    public function saveProfilePicture($imageObject){
        
        $allowed_file_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $error_message = '';
        $has_error     = 0;
        $size          = 0;
        $save_name     = '';

        if ($imageObject) {
            $image = $imageObject;
            $image_ext      = $image->getClientOriginalExtension();
            $image_ext      = strtolower(trim($image_ext));

            if(in_array($image_ext, $allowed_file_extensions)){
                $original_name  = $image->getClientOriginalName();
                $original_name  = str_replace(' ', '-', substr($original_name, 0, 75));
                $save_name      = time() . str_random(10) . '.' . $original_name;

                //Store Path For both thumbs and orginal picture
                $path       = public_path() . '/uploads/profile/';
                $thumb_path = public_path() . '/uploads/profile/thumbs/';

                if ($image_ext == 'gif') {
                    $image->move($path, $save_name);
                    copy($path . $save_name, $thumb_path . $save_name);
                }
                else {
                     $image_make = Image::make($image->getRealPath());
                     $size       = $image_make->filesize();
                     $size_mb    = floor($size/pow(1024,2));
                     if($size_mb <= 5)
                     {
                             $width  = $this->image_width;
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
                     }
                     else{
                        $error_message = 'Images only less than 5 mb are allowed';
                        $has_error     = 1;
                     } 
                }
            }
            else{
                $error_message = 'Only Jpg,Jpeg and png images are allowed';
                $has_error     = 1;
            }
        }

        return $save_name;
    }

    /**
     * Uploads and save user profile picture from social networking site
     *
     * @param string $url
     * @param string $picture_name
     * @param string $path
     * @return string
     */
    public function saveProfilePictureFromSocialSite($url =' ', $picture_name='', $path=''){
        if($url =='' || $picture_name == '' || $path == ''){
            return '';
        }
        $data = file_get_contents($url);
        file_put_contents($path.$picture_name, $data);

        $path         = public_path() . '/uploads/profile/';
        $thumb_path   = public_path() . '/uploads/profile/thumbs/';
        /* Upload file to aws s3 */
        move_to_s3('/profile/' . $picture_name, $path . $picture_name);
        move_to_s3('/profile/thumbs/' . $picture_name, $thumb_path . $picture_name);

        return $picture_name;
    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $facebookUser
     * @return User
     */
    /*public function findOrCreateUser($data) {
        $session = array();
        $user_data = User::where(['verification_id' => $data->id])->first();
        if ($user_data) {
            $session = array(
                'name' => $user_data->first_name,
                'last_name' => $user_data->last_name,
                'email' => $user_data->email,
                'id' => $user_data->id,
                'signup_via' => 'twitter',
                'twitter_token' => $user_data->twitter_token
            );
            Session::push('user', $session);
            $request->session()->push('user.teams', 'developers');
        }
        return $session;
    }*/

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */
    public function redirectToProviderFacebook($param) {
       // dd(1);
        // $parameter = base64_decode(trim($param));
        
        // //Check For Valid Unqiue Code
        // if($parameter !== "only_login"){
        //     $uniquecode = $parameter;
        //     $check = DB::table('invitation')->where([
        //         ['uniquecode', '=', ''.$uniquecode.''],
        //         ['status', '=', 'Active']
        //         ])->first();
        //      if(empty($check) == false){
        //         $parameter = "login_register";
        //      }
        // }

        // if($parameter==="only_login" || $parameter==="login_register"){
        //     Session::put("fb_status", $param);
        // }
        // else{
        //    // return redirect("login");
        //    return redirect("explore"); 
        // }
        return Socialite::driver('facebook')->redirect();
    }
    
    /**
     * Redirect the user to the Twitter authentication page.
     *
     * @return Response
     */
    public function redirectToProviderTwitter($param) {

        // $parameter = base64_decode(trim($param));
        
        // //Check For Valid Unqiue Code
        // if($parameter !== "only_login"){
        //     $uniquecode = $parameter;
        //     $check = DB::table('invitation')->where([
        //         ['uniquecode', '=', ''.$uniquecode.''],
        //         ['status', '=', 'Active']
        //         ])->first();
        //     if(empty($check) == false){
        //         $parameter = "login_register";
        //     }
        // }

        // if($parameter==="only_login" || $parameter==="login_register"){
        //     Session::put("twitter_status", $param);
        // }
        // else{
        //     return redirect("login");
        // }

        return Socialite::with('twitter')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return Response
     */
    public function handleProviderCallbackFacebook() {
    
        $fb_user = Socialite::driver('facebook')->user();
        
        

        //Check fb status to decide whether to allow only login or login &register both
        $fb_status = base64_decode(Session::get("fb_status"));
        Session::forget("fb_status");
        
        //dd($fb_user);
        $user = $fb_user->user;
        $access_token = str_replace('"', '', $fb_user->token);
        
        if(Auth::check())
        {
            $login_user =  Auth::user();
            $login_user->facebook_connect = 'Y';
            $login_user->facebook_token = $user['id'];
            $login_user->facebook_access_token = $access_token;
            $login_user->save();
            //return redirect('account/profile/edit');
            return redirect('explore');
        }

        /*$conditions = [
            'facebook_token' => $user['id']
        ];*/

        $conditions = [
          'email' => $user['email']
        ];


        $result = User::where($conditions)->first();
       

        //If User is not registered and not have unique code then redirect user to landing page
        // if(empty($result) && $fb_status === "only_login"){
        //     return redirect('auth/social/redirect/landing');
        // }//If user has uniquecode then allow new user registration
        // else if(empty($result) && $fb_status !== "only_login"){
            if(empty($result) ){ 
            $name = $user['name'];
            $arr  = explode(' ', $name);
            $first_name = ucwords($arr[0]);
            $last_name  = ucwords($arr[1]);
            $user_name  = $first_name.$user['id'];
            $email      = $user['email'];

            /**Commented on Purpose**/
            //Check For Valid Invite Users
            /*if($fb_status != null){
                $uniquecode = $fb_status;
                $check      = DB::table('invitation')->where([
                    ['uniquecode', '=', ''.$uniquecode.''],
                    ['recipientemailaddress', '=', ''.$email.''],
                    ['status', '=', 'Active']
                    ])->first();
                if(empty($check)){
                    return redirect('signup?code='.$fb_status)
                    ->with('flash_notification.level', 'alert-error')
                    ->with('flash_notification.message', "The email used to send invitation code and the email for facebook login are not the same");
                }
            }*/

            $user_info = [
                'first_name'            => $first_name,
                'last_name'             => $last_name,
                //'username'              => $user_name,
                'email'                 => $email,
                'facebook_token'        => $user['id'],
                'facebook_access_token' => $access_token,
                'sign_up_via'           => 'facebook',
                'invitation_code'       => $fb_status,
                'avatar'                => $fb_user->avatar,
                'avatar_original'       => $fb_user->avatar_original
            ];

            Session::put('user', $user_info);
            Session::put('sign_up_incomplete', true);
            return redirect('auth/facebook/signup/facebook');

            //Code Commented on purpose because there will be an inermediate step before registration
            // $user = new User($user_info);
            // $user->username=  $first_name;
            // $user->facebook_connect         = 'Y';
            // $user->facebook_access_token    = $access_token;
            // $user->email_verified           = '1';
            // $user->save();

            // if(!empty($user) && $fb_status != null){

            //     $uniquecode = $fb_status;
                
            //     //Update Unqiue code status
            //     $affected = DB::update("UPDATE `invitation` set `status` = 'Inactive' where  `uniquecode` = ?", [$uniquecode]);
            // }
            // Auth::login($user);
            // return redirect('explore');

        }
        else{
            // Already registered ? Yes.
            Auth::login($result);
            
            //Save user Information in db
            $login_user =  Auth::user();
            $login_user->facebook_connect = 'Y';
            $login_user->facebook_token = $user['id'];
            $login_user->facebook_access_token = $access_token;
            $login_user->save();
            //dd(Auth::user()->id);
            return redirect('explore'); // need to change it to explore 
        }

        
    }

    /**
     * Obtain the user information from Twitter.
     *
     * @return Response
     */
    public function handleProviderCallbackTwitter() {

        $tw_user = Socialite::driver('twitter')->user();

        $user = $tw_user->user;

        //Check twitter status to decide whether to allow only login or login & register both
        $twitter_status = base64_decode(Session::get("twitter_status"));
        Session::forget("twitter_status");

        $access_token = str_replace('"', '', $tw_user->token);
        $tokenSecret = str_replace('"', '', $tw_user->tokenSecret);
        
        if(Auth::check())
        {
            $login_user =  Auth::user();
            $login_user->twitter_connect = 'Y';
            $login_user->twitter_token = $user['id_str'];
            $login_user->twitter_access_token = $access_token;
            $login_user->twitter_access_tokensecret = $tokenSecret;
            $login_user->save();
            //return redirect('account/profile/edit');
            //return redirect('/');
              return redirect('explore');
        }

       /* $conditions = [
            'twitter_token' => $user['id_str']
        ];*/

       $conditions = [
           'email' =>  $tw_user->email
       ];

        $result = User::where($conditions)->first();

        //If User is not registered and not have unique code then redirect user to landing page
        // if(empty($result) && $twitter_status === "only_login"){
        //     return redirect('auth/social/redirect/landing');
        // }//If user has uniquecode then allow new user registration
        // else if(empty($result) && $twitter_status !== "only_login"){
            if(empty($result)){  
            $name = $tw_user->name;
            $arr  = explode(' ', $name);
            $first_name = isset($arr[0]) ? ucwords($arr[0]) : "";
            $last_name  = isset($arr[1]) ? ucwords($arr[1]) : "";
            $user_name  = $first_name.$user['id_str'];
            $email      = $tw_user->email;

            /**Commented on Purpose**/
            //Check For Valid Invite Users
            /*if($twitter_status != null){
                $uniquecode = $twitter_status;
                $check      = DB::table('invitation')->where([
                    ['uniquecode', '=', ''.$uniquecode.''],
                    ['recipientemailaddress', '=', ''.$email.''],
                    ['status', '=', 'Active']
                    ])->first();
                if(empty($check)){
                    return redirect('signup?code='.$twitter_status)
                    ->with('flash_notification.level', 'alert-error')
                    ->with('flash_notification.message', "The email used to send invitation code and the email for twitter login are not the same");
                }
            }*/

             $user_info = [
                'first_name'            => $first_name,
                'last_name'             => $last_name,
                //'username'              => $user_name,
                'email'                 => $email,
                'twitter_token'         => $user['id_str'],
                'twitter_access_token'  => $access_token,
                'twitter_access_tokensecret' => $tokenSecret,
                'sign_up_via'           => 'twitter',
                'invitation_code'       => $twitter_status,
                'avatar'                => $tw_user->avatar,
                'avatar_original'       => $tw_user->avatar_original
            ];

            Session::put('user', $user_info);
            Session::put('sign_up_incomplete', true);
            return redirect('auth/twitter/signup/twitter');
          //Code Commented on purpose because there will be an inermediate step before registration
        //   $user = new User($user_info);
        //   $user->username=  $first_name;
        //   $user->facebook_connect         = 'Y';
        //   $user->facebook_access_token    = $access_token;
        //   $user->email_verified           = '1';
        //   $user->save();

       
        //   Auth::login($user);
        //   return redirect('explore');

        }
        else{
            // Already registered ? Yes.
            Auth::login($result);
            //Save User Credentials into db
            $login_user =  Auth::user();
            $login_user->twitter_connect = 'Y';
            $login_user->twitter_token = $user['id_str'];
            $login_user->twitter_access_token = $access_token;
            $login_user->twitter_access_tokensecret = $tokenSecret;
            $login_user->save();
           // return redirect('auth/social/redirect/explore');
           return redirect('explore');
        }

        /*if (empty($result) || (!empty($result) && $result['email_verified'] == 0)) {
            return redirect('auth/twitter/signup/twitter')
                    ->with('user', $user)
                    ->with('sign_up_incomplete', true);
        }*/

    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */
    public function redirectToProviderFacebookConnect() {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Redirect the user to the Twitter authentication page.
     *
     * @return Response
     */
    public function redirectToProviderTwitterConnect() {
        return Socialite::with('twitter')->redirect();
    }

    /**
     * Redirect the user to the LinkedIn authentication page.
     *
     * @return Response
     */
    public function redirectToProviderLinkedInConnect() {
        return Socialite::with('linkedin')->redirect();
    }


    /**
     * Obtain the user information from LinkedIn.
     *
     * @return Response
     */
    public function handleProviderCallbackLinkedin(Request $request) {
       
       if(Auth::check())
        {
            $login_user =  Auth::user();
            $login_user->linkedin_connect = 'Y';
            $login_user->linkedin_id = $request->input('member_id');
            $login_user->linkedin_token = $request->input('oauth_token');
            $login_user->save();
            return 1;
        }
    }

     /**
     * Intermediate page for redirection to 
     *
     * @return Response
     */
    public function redirectToLanding($param){
        switch ($param) {
            case 'landing':
                # code...
                //return redirect($this->landingPageURL);
                return view('auth.landing');
                break;
            case 'explore':
                return redirect('/');
                break;
            default:
                # code...
                return redirect('login');
                break;
        }
        //return view('auth.landing');
    }

}
