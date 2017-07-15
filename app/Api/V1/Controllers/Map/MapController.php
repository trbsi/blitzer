<?php

namespace App\Api\V1\Controllers\Map;

use App\Api\V1\Controllers\BaseAuthController;
use Illuminate\Http\Request;
use App\Models\Pin;
use App\Models\PinTag;
use App\Models\Tag;
//use Illuminate\Support\Facades\Redis;
use App\Helpers\CacheHelper;
use App\Models\User;
use DB;

class MapController extends BaseAuthController
{
    /**
     * Instantiate a new Controller instance.
     */
    public function __construct(Pin $pin, PinTag $pinTag, Tag $tag, User $user)
    {
        parent::__construct($user);
        $this->pin = $pin;
        $this->pinTag = $pinTag;
        $this->tag = $tag;
        $this->middleware('currentTimeFixer');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pins(Request $request)
    {
        $showAlert = false;
        $blink = false;
        $message =
            [
                'body' => null,
                'title' => null,
            ];
        $authUser = $this->authUser;
        $enableAllPins = true;

        //check if user has any active pin
        $latestUserPin = $this->pin->getUserLatestPin($this->authUser->id, $request->current_time);

        if (empty($latestUserPin)) {
            $showAlert = false;
            $enableAllPins = false;
            $blink = true;
            $message = [
                'body' => trans('core.map.user_didnt_publish_pin_body'),
                'title' => trans('core.map.user_didnt_publish_pin_title'),
            ];
        }

        $pins = $this->pin->getPins($request, $authUser, $latestUserPin);

        return response()
            ->json([
                'enableAllPins' => $enableAllPins,
                'showAlert' => $showAlert,
                'blink' => $blink,
                'message' => $message,
                'pins' => $pins
            ], 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pinPublish(Request $request)
    {
        DB::beginTransaction();
        try {
            $tags = $this->pin->checkTags($request->tags);
            if (empty($tags)) {
                return response()
                    ->json([
                        'status' => false,
                        'message' =>
                            [
                                'body' => trans('core.map.missing_tags_body'),
                                'title' => trans('core.map.missing_tags_title'),
                            ],
                        'showAlert' => true,
                    ]);
            }

            $user = $this->authUser;
            $pin = $this->pin;

            $comment = (empty($request->comment)) ? NULL : $request->comment;
            if (strlen($comment) > Pin::COMMENT_LENGTH) {
                $comment = substr($comment, 0, Pin::COMMENT_LENGTH);
            }

            //get latest user pin and just update updated_at one hour back just in case user publishes few pins in a row
            if($latestUserPin = $this->pin->getUserLatestPin($user->id, $request->current_time)) {
                //revert last user pin one hour back
                $latestUserPin->update(['updated_at' => DB::raw('DATE_SUB(updated_at,INTERVAL 1 HOUR)')]);
            }

            $pin->fill($request->all());
            $pin->user_id = $user->id;
            $pin->publish_time = $pin->updated_at = $request->current_time;
            $pin->lng = $request->lng;
            $pin->lat = $request->lat;
            $pin->comment = $comment;
            if ($pin->save()) {
                //save in redis so you can get latest user's pin id
                //@TODO Redis::set("user:$user->id:pin", $pin->id);
                CacheHelper::saveCache("user_pin_id", ["user_id" => $user->id], $pin->id, 360);

                //save tags
                $pin->tags()->attach(array_flip($tags));
                $status = true;
            }

            $pin_info = $pin->generateContentForInfoWindow($pin);
            DB::commit();
            return response()
                ->json([
                    'status' => $status,
                    'pins' => [$pin_info],
                ], 200, [], JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            DB::rollBack();
            abort(403, $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags(Request $request)
    {
        $tags = [];
        if (isset($request->filter_by_tag)) {
            if ($request->filter_by_tag == "get_top_tags") {
                $tags = $this->tag->getTopHashtags();
            } else {
                $tags = $this->tag->filterByTags($request);
            }
        }


        return response()->json($tags, 200, [], JSON_NUMERIC_CHECK);
    }

}
