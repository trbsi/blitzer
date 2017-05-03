<?php

namespace App\Models;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JWTAuth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'birthday', 'first_name', 'last_name', 'gender', 'facebook_id', 'profile_picture',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Automatically creates hash for the user password.
     *
     * @param  string $value
     * @return void
     */
    /*public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }*/

    /**
     * Get user by email
     * @param $email
     * @return mixed
     */
    public function getUserByEmail($email)
    {
        return User::where(['email' => $email])->first();
    }

    // somewhere in your controller
    public function getAuthenticatedUser()
    {
        $token = JWTAuth::getToken();
        if (!$token)
            return false;

        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return $user;
    }

    //RELATIONS
    public function usersBlockedBy()
    {
        return $this->belongsToMany(\App\Models\User::class, 'block_user', 'blocked_by', 'who_is_blocked');
    }

    public function usersWhoisBlocked()
    {
        return $this->belongsToMany(\App\Models\User::class, 'block_user', 'who_is_blocked', 'blocked_by');
    }

    public function messages()
    {
        return $this->belongsToMany(\App\Models\Message::class, 'messages_reply', 'user_id', 'message_id');
    }

    public function blockUsersBlockedBy()
    {
        return $this->hasMany(\App\Models\BlockUser::class, 'blocked_by', 'id');
    }

    public function blockUsersWhoIsBlocked()
    {
        return $this->hasMany(\App\Models\BlockUser::class, 'who_is_blocked', 'id');
    }

    public function locations()
    {
        return $this->hasMany(\App\Models\Location::class, 'user_id', 'id');
    }

    public function messagesUserOne()
    {
        return $this->hasMany(\App\Models\Message::class, 'user_one', 'id');
    }

    public function messagesUserTwo()
    {
        return $this->hasMany(\App\Models\Message::class, 'user_two', 'id');
    }

    public function messagesReplies()
    {
        return $this->hasMany(\App\Models\MessagesReply::class, 'user_id', 'id');
    }

    public function pushNotificationsTokens()
    {
        return $this->hasMany(\App\Models\PushNotificationsToken::class, 'user_id', 'id');
    }

}
