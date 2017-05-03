<?php

namespace App\Models\Helper;

/**
* 
*/
class PinHelper
{
	/**
     * I'm getting current time from mobile in format Y-m-d_H:i:s, so you have to explode "_" and return date
     * @param $current_time
     * @param bool|true $strtotime - do you want to change to "strtotime"
     * @return bool|string
     */
    public static function formatCurrentTime($current_time, $strtotime = true)
    {
        $current_time = explode("_", $current_time); //[0]Y-m-d, [1]H:i:s
        $time = $current_time[0] . " " . $current_time[1];
        if ($strtotime == true)
            return date("Y-m-d H:i:s", strtotime($time));
        else
            return $time;
    }
}