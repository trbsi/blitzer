<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\Helper\PinHelper;

class Pin extends Model
{
    const MAX_TAG_LENGTH = 50;

    /**
     * Generated
     */

    protected $table = 'pins';
    public $timestamps = false;
    protected $fillable = ['comment', 'post_time', 'lat', 'lng', 'user_id'];


    /**
     * @TODO - check if tags exists, put in redis as key => value and check in that way
     * @param $tags
     * @return array
     */
    public function checkTags($tags)
    {
        $return = [];
        preg_match_all("(#[a-zA-Z0-9]*)", $tags, $tags);
        foreach ($tags[0] as $tag) {
            if (strlen($tag) > self::MAX_TAG_LENGTH) {
                $tag = substr($tag, 0, self::MAX_TAG_LENGTH);
            }

            $t = Tag::where(['tag' => $tag])->first();

            if (empty($t)) {
                $t = new Tag;
                $t->tag = $tag;
                $t->save();
            }

            $return[$t->id] = $tag;
        }

        return $return;
    }

    /**
     * @param $pin - loaded Pin model
     * @param $user - current user
     * @return array
     */
    public function generateContentForInfoWindow($pin, $user)
    {
        if (!empty($pin->comment))
            $comment = htmlentities($pin->comment); //decode html

        $i = 0;
        foreach ($pin->relationPinTag as $pin2) {
            $tags[$i]["tag_id"] = $pin2->tag_id;
            $tags[$i]["tag_name"] = $pin2->relationTag->tag;
            $i++;
        }

        $lat = $this->fakeLocation($pin->lat);
        $lng = $this->fakeLocation($pin->lng);

        return
            [
                'user' =>
                    [
                        'name' => $user->first_name." ".$user->last_name,
                        'gender' => $user->gender,
                        'user_id' => $user->id,
                        'age' => PinHelper::calculateAge($user->birthday),
                        'profile_picture' => $user->profile_picture,
                    ],
                'pin' =>
                    [
                        'post_time' => $pin->post_time,
                        'comment' => $comment,
                        'lat' => (float)$lat,
                        'lng' => (float)$lng,
                        'location_id' => $pin->id,
                        'tags' => $tags,
                    ]

            ];
    }

    /**
     * fake user's real location
     * @param $number - lng/lat coordinates
     * @return float
     */
    private function fakeLocation($number)
    {
        //this moves pin's location to about 100m
        $rand = 0.000400; //rand(1000,1100);
        $date_sum = date("Y") + date("m") + date("d");

        if ($date_sum % 2 == 0)
            return (float)($number + $rand);
        else
            return (float)($number - $rand);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(\App\Models\Tag::class, 'pin_tag', 'pin_id', 'tag_id');
    }

    public function relationPinTag()
    {
        return $this->hasMany(\App\Models\PinTag::class, 'pin_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class, 'pin_id', 'id');
    }


}
