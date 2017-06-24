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

                '#walkingdownthestreet', '#buysomeweed', '#runningaround', '#party', '#drink', '#bored', '#letsgoswim', '#ridingabike', '#businessmeeting', '#groupmeetup',
                '#watchmovies', '#homecinema', '#ontheroof', '#swimminginthepool', '#poolparty', '#inmypool', '#drinking', '#beer', '#localbar', '#party', '#club', '#disco', '#eating', '#food', '#fivestar', '#restaurant', '#beach', '#hot', '#chillingonthebeach', '#dinner', '#bbq', '#barbacue', '#house', '#letstalk', '#standupcomedy', '#comedy', '#letsplaysoccer', '#needone', '#tennistime', '#with', '#fun', '#court', '#playingrugby', '#rugby', '#joinus', '#fridaynight', '#anyplans', '#boringmonday', '#asalways', '#letsdosomething', '#anyplans', '#today', '#girlsnight', '#disco', '#shopping', '#mall', '#spendingtime', '#watchtv', '#inbar', '#inpark', '#ridebike', '#workout', '#walking', '#speedwalking', '#adventure', '#nearyou', '#crashwedding', '#wedding', '#donttell', '#hitchhiking', '#aroundcity', '#adventuretime', '#righthere', '#needsomeone', '#havingfun', '#chillin', '#weed', '#relaxing', '#middaydrinking', '#afterworkrelax', '#watchingsunset', '#happyhour', '#freedrinks', '#quick', '#festival', '#competition', '#gig', '#concert', '#greenday', '#karaoke', '#karaokenight', '#garagesales', '#selling', '#shoothoops', '#basketball', '#need2more', '#dogwalking', '#dogwalk', '#dogs', '#dogpartner', '#freephotoshooting', '#takingprofilepicture', '#guitarsession', '#guitar', '#edsheernstyle', '#campfire', '#camping', '#eating', '#campfire', '#kumbaya', '#date', '#datenight', '#guy', '#lookingforfurn', '#chess', '#chesscompetition', '#finals', '#show', '#showtime', '#theater', '#classic', '#cinema', '#newblockbuster', '#anyone', '#streetjam', '#jamming', '#streetrace', '#racing', '#fastandfurious', '#danger', '#hangout', '#lookingforafriend', '#gocart', '#carting', '#raceme', '#joinmeforadrink', '#afterparty', '#comeanddrink', '#grababite', '#mcdonalds', '#quickmeal', '#break', '#work', '#work', '#work', '#earn5bucks',
                '#gledanjefilma', '#kucnokino', '#krovzgrade', '#bazeni', '#bazenparty', '#plivanje', '#pijanka', '#pivo', '#lokalnibar', '#party', '#klub', '#disko', '#jedenje', '#hrana', '#klasika', '#restoran', '#plaza', '#vrucina', '#cilanjekrajvode', '#vecera', '#rostiljudvoristu', '#rostiljanje', '#ajmopricat', '#standupkomedija', '#komedija', '#nogos', '#nogomet', '#trebanamjedan', '#tenis', '#turnir', '#pridruzise', '', '#basket', '#dodi_i_ti', '#haklanje', '#fridaynight', '#ajmoraditnesta', '#dosadnooo', '#rutina', '#druzenje', '#ikakviplanovi', '#zadanas', '#girlsnight', '#disco', '#shoping', '#shopingcentar', '#trosenjepara', '#gledanjeutakmice', '#inbar', '#uparku', '#voznjabicom', '#workout', '#brzohodanje', '#speedwalking', '#avantura', '#nestoludo', '#crashwedding', '#vjencanje', '#besplajelo', '#besplapice', '#stopiranje', '#voznjaokograda', '#vrijemezaavanturu', '#sada', '#odmah', '#trazimnekog', '#druzenje', '#cilanje', '#zabava', '#chillin', '#weed', '#relaksiranje', '#poslijeposla', '#pice', '#relakacija', '#gledanjezalaska', '#zalazak', '#happyhour', '#besplapice', '#pozuri', '#festival', '#natjecanje', '#gig', '#gaza', '#koncert', '#lokalnibendovi', '#karaoke', '#karaokenight', '#prodajem', '#sell', '#mobitel', '#kosarka', '#basket', '#dogwalking', '#psi', '#setanjepsa', '#photoshot', '#naucifotkat', '#gitara', '#gitarasession', '#edsheernstyle', '#kampiranje', '#kampiranje', '#prezderavanje', '#vatrica', '#kumbaya', '#spoj', '#dating', '#guy', '#trazimzabavu', '#sah', '#natjecanjeusahu', '#finale', '#show', '#showtime', '#kazaliste', '#predstava', '#kino', '#noviblockbuster', '#idemo', '#streetjam', '#jamming', '#ulicnautrka', '#streetrace', '#fastandfurious', '#druzenje', '#netkozakavu', '#gocart', '#carting', '#utrkujmose', '#pridruzise', '#idemonapice', '#afterparty', '#idemopit', '#glad', '#mcdonalds', '#idemopojest', '#break', '#work', '#work', '#work', '#zaradi5kuna',

            ];

        foreach ($data as $key => $value) {
            $insert =
                [
                    'tag_name' => $value,
                    'popularity' => rand(1000, 5000),
                ];

            $tags->create($insert);
        }
    }
}
