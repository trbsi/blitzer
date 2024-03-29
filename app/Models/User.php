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
    protected $casts =
        [
            'id' => 'int',
            'facebook_id' => 'int',
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
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
    public function messages()
    {
        return $this->belongsToMany(\App\Models\Message::class, 'messages_reply', 'user_id', 'message_id');
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

    public function favoriteUser()
    {
        return $this->belongsToMany(\App\Models\User::class, 'favorite_users', 'favorited_by', 'favorited');
    }
}
