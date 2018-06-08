<?php

namespace App\Http\Controllers;

use Auth;
use Response;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ValidationController extends Controller {

    /**
     * Check the username is already taken.
     * 
     * @param  Request $request
     * @return Boolean
     */
    public function checkUsername(Request $request) {

        $count = User::where(['username' => $request->input('username')])->count();
        
        //$status = ($count == 0 || Auth::user()->username == $request->input('username')) ? true : false;
        if (Auth::check()) {
            $status = ($count == 0 || Auth::user()->username == $request->input('username')) ? true : false;
        }else{
            $status = ($count == 0) ? true : false;
        }

        echo $status;
    }
    
    /**
     * Check the email is already registered.
     * 
     * @param  Request $request
     * @return Boolean
     */
    public function checkEmail(Request $request) {
        $count = User::where(['email' => $request->input('email')])->count();

        echo ($count == 0) ? true : false;
    }

}
