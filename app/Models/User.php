<?php

namespace App\Models;

use Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function users() {
        return $this->belongsToMany(\App\Models\User::class, 'block_user', 'blocked_by', 'who_is_blocked');
    }

    public function users() {
        return $this->belongsToMany(\App\Models\User::class, 'block_user', 'who_is_blocked', 'blocked_by');
    }

    public function messages() {
        return $this->belongsToMany(\App\Models\Message::class, 'messages_reply', 'user_id', 'message_id');
    }

    public function blockUsers() {
        return $this->hasMany(\App\Models\BlockUser::class, 'blocked_by', 'id');
    }

    public function blockUsers() {
        return $this->hasMany(\App\Models\BlockUser::class, 'who_is_blocked', 'id');
    }

    public function locations() {
        return $this->hasMany(\App\Models\Location::class, 'user_id', 'id');
    }

    public function messages() {
        return $this->hasMany(\App\Models\Message::class, 'user_one', 'id');
    }

    public function messages() {
        return $this->hasMany(\App\Models\Message::class, 'user_two', 'id');
    }

    public function messagesReplies() {
        return $this->hasMany(\App\Models\MessagesReply::class, 'user_id', 'id');
    }

    public function pushNotificationsTokens() {
        return $this->hasMany(\App\Models\PushNotificationsToken::class, 'user_id', 'id');
    }

}
