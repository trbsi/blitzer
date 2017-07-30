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
    const DISTANCE = 25;
    const COMMENT_LENGTH = 255;

    /**
     * Generated
     */

    protected $table = 'pins';
    public $timestamps = false;
    protected $fillable = ['comment', 'publish_time', 'lat', 'lng', 'user_id', 'updated_at'];
    protected $casts =
        [
            'id' => 'int',
            'user_id' => 'int',
            'lat' => 'float',
            'lng' => 'float',
            'favorited' => 'boolean', // see getPinsQuery()
            'message_user_read' => 'int', // see getPinsQuery()
        ];

    /**
     * @param  string $value
     * @return string
     */
    public function getMessageUserReadAttribute($value)
    {
        return (isset($value) ? $value : 0);
    }

    /**
     * @param  string $value
     * @return string
     */
    public function getFavoritedAttribute($value)
    {
        return ($value == NULL || $value == 0 ? false : true);
    }


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

            $tagSave = Tag::updateOrCreate(['tag_name' => $tag]);
            $tagSave->increment("popularity");

            $return[$tagSave->id] = $tag;
        }

        return $return;
    }


    /**
     * check if user has any active pin
     * @param $request
     * @param $user
     * @return mixed
     */
    /*public function userHasActivePin($request, $user)
    {
        $minusOneHour = PinHelper::returnTime('minus-1hour', $request->current_time);

        return Pin::whereBetween("updated_at", [$minusOneHour, $request->current_time])
            ->where('user_id', $user->id)
            ->count();
    }*/

    /**
     * get user's latest pin
     * @param $current_time
     * @param $user
     * @return mixed
     */
    public function getUserLatestPin($user_id, $current_time)
    {
        $minusOneHour = PinHelper::returnTime('minus-1hour', $current_time);

        return Pin::where('id', DB::raw("(SELECT MAX(id) FROM " . Pin::getTable() . " WHERE user_id=$user_id)"))
            ->whereBetween("updated_at", [$minusOneHour, $current_time])
            ->with(['tags', 'relationUser'])
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
     * @param  User $authUser [authenticated user
     * @param integer $latestUserPinId [use this latest user pin id to get all message between my pin and pin of other people so you can return badge]
     * @return [Laravel Eloquent] [laravel prepared query]
     */
    public function getPinsQuery($request, $authUser, $latestUserPinId)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $current_time = $request->current_time;
        $km = (Pin::MEAUREMENT == 'km') ? 6371 : 3959;
        $minusOneHour = PinHelper::returnTime('minus-1hour', $current_time);
        $pinTable = Pin::getTable();

        $query = Pin::whereBetween("$pinTable.updated_at", [$minusOneHour, $current_time])
            ->where("$pinTable.user_id", '<>', $authUser->id)
            ->with(['tags', 'relationUser'])
            ->select("$pinTable.*")
            ->selectRaw("($km * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)) )) AS distance", [$lat, $lng, $lat])
            ->selectRaw("IF(EXISTS(SELECT null FROM favorite_users WHERE favorited_by = $authUser->id AND favorited = $pinTable.user_id),1,0) AS favorited")
            ->having("distance", "<=", Pin::DISTANCE)
            ->groupBy("$pinTable.id");

        //if user wants to filter by tag
        if (isset($request->filter_by_tag)) {
            $pinTagTable = (new PinTag)->getTable();

            $query = $query
                ->where("tag_id", "=", $request->filter_by_tag)
                ->join($pinTagTable, "$pinTable.id", "=", "$pinTagTable.pin_id", 'inner');
        } //if user has published pin join with messages to get if user has unread messages so you can set badge
        else if ($latestUserPinId) {
            $messagesTable = (new Message)->getTable();
            //if user_one_read = 0, user one didn't read a message, set badge to 1, else to 0
            //IF(messages.user_one = 5, IF(messages.user_one_read = 0, 1, 0), IF(messages.user_two_read = 0, 1, 0)) AS message_user_read
            //LEFT JOIN messages ON ((messages.pin_one = 1 OR messages.pin_two = 1) AND (messages.pin_one = pins.id OR messages.pin_two = pins.id))
            $query = $query
                ->leftJoin($messagesTable, function ($join) use ($messagesTable, $latestUserPinId, $pinTable) {
                    $join->on(function ($join) use ($messagesTable, $latestUserPinId, $pinTable) {
                        $join->on("$messagesTable.pin_one", "=", DB::raw($latestUserPinId))
                            ->on("$messagesTable.pin_two", "=", "$pinTable.id");
                    })
                        ->orOn(function ($join) use ($messagesTable, $latestUserPinId, $pinTable) {
                            $join->on("$messagesTable.pin_one", "=", "$pinTable.id")
                                ->on("$messagesTable.pin_two", "=", DB::raw($latestUserPinId));
                        });
                })
                ->selectRaw("IF($messagesTable.user_one = $authUser->id, IF($messagesTable.user_one_read = 0, 1, 0), IF($messagesTable.user_two_read = 0, 1, 0)) AS message_user_read");

        }

        return $query;
    }

    /**
     * return all pins formatted
     * @param  Request $request [Laravel request]
     * @param  User $authUser [authenticated user]
     * @param  Pin $latestUserPin [returned model from getUserLatestPin()]
     * @return [array]           [formatted pins]
     */
    public function getPins($request, $authUser, $latestUserPin)
    {
        $jsonPins = [];
        $latestUserPinId = null;

        //add latest user pin to array
        if (!empty($latestUserPin)) {
            $latestUserPinId = $latestUserPin->id;
            $jsonPins[] = $this->generateContentForInfoWindow($latestUserPin);
        }

        $pins = $this->getPinsQuery($request, $authUser, $latestUserPinId)->get();

        //return json data
        foreach ($pins as $key => $pin) {
            $jsonPins[] = $this->generateContentForInfoWindow($pin);
        }

        /*if (count($jsonPins) < 20) {
            $fakePins = $this->generateFakePins($authUser->id, $request);
            $jsonPins = array_merge($jsonPins, $fakePins);
        }*/

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
