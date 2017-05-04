<?php

namespace App\Api\V1\Controllers\Messages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        if (isset($_POST["IDmessage"]) && isset($_POST["reply"]) && !empty($_POST["IDmessage"])) {
            $current_time = Api::formatCurrentTime($current_time, $strtotime = true);
            $IDmessage = (int)$_POST["IDmessage"];
            $reply = $_POST["reply"];
            $IDuser = Yii::$app->user->getId();

            $messagesTable = Messages::tableName();
            $locationsTable = Locations::tableName();
            $Messages = $this->findViewModel($IDmessage, $current_time);
            if ($Messages == false) {
                //delete cached data so new data is recached
                Helpers::deleteCache(Api::CACHE_MESSAGES_INBOX, $IDuser);
                return [["message" => Yii::t("app", "This conversation doesnt exist"), "success" => false, "showAlert" => true]];
            }

            //if user wants to share his location
            /*$share_location=false;
            if(isset($_GET["share_location"]) && isset($_GET["lng"]) && isset($_GET["lat"]))
                $share_location=true;

            if($share_location==true)
            {

                //get username of a user who shared location
                if($Messages->user_one==$IDuser)
                {
                    $username=$Messages->relationUserOne->username;
                    $gender=$Messages->relationUserOne->relationUserInformation->gender;
                }
                else
                {
                    $username=$Messages->relationUserTwo->username;
                    $gender=$Messages->relationUserTwo->relationUserInformation->gender;
                }

                if($gender==UserInformation::GENDER_X)
                    $userShared = Yii::t("app", "User has shared his location");
                else
                    $userShared = Yii::t("app", "User has shared hers location");

                $lat=$_GET["lat"];
                $lng=$_GET["lng"];
                $reply=$username." ".$userShared;
                $location=$lat.",".$lng;
            }*/

            //if reply is still empty, show warning
            if (empty($reply))
                return [["message" => Yii::t("app", "Type in a message before hitting Send"), "success" => false, "showAlert" => true]];

            //set as unread
            if ($Messages->user_one == $IDuser) {
                $sendNotificationToThisUser = $Messages->user_two; //to who to send notification about new message
                $Messages->user_two_read = 0;
                $Messages->user_one_read = 1;
                $blocked_by_user = $Messages->user_two;

                //if user wants to share his location
                /*if($share_location==true)
                    $Messages->user_one_location=$location;*/
            } else {
                $sendNotificationToThisUser = $Messages->user_one; //to who to send notification about new message
                $Messages->user_one_read = 0;
                $Messages->user_two_read = 1;
                $blocked_by_user = $Messages->user_one;

                //if user wants to share his location
                /*if($share_location==true)
                    $Messages->user_two_location=$location;*/
            }

            //I want to send a message to another user, check if I'm blocked by another user
            $blocked_by = ApiUser::whoIsBlocked($IDuser);
            if (in_array($blocked_by_user, $blocked_by)) {
                return [["message" => Yii::t("app", "You cannot send a message to this user anymore"), "success" => false, "showAlert" => true]];
            }

            $Messages->last_updated = $current_time;
            $Messages->update();

            $MessagesReply = new MessagesReply;
            $MessagesReply->IDmessage = $IDmessage;
            $MessagesReply->reply = $reply;
            $MessagesReply->IDuser = $IDuser; //user who sent message. (ME)
            $MessagesReply->send_date = $current_time;
            //user shared location, mark that in database
            /*if($share_location==true)
                $MessagesReply->message_type=MessagesReply::MESSAGE_TYPE_SHARE_LOCATION;*/

            if ($MessagesReply->save()) {
                $MessagesReplyArray =
                    [
                        "reply" => $MessagesReply->reply,
                        "IDmessage" => (int)$MessagesReply->IDmessage,
                        "IDreply" => (int)$MessagesReply->ID,
                        "send_date" => Helpers::formatDate($MessagesReply->send_date),
                        "IDuser" => (int)$MessagesReply->IDuser,
                        "shareLocation" => false,
                        "username" => $MessagesReply->relationIDuser->username
                    ];

                //trigger PubNub event
                $success = ApiEvents::triggerMessageEvent($MessagesReplyArray, $IDuser);
                //trigger message notification. Send notification to another suer
                $success = ApiNotifications::triggerMessageNotification($Messages->relationIDlocation, $sendNotificationToThisUser, $MessagesReply, $current_time);

                /*if($share_location==true)
                {
                    //trigger event
                    $args=
                    [
                        "IDuser"=>$IDuser,
                        "IDlocation"=>$Messages->IDlocation,
                    ];
                    Event::fire("realLocationShared", $args);
                }*/
                //phone is expecting some kind of json response
                return
                    [[
                        "message" => false,
                        "success" => true,
                        "showAlert" => false,
                        "reply" => $MessagesReplyArray["reply"],
                        "send_date" => $MessagesReplyArray["send_date"],
                        "IDuser" => $MessagesReplyArray["IDuser"],
                        "shareLocation" => $MessagesReplyArray["shareLocation"],
                        "username" => $MessagesReplyArray["username"],
                        "IDreply" => $MessagesReplyArray["IDreply"],
                    ]];
            } else
                return [["message" => Yii::t("app", "Something went wrong. Try again."), "success" => false, "showAlert" => true]];
        } else
            return [["message" => Yii::t("app", "Something went wrong. Try again."), "success" => false, "showAlert" => true]];
    }


    public function view(Request $request)
    {

        $id = (int)$id;
        $IDuser = Yii::$app->user->getId();
        $current_time = Api::formatCurrentTime($current_time, $strtotime = true);
        //return that one active conversation
        $model = $this->findViewModel($id, $current_time);
        if ($model == false) {
            //delete cached data so new data is recached
            Helpers::deleteCache(Api::CACHE_MESSAGES_INBOX, $IDuser);

            return [["message" => Yii::t("app", "This conversation doesnt exist"), "success" => false]];
        }

        if ($model->user_one == $IDuser) {
            $model->user_one_read = 1;
        } else {
            $model->user_two_read = 1;
        }
        $model->date_modified = $current_time; //because of user_one_read and user_two_read cache
        $model->update();

        $messagesReplyTable = MessagesReply::getTableSchema();

        $previousMessagesQuery = MessagesReply::find()->joinWith(['relationIDuser']);
        if ($load_all == NULL) {
            //get the rest of messages, last 5 but in reverse order
            //http://stackoverflow.com/questions/9424327/mysql-select-from-table-get-newest-last-10-rows-in-table
            $previousMessages = $previousMessagesQuery->from("(SELECT * FROM $messagesReplyTable->name WHERE IDmessage=$id ORDER BY send_date DESC LIMIT 10) AS temp_table")
                ->limit(10)
                ->orderBy('send_date ASC')
                ->all();
        } else {
            //get the rest of messages, last 5 but in reverse order
            //http://stackoverflow.com/questions/9424327/mysql-select-from-table-get-newest-last-10-rows-in-table
            $previousMessages = $previousMessagesQuery->from("(SELECT * FROM $messagesReplyTable->name WHERE IDmessage=$id ORDER BY send_date DESC) AS temp_table")
                ->orderBy('send_date ASC')
                ->all();
        }

        $return = [];
        $return["success"] = true;
        $return["viewLocation"] = false;
        //if user one and user two shared their location (!=NULL) you can tell app to share location
        if ($model->user_one_location != NULL && $model->user_two_location != NULL) {
            $return["viewLocation"] = true;
            //if IDuser==user_one, get location of another user (user two)
            if ($IDuser == $model->user_one) {
                $location = $model->user_two_location;
            } else {
                $location = $model->user_one_location;
            }

            //in database it is lat,lng=25.0000,36.00000. So you have to explode and make json
            $tmp = explode(",", $location);
            $return["currentLocation"] = [
                "lat" => (float)$tmp[0],
                "lng" => (float)$tmp[1],
            ];
        }


        foreach ($previousMessages as $message) {
            //so we can change bg color of chat bubbles  for a user in app
            /*if($message->IDuser==$IDuser)
            {
                $user_one=true;
                $user_two=false;
            }
            else
            {
                $user_one=false;
                $user_two=true;
            }*/

            $shareLocation = ($message->message_type == MessagesReply::MESSAGE_TYPE_SHARE_LOCATION) ? true : false;
            $return["messages"][] =
                [
                    "reply" => $message->reply,
                    "send_date" => Helpers::formatDate($message->send_date),
                    "username" => $message->relationIDuser->username,
                    //"user_one"=>$user_one,
                    //"user_two"=>$user_two,
                    "IDuser" => $message->IDuser,
                    "shareLocation" => $shareLocation,
                ];
        }

        //return $this->render('@app/modules/api/views/layouts/blank');
        return [$return];
    }

}