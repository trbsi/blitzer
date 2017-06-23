<?php

use Illuminate\Database\Seeder;
use App\Models\Pin;
use App\Models\Message;
use App\Models\MessagesReply;
use Faker\Factory;
use App\Models\User;
use App\Helpers\SeederHelper;

class MessagesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Message $message, MessagesReply $messageReply, User $user, Pin $pin, SeederHelper $seederHelper)
    {
        $faker = Factory::create();
        $date = date("Y-m-d H:i:s");

        //conversation between fake users
        $data =
        [
            [
                'mail_1' => $seederHelper->fakeMails["mail1"],
                'mail_2' => $seederHelper->fakeMails["mail2"],
            ],
            [
                'mail_1' => $seederHelper->fakeMails["mail1"],
                'mail_2' => $seederHelper->fakeMails["mail3"],
            ],
            [
                'mail_1' => $seederHelper->fakeMails["mail2"],
                'mail_2' => $seederHelper->fakeMails["mail3"],
            ],

            //conversation between Osijek pins
            [
                'mail_1' => $seederHelper->fakeMails["mail1"],
                'mail_2' => $seederHelper->realMails["timmydario"],
            ],
            [
                'mail_1' => $seederHelper->fakeMails["mail4"],
                'mail_2' => $seederHelper->realMails["timmydario"],
            ],
            //conversation between Zagreb pins
            [
                'mail_1' => $seederHelper->fakeMails["mail3"],
                'mail_2' => $seederHelper->realMails["msikic"],
            ],
            [
                'mail_1' => $seederHelper->fakeMails["mail2"],
                'mail_2' => $seederHelper->realMails["msikic"],
            ],
        ];

        foreach ($data as $value) {

            $data = 
            [
                'pin_one' => $pin->getUserLatestPin($user->getUserByEmail($value["mail_1"])->id)->id,
                'pin_two' => $pin->getUserLatestPin($user->getUserByEmail($value["mail_2"])->id)->id,
                'user_one' => $user->getUserByEmail($value["mail_1"])->id,
                'user_two' => $user->getUserByEmail($value["mail_2"])->id, 
            ];

            $msg = $message->create($data);

            for ($i=0; $i < 10; $i++) { 
                $replies = 
                [
                    'reply' => $faker->realText($maxNbChars = rand(10,100), $indexSize = 2),
                    'user_id' => (rand(0,1)%2 == 0) ? $data["user_one"] : $data["user_two"],
                    'send_date' => date("Y-m-d H:i:s")
                ];
                $msg->messagesReplies()->create($replies);
            }
        }
    }
}
