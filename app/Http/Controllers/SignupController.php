<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mailers\UserMailer;
use Auth;
use Session;
use App\Notifications\Flash;
use App\Models\User;
use DB;

class SignupController extends Controller {

    use AuthenticatesAndRegistersUsers;

    private $mailer;
    private $registration   = 'Invite';
    private $landingPageURL;

    public function __construct(UserMailer $mailer) {
        $this->mailer = $mailer;
        $this->landingPageURL = config('app.url');
    }

    /**
     * Save the user to DB.
     * 
     * @param  Request $request
     * @return Response
     */
    public function postRegister(Request $request) {

        // Disable and redirect to 404
        //abort(404);
        // Validation
    

        $email      = '';
        $uniquecode = '';

        $this->validate($request, [
            'first_name' => 'required|max:255',
            //'last_name' => 'required|max:255',
            'username' => 'required|alpha_num|unique:users,username|min:2|max:15',
            'password' => 'required|min:8|max:25|confirmed',
            'password_confirmation' => 'required',
            'email' => 'required|unique:users,email|max:255',
            'termsconditions' => 'required'
        ]);
        /********remove the coditions for invite user check(13-11-17) *****************/
                //Allow registration based on curent registration type
                // if($this->registration == 'Invite'){
                //     if($request->input('uniquecode') == null){
                //         // Disable and redirect to 404
                //         abort(404);
                //     }
                    
                // }
        /********remove the coditions for invite user check(13-11-17) *****************/
        //Check For Valid Invite Users
        /**Commented on Purpose**/
        /*if($request->input('uniquecode') != null){
            $email      = $request->input('email');
            $uniquecode = $request->input('uniquecode');
            $check      = DB::table('invitation')->where([
                ['uniquecode', '=', ''.$uniquecode.''],
                ['recipientemailaddress', '=', ''.$email.''],
                ['status', '=', 'Active']
                ])->first();
            if(empty($check)){
                // Disable and redirect to 404
                abort(404);
            }
        }*/

        // Save user
        $data = $request->only('first_name', 'last_name', 'username', 'email', 'password');
        $data['email_verification_token'] = str_random(30);
        $data['sign_up_via'] = 'email';

        $user = User::create($data);



        if(!empty($user) && $request->input('uniquecode') != null){

            $uniquecode = $request->input('uniquecode');
            
            //Update Unqiue code status
            $affected = DB::update("UPDATE `invitation` set `status` = 'Inactive' where  `uniquecode` = ?", [$uniquecode]);
        }

        // Send email
        $this->mailer->welcome($user);

        return redirect('explore?verifyemail=0')
                ->with('flash_notification.level', 'alert-success')
                ->with('flash_notification.message', trans('messages.success_email_send'));
    }
	
	
	/**
	 * Redirect User To Register Page.
	 * 
	 * @param  Request $request
	 * @return Response
	 */
	public function getRegister(Request $request){
       
       
        $data = [];
/********remove the coditions for invite user check(13-11-17) *****************/
        /***********Switch Between registration types according to your wish***********/
        // switch($this->registration){
        //     case 'Invite':
        //     if(!isset($_GET['code'])){
        //         // Disable and redirect to 404
        //         //abort(404);
        //         //Redirect To Landing Page
        //         return redirect($this->landingPageURL);  
        //     }
        //     else{
        //         $uniquecode = trim($_GET['code']);
        //         $check = DB::table('invitation')->where([
        //             ['uniquecode', '=', ''.$uniquecode.''],
        //             ['status', '=', 'Active']
        //             ])->first();
        //         if(empty($check) == true){
        //             // Disable and redirect to 404
        //             //abort(404);
        //             //Redirect To Landing Page
        //             return redirect($this->landingPageURL);   
        //         }
        //         else{
        //             $data = [
        //                 'uniquecode' => $uniquecode,
        //                 'email'      => $check->recipientemailaddress
        //             ];
        //             return view('auth.register', $data);
        //         }
                
        //     }
        //     break;
        //     case 'All':
            
        //     if(isset($_GET['code'])){
        //         $uniquecode = trim($_GET['code']);
        //         $check = DB::table('invitation')->where([
        //             ['uniquecode', '=', ''.$uniquecode.''],
        //             ['status', '=', 'Active']
        //             ])->first();
        //         if(empty($check) == true){
        //             // Disable and redirect to 404
        //             //abort(404); 
        //             //Redirect To Landing Page
        //             return redirect($this->landingPageURL);  
        //         }
        //          $data = [
        //             'uniquecode' => $uniquecode
        //         ];

        //     }
        //     return view('auth.register', $data);
        //     break;

       // }

/********remove the coditions for invite user check(13-11-17) *****************/
       return view('auth.register', $data);
        
		
		
	}

    /**
     * Verify the email from the token.
     *
     * @param String $token
     * @return Response
     */
    public function verifyEmail($token) {
        $user = User::where('email_verification_token', '=', $token)->firstOrFail();

        // Update the DB.
        $user->email_verification_token = null;
        $user->email_verified = 1;
        $user->status = 1;
        $user->save();

        //Add User To Mail Chimp subscription list
        $user_data = [
            'email'     => $user->email,
            'status'    => 'subscribed',
            'firstname' => $user->first_name,
            'lastname'  => $user->last_name
        ];

        $info = addUserToMailchimp($user_data);

        Auth::login($user);

        //Send email to user after successful email verification
        $this->mailer->welcomeToSwolk($user);



        //return redirect('account/dashboard');
        return redirect('/')
                ->with('flash_notification.level', 'alert-success')
                ->with('flash_notification.message', trans('messages.success_email_verified'));
    }

    /**
     * Check the username is already taken.
     * 
     * @param  Request $request
     * @return Boolean
     */
    public function checkUsername(Request $request) {

        $count = User::where(['username' => $request->input('username')])->count();

        $status = ($count == 0) ? true : false;

        return $status;
    }

    /**
     * Check the email is already registered.
     * 
     * @param  Request $request
     * @return Boolean
     */
    public function checkEmail(Request $request) {
        $count = User::where(['email' => $request->input('email')])->count();

        return ($count == 0) ? true : false;
    }

}
