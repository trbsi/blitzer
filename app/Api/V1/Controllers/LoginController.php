<?php

namespace App\Api\V1\Controllers;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login(Request $request, JWTAuth $JWTAuth)
    {
        //check if user exists
        $user = $this->user->getUserByEmail($request->email);
        if(empty($user))
        {
            $user = $this->user;
        }

        //init var
        $status = true;
        $showAlert = false;
        $token = false;

        //save or update
        $user->email = $request->email;
        $user->birthday = date("Y-m-d H:i:s", strtotime($request->birthday));
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->gender = $request->gender;
        $user->facebook_id = $request->facebook_id;
        if($user->save())
        {
            $credentials = $request->only(['email']);

            try
            {
                $token = $JWTAuth->fromUser($user);

                if(!$token) {
                    $status = false;
                    $showAlert = true;
                }

            }
            catch (JWTException $e)
            {
                $status = false;
                $showAlert = true;
            }
        }
        else
        {
            $status = false;
            $showAlert = true;
        }

        return response()
            ->json([
                'status' => $status,
                'token' => $token,
                'message' => 
                [
                    'body' => trans('core.login.login_failed_title'),
                    'title' => trans('core.login.login_failed_body'),
                ],
                'showAlert' => $showAlert,
            ]);

    }
}
