<?php

use Illuminate\Database\Seeder;
use App\Models\Pin;
use App\Models\PinTag;
use App\Models\Tag;
use App\Helpers\CacheHelper;

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
                //Osijek pin
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

                //Osijek pins
                [
                    'comment' => 'Hello from Osijek.',
                    'publish_time' => $time,
                    'lat' => 45.56117947133065,
                    'lng' => 18.682594299316406,
                    'user_id' => 4,
                ],
                
            ];

        foreach ($data as $key => $value) {
            $pinTmp = $pin->create($value);
            for ($i = 0; $i < rand(1, count($tags)); $i++) {
                $pinTmp->relationPinTag()->create(['tag_id' => rand(1, count($tags))]);
            }
            CacheHelper::saveCache("user_pin_id", ["user_id" => $value["user_id"]], $pinTmp->id, 360);
        }
    }
}

