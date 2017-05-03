<?php

namespace App\Api\V1\Controllers\Map;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pin;
use App\Models\PinTag;
use App\Models\User;
use App\Api\V1\Requests\Map\PinRequest;

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
        $this->middleware('currentTimeFixer');
    }


    public function pins($lat, $lng, $current_time)
    {
        $jsonPins = [];

        //check if user has any pin published and active within 30min
       // $activePin = PinHelper::userHasActivePin($current_time);

        //no pins, return reponse
        if (empty($activePin["within_1_hour"])) {
            return
                [[
                    'showAlert' => true,
                    'blink' => 'publish-pin',
                    'message' => Yii::t("app", "User did not publish a pin"),
                    'pins' => $jsonPins
                ]];
        }

        $locationsTable = Locations::tableName();
        $query = ApiLocation::searchLocationsQuery();
        $pins = $query->all();

        //return json data
        foreach ($pins as $key => $pin) {
            $include_pin = true;
            if (in_array($pin->IDuser, $blocked_by))
                $include_pin = false;

            if ($include_pin == true)
                $jsonPins[] = ApiLocation::generateContentForInfoWindow($pin);
        }

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

        return
            [[
                'showAlert' => $showAlert,
                'blink' => 'none',
                'message' => $message,
                'pins' => $jsonPins,
            ]];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPin(Request $request)
    {
        $tags = $this->pin->checkTags($request->tags);
        if(empty($tags))
        {
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
        
        $user = $this->user->getAuthenticatedUser();
        $pin = $this->pin;

        $pin->user_id = $user->id;
        $pin->post_time = $request->current_time;
        $pin->lng = $request->lng;
        $pin->lat = $request->lat;
        $pin->fill($request->all());
        if($pin->save())
        {
            //save tags
            foreach($tags as $tag_id => $tag_name)
            {
                $tmp = new $this->pinTag;
                $tmp->pin_id = $pin->id;
                $tmp->tag_id = $tag_id;
                $tmp->save();
            }
            $status = true;
        }

        $pin_info = $pin->generateContentForInfoWindow($pin, $user);
        return response()
            ->json([
                'status' => $status,
                'pin' => $pin_info,
            ]);
    }
}
