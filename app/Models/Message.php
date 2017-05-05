<?php 

namespace App\Models;

use App\Models\Helper\PubNubHelper;
use App\Models\Pin;
use Illuminate\Database\Eloquent\Model;
use Pubnub\Pubnub;
use App\Models\Helper\SendPushNotification;

class Message extends Model
{

    /**
     * Generated
     */

    protected $table    = 'messages';
    protected $fillable = ['id', 'pin_id', 'user_one', 'user_two', 'user_one_read', 'user_two_read', 'created_at', 'updated_at'];

    /**
     * @param $id
     * @param $authUser
     * @param $user_id
     * @param $pin_id
     * @return mixed
     */
    public function findMessageByIdOrCreate($id, $authUser, $user_id, $pin_id)
    {
        return Message::firstOrCreate(
            ['id' => $id],
            [
                'pin_id'   => $pin_id,
                'user_one' => $user_id,
                'user_two' => $authUser->id,
            ]
        );
    }

    /**
     * find specific conversation based on pin id and user id
     * @param $pin_id
     * @param $authUser
     * @return mixed
     */
    public function findByPinId($pin_id, $authUser)
    {
        $msgTable = Message::getTable();
        $pinTable = (new Pin)->getTable();
        return Pin::select("$msgTable.*")
            ->where("$pinTable.id", '=', $pin_id)
            ->whereRaw("(user_one = $authUser->id OR user_two = $authUser->id)")
            ->join($msgTable, "$msgTable.pin_id", "=", "$pinTable.id", 'inner')
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
        $pubnub         = PubNubHelper::initPubNub($user_id);
        $pubnub_channel = PubNubHelper::PUBNUB_CHANNEL_MSG . $data["message_id"];
        //publish message
        $pubnubResult = $pubnub->publish($pubnub_channel, $data);

        if ($pubnubResult[0] == 1) {
            $return = true;
        } else {
            $return = false;
        }

        return $return;
    }

    /**
     * trigger notification for message
     * @param $Pin - Loaded Pin model
     * @param $user_id - ID of a user who needs to receive message
     * @param $MessagesReply - MessagesReply model (saved or loaded)
     * @param $current_time - taken from $_GET["current_time"]
     * @return bool
     */
    public function triggerMessageNotification($Pin, $user_id, $MessagesReply, $current_time)
    {
        $return = false;

        //check for all unread messages
        $body  = (strlen($MessagesReply->reply) > 140) ? substr($MessagesReply->reply, 0, 140) : $MessagesReply->reply;

        $relationUser = $MessagesReply->relationUser;
        // Message payload
        $data =
        [
            'title'      => $relationUser->first_name . " " . $relationUser->last_name, //user who sent message (ME)
            'body'       => $body,
            'sound'      => "message.wav",
            'event'      => 'message', //so you can redirect users to messages screen, directly to that message
            'message_id' => $MessagesReply->message_id, //so you can redirect users to a specific conversation
            'user_id'    => $MessagesReply->user_id, //id of a user who sent a message
        ];

        //send notification to a user
        $return = SendPushNotification::sendNotification($user_id, $data);

        return $return;

    }

    public function relationPin()
    {
        return $this->belongsTo(\App\Models\Pin::class, 'pin_id', 'id');
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
