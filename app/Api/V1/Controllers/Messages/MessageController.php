<?php

namespace App\Api\V1\Controllers\Messages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\MessagesReply;
use App\Models\Helper\Helper;

class MessageController extends Controller
{
    public function __construct(User $user, Message $message, MessagesReply $messageReply)
    {
        $this->user = $user;
        $this->message = $message;
        $this->messageReply = $messageReply;
        $this->authUser = $this->user->getAuthenticatedUser();
        $this->middleware('currentTimeFixer');
    }

    public function send(Request $request)
    {
        if (isset($request->reply) && isset($request->user_id) && isset($request->pin_id)) {
            $message_id = (int)$request->message_id; //may be null or 0
            $reply = $request->reply;
            $authUser = $this->authUser;
            $user_id = $request->user_id; //id of a user whose pin it is
            $pin_id = $request->pin_id;


            //if reply is still empty, show warning
            if (empty($reply)) {
                return response()
                    ->json([
                        'status' => false,
                        'message' =>
                            [
                                'body' => trans('core.message.no_reply_body'),
                                'title' => trans('core.message.no_reply_title'),
                            ],
                        'showAlert' => true,
                    ]);
            }
            $Message = $this->message->findMessageById($message_id, $authUser, $user_id, $pin_id);

            //set as unread
            if ($Message->user_one == $authUser->id) {
                $sendNotificationToThisUser = $Message->user_two; //to who to send notification about new message
                $Message->user_two_read = 0;
                $Message->user_one_read = 1;
            } else {
                $sendNotificationToThisUser = $Message->user_one; //to who to send notification about new message
                $Message->user_two_read = 1;
                $Message->user_one_read = 0;
            }
            $Message->update();

            $MessagesReply = $this->messageReply;
            $MessagesReply->message_id = $Message->id;
            $MessagesReply->reply = $reply;
            $MessagesReply->user_id = $authUser->id; //user who sent message. (authenitacted user)
            $MessagesReply->send_date = $request->current_time;


            if ($MessagesReply->save()) {
                $MessagesReplyArray =
                    [
                        "reply" => $MessagesReply->reply,
                        "message_id" => (int)$MessagesReply->message_id,
                        "reply_id" => (int)$MessagesReply->id,
                        "send_date" => Helper::formatDate($MessagesReply->send_date),
                        "user_id" => (int)$MessagesReply->user_id,
                        "user_name" => $authUser->first_name . " " . $authUser->last_name
                    ];

                //trigger PubNub event
                //$success = ApiEvents::triggerMessageEvent($MessagesReplyArray, $IDuser);
                //trigger message notification. Send notification to another suer
                // $success = ApiNotifications::triggerMessageNotification($Messages->relationIDlocation, $sendNotificationToThisUser, $MessagesReply, $current_time);

                //phone is expecting some kind of json response
                return response()
                    ->json([
                        "message" => false,
                        "success" => true,
                        "showAlert" => false,
                        "reply" => $MessagesReplyArray["reply"],
                        "send_date" => $MessagesReplyArray["send_date"],
                        "user_id" => $MessagesReplyArray["user_id"],
                        "user_name" => $MessagesReplyArray["user_name"],
                        "reply_id" => $MessagesReplyArray["reply_id"],
                    ]);
            }
        } else {
            return response()
                ->json([
                    'status' => false,
                    'message' =>
                        [
                            'body' => trans('core.general.smth_went_wront_body'),
                            'title' => trans('core.general.smth_went_wront_title'),
                        ],
                    'showAlert' => true,
                ]);
        }
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