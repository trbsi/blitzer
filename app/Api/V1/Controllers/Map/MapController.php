<?php

namespace App\Api\V1\Controllers\Map;

use App\Http\Controllers\Controller;
use App\Models\Helper\PinHelper;

class MapController extends Controller
{
    public function returnPins($lat, $lng, $current_time)
    {
        $current_time = PinHelper::formatCurrentTime($current_time, true);
        $jsonPins = [];

        //check if user has any pin published and active within 30min
        $activePin = PinHelper::userHasActivePin($current_time);

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
}
