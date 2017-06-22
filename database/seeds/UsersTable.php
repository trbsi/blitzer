<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Helpers\SeederHelper;

class UsersTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user, SeederHelper $seederHelper)
    {
        $data =
            [
                [
                    'email' => $seederHelper->fakeMails["mail1"],
                    'birthday' => '1991-12-29',
                    'first_name' => 'Ivan',
                    'last_name' => 'Bucko',
                    'gender' => 'male',
                    'facebook_id' => mt_rand(),
                    'profile_picture' => 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-1/c0.8.100.100/p100x100/19396652_10208610263432055_7245071370263027440_n.jpg?oh=b52c194c5614ab8ee8cbd8b1ad6503c0&oe=59C6B10B',
                ],
                [
                    'email' => $seederHelper->fakeMails["mail2"],
                    'birthday' => '1995-10-29',
                    'first_name' => 'Mila',
                    'last_name' => 'Kunich',
                    'gender' => 'female',
                    'facebook_id' => mt_rand(),
                    'profile_picture' => 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-9/18557332_1330208330347600_5604415580343858335_n.jpg?oh=29f4054459ac5c46a81a5f13485b8ce6&oe=59AAF29B',
                ],
                [
                    'email' => $seederHelper->fakeMails["mail3"],
                    'birthday' => '1985-01-29',
                    'first_name' => 'Emma',
                    'last_name' => 'Smithson',
                    'gender' => 'female',
                    'facebook_id' => mt_rand(),
                    'profile_picture' => 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-9/18582028_268048996999657_3170307703511889749_n.jpg?oh=1dd5be0a9669c1cee6a6d1b6eef90ed3&oe=59AAFCA7',
                ],
                [
                    'email' => $seederHelper->fakeMails["mail4"],
                    'birthday' => '1997-01-29',
                    'first_name' => 'Maria',
                    'last_name' => 'Weeds',
                    'gender' => 'female',
                    'facebook_id' => mt_rand(),
                    'profile_picture' => 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-1/p100x100/19247811_1326090544177900_832433198054849718_n.jpg?oh=66ca8606fe45863d389f0f9680da9ba5&oe=59E806C2',
                ],
                [
                    'email' => $seederHelper->realMails["timmydario"],
                    'birthday' => '1991-12-29',
                    'first_name' => 'Dario',
                    'last_name' => 'Isaac',
                    'gender' => 'male',
                    'facebook_id' => mt_rand(),
                    'profile_picture' => 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-9/18582028_268048996999657_3170307703511889749_n.jpg?oh=1dd5be0a9669c1cee6a6d1b6eef90ed3&oe=59AAFCA7',
                ],
                [
                    'email' => $seederHelper->realMails["msikic"],
                    'birthday' => '1991-05-15',
                    'first_name' => 'Marijan',
                    'last_name' => 'Šikić',
                    'gender' => 'male',
                    'facebook_id' => mt_rand(),
                    'profile_picture' => 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-9/18582028_268048996999657_3170307703511889749_n.jpg?oh=1dd5be0a9669c1cee6a6d1b6eef90ed3&oe=59AAFCA7',
                ],

            ];

        foreach ($data as $key => $value) {
            $user->create($value);
        }
    }
}
