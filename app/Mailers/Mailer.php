<?php

namespace App\Mailers;

use Mail;

abstract class Mailer
{
	
	/**
	 * Send email with subject and body to given user.
	 *
	 * @param App\Models\User $user
	 * @param String $subject
	 * @param View $view
	 * @param Array $data
	 * @return Response.
	 */
	public function sendTo($user, $subject, $view, $data = []) {
		Mail::send($view, $data, function($message) use($user, $subject) {
			if($user->username !== null && $user->fullname !== null){
				$message->from($user->username."@swolk.com", $user->fullname);
			}
			$message->to($user->email)->subject($subject);
		});
	}
}