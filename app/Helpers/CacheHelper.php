<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * return cache key
     */
    private static function cacheKey($type, $data)
    {
        switch ($type) {
            case 'user_pin_id':
                $key = "user:" . $data["user_id"] . ":pin";
                break;
            case 'fake_pins':
                $key = "fake_pins:location:" . $data["location"];
                break;
        }

        return $key;
    }

    /**
     * get data from cache
     * @param  [string] $type [determine type of cache to get: user_pin_id, fake_pins...]
     * @param  [array] $data [array of data]
     * @return [mix]       [cache data]
     */
    public static function getCache($type, $data)
    {
        $id = self::cacheKey($type, $data);

        return Cache::get($id);
    }

    /**
     * save cache
     * @param  [string]  $type  [determine type of cache to get: user_pin_id, fake_pins...]
     * @param  [array]  $data  [e.g. ["user_id" => 5] or ["location" => 256]]
     * @param  [anything]  $value [any kind of data to save]
     * @param  integer $time [description]
     */
    public static function saveCache($type, $data, $value, $time = 900)
    {
        $key = self::cacheKey($type, $data);
        if (!Cache::add($key, $value, $time)) {
            Cache::put($key, $value, $time);
        }
    }
}