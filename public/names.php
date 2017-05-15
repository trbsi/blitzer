<?php

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

$countMale   = count($male["first"]);
$countFemale = count($female["first"]);

$fake = [];
for ($i = 0; $i < ($countMale); $i++) {

    $data =
    [
    	'type' => 'male',
        'gender' => $male,
        'age' => 20,
        'lat' => 45.756946,
        'lng' => 18.505440999999998,
        'time' => '2017-05-13 18:44:00',
        'pin_id' => 1,
    ];

    $fake[] = generateArray($data);

}

for ($i = 0; $i < ($countFemale); $i++) {

    $data =
    [
    	'type' => 'female',
        'gender' => $female,
        'age' => 20,
        'lat' => 45.756946,
        'lng' => 18.505440999999998,
        'time' => '2017-05-13 18:44:00',
        'pin_id' => 1,
    ];

    $fake[] = generateArray($data);

}



function generateArray($data)
{
	$type = $data["type"];
	$countName = count($data['gender']["first"]);
	$plusMinusage = 5;
	$date = new \DateTime($data["time"]);
	$date->sub(new \DateInterval('PT'.rand(1,120)."M".rand(1,60)."S"));
	$lat = $data["lat"]+(float)("0.000".rand(1000, 9000));
	$lng = $data["lng"]+(float)("0.000".rand(1000, 9000));

    return
    [
        "user" =>
        [
            "name"            => $data['gender']["first"][rand(0,$countName-1)]." ".$data['gender']["last"][rand(0,$countName-1)],
            "gender"          => $type,
            "user_id"         => 0,
            "age"             => rand($data["age"]-$plusMinusage, $data["age"]+$plusMinusage),
            "profile_picture" => "https://www.x.com/x.jpg",
        ],
        "pin" =>
        [
            "publish_time" => $date->format('Y-m-d H:i:s'),
            "comment"      => "",
            "lat"          => $lat,
            "lng"          => $lng,
            "pin_id"       => $data["pin_id"],
        ],
    ];
}
