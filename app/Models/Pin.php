<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\PinTag;
use App\Models\Helper\PinHelper;

class Pin extends Model
{
    const MAX_TAG_LENGTH = 50;
    const MEAUREMENT = 'miles';
    const DISTANCE = 20;
    const COMMENT_LENGTH = 255;

    /**
     * Generated
     */

    protected $table = 'pins';
    public $timestamps = false;
    protected $fillable = ['comment', 'publish_time', 'lat', 'lng', 'user_id'];

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
    public function generateContentForInfoWindow($pin)
    {
        $user = $pin->relationUser;
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
                        'name' => $user->first_name . " " . $user->last_name,
                        'gender' => $user->gender,
                        'user_id' => $user->id,
                        'age' => PinHelper::calculateAge($user->birthday),
                        'profile_picture' => $user->profile_picture,
                    ],
                'pin' =>
                    [
                        'publish_time' => $pin->publish_time,
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

    /**
     * check if user has any active pin
     * @param $request
     * @param $user
     * @return mixed
     */
    public function userHasActivePin($request, $user)
    {
        $onehour = PinHelper::returnTime('minus-1hour', $request->current_time);

        $activePin = Pin::where('updated_at', '>=', $onehour)
            ->where('updated_at', '<=', $request->current_time)
            ->where('user_id', $user->id)
            ->count();

        return $activePin;
    }

    /**
     * always search for locations that are between now and 1 hour back,
     * So I use this function because it always returns that time between now and 3hours back, and this is for global use
     * @return $this
     */
    public function getPins($request)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $current_time = $request->current_time;
        $km = (Pin::MEAUREMENT == 'km') ? 6371 : 3959;
        $distance = Pin::DISTANCE;
        $onehour = PinHelper::returnTime('minus-1hour', $current_time);
        $pinTable = Pin::getTable();

        $query = Pin::where('updated_at', '>=', $onehour)
            ->where('updated_at', '<=', $current_time)
            ->with('relationPinTag.relationTag', 'relationUser')
            ->select("$pinTable.*")
            ->selectRaw("($km * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)) )) AS distance", [$lat, $lng, $lat])
            ->having("distance", "<=", $distance);

        //if user wants to filter by tag
        if (isset($request->filter_by_tag)) {
            $pinTagTable = new PinTag;
            $pinTagTable = $pinTagTable->getTable();

            $query
                ->where("tag_id", "=", $request->filter_by_tag)
                ->join($pinTagTable, "$pinTable.id", "=", "$pinTagTable.pin_id", 'inner');
        }
        return $query;
    }

    public function relationUser()
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
