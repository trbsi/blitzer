<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    /**
     * Generated
     */

    protected $table = 'messages';
    protected $fillable = ['id', 'location_id', 'user_one', 'user_two', 'user_one_read', 'user_two_read', 'last_updated', 'date_modified'];


    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_one', 'id');
    }

    public function user()
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
