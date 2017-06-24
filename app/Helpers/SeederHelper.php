<?php

namespace App\Helpers;

class SeederHelper
{
    public function __construct()
    {
        $this->fakeMails =
            [
                'mail1' => 'user1@mail.com',
                'mail2' => 'user2@mail.com',
                'mail3' => 'user3@mail.com',
                'mail4' => 'user4@mail.com',
            ];

        $this->realMails =
            [
                'timmydario' => 'timmy.dario@gmail.com',
                'msikic' => 'marijansikic@hotmail.com',
            ];
    }
}