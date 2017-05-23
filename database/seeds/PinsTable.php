<?php

use Illuminate\Database\Seeder;
use App\Models\Pin;

class PinsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Pin $pin)
    {
    	$time = date("Y-m-d H:i:s");
        $data = 
        [
        	[
        		'comment' => '',
        		'publish_time' => '',
        		'lat' => '',
        		'lng' => '',
        		'user_id' => '',
        	]
        ];

        foreach ($data as $key => $value) {
        	# code...
        }
    }
}

