<?php

namespace App\Api\V1\Controllers\Map;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pin;
use App\Models\PinTag;
use App\Models\User;
use App\Models\Tag;
use App;

class MapController extends Controller
{
    /**
     * Instantiate a new Controller instance.
     */
    public function __construct(Pin $pin, User $user, PinTag $pinTag, Tag $tag)
    {
        $this->pin = $pin;
        $this->user = $user;
        $this->pinTag = $pinTag;
        $this->tag = $tag;
        $this->authUser = $this->user->getAuthenticatedUser();
        $this->middleware('currentTimeFixer');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pins(Request $request)
    {
        $jsonPins = [];
        $showAlert = false;
        $blink = NULL;
        $message = NULL;

        //check if user has any active pin
        $activePin = $this->pin->userHasActivePin($request, $this->authUser);

        if ($activePin == 0) {
            $showAlert = true;
            $blink = 'publish-pin';
            $message = [
                'body' => trans('core.map.user_didnt_publish_pin_body'),
                'title' => trans('core.map.user_didnt_publish_pin_title'),
            ];
        }

        $pins = $this->pin->getPins($request)->get();

        //return json data
        foreach ($pins as $key => $pin) {
            $jsonPins[] = $this->pin->generateContentForInfoWindow($pin);
        }

        return response()
            ->json([
                'showAlert' => $showAlert,
                'blink' => $blink,
                'message' => $message,
                'pins' => $jsonPins
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pinAdd(Request $request)
    {
        $tags = $this->pin->checkTags($request->tags);
        if (empty($tags)) {
            return response()
                ->json([
                    'status' => false,
                    'message' =>
                        [
                            'body' => trans('core.map.missing_tags_title'),
                            'title' => trans('core.map.missing_tags_body'),
                        ],
                    'showAlert' => true,
                ]);
        }

        $user = $this->authUser;
        $pin = $this->pin;

        $pin->user_id = $user->id;
        $pin->publish_time = $pin->updated_at = $request->current_time;
        $pin->lng = $request->lng;
        $pin->lat = $request->lat;
        $pin->fill($request->all());
        if ($pin->save()) {
            //save tags
            foreach ($tags as $tag_id => $tag_name) {
                $tmp = new $this->pinTag;
                $tmp->pin_id = $pin->id;
                $tmp->tag_id = $tag_id;
                $tmp->save();
            }
            $status = true;
        }

        $pin_info = $pin->generateContentForInfoWindow($pin);
        return response()
            ->json([
                'status' => $status,
                'pin' => $pin_info,
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags(Request $request)
    {
        $tags = [];
        if(!empty($request->tag))
        {
            $tags = $this->tag->filterByTags($request);
        }

        return response()
            ->json($tags);
    }

}
