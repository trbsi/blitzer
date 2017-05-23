<?php
namespace App\Traits;

use App\Helpers\PinHelper;
use App\Helpers\CacheHelper;

trait PinTrait
{
    /**
     * fake user's real location
     * @param $number - lng/lat coordinates
     * @return float
     */
    private function fakeLocation($number)
    {
        //this moves pin's location to about 100m
        $rand     = 0.000400; //rand(1000,1100);
        $date_sum = date("Y") + date("m") + date("d");

        if ($date_sum % 2 == 0) {
            return (float) ($number + $rand);
        } else {
            return (float) ($number - $rand);
        }

    }

    /**
     * @param $pin - loaded Pin model
     * @param $user - current user
     * @return array
     */
    public function generateContentForInfoWindow($pin)
    {
        $user = $pin->relationUser;
        if (!empty($pin->comment)) {
            $comment = htmlentities($pin->comment);
        }
        //decode html

        $i = 0;
        foreach ($pin->relationPinTag as $pin2) {
            $tags[$i]["tag_id"]   = $pin2->tag_id;
            $tags[$i]["tag_name"] = $pin2->relationTag->tag;
            $i++;
        }

        $lat = $this->fakeLocation($pin->lat);
        $lng = $this->fakeLocation($pin->lng);

        return
            [
            'user' =>
            [
                'name'            => $user->first_name . " " . $user->last_name,
                'gender'          => $user->gender,
                'user_id'         => $user->id,
                'age'             => PinHelper::calculateAge($user->birthday),
                'profile_picture' => $user->profile_picture,
            ],
            'pin'  =>
            [
                'publish_time' => $pin->publish_time,
                'comment'      => $comment,
                'lat'          => (float) $lat,
                'lng'          => (float) $lng,
                'pin_id'       => $pin->id,
                'tags'         => $tags,
            ],

        ];
    }

    //****************************FAKE PINS START********************************
    /**
     * [generateFakePins description]
     * @param  [type] $user_id [Auth user id]
     * @return [type]          [description]
     */
    private function generateFakePins($user_id, $request)
    {
        $userPin = CacheHelper::getCache("user_pin_id", ["user_id" => $user_id]);
    	$pin = $this->getPinById($userPin);

        if(!empty($pin))
        {
            $lat = $pin->lat;
            $lng = $pin->lng;
            $publish_time = $pin->publish_time;
            $pin_id = $pin->id;
            $age = PinHelper::calculateAge($pin->relationUser->birthday);
        }
        else
        {
            $lat = $request->lat;
            $lng = $request->lng;
            $publish_time = $request->current_time;
            $pin_id = 0;
            $age = 25;
        }

        $pins = CacheHelper::getCache("fake_pins", ["location" => round($lat+$lng)]);

        if(!empty($pins))
        {
            return $pins;
        }

        $male =
        [
            'first' =>
            [
                'Donovan', 'Randolph', 'Shaun', 'Garth', 'Ty', 'Stefan', 'Doug', 'Carter', 'Werner', 'Ignacio', 'Truman', 'Coy', 'Reynaldo', 'Valentin', 'Roscoe', 'Randall', 'Andres', 'Leonard', 'Dewayne', 'Leon',

            ],
            'last'  =>
            [
                'Eason', 'Beltran', 'Albanese', 'Ellington', 'Mccandless', 'Chappel', 'Dufner', 'Stanberry', 'Linger', 'Frisch', 'Chagnon', 'Borman', 'Vanderpool', 'Kerby', 'Funnell', 'Erhardt', 'Mcalister', 'Fenn', 'Crispin', 'Harp',
            ],
        ];

        $female =
            [
            'first' => [
                'Shella', 'Jaqueline', 'Janey', 'Sha', 'Sudie', 'Katherine', 'Jennie', 'Arlene', 'Lizbeth', 'Allyson', 'Elinore', 'Hsiu', 'Pei', 'Janiece', 'Cinda', 'Ora', 'Geralyn', 'Sebrina', 'Lura', 'Ann', 'Nadene', 'Krista', 'Nieves', 'Johanna', 'Joella', 'Janna', 'Charis', 'Yon', 'Anissa', 'Charita',
            ],
            'last'  => [
                'Hockman', 'Haus', 'Ames', 'Kephart', 'Monfort', 'Meche', 'Parrinello', 'Abercrombie', 'Colone', 'Ellison', 'Monson', 'Austin', 'Robitaille', 'Cargill', 'Peckham', 'Castanon', 'Dare', 'Magwood', 'Booth', 'Pitre', 'Huth', 'Muth', 'Kauppi', 'Galyean', 'Cousin', 'Ditullio', 'Hawes', 'Vuong', 'Trinidad', 'Hayse',
            ],
        ];

        $tags = 
        [
            [['tag_name'=>'#mama'], ['tag_name'=>'#tata']], [['tag_name'=>'#jedan'], ['tag_name'=>'#dva']], [['tag_name'=>'#plaža'], ['tag_name'=>'#sunce']]
        ];

        $countMale   = count($male["first"]);
        $countFemale = count($female["first"]);

        $fake = [];
        $data =
        [
            'age' => $age,
            'lat' => $lat,
            'lng' => $lng,
            'time' => $publish_time,
            'pin_id' => $pin_id,
            'tags' => $tags,
            'user_id' => $user_id
        ];

        $data["gender"] = 'male';
        $data["names"] = $male;
        for ($i = 0; $i < ($countMale); $i++) {

            $fake[] = $this->generateArray($data);

        }

        $data["type"] = 'female';
        $data["names"] = $female;
        for ($i = 0; $i < ($countFemale); $i++) {

            $fake[] = $this->generateArray($data);

        }

        //save pins to cache
        CacheHelper::saveCache("fake_pins", ["location" => round($lat+$lng)], $fake, 60);
        return $fake;

    }

    private function generateArray($data)
    {
        $gender = $data["gender"];
        $countName = count($data['names']["first"]);
        $plusMinusage = 5;
        $date = new \DateTime($data["time"]);
        $date->sub(new \DateInterval('PT'.rand(1,120)."M".rand(1,60)."S"));
        $lat = $data["lat"]+(float)("0.000".rand(1000, 9000));
        $lng = $data["lng"]+(float)("0.000".rand(1000, 9000));

        return
        [
            "user" =>
            [
                "name"            => $data['names']["first"][rand(0,$countName-1)]." ".$data['names']["last"][rand(0,$countName-1)],
                "gender"          => $gender,
                "user_id"         => $data["user_id"],
                "age"             => rand($data["age"]-$plusMinusage, $data["age"]+$plusMinusage),
                "profile_picture" => env('APP_URL').'/files/'.$gender.'/'.rand(0,$countName-1).'.jpg',
            ],
            "pin" =>
            [
                "publish_time" => $date->format('Y-m-d H:i:s'),
                "comment"      => "",
                "lat"          => round($lat,6),
                "lng"          => round($lng,6),
                "pin_id"       => $data["pin_id"],
                "tags" => $data["tags"][rand(0,count($data["tags"])-1)]
            ],
        ];
    }
    //****************************FAKE PINS END********************************

	
}
