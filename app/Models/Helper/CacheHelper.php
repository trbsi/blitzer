<?php
namespace App\Models\Helper;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
	/**
	 * get data from cache
	 * @param  [string] $type [determine type of cache to get]
	 * @param  [array] $data [array of data]
	 * @return [mix]       [cache data]
	 */
	public static function getCache($type, $data)
	{
		switch ($type) 
		{
			case 'pin_id':
				$id = "user:".$data["user_id"].":pin";
				break;
		}

		return Cache::get($id);
	}
}