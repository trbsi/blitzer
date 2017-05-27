<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\PushNotificationsToken;

class CronController extends Controller
{
    public function __construct(PushNotificationsToken $PNT)
    {
        $this->PNT = $PNT;
    }

    /**
     * Clear old push tokens, older than 7 days
     */
    public function clearOldPushTokens()
    {
        $this->PNT->clearPushTokens();
    }
}    
