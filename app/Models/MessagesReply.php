<?php namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class MessagesReply extends Model
{
    /**
     * Generated
     */

    protected $table = 'messages_reply';
    public $timestamps = false;
    protected $fillable = ['id', 'message_id', 'reply', 'user_id', 'send_date', 'message_type'];
    protected $casts =
        [
            'id' => 'int',
            'message_id' => 'int',
            'user_id' => 'int',
            'message_type' => 'int',
        ];

    //@FAKEPINSSTART
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        if(request('pin_id') < 0 && request('user_id'))
        {
            $this->table = 'messages_reply_fake';
        }
    }
    //@FAKEPINSEND

    /**
     * @param $load_all
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getMessages($load_all, $message_id)
    {
        $messagesReplyTable = MessagesReply::getTable();

        $query = MessagesReply::with(['relationUser']);

        //@TODO - enable later, choose between loading all messages and last 10
        /*if (!isset($load_all)) {
            //get newest last 10 messages
            //http://stackoverflow.com/questions/9424327/mysql-select-from-table-get-newest-last-10-rows-in-table
            $previousMessages = $query->from(DB::raw("(SELECT * FROM $messagesReplyTable WHERE message_id=$message_id ORDER BY send_date DESC LIMIT 10) AS temp_table"))
                ->orderBy('send_date', 'ASC')
                ->get();
        } else {
            //get all messages
            $previousMessages =
                $query->orderBy('send_date', 'ASC')
                    ->where("message_id", "=", $message_id)
                    ->get();
        }*/

        $previousMessages = $query->orderBy('send_date', 'ASC')
            ->where("message_id", "=", $message_id)
            ->get();

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
