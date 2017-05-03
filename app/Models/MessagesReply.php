<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessagesReply extends Model
{

    /**
     * Generated
     */

    protected $table = 'messages_reply';
    protected $fillable = ['id', 'message_id', 'reply', 'user_id', 'send_date', 'message_type'];


    public function message()
    {
        return $this->belongsTo(\App\Models\Message::class, 'message_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }


}
