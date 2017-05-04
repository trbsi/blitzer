<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class MessagesReply extends Model
{

    /**
     * Generated
     */

    protected $table = 'messages_reply';
    public $timestamps = false;
    protected $fillable = ['id', 'message_id', 'reply', 'user_id', 'send_date', 'message_type'];

    /**
     * @param $request
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getMessages($request, $message_id)
    {
        $messagesReplyTable = MessagesReply::getTable();

        $query = MessagesReply::with(['relationUser']);
        if (!isset($request->load_all)) {
            //get newest last 10 messages
            //http://stackoverflow.com/questions/9424327/mysql-select-from-table-get-newest-last-10-rows-in-table
            $previousMessages = $query->from(DB::raw("(SELECT * FROM $messagesReplyTable WHERE message_id=$message_id ORDER BY send_date DESC LIMIT 2) AS temp_table"))
                ->orderBy('send_date', 'ASC')
                ->get();
        } else {
            //get all messages
            $previousMessages = $query->orderBy('send_date', 'ASC')
                ->get();
        }

        return $previousMessages;
    }

    public function message()
    {
        return $this->belongsTo(\App\Models\Message::class, 'message_id', 'id');
    }

    public function relationUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }


}
