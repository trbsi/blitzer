<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Redis;
use App\Models\PinTimeUpdate;
use App\Models\Pin;
use Illuminate\Support\Facades\Cache;
use DB;

class PinController extends Controller
{
    /**
     * [Update user's pin time if user is active so pin doesn't expire. It is saved in pin_time_update table everytime user sends a message to someone]
     */
    public function updatePinTime()
    {
        /*Redis::command("sadd", [PinHelper::REDIS_PINS_TO_UPDATE_TIME, 2,5,6, "j", "aaa"]);
        $x = Redis::command('spop', [PinHelper::REDIS_PINS_TO_UPDATE_TIME, 100]);*/
        $pin_id = [];
        $result = PinTimeUpdate::limit(100)->get();
        foreach ($result as $value) {
            $pin_id[] = Cache::get("user:$value->user_id:pin");
            $value->delete();
        }

        Pin::whereIn("id", $pin_id)->update(['updated_at' => DB::raw('DATE_ADD(updated_at,INTERVAL 10 MINUTE)')]);
    }
}
