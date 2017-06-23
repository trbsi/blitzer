<?php

namespace App\Api\V1\Controllers\Messages;

use App\Api\V1\Controllers\BaseAuthController;
use App\Helpers\Helper;
use App\Models\Message;
use App\Models\MessagesReply;
use App\Models\PinTimeUpdate;
use App\Models\User;
//use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Helpers\CacheHelper;

class MessageController extends BaseAuthController
{
    public function __construct(Message $message, MessagesReply $messageReply, User $user)
    {
        parent::__construct($user);
        $this->message = $message;
        $this->messageReply = $messageReply;
        $this->middleware('currentTimeFixer');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        if (isset($request->reply) && isset($request->user_id) && isset($request->pin_id)) {
            $reply = $request->reply;
            $authUser = $this->authUser;
            $user_one = $request->user_id; //id of a user whose pin it is
            $pin_one = $request->pin_id;
            $user_two = $authUser->id;
            //$badgeForPin - if I send a message other user will get notification and badge has to be set on my pin.
            $pin_two = $badgeForPin = CacheHelper::getCache("user_pin_id", ["user_id" => $user_two]);

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

            $Message = $this->message->findMessageByPinIdOrCreate($user_one, $user_two, $pin_one, $pin_two);

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
            $Message->updated_at = $request->current_time;
            $Message->update();

            $MessagesReply = $this->messageReply;
            $MessagesReply->message_id = $Message->id;
            $MessagesReply->reply = $reply;
            $MessagesReply->user_id = $authUser->id; //user who sent message. (authenitacted user)
            $MessagesReply->send_date = $request->current_time;

            if ($MessagesReply->save()) {
                //save to redis so you know you have to update updated_at in pins table
                //@TODO Redis::command("sadd", [PinHelper::REDIS_PINS_TO_UPDATE_TIME, $pin_id]);
                try {
                    PinTimeUpdate::create(['user_id' => $authUser->id]);
                } catch (\Exception $e) {
                }

                $MessagesReplyArray =
                    [
                        "reply" => $MessagesReply->reply,
                        "message_id" => (int)$MessagesReply->message_id,
                        "reply_id" => (int)$MessagesReply->id,
                        "send_date" => Helper::formatDate($MessagesReply->send_date),
                        "user_id" => (int)$MessagesReply->user_id,
                        "user_name" => $authUser->first_name . " " . $authUser->last_name,
                    ];

                //trigger PubNub event
                $this->message->triggerMessageEvent($MessagesReplyArray, $authUser->id);

                //trigger message notification. Send notification to another suer
                $this->message->triggerMessageNotification
                (
                    $MessagesReply,
                    ["sendNotificationToThisUser" => $sendNotificationToThisUser, 'badgeForPin' => $badgeForPin]
                );

                //phone is expecting some kind of json response
                return response()
                    ->json([
                        "message" => 
                        [
                            'body' => null,
                            'title' => null,
                        ],
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
        $pin_one = (int)$request->pin_id;
        $authUser = $this->authUser;
        $pin_two = (int)CacheHelper::getCache("user_pin_id", ["user_id" => $authUser->id]);
        $return = [];
        $return["success"] = true;
        $return["message_id"] = NULL;
        $return["messages"] = [];

        //find message by pin id and logged user
        $Message = $this->message->getConversationByPin($pin_one, $pin_two);

        if (!empty($Message)) {
            //set message_id so mobile phone can use it for PubNub
            $return["message_id"] = $Message->id;

            if ($Message->user_one == $authUser->id) {
                $Message->user_one_read = 1;
            } else {
                $Message->user_two_read = 1;
            }
            $Message->update();

            $messages = $this->messageReply->getMessages($request->load_all, $Message->id);

            foreach ($messages as $message) {
                $user = $message->relationUser;
                $return["messages"][] =
                    [
                        "reply" => $message->reply,
                        "send_date" => Helper::formatDate($message->send_date),
                        "user_name" => $user->first_name . " " . $user->last_name,
                        "user_id" => $message->user_id,
                        "reply_id" => $message->id,
                    ];
            }

        }

        return response()->json($return);
    }

}
