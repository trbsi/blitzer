<?php

namespace App\Helpers;

class Helper
{
    /**
     * format date: Y-m-d H:i:s
     * @param $date
     * @return bool|string
     */
    public static function formatDate($date)
    {
        //return date("M d, Y h:i:s a"); December 12, 1999 12:00:01 pm
        return date("h:i:s a", strtotime($date));
    }
}