<?php

use Illuminate\Database\Seeder;
use App\Models\Pin;
use App\Models\Message;
use App\Models\MessagesReply;
use Faker\Factory;

class MessagesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Message $message, MessagesReply $messageReply)
    {
        $date = date("Y-m-d H:i:s");
        $data =
            [
                [
                    'pin_one' => 1,
                    'pin_two' => 2,
                    'user_one' => 1,
                    'user_two' => 2,
                    'replies' =>
                        [
                            [
                                'reply' => 'Hi',
                                'user_id' => 1,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => 'Hello',
                                'user_id' => 2,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => 'How are you',
                                'user_id' => 1,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => 'Good :)',
                                'user_id' => 2,
                                'send_date' => $date,
                            ],
                        ]
                ],
                [
                    'pin_one' => 1,
                    'pin_two' => 3,
                    'user_one' => 1,
                    'user_two' => 3,
                    'replies' =>
                        [
                            [
                                'reply' => 'New Music Video! "Tacos On My Mind" ',
                                'user_id' => 1,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => 'Found out about this last night. Still in shock.',
                                'user_id' => 3,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => 'LAST SONG SYNDROME tacos on my mind, tacos on my mind! I got tacos on my min',
                                'user_id' => 1,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => 'My Izzy profile just got nominated for an L.A. Press Club Award. @IzzyStradlin999: Did you hear?',
                                'user_id' => 1,
                                'send_date' => $date,
                            ],
                        ]
                ],
                [
                    'pin_one' => 2,
                    'pin_two' => 3,
                    'user_one' => 2,
                    'user_two' => 3,
                    'replies' =>
                        [
                            [
                                'reply' => 'I saw you standing here, and I just had to come tell you you have the most striking sense of style Ive seen all day. Im Joe.',
                                'user_id' => 2,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => 'Hi… Im Tina.',
                                'user_id' => 3,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => "Okay. How's your night going?",
                                'user_id' => 2,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => "It's going all right. So tell me, New York native or you come from somewhere far away?",
                                'user_id' => 3,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => "Nope, I'm New York, born and raised.",
                                'user_id' => 2,
                                'send_date' => $date,
                            ],
                            [
                                'reply' => "Ah, all right. So you know all the secret places the tourists and I can only guess about",
                                'user_id' => 3,
                                'send_date' => $date,
                            ],
                        ]
                ],
            ];

        //default conversation
        foreach ($data as $key => $value) {
            $msg = $message->create(array_except($value, ['replies']));

            $msg->messagesReplies()->createMany($value["replies"]);
        }

        //create a conversation between real and fake users
        $data =
        [
            //conversation between Osijek pins
            [
                'pin_one' => 1,
                'pin_two' => 4,
                'user_one' => 1,
                'user_two' => 4,  
            ]
        ];

        $faker = Factory::create();
        
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
