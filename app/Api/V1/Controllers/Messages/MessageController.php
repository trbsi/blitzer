<?php

namespace App\Api\V1\Controllers\Messages;

use App\Http\Controllers\Controller;
use App\Models\Helper\Helper;
use App\Models\Helper\PinHelper;
use App\Models\Message;
use App\Models\MessagesReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class MessageController extends Controller
{
    public function __construct(User $user, Message $message, MessagesReply $messageReply)
    {
        $this->user         = $user;
        $this->message      = $message;
        $this->messageReply = $messageReply;
        $this->authUser     = $this->user->getAuthenticatedUser();
        $this->middleware('currentTimeFixer');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        if (isset($request->reply) && isset($request->user_id) && isset($request->pin_id)) {
            $message_id = (int) $request->message_id; //may be null or 0
            $reply      = $request->reply;
            $authUser   = $this->authUser;
            $user_id    = $request->user_id; //id of a user whose pin it is
            $pin_id     = $request->pin_id;

            //if reply is still empty, show warning
            if (empty($reply)) {
                return response()
                    ->json([
                        'status'    => false,
                        'message'   =>
                        [
                            'body'  => trans('core.message.no_reply_body'),
                            'title' => trans('core.message.no_reply_title'),
                        ],
                        'showAlert' => true,
                    ]);
            }
            $Message = $this->message->findMessageByIdOrCreate($message_id, $authUser, $user_id, $pin_id);

            //set as unread
            if ($Message->user_one == $authUser->id) {
                $sendNotificationToThisUser = $Message->user_two; //to who to send notification about new message
                $Message->user_two_read     = 0;
                $Message->user_one_read     = 1;
            } else {
                $sendNotificationToThisUser = $Message->user_one; //to who to send notification about new message
                $Message->user_two_read     = 1;
                $Message->user_one_read     = 0;
            }
            $Message->updated_at = $request->current_time;
            $Message->update();

            $MessagesReply             = $this->messageReply;
            $MessagesReply->message_id = $Message->id;
            $MessagesReply->reply      = $reply;
            $MessagesReply->user_id    = $authUser->id; //user who sent message. (authenitacted user)
            $MessagesReply->send_date  = $request->current_time;

            if ($MessagesReply->save()) {
                //save to redis so you know you have to update updated_at in pins table
                Redis::command("sadd", [PinHelper::REDIS_PINS_TO_UPDATE_TIME, $pin_id]);

                $MessagesReplyArray =
                    [
                    "reply"      => $MessagesReply->reply,
                    "message_id" => (int) $MessagesReply->message_id,
                    "reply_id"   => (int) $MessagesReply->id,
                    "send_date"  => Helper::formatDate($MessagesReply->send_date),
                    "user_id"    => (int) $MessagesReply->user_id,
                    "user_name"  => $authUser->first_name . " " . $authUser->last_name,
                    "badge"      => 1,
                    "pin_id"     => (int) $pin_id,
                ];

                //trigger PubNub event
                $this->message->triggerMessageEvent($MessagesReplyArray, $authUser->id);

                //trigger message notification. Send notification to another suer
                $this->message->triggerMessageNotification
                    (
                    $Message,
                    $sendNotificationToThisUser,
                    $MessagesReply
                );

                //phone is expecting some kind of json response
                return response()
                    ->json([
                        "message"   => false,
                        "success"   => true,
                        "showAlert" => false,
                        "reply"     => $MessagesReplyArray["reply"],
                        "send_date" => $MessagesReplyArray["send_date"],
                        "user_id"   => $MessagesReplyArray["user_id"],
                        "user_name" => $MessagesReplyArray["user_name"],
                        "reply_id"  => $MessagesReplyArray["reply_id"],
                    ]);
            }
        } else {
            return response()
                ->json([
                    'status'    => false,
                    'message'   =>
                    [
                        'body'  => trans('core.general.smth_went_wront_body'),
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
        $pin_id             = (int) $request->pin_id;
        $authUser           = $this->authUser;
        $return             = [];
        $return["success"]  = true;
        $return["messages"] = [];

        //find message by pin id and logged user
        $Message = $this->message->findByMessageId($pin_id);

        if (!empty($Message)) {
            if ($Message->user_one == $authUser->id) {
                $Message->user_one_read = 1;
            } else {
                $Message->user_two_read = 1;
            }
            $Message->update();

            $messages = $this->messageReply->getMessages($request, $Message->id);

            foreach ($messages as $message) {
                $usr                  = $message->relationUser;
                $return["messages"][] =
                    [
                    "reply"     => $message->reply,
                    "send_date" => Helper::formatDate($message->send_date),
                    "user_name" => $usr->first_name . " " . $usr->last_name,
                    "user_id"   => $message->user_id,
                ];
            }
        }

        return response()
            ->json($return);
    }

}
