<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotificationsToken extends Model {

    /**
     * Generated
     */

    protected $table = 'push_notifications_token';
    protected $fillable = ['ID', 'user_id', 'token', 'device', 'device_id', 'date_modified'];


    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }


}
