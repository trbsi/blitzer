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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
            $Message = $this->message->findMessageByIdOrCreate($message_id, $authUser, $user_id, $pin_id);

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
    
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(Request $request)
    {
        $pin_id = (int)$request->pin_id;
        $authUser = $this->authUser;

        //find message by pin id and logged user
        $Message = $this->message->findByPinid($pin_id, $authUser);

        if ($Message->user_one == $authUser->id) {
            $Message->user_one_read = 1;
        } else {
            $Message->user_two_read = 1;
        }
        $Message->update();

        $messages = $this->messageReply->getMessages($request, $Message->id);

        $return = [];
        $return["success"] = true;
        $return["messages"] = [];

        foreach ($messages as $message) {
            $usr = $message->relationUser;
            $return["messages"][] =
                [
                    "reply" => $message->reply,
                    "send_date" => Helper::formatDate($message->send_date),
                    "user_name" => $usr->first_name . " " . $usr->last_name,
                    "user_id" => $message->user_id,
                ];
        }

        return response()
            ->json($return);
    }

}