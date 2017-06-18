<?php

namespace App\Helpers;

/**
 *
 */
class PinHelper
{
    const ONE_HOUR_BACK = 1;
    const MINUTES_BACK_30 = 30;
    const REDIS_PINS_TO_UPDATE_TIME = "pinsToUpdateTime";

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


    /**
     * calculate age
     * @param $birthday
     * @return int
     */
    public static function calculateAge($birthday)
    {
        if(empty($birthday)) {
            return '?';
        }

        $datetime1 = new \DateTime($birthday);
        $datetime2 = new \DateTime();
        $interval = $datetime1->diff($datetime2);
        return (int)$interval->format('%y');
    }

    /**
     * return gender
     * @param  [string] $gender [male/female]
     */
    public static function returnGender($gender)
    {
        if(empty($gender)) {
            return '?';
        }

        return $gender;
    }

    /**
     * get current time
     * @param $x - detect where to add or sub date
     * @param null $currentDate - current time
     * @return bool|null|string
     */
    public static function returnTime($x, $currentDate = NULL)
    {
        if ($currentDate == NULL)
            $currentDate = date("Y-m-d H:i:s");
        else
            $currentDate = date("Y-m-d H:i:s", strtotime($currentDate));

        $date = new \DateTime($currentDate);
        if ($x == "current") {
            return $currentDate;
        } //30min
        else if ($x == "minus-30min") {
            $date->sub(new \DateInterval('PT' . PinHelper::MINUTES_BACK_30 . 'M'));
            return $date->format('Y-m-d H:i:s');
        } //1h (30min extra)
        else if ($x == "minus-1hour") {
            $date->sub(new \DateInterval('PT' . PinHelper::ONE_HOUR_BACK . 'H'));
            return $date->format('Y-m-d H:i:s');
        }

    }

}