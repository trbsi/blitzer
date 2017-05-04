<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pin;

class Message extends Model
{

    /**
     * Generated
     */

    protected $table = 'messages';
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
                'pin_id' => $pin_id,
                'user_one' => $user_id,
                'user_two' => $authUser->id,
            ]
        );
    }

    /**
     * @param $pin_id
     * @param $authUser
     * @return mixed
     */
    public function findByPinid($pin_id, $authUser)
    {
        $msgTable = Message::getTable();
        $pinTable = (new Pin)->getTable();
        return Pin::where("$pinTable.id", '=', $pin_id)
            ->whereRaw("(user_one = $authUser->id OR user_two = $authUser->id) AND (user_one = $pinTable.user_id OR user_two = $pinTable.user_id)")
            ->join($msgTable, "$msgTable.pin_id", "=", "$pinTable.id", 'inner')
            ->select("$msgTable.*")
            ->first()
            ;
    }

    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'pin_id', 'id');
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
