<?php

namespace App\Mailers;

use App\Mailers\Mailer;

class UserMailer extends Mailer
{
	
	/**
	 * Send the verification email.
	 * 
	 * @return Boolean
	 */
	public function welcome($user) {

		$view 		= 'emails.welcome';
		$data		= [
		                'token'    => $user->email_verification_token,
                        'fullname' => $user->first_name." ".$user->last_name
                      ];
		$subject	= 'Swolk - Please verify your email address.';

        return $this->sendTo($user, $subject, $view, $data);
	}

	/**
	 * Send the invitation email.
	 * 
	 * @return Boolean
	 */
	public function invite($user) {

		$view 		= 'emails.invite';
		$data		= ['uniquecode' => $user->uniquecode, 'invite_message'=>$user->invite_message, 'useremail'=>$user->username."@swolk.com", 'fullname'=>$user->fullname];
		$subject	= 'Invitation to swolk';

		return $this->sendTo($user, $subject, $view, $data);
	}

	public function welcomeToSwolk($user){

	    $view    = "emails.confirm-signup";
        $data    = [
                'fullname' => $user->first_name." ".$user->last_name,
        ];
        $subject = 'Welcome to swolk';
        return $this->sendTo($user, $subject, $view, $data);

    }




}