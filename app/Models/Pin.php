<?php
namespace App\Models;

use App\Traits\PinTrait;
use App\Helpers\PinHelper;
use App\Models\Message;
use App\Models\PinTag;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use DB;

class Pin extends Model
{
    use PinTrait;

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
                $t->popularity = 1;
                $t->save();
            } else {
                $t->popularity = $t->popularity + 1;
                $t->update();
            }

            $return[$t->id] = $tag;
        }

        return $return;
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

        return Pin::where('updated_at', '>=', $onehour)
            ->where('updated_at', '<=', $request->current_time)
            ->where('user_id', $user->id)
            ->count();
    }

    /**
     * get user's latest pin
     * @param $request
     * @param $user
     * @return mixed
     */
    public function getUserLatestPin($user_id)
    {
        return Pin::where('id', DB::raw("(SELECT MAX(id) FROM " . Pin::getTable() . " WHERE user_id=$user_id)"))
        ->with(['relationPinTag.relationTag', 'relationUser'])
        ->first();
    }

    /**
     * get pin by id
     * @param $pin_id
     */
    public function getPinById($pin_id)
    {
        return Pin::where('id', $pin_id)->first();
    }

    /**
     * get only query for pins
     * @param  Request $request [Laravel request]
     * @param  User $authUser [authenticated user]
     * @return [Laravel Eloquent]           [laravel prepared query]
     */
    public function getPinsQuery($request, $authUser)
    {
        $user_id = $authUser->id;
        $lat = $request->lat;
        $lng = $request->lng;
        $current_time = $request->current_time;
        $km = (Pin::MEAUREMENT == 'km') ? 6371 : 3959;
        $minusOneHour = PinHelper::returnTime('minus-1hour', $current_time);
        $pinTable = Pin::getTable();

        $query = Pin::where("$pinTable.updated_at", '>=', $minusOneHour
            )
            ->where("$pinTable.updated_at", '<=', $current_time)
            ->where("$pinTable.user_id", '<>', $user_id)
            ->with(['relationPinTag.relationTag', 'relationUser'])
            ->select("$pinTable.*")
            ->selectRaw("($km * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)) )) AS distance", [$lat, $lng, $lat])
            ->having("distance", "<=", Pin::DISTANCE)
            ->groupBy("$pinTable.id");

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

    /**
     * return all pins formatted
     * @param  Request $request [Laravel request]
     * @param  User $authUser [authenticated user]
     * @return [array]           [formatted pins]
     */
    public function getPins($request, $authUser)
    {
        $pins = $this->getPinsQuery($request, $authUser)->get();

        $jsonPins = [];

        //add latest user pin to array
        $latestUserPin = $this->getUserLatestPin($authUser->id);
        if (!empty($latestUserPin)) {
            $jsonPins[] = $this->generateContentForInfoWindow($latestUserPin);
        }

        //return json data
        foreach ($pins as $key => $pin) {
            $jsonPins[] = $this->generateContentForInfoWindow($pin);
        }

        if (count($jsonPins) < 20) {
            $fakePins = $this->generateFakePins($authUser->id, $request);
            $jsonPins = array_merge($jsonPins, $fakePins);
        }

        return $jsonPins;

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

}
