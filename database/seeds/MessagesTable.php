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
                    'pin_one' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->fakeMails["mail1"])->id)->id,
                    'pin_two' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->fakeMails["mail2"])->id)->id,
                    'user_one' => $user->getUserByEmail($seederHelper->fakeMails["mail1"])->id,
                    'user_two' => $user->getUserByEmail($seederHelper->fakeMails["mail2"])->id,
                ],
                [
                    'pin_one' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->fakeMails["mail1"])->id)->id,
                    'pin_two' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->fakeMails["mail3"])->id)->id,
                    'user_one' => $user->getUserByEmail($seederHelper->fakeMails["mail1"])->id,
                    'user_two' => $user->getUserByEmail($seederHelper->fakeMails["mail3"])->id,
                ],
                [
                    'pin_one' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->fakeMails["mail2"])->id)->id,
                    'pin_two' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->fakeMails["mail3"])->id)->id,
                    'user_one' => $user->getUserByEmail($seederHelper->fakeMails["mail2"])->id,
                    'user_two' => $user->getUserByEmail($seederHelper->fakeMails["mail3"])->id,
                ],
            ];

        foreach ($data as $key => $value) {
            $msg = $message->create($value);

            for ($i=0; $i < 10; $i++) { 
                $replies = 
                [
                    'reply' => $faker->realText($maxNbChars = rand(20,200), $indexSize = 2),
                    'user_id' => (rand(0,1)%2 == 0) ? $value["user_one"] : $value["user_two"],
                    'send_date' => date("Y-m-d H:i:s")
                ];
                $msg->messagesReplies()->create($replies);
            }
        }

        //create a conversation between real and fake users
        $data =
        [
            //conversation between Osijek pins
            [
                'pin_one' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->fakeMails["mail1"])->id)->id,
                'pin_two' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->realMails["timmydario"])->id)->id,
                'user_one' => $user->getUserByEmail($seederHelper->fakeMails["mail1"])->id,
                'user_two' => $user->getUserByEmail($seederHelper->realMails["timmydario"])->id, 
            ],
            //conversation between Zagreb pins
            [
                'pin_one' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->fakeMails["mail2"])->id)->id,
                'pin_two' => $pin->getUserLatestPin($user->getUserByEmail($seederHelper->realMails["msikic"])->id)->id,
                'user_one' => $user->getUserByEmail($seederHelper->fakeMails["mail2"])->id,
                'user_two' => $user->getUserByEmail($seederHelper->realMails["msikic"])->id, 
            ]
        ];

        
        foreach ($data as $value) {
            $msg = $message->create($value);

            for ($i=0; $i < 20; $i++) { 
                $replies = 
                [
                    'reply' => $faker->realText($maxNbChars = rand(20,200), $indexSize = 2),
                    'user_id' => (rand(0,1)%2 == 0) ? $value["user_one"] : $value["user_two"],
                    'send_date' => date("Y-m-d H:i:s")
                ];
                $msg->messagesReplies()->create($replies);
            }
        }

    }
}
