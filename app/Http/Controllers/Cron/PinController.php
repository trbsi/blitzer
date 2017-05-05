<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Models\Helper\PinHelper;
use Illuminate\Support\Facades\Redis;

class PinController extends Controller
{
    public function updatePinTime()
    {
        $x = Redis::command('spop', [PinHelper::REDIS_PINS_TO_UPDATE_TIME, 100]);
        var_dump($x);
    }
}