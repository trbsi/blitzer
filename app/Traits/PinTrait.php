<?php
namespace App\Traits;

use App\Helpers\CacheHelper;
use App\Helpers\PinHelper;

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
        $rand = 0.000400;
        $date_sum = date("Y") + date("m") + date("d");

        if ($date_sum % 2 == 0) {
            return (float)($number + $rand);
        } else {
            return (float)($number - $rand);
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
        $comment = htmlentities($pin->comment); //decode html

        $i = 0;
        $tags = [];

        foreach ($pin->tags as $pin2) {
            $tags[$i]["tag_id"] = $pin2->id;
            $tags[$i]["tag_name"] = $pin2->tag_name;
            $i++;
        }

        $lat = $this->fakeLocation($pin->lat);
        $lng = $this->fakeLocation($pin->lng);

        return
            [
                'user' =>
                    [
                        'name' => $user->first_name . " " . $user->last_name,
                        'gender' => PinHelper::returnGender($user->gender),
                        'user_id' => $user->id,
                        'age' => PinHelper::calculateAge($user->birthday),
                        'profile_picture' => $user->profile_picture,
                    ],
                'pin' =>
                    [
                        'badge' => $pin->message_user_read,
                        'publish_time' => $pin->publish_time,
                        'comment' => $comment,
                        'lat' => $lat,
                        'lng' => $lng,
                        'pin_id' => $pin->id,
                        'tags' => $tags,
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

        $lat = $request->lat;
        $lng = $request->lng;

        $pins = CacheHelper::getCache("fake_pins", ["location" => round($lat,1).":".round($lng,1)]);

        if (!empty($pins)) {
            return $pins;
        }

        if (!empty($pin)) {
            $publish_time = $pin->publish_time;
            $pin_id = $pin->id;
            $age = PinHelper::calculateAge($pin->relationUser->birthday);
        } else {
            $publish_time = $request->current_time;
            $pin_id = 0;
            $age = 25;
        }

        $male =
            [
                'en' =>
                    [
                        'first' =>
                            [
                                'Donovan', 'Randolph', 'Shaun', 'Garth', 'Ty', 'Stefan', 'Doug', 'Carter', 'Werner', 'Ignacio', 'Truman', 'Coy', 'Reynaldo', 'Valentin', 'Roscoe', 'Randall', 'Andres', 'Leonard', 'Dewayne', 'Leon',

                            ],
                        'last' =>
                            [
                                'Eason', 'Beltran', 'Albanese', 'Ellington', 'Mccandless', 'Chappel', 'Dufner', 'Stanberry', 'Linger', 'Frisch', 'Chagnon', 'Borman', 'Vanderpool', 'Kerby', 'Funnell', 'Erhardt', 'Mcalister', 'Fenn', 'Crispin', 'Harp',
                            ],
                    ],
                'hr' =>
                    [
                        'first' =>
                            [
                                'Nenad', 'Jan', 'Mirko', 'Dražen', 'Adam', 'David', 'Viktor', 'Ivan', 'Marijan', 'Ilija', 'Velimir', 'Davor', 'Pavao', 'Goran', 'Robert', 'Saša', 'Mario', 'Marko', 'Branimir', 'Mirko',

                            ],
                        'last' =>
                            [
                                'Gotal', 'Radanović', 'Vinković', 'Lončar', 'Nenadić', 'Bečanić', 'Brković', 'Ivanović', 'Bobić', 'Malenica ', 'Peruško', 'Roguljić', 'Slunjski', 'Goriški', 'Blažević', 'Acinger', 'Hrgović', 'Antolović', 'Varga', 'Ćorak',
                            ],
                    ],
            ];

        $female =
            [
                'en' =>
                    [
                        'first' => [
                            'Shella', 'Jaqueline', 'Janey', 'Sha', 'Sudie', 'Katherine', 'Jennie', 'Arlene', 'Lizbeth', 'Allyson', 'Elinore', 'Hsiu', 'Pei', 'Janiece', 'Cinda', 'Ora', 'Geralyn', 'Sebrina', 'Lura', 'Ann', 'Nadene', 'Krista', 'Nieves', 'Johanna', 'Joella', 'Janna', 'Charis', 'Yon', 'Anissa', 'Charita',
                        ],
                        'last' => [
                            'Hockman', 'Haus', 'Ames', 'Kephart', 'Monfort', 'Meche', 'Parrinello', 'Abercrombie', 'Colone', 'Ellison', 'Monson', 'Austin', 'Robitaille', 'Cargill', 'Peckham', 'Castanon', 'Dare', 'Magwood', 'Booth', 'Pitre', 'Huth', 'Muth', 'Kauppi', 'Galyean', 'Cousin', 'Ditullio', 'Hawes', 'Vuong', 'Trinidad', 'Hayse',
                        ],
                    ],
                'hr' =>
                    [
                        'first' => [
                            'Valentina', 'Lea', 'Sara', 'Irma', 'Gordana', 'Danica', 'Kristina', 'Ivana', 'Anita', 'Tea', 'Vanja', 'Klara', 'Ela', 'Anita', 'Tatjana', 'Irena', 'Dubravka', 'Nikolina', 'Anastazija', 'Maja', 'Danijela', 'Leonarda', 'Petra', 'Veronika', 'Ružica', 'Lidija', 'Jadranka', 'Antonija', 'Eva', 'Julija',
                        ],
                        'last' => [
                            'Komušar', 'Barac', 'Dolenec', 'Brković', 'Kvelić', 'Davidović', 'Božanović', 'Vulić', 'Pavković', 'Butković', 'Krizmanić', 'Mihajlović', 'Medanlić', 'Krstić', 'Peršić', 'Medved', 'Vojković', 'Ranogajec', 'Marić', 'Jurjević', 'Mandić', 'Vidić', 'Knežić', 'Obradović', 'Bosak', 'Ivanišević', 'Šuk', 'Jelić', 'Malež', 'Lukić',
                        ],
                    ],
            ];

        $tags =
            [
                'en' =>
                    [
                        [
                            ['tag_name' => '#watchmovies'],
                            ['tag_name' => '#homecinema'],
                            ['tag_name' => '#ontheroof'],
                        ],
                        [
                            ['tag_name' => '#swimminginthepool'],
                            ['tag_name' => '#poolparty'],
                            ['tag_name' => '#inmypool'],
                        ],
                        [
                            ['tag_name' => '#drinking'],
                            ['tag_name' => '#beer'],
                            ['tag_name' => '#localbar'],
                        ],
                        [
                            ['tag_name' => '#party'],
                            ['tag_name' => '#club'],
                            ['tag_name' => '#disco'],
                        ],
                        [
                            ['tag_name' => '#eating'],
                            ['tag_name' => '#food'],
                            ['tag_name' => '#fivestar'],
                            ['tag_name' => '#restaurant'],
                        ],
                        [
                            ['tag_name' => '#beach'],
                            ['tag_name' => '#hot'],
                            ['tag_name' => '#chillingonthebeach'],
                        ],
                        [
                            ['tag_name' => '#dinner'],
                            ['tag_name' => '#bbq'],
                            ['tag_name' => '#barbacue'],
                            ['tag_name' => '#house'],
                        ],
                        [
                            ['tag_name' => '#letstalk'],
                            ['tag_name' => '#standupcomedy'],
                            ['tag_name' => '#comedy'],
                        ],
                        [
                            ['tag_name' => '#letsplaysoccer'],
                            ['tag_name' => '#needone'],
                        ],
                        [
                            ['tag_name' => '#tennistime'],
                            ['tag_name' => '#with'],
                            ['tag_name' => '#fun'],
                            ['tag_name' => '#court'],
                        ],
                        [
                            ['tag_name' => '#playingrugby'],
                            ['tag_name' => '#rugby'],
                            ['tag_name' => '#joinus'],
                        ],
                        [
                            ['tag_name' => '#fridaynight'],
                            ['tag_name' => '#anyplans'],
                        ],
                        [
                            ['tag_name' => '#boringmonday'],
                            ['tag_name' => '#asalways'],
                            ['tag_name' => '#letsdosomething'],
                        ],
                        [
                            ['tag_name' => '#anyplans'],
                            ['tag_name' => '#today'],
                        ],
                        [
                            ['tag_name' => '#girlsnight'],
                            ['tag_name' => '#disco'],
                        ], [
                        ['tag_name' => '#shopping'],
                        ['tag_name' => '#mall'],
                        ['tag_name' => '#spendingtime'],
                    ],
                        [
                            ['tag_name' => '#watchtv'],
                            ['tag_name' => '#inbar'],
                        ],
                        [
                            ['tag_name' => '#inpark'],
                            ['tag_name' => '#ridebike'],
                            ['tag_name' => '#workout'],
                        ],
                        [
                            ['tag_name' => '#walking'],
                            ['tag_name' => '#speedwalking'],
                        ],
                        [
                            ['tag_name' => '#adventure'],
                            ['tag_name' => '#nearyou'],
                        ],
                        [
                            ['tag_name' => '#crashwedding'],
                            ['tag_name' => '#wedding'],
                            ['tag_name' => '#donttell'],
                        ],
                        [
                            ['tag_name' => '#hitchhiking'],
                            ['tag_name' => '#aroundcity'],
                        ],
                        [
                            ['tag_name' => '#adventuretime'],
                            ['tag_name' => '#righthere'],
                            ['tag_name' => '#needsomeone'],
                        ],
                        [
                            ['tag_name' => '#havingfun'],
                            ['tag_name' => '#chillin'],
                        ],
                        [
                            ['tag_name' => '#weed'],
                            ['tag_name' => '#relaxing'],
                        ],
                        [
                            ['tag_name' => '#middaydrinking'],
                            ['tag_name' => '#afterworkrelax'],
                        ],
                        [
                            ['tag_name' => '#watchingsunset'],
                        ],
                        [
                            ['tag_name' => '#happyhour'],
                            ['tag_name' => '#freedrinks'],
                            ['tag_name' => '#quick'],
                        ],
                        [
                            ['tag_name' => '#festival'],
                            ['tag_name' => '#competition'],
                        ],
                        [
                            ['tag_name' => '#gig'],
                            ['tag_name' => '#concert'],
                            ['tag_name' => '#greenday'],
                        ],
                        [
                            ['tag_name' => '#karaoke'],
                            ['tag_name' => '#karaokenight'],
                        ],
                        [
                            ['tag_name' => '#garagesales'],
                            ['tag_name' => '#selling'],
                        ],
                        [
                            ['tag_name' => '#shoothoops'],
                            ['tag_name' => '#basketball'],
                            ['tag_name' => '#need2more'],
                        ],
                        [
                            ['tag_name' => '#dogwalking'],
                            ['tag_name' => '#dogwalk'],
                            ['tag_name' => '#dogs'],
                            ['tag_name' => '#dogpartner'],
                        ],
                        [
                            ['tag_name' => '#freephotoshooting'],
                            ['tag_name' => '#takingprofilepicture'],
                        ],
                        [
                            ['tag_name' => '#guitarsession'],
                            ['tag_name' => '#guitar'],
                            ['tag_name' => '#edsheernstyle'],
                            ['tag_name' => '#campfire'],
                        ],
                        [
                            ['tag_name' => '#camping'],
                            ['tag_name' => '#eating'],
                            ['tag_name' => '#campfire'],
                            ['tag_name' => '#kumbaya'],
                        ],
                        [
                            ['tag_name' => '#date'],
                            ['tag_name' => '#datenight'],
                        ],
                        [
                            ['tag_name' => '#guy'],
                            ['tag_name' => '#lookingforfurn'],
                        ],
                        [
                            ['tag_name' => '#chess'],
                            ['tag_name' => '#chesscompetition'],
                            ['tag_name' => '#finals'],
                        ],
                        [
                            ['tag_name' => '#show'],
                            ['tag_name' => '#showtime'],
                            ['tag_name' => '#theater'],
                            ['tag_name' => '#classic'],
                        ],
                        [
                            ['tag_name' => '#cinema'],
                            ['tag_name' => '#newblockbuster'],
                            ['tag_name' => '#anyone'],
                        ],
                        [
                            ['tag_name' => '#streetjam'],
                            ['tag_name' => '#jamming'],
                        ],
                        [
                            ['tag_name' => '#streetrace'],
                            ['tag_name' => '#racing'],
                            ['tag_name' => '#fastandfurious'],
                            ['tag_name' => '#danger'],
                        ],
                        [
                            ['tag_name' => '#hangout'],
                            ['tag_name' => '#lookingforafriend'],
                        ],
                        [
                            ['tag_name' => '#gocart'],
                            ['tag_name' => '#carting'],
                            ['tag_name' => '#raceme'],
                        ],
                        [
                            ['tag_name' => '#joinmeforadrink'],
                        ],
                        [
                            ['tag_name' => '#afterparty'],
                            ['tag_name' => '#comeanddrink'],
                        ],
                        [
                            ['tag_name' => '#grababite'],
                            ['tag_name' => '#mcdonalds'],
                            ['tag_name' => '#quickmeal'],
                            ['tag_name' => '#break'],
                        ],
                        [
                            ['tag_name' => '#work'],
                            ['tag_name' => '#work'],
                            ['tag_name' => '#work'],
                            ['tag_name' => '#earn5bucks'],
                        ],
                    ],
                'hr' =>
                    [
                        [
                            ['tag_name' => '#gledanjefilma'],
                            ['tag_name' => '#kucnokino'],
                            ['tag_name' => '#krovzgrade'],
                        ],
                        [
                            ['tag_name' => '#bazeni'],
                            ['tag_name' => '#bazenparty'],
                            ['tag_name' => '#plivanje'],
                        ],
                        [
                            ['tag_name' => '#pijanka'],
                            ['tag_name' => '#pivo'],
                            ['tag_name' => '#lokalnibar'],
                        ],
                        [
                            ['tag_name' => '#party'],
                            ['tag_name' => '#klub'],
                            ['tag_name' => '#disko'],
                        ],
                        [
                            ['tag_name' => '#jedenje'],
                            ['tag_name' => '#hrana'],
                            ['tag_name' => '#klasika'],
                            ['tag_name' => '#restoran'],
                        ],
                        [
                            ['tag_name' => '#plaza'],
                            ['tag_name' => '#vrucina'],
                            ['tag_name' => '#cilanjekrajvode'],
                        ],
                        [
                            ['tag_name' => '#vecera'],
                            ['tag_name' => '#rostiljudvoristu'],
                            ['tag_name' => '#rostiljanje'],
                        ],
                        [
                            ['tag_name' => '#ajmopricat'],
                            ['tag_name' => '#standupkomedija'],
                            ['tag_name' => '#komedija'],
                        ],
                        [
                            ['tag_name' => '#nogos'],
                            ['tag_name' => '#nogomet'],
                            ['tag_name' => '#trebanamjedan'],
                        ],
                        [
                            ['tag_name' => '#tenis'],
                            ['tag_name' => '#turnir'],
                            ['tag_name' => '#pridruzise'],
                            ['tag_name' => ''],
                        ],
                        [
                            ['tag_name' => '#basket'],
                            ['tag_name' => '#dodi_i_ti'],
                            ['tag_name' => '#haklanje'],
                        ],
                        [
                            ['tag_name' => '#fridaynight'],
                            ['tag_name' => '#ajmoraditnesta'],
                        ],
                        [
                            ['tag_name' => '#dosadnooo'],
                            ['tag_name' => '#rutina'],
                            ['tag_name' => '#druzenje'],
                        ],
                        [
                            ['tag_name' => '#ikakviplanovi'],
                            ['tag_name' => '#zadanas'],
                        ],
                        [
                            ['tag_name' => '#girlsnight'],
                            ['tag_name' => '#disco'],
                        ],
                        [
                            ['tag_name' => '#shoping'],
                            ['tag_name' => '#shopingcentar'],
                            ['tag_name' => '#trosenjepara'],
                        ],
                        [
                            ['tag_name' => '#gledanjeutakmice'],
                            ['tag_name' => '#inbar'],
                        ],
                        [
                            ['tag_name' => '#uparku'],
                            ['tag_name' => '#voznjabicom'],
                            ['tag_name' => '#workout'],
                        ],
                        [
                            ['tag_name' => '#brzohodanje'],
                            ['tag_name' => '#speedwalking'],
                        ],
                        [
                            ['tag_name' => '#avantura'],
                            ['tag_name' => '#nestoludo'],
                        ],
                        [
                            ['tag_name' => '#crashwedding'],
                            ['tag_name' => '#vjencanje'],
                            ['tag_name' => '#besplajelo'],
                            ['tag_name' => '#besplapice'],
                        ],
                        [
                            ['tag_name' => '#stopiranje'],
                            ['tag_name' => '#voznjaokograda'],
                        ],
                        [
                            ['tag_name' => '#vrijemezaavanturu'],
                            ['tag_name' => '#sada'],
                            ['tag_name' => '#odmah'],
                            ['tag_name' => '#trazimnekog'],
                        ],
                        [
                            ['tag_name' => '#druzenje'],
                            ['tag_name' => '#cilanje'],
                            ['tag_name' => '#zabava'],
                            ['tag_name' => '#chillin'],
                        ],
                        [
                            ['tag_name' => '#weed'],
                            ['tag_name' => '#relaksiranje'],
                        ],
                        [
                            ['tag_name' => '#poslijeposla'],
                            ['tag_name' => '#pice'],
                            ['tag_name' => '#relakacija'],
                        ],
                        [
                            ['tag_name' => '#gledanjezalaska'],
                            ['tag_name' => '#zalazak'],
                        ],
                        [
                            ['tag_name' => '#happyhour'],
                            ['tag_name' => '#besplapice'],
                            ['tag_name' => '#pozuri'],
                        ],
                        [
                            ['tag_name' => '#festival'],
                            ['tag_name' => '#natjecanje'],
                        ],
                        [
                            ['tag_name' => '#gig'],
                            ['tag_name' => '#gaza'],
                            ['tag_name' => '#koncert'],
                            ['tag_name' => '#lokalnibendovi'],
                        ],
                        [
                            ['tag_name' => '#karaoke'],
                            ['tag_name' => '#karaokenight'],
                        ],
                        [
                            ['tag_name' => '#prodajem'],
                            ['tag_name' => '#sell'],
                            ['tag_name' => '#mobitel'],
                        ],
                        [
                            ['tag_name' => '#kosarka'],
                            ['tag_name' => '#basket'],
                        ],
                        [
                            ['tag_name' => '#dogwalking'],
                            ['tag_name' => '#psi'],
                            ['tag_name' => '#setanjepsa'],
                        ],
                        [
                            ['tag_name' => '#photoshot'],
                            ['tag_name' => '#naucifotkat'],
                        ],
                        [
                            ['tag_name' => '#gitara'],
                            ['tag_name' => '#gitarasession'],
                            ['tag_name' => '#edsheernstyle'],
                            ['tag_name' => '#kampiranje'],
                        ],
                        [
                            ['tag_name' => '#kampiranje'],
                            ['tag_name' => '#prezderavanje'],
                            ['tag_name' => '#vatrica'],
                            ['tag_name' => '#kumbaya'],
                        ],
                        [
                            ['tag_name' => '#spoj'],
                            ['tag_name' => '#dating'],
                        ],
                        [
                            ['tag_name' => '#guy'],
                            ['tag_name' => '#trazimzabavu'],
                        ],
                        [
                            ['tag_name' => '#sah'],
                            ['tag_name' => '#natjecanjeusahu'],
                            ['tag_name' => '#finale'],
                        ],
                        [
                            ['tag_name' => '#show'],
                            ['tag_name' => '#showtime'],
                            ['tag_name' => '#kazaliste'],
                            ['tag_name' => '#predstava'],
                        ],
                        [
                            ['tag_name' => '#kino'],
                            ['tag_name' => '#noviblockbuster'],
                            ['tag_name' => '#idemo'],
                        ],
                        [
                            ['tag_name' => '#streetjam'],
                            ['tag_name' => '#jamming'],
                        ],
                        [
                            ['tag_name' => '#ulicnautrka'],
                            ['tag_name' => '#streetrace'],
                            ['tag_name' => '#fastandfurious'],
                        ],
                        [
                            ['tag_name' => '#druzenje'],
                            ['tag_name' => '#netkozakavu'],
                        ],
                        [
                            ['tag_name' => '#gocart'],
                            ['tag_name' => '#carting'],
                            ['tag_name' => '#utrkujmose'],
                        ],
                        [
                            ['tag_name' => '#pridruzise'],
                            ['tag_name' => '#idemonapice'],
                        ],
                        [
                            ['tag_name' => '#afterparty'],
                            ['tag_name' => '#idemopit'],
                        ],
                        [
                            ['tag_name' => '#glad'],
                            ['tag_name' => '#mcdonalds'],
                            ['tag_name' => '#idemopojest'],
                            ['tag_name' => '#break'],
                        ],
                        [
                            ['tag_name' => '#work'],
                            ['tag_name' => '#work'],
                            ['tag_name' => '#work'],
                            ['tag_name' => '#zaradi5kuna'],
                        ],
                    ],
            ];

        $reverseGeocode = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng . "&key=" . env('GOOGLE_MAPS_API_KEY')));

        $get = 'en';
        foreach ($reverseGeocode->results[0]->address_components as $value) {
            if ($value->long_name == "Croatia") {
                $get = "hr";
            }
        }

        $countMale = count($male[$get]["first"]);
        $countFemale = count($female[$get]["first"]);

        $fake = [];
        $data =
            [
                'age' => $age,
                'lat' => $lat,
                'lng' => $lng,
                'time' => $publish_time,
                'pin_id' => $pin_id,
                'tags' => $tags,
                'user_id' => $user_id,
            ];

        $data["gender"] = 'male';
        $data["names"] = $male[$get];
        for ($i = 0; $i < ($countMale); $i++) {

            $fake[] = $this->generateArray($data, $get, $i);

        }

        $data["gender"] = 'female';
        $data["names"] = $female[$get];
        for ($i = 0; $i < ($countFemale); $i++) {

            $fake[] = $this->generateArray($data, $get, $i);

        }

        //save pins to cache
        CacheHelper::saveCache("fake_pins", ["location" => round($lat,1).":".round($lng,1)], $fake, 60);
        return $fake;

    }

    /**
     * create random coordinate all over the city
     * @param  [type] $latLng [lat or lng]
     */
    private function randomCoordinates($latLng)
    {
        $rand_start = 3000;
        $rand_end = 90000;

        if (rand(0, 1) % 2 == 0) {
            $x = "0.00";
        } else {
            $x = "0.0";
        }

        if (rand(0, 1) % 2 == 0) {
            return $latLng + (float)($x . rand($rand_start, $rand_end));
        } else {
            return $latLng - (float)($x . rand($rand_start, $rand_end));
        }
    }

    /**
     * check here if coordinates are on water, they shouldn't be
     * https://stackoverflow.com/questions/3645649/i-need-to-know-the-predefined-point-is-in-the-sea-or-on-the-land/25275752#25275752
     */
    private function checkIfWater($data)
    {
        $lat = $this->randomCoordinates($data["lat"]);
        $lng = $this->randomCoordinates($data["lng"]);

        $im = imagecreatefrompng('http://maps.googleapis.com/maps/api/staticmap?center=' . $lat . ',' . $lng . '&zoom=21&format=png&sensor=false&size=1x1&maptype=roadmap&style=feature:administrative|visibility:off&style=feature:landscape|color:0x000000&style=feature:water|color:0xffffff&style=feature:road|visibility:off&style=feature:transit|visibility:off&style=feature:poi|visibility:off&key=' . env('GOOGLE_MAPS_API_KEY'));
        //get pixel color, put it in an array
        $color_index = imagecolorat($im, 0, 0);
        $color_tran = imagecolorsforindex($im, $color_index);

        //if, for example, red value of the pixel is 0 we are on land
        if ($color_tran['red'] == 0) {
            //this is land
            return ['lat' => $lat, 'lng' => $lng];
        } else {
            return $this->checkIfWater($data);
        }
    }

    private function generateArray($data, $get, $i)
    {
        $gender = $data["gender"];
        $countName = count($data['names']["first"]);
        $plusMinusage = 5;
        $date = new \DateTime($data["time"]);
        $date->sub(new \DateInterval('PT' . rand(1, 120) . "M" . rand(1, 60) . "S"));
        $first = trim($data['names']["first"][rand(0, $countName - 1)]);
        $last = trim($data['names']["last"][rand(0, $countName - 1)]);
        $name = $first . " " . $last;

        $latLng = $this->checkIfWater($data);

        foreach ($data["tags"][$get][$i] as $key => $value) {
            $value["tag_id"] = 0;
            $tags[] = $value;
        }

        $comment = "";
        $badge = 0;
        if (env('APP_ENV') != 'live') {
            $badge = rand(0, 5);
            $comment = rand(0, 1) % 2 ? "" : $this->randomizer('{Please|Just} make this {cool|awesome|random} test sentence {rotate {quickly|fast} and random|spin and be random}');
        }

        return
            [
                "user" =>
                    [
                        "name" => $name,
                        "gender" => $gender,
                        "user_id" => $data["user_id"],
                        "age" => rand(23 - $plusMinusage, 23 + $plusMinusage), //$data["age"] instead of 23
                        "profile_picture" => env('APP_URL') . '/files/' . $get . '/' . $gender . '/' . $i . '.jpg',
                    ],
                "pin" =>
                    [
                        "publish_time" => $date->format('Y-m-d H:i:s'),
                        "badge" => $badge,
                        "comment" => $comment,
                        "lat" => $latLng["lat"],
                        "lng" => $latLng["lng"],
                        "pin_id" => $data["pin_id"],
                        "tags" => $tags,
                    ],
            ];
    }

    private function randomizer($sentence)
    {
        $rand_sentence = '';

        // Split sentence into characters
        $char_array = str_split($sentence);

        // Open braces counter
        $br_count = 0;

        // Opening left brace offset
        $br_start = 0;

        // Iterate over characters
        foreach ($char_array as $key => $char) {
            switch ($char) {

                // Is it left brace?
                case '{':
                    // Is it opening brace?
                    if ($br_count == 0) {
                        // If it is note the offset
                        $br_start = $key;
                    }

                    // Increment open braces counter
                    $br_count++;
                    break;

                // Is it right brace?
                case '}':
                    // Decrement open braces counter
                    $br_count--;

                    // Is it closing brace?
                    if ($br_count == 0) {
                        /* Call randomizer again to detect nested
                         * braces in the current section. Randomizer
                         * returns current section but without any
                         * nested braces so we can proceed with
                         * exploding it using | character */
                        $rands = explode(
                            '|',
                            $this->randomizer(
                                implode(
                                    '',
                                    array_slice(
                                        $char_array,
                                        $br_start + 1,
                                        $key - $br_start - 1
                                    )
                                )
                            )
                        );

                        /* Store random element of current section's
                         * | exploded array */
                        $rand_sentence .= $rands[array_rand($rands)];
                    }
                    break;

                // Is it any other character?
                default:
                    /* Since | is only inside {} delimited section,
                     * if there aren't any open braces we can just store
                     * other chracters. */

                    if ($br_count == 0) {
                        $rand_sentence .= $char;
                    }
            }
        }

        return $rand_sentence;
    }

    //****************************FAKE PINS END********************************

}
