<?php

use Illuminate\Database\Seeder;
use App\Models\Pin;
use App\Models\PinTag;
use App\Models\Tag;

class PinsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Pin $pin, PinTag $pinTag, Tag $tag)
    {

    	$tags = $tag->all();

    	$time = date("Y-m-d H:i:s");
        $data = 
        [
        	[
        		'comment' => 'Hello how are you?',
        		'publish_time' => $time,
        		'lat' => 45.5549624,
        		'lng' => 18.69551439999998,
        		'user_id' => 1,
        	],
        	[
        		'comment' => '',
        		'publish_time' => $time,
        		'lat' => 45.8150108,
        		'lng' => 15.981919000000062,
        		'user_id' => 2,
        	],
        	[
        		'comment' => 'So this is my time to shine.',
        		'publish_time' => $time,
        		'lat' => 39.7392358,
        		'lng' => -104.990251,
        		'user_id' => 3,
        	],
        ];

        foreach ($data as $key => $value) {
        	$pinTmp = $pin->create($value);
        	for($i=0;$i<rand(1, count($tags)); $i++)
        	{
        		$pinTmp->relationPinTag()->create(['tag_id' => rand(1, count($tags))]);
        	}
        }
    }
}

