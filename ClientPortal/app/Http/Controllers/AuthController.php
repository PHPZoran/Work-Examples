<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller{

    /**
     * Returns LogIn view
     *
     * @return mixed
     */
    public function getLogin()
    {
        // show the form
        return view('login.login');
    }

    /**
     * Logs out user from portal and navigates him to LogIn view
     *
     * @return mixed
     */
    public function getLogOut()
    {
        // show the form
        return Auth::logOut();
    }

    /**
     * Logs in user into portal
     *
     * @param Request $request
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        // validate the info, create rules for the inputs
        $rules = array(
            'username'    => 'required|min:3',
            'password' => 'required|alphaNum|min:3'
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make($request->all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('login')
                // send back all errors to the login form
                ->withErrors($validator)
                // send back the input (not the password) so that we can repopulate the form
                ->withInput(Input::except('password'));
        }
        else {

            // create our user data for the authentication
            $userdata = array(
                'username'     => Input::get('username'),
                'password'  => Input::get('password')
            );

            // make sure that user is logged in portal app
            if(Auth::authenticate($userdata)){

                // redirects user to the report view
                return Redirect::to('report');
            }
            else{

                // return user to login screen and display errors
                return Redirect::to('login')
                    ->withErrors("Could not login with user: ".Input::get('username').". Please check your credentials and try again.");
            }
        }
    }
}