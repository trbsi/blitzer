<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotificationsToken extends Model
{

    /**
     * Generated
     */

    protected $table    = 'push_notifications_token';
    protected $fillable = ['id', 'user_id', 'token', 'device', 'device_id', 'updated_at', 'created_at'];

    /**
     * Clear old push tokens, older than 7 days
     */
    public function clearPushTokens()
    {
        //SELECT * FROM `rre_push_notifications_token`  WHERE date_modified < DATE_SUB(NOW(), INTERVAL 7 DAY)
        self::whereRaw('updated_at < DATE_SUB(NOW(), INTERVAL 7 DAY)')->delete();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

}
