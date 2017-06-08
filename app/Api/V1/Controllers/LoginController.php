<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class LoginController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * refresh the token after it expires
     * @param  Request $request [description]
     * @param  JWTAuth $JWTAuth [description]
     * @return [type]           [description]
     */
    /*public function refreshToken(Request $request, JWTAuth $JWTAuth)
    {
        $token = $JWTAuth->refresh($JWTAuth->getToken());
        return response()
            ->json([
                'status' => true,
                'token' => $token,
            ]);
    }*/

    public function login(Request $request, JWTAuth $JWTAuth)
    {
        //init var
        $status = true;
        $showAlert = false;
        $token = false;
        $message = [
            'body' => trans('core.login.login_failed_title'),
            'title' => trans('core.login.login_failed_body'),
        ];

        //save or update
        $request["birthday"] = date("Y-m-d", strtotime($request->birthday));
        $user = User::updateOrCreate(['email' => $request->email], $request->all());
        if ($user) {

            try {
                $token = $JWTAuth->fromUser($user);
                if (!$token) {
                    $status = false;
                    $showAlert = true;
                } else {
                    $message = null;
                }

            } catch (JWTException $e) {
                $status = false;
                $showAlert = true;
            }
        } else {
            $status = false;
            $showAlert = true;

        }

        return response()
            ->json([
                'status' => $status,
                'token' => $token,
                'message' => $message,
                'showAlert' => $showAlert,
            ]);

    }
}
