<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\PushNotificationsToken;
use Illuminate\Http\Request;
use App\Models\Pin;
use DB;

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

    /**
     * enable test pins
     * @param  Request $request
     */
    public function enableTestPins(Request $request)
    {
        date_default_timezone_set(isset($request->timezone) ? $request->timezone : "Europe/Zagreb");
        DB::table((new Pin)->getTable())
            ->update(['updated_at' => date("Y-m-d H:i:s")]);
        echo "Done. Check the app!";
    }
}    
