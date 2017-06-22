<?php

use Illuminate\Database\Seeder;
use App\Models\Pin;
use App\Models\PinTag;
use App\Models\Tag;
use App\Models\User;
use App\Helpers\CacheHelper;
use App\Helpers\SeederHelper;

class PinsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Pin $pin, PinTag $pinTag, Tag $tag, User $user, SeederHelper $seederHelper)
    {

        $tags = $tag->all();

        $time = date("Y-m-d H:i:s");
        $data =
            [                
                //Osijek pins
                [
                    'comment' => 'Hello from Osijek.',
                    'publish_time' => $time,
                    'lat' => 45.56117947133065,
                    'lng' => 18.682594299316406,
                    'user_id' => $user->getUserByEmail($seederHelper->realMails["timmydario"])->id,
                ],
                [
                    'comment' => 'Hello how are you?',
                    'publish_time' => $time,
                    'lat' => 45.5549624,
                    'lng' => 18.69551439999998,
                    'user_id' => $user->getUserByEmail($seederHelper->fakeMails["mail1"])->id,
                ],  
                [
                    'comment' => 'Osijek rulzzz?',
                    'publish_time' => $time,
                    'lat' => 45.5562515372263,
                    'lng' => 18.71872901916504,
                    'user_id' => $user->getUserByEmail($seederHelper->fakeMails["mail4"])->id,
                ],  
                //Zagreb pins
                [
                    'comment' => 'Hello from Zagreb.',
                    'publish_time' => $time,
                    'lat' => 45.81061488635732,
                    'lng' => 16.017208099365234,
                    'user_id' => $user->getUserByEmail($seederHelper->realMails["msikic"])->id,
                ],
                [
                    'comment' => '',
                    'publish_time' => $time,
                    'lat' => 45.8150108,
                    'lng' => 15.981919000000062,
                    'user_id' => $user->getUserByEmail($seederHelper->fakeMails["mail2"])->id,
                ],
                [
                    'comment' => 'So this is my time to shine.',
                    'publish_time' => $time,
                    'lat' => 45.802118838560446,
                    'lng' => 15.973520278930664,
                    'user_id' => $user->getUserByEmail($seederHelper->fakeMails["mail3"])->id,
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

