<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotificationsToken extends Model
{

    /**
     * Generated
     */

    protected $table = 'push_notifications_token';
    protected $fillable = ['id', 'user_id', 'token', 'device', 'device_id', 'updated_at', 'created_at'];
    protected $casts =
        [
            'id' => 'int',
            'user_id' => 'int',
        ];

    /**
     * Clear old push tokens, older than 7 days
     */
    public function clearPushTokens()
    {
        //SELECT * FROM `rre_push_notifications_token`  WHERE date_modified < DATE_SUB(NOW(), INTERVAL 7 DAY)
        self::whereRaw('updated_at < DATE_SUB(NOW(), INTERVAL 7 DAY)')->delete();
    }

    /**
     * get push tokens of a user so you can send notification to him
     * @param $user_id - ID of a user
     * @return PushNotificationsToken[]|array
     */
    public static function getNotificationTokens($user_id)
    {
        //you have to order it by date_modified becuse there was a case when I had 4 tokens and some were old and when I tried to send notification to those tokens it succeeded but user didn't receive it because it expired and notification wasn't sent to newest token
        return PushNotificationsToken::where('user_id', $user_id)->orderBy("updated_at", "DESC")->get();
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

}
