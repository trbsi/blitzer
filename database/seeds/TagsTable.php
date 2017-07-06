<?php

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Tag $tags)
    {
        $data =
            [

                'en' => 
                [
                    '#walkingdownthestreet', '#buysomeweed', '#runningaround', '#drink', '#bored', '#letsgoswim', '#ridingabike', '#businessmeeting', '#groupmeetup',
                    '#watchmovies', '#homecinema', '#ontheroof', '#swimminginthepool', '#poolparty', '#inmypool', '#drinking', '#beer', '#localbar', '#party', '#club', '#eating', '#food', '#fivestar', '#restaurant', '#beach', '#hot', '#chillingonthebeach', '#dinner', '#bbq', '#barbacue', '#house', '#letstalk', '#standupcomedy', '#comedy', '#letsplaysoccer', '#needone', '#tennistime', '#with', '#fun', '#court', '#playingrugby', '#rugby', '#joinus', '#fridaynight', '#boringmonday', '#asalways', '#letsdosomething', '#anyplans', '#today', '#girlsnight', '#disco', '#shopping', '#mall', '#spendingtime', '#watchtv', '#inbar', '#inpark', '#ridebike', '#workout', '#walking', '#speedwalking', '#adventure', '#nearyou', '#crashwedding', '#wedding', '#donttell', '#hitchhiking', '#aroundcity', '#adventuretime', '#righthere', '#needsomeone', '#havingfun', '#chillin', '#weed', '#relaxing', '#middaydrinking', '#afterworkrelax', '#watchingsunset', '#happyhour', '#freedrinks', '#quick', '#festival', '#competition', '#gig', '#concert', '#greenday', '#karaoke', '#karaokenight', '#garagesales', '#selling', '#shoothoops', '#basketball', '#need2more', '#dogwalking', '#dogwalk', '#dogs', '#dogpartner', '#freephotoshooting', '#takingprofilepicture', '#guitarsession', '#guitar', '#edsheernstyle', '#campfire', '#camping', '#kumbaya', '#date', '#datenight', '#guy', '#lookingforfurn', '#chess', '#chesscompetition', '#finals', '#show', '#showtime', '#theater', '#classic', '#cinema', '#newblockbuster', '#anyone', '#streetjam', '#jamming', '#streetrace', '#racing', '#fastandfurious', '#danger', '#hangout', '#lookingforafriend', '#gocart', '#carting', '#raceme', '#joinmeforadrink', '#afterparty', '#comeanddrink', '#grababite', '#mcdonalds', '#quickmeal', '#break', '#work', '#earn5bucks',
                ],
                'cro' =>
                [
                    '#gledanjefilma', '#kucnokino', '#krovzgrade', '#bazeni', '#bazenparty', '#plivanje', '#pijanka', '#pivo', '#lokalnibar', '#klub', '#disko', '#jedenje', '#hrana', '#klasika', '#restoran', '#plaza', '#vrucina', '#cilanjekrajvode', '#vecera', '#rostiljudvoristu', '#rostiljanje', '#ajmopricat', '#standupkomedija', '#komedija', '#nogos', '#nogomet', '#trebanamjedan', '#tenis', '#turnir', '#basket', '#dodi_i_ti', '#haklanje', '#ajmoraditnesta', '#dosadnooo', '#rutina', '#ikakviplanovi', '#zadanas', '#shoping', '#shopingcentar', '#trosenjepara', '#gledanjeutakmice', '#uparku', '#voznjabicom', '#brzohodanje', '#avantura', '#nestoludo', '#vjencanje', '#besplajelo', '#besplapice', '#stopiranje', '#voznjaokograda', '#vrijemezaavanturu', '#sada', '#odmah', '#trazimnekog', '#cilanje', '#zabava', '#relaksiranje', '#poslijeposla', '#pice', '#relakacija', '#gledanjezalaska', '#zalazak', '#pozuri',  '#natjecanje', '#gaza', '#koncert', '#lokalnibendovi', '#prodajem', '#sell', '#mobitel', '#kosarka', '#psi', '#setanjepsa', '#photoshot', '#naucifotkat', '#gitara', '#gitarasession', '#kampiranje', '#prezderavanje', '#vatrica', '#spoj', '#dating',  '#trazimzabavu', '#sah', '#natjecanjeusahu', '#finale','#kazaliste', '#predstava', '#kino', '#noviblockbuster', '#idemo', '#ulicnautrka', '#druzenje', '#netkozakavu', '#utrkujmose', '#pridruzise', '#idemonapice', '#idemopit', '#glad', '#idemopojest',  '#zaradi5kuna',
                ]
            ];

        foreach ($data as $key => $tagsArray) {
            foreach ($tagsArray as $tag_name) {
                if($key == 'en') {
                    $popularity = rand(1000, 5000);
                } else {
                    $popularity = rand(100, 500);
                }

                try 
                {
                    
                    $insert =
                    [
                        'tag_name' => $tag_name,
                        'popularity' => $popularity,
                    ];

                    $tags->create($insert); 
                }
                catch(\Exception $e) {
                    var_dump($e->getMessage());
                }
            }           
        }
    }
}
