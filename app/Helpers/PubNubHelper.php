<?php

namespace App\Helpers;

use PubNub\PubNub;

class PubNubHelper
{
    const PUBNUB_CHANNEL_MSG = "pubnub_channel_msg_";

    /**
     * initialize pubnub
     * @param $IDuser
     * @return Pubnub
     */
    private static function pubNubObject($IDuser)
    {
        return new PubNub(array(
            'subscribe_key' => env('PUBNUB_SUBSCRIBE_KEY'),
            'publish_key' => env('PUBNUB_PUBLISH_KEY'),
            'uuid' => $IDuser,
            'ssl' => false, // kad je true ima neka greška sa PEM fajlom
            //'verify_peer' => false
        ));
    }

    /**
     * initialize pubnub
     * @param $IDuser
     * @return mixed|Pubnub
     */
    public static function initPubNub($IDuser)
    {
        $currentTime = time();
        $sessionTime = 600; //10min
        $pubnub_activity = "pubnub_activity";
        $pubnub_object = "pubnub_object_" . $IDuser;

        //if session is not set, set it
        if (!isset($_SESSION[$pubnub_activity])) {
            $pubnub = self::pubNubObject($IDuser);
            $_SESSION[$pubnub_activity] = $currentTime;
            $_SESSION[$pubnub_object] = serialize($pubnub);
        }

        //if pubnub_object session is set get it
        if (isset($_SESSION[$pubnub_object]))
            $pubnub = unserialize($_SESSION[$pubnub_object]);

        //put this in session to speed up process
        //http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
        if (isset($_SESSION[$pubnub_activity]) && ($currentTime - $_SESSION[$pubnub_activity]) >= $sessionTime) {
            $pubnub = self::pubNubObject($IDuser);
            //session expired, put new data in session
            $_SESSION[$pubnub_activity] = $currentTime;
            $_SESSION[$pubnub_object] = serialize($pubnub);
        }

        return $pubnub;
    }
}