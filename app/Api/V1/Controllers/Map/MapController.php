<?php

namespace App\Api\V1\Controllers\Map;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pin;
use App\Models\PinTag;
use App\Models\User;

class MapController extends Controller
{
    /**
     * Instantiate a new Controller instance.
     */
    public function __construct(Pin $pin, User $user, PinTag $pinTag)
    {
        $this->pin = $pin;
        $this->user = $user;
        $this->pinTag = $pinTag;
        $this->authUser = $this->user->getAuthenticatedUser();
        $this->middleware('currentTimeFixer');
    }


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

        $pins = $this->pin->getPins($request)->get()->toArray();
        dd($pins); die();

        //return json data
        foreach ($pins as $key => $pin) {
            $jsonPins[] = $this->pin->generateContentForInfoWindow($pin);
        }
dd($jsonPins); die();
        //return $this->render('@app/modules/api/views/layouts/main');
        //if number of elements in array is == 1 that means there is only user's pin, because script can't get here if user didn't publish a pin
        //$jsonPins can be empty because there are no pins, because user's pin can expire after 30 min, so it's active but not visible on the map
        if (count($jsonPins) == 1 || empty($jsonPins)) {
            $minutesLeft = ApiLocation::minutesLeftForActivePin($activePin, $current_time);
            $message = Yii::t("app", "No pins on the map", ['0' => $minutesLeft]);
            $showAlert = true;
        } else {
            $message = NULL;
            $showAlert = false;
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
    public function addPin(Request $request)
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
}
