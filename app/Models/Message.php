<?php

namespace App\Models;

use App\Helpers\PubNubHelper;
use App\Helpers\SendPushNotification;
use App\Models\Pin;
use Illuminate\Database\Eloquent\Model;
use Pubnub\Pubnub;

class Message extends Model
{

    /**
     * Generated
     */

    protected $table = 'messages';
    protected $fillable = ['id', 'pin_one', 'pin_two', 'user_one', 'user_two', 'user_one_read', 'user_two_read', 'created_at', 'updated_at'];

    public function getConversationByPin($pin_one, $pin_two)
    {
        return Message::whereRaw("(pin_one = ? OR pin_two = ?) AND (pin_one = ? OR pin_two = ?)",
            [$pin_one, $pin_one, $pin_two, $pin_two])->first();
    }

    /**
     * find message by pin id
     * @param  [type] $user_one [description]
     * @param  [type] $user_two [description]
     * @param  [type] $pin_one  [description]
     * @param  [type] $pin_two  [description]
     * @return [type]           [description]
     */
    public function findMessageByPinIdOrCreate($user_one, $user_two, $pin_one, $pin_two, $create = true)
    {
        $result = $this->getConversationByPin($pin_one, $pin_two);

        //if user wants to see a conversation but it doesn't exist, don't create it. Create only if he wasnts to send a message
        if (empty($result) && $create == true) {

            $result = Message::create([
                'pin_one' => $pin_one,
                'pin_two' => $pin_two,
                'user_one' => $user_one,
                'user_two' => $user_two,
            ]);
        }

        return $result;
    }

    /**
     * find specific conversation based on pin id and user id
     * @param $authUser
     * @return mixed
     */
    public function findByMessageId($message_id)
    {
        return Message::where("id", '=', $message_id)
            ->first();
    }

    /**
     * this is used for chatting
     * @param $MessagesReplyArray -  array of values from MessageReply model
     * @param null $user_id - ID of a user
     * @return bool
     * @throws \Pubnub\PubnubException
     */
    public function triggerMessageEvent($data, $user_id)
    {
        $pubnub = PubNubHelper::initPubNub($user_id);
        $pubnub_channel = PubNubHelper::PUBNUB_CHANNEL_MSG . $data["message_id"];
        //publish message
        $pubnub->publish($pubnub_channel, $data);
    }

    /**
     * trigger notification for message
     * @param $Pin - Loaded Pin model
     * @param $user_id - ID of a user who needs to receive message
     * @param $MessagesReply - MessagesReply model (saved or loaded)
     * @param $current_time - taken from $_GET["current_time"]
     * @return bool
     */
    public function triggerMessageNotification($MessagesReply, $ids)
    {
        //check for all unread messages
        $body = (strlen($MessagesReply->reply) > 140) ? substr($MessagesReply->reply, 0, 140) . "..." : $MessagesReply->reply;

        $relationUser = $MessagesReply->relationUser;
        // Message payload
        $data =
            [
                'title' => $relationUser->first_name . " " . $relationUser->last_name, //user who sent message (ME)
                'body' => $body,
                'sound' => "message.wav",
                'event' => 'message', //so you can redirect users to messages screen, directly to that message
                'pin_id' => (int)$ids["badgeForPin"],
                'badge' => rand(1, 9),
            ];

        //send notification to a user
        SendPushNotification::sendNotification($ids["sendNotificationToThisUser"], $data);
    }

    public function pinOne()
    {
        return $this->belongsTo(\App\Models\Pin::class, 'pin_one', 'id');
    }

    public function userOne()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_one', 'id');
    }

    public function userTwo()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_two', 'id');
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'messages_reply', 'message_id', 'user_id');
    }

    public function messagesReplies()
    {
        return $this->hasMany(\App\Models\MessagesReply::class, 'message_id', 'id');
    }

}
