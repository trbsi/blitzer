<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(User $user)
    {
        $data =
        [
        	[
        		'email' => 'user1@mail.com',
        		'password' => '123456',
        		'birthday' => '1991-12-29',
        		'first_name' => 'Dario',
        		'last_name' => 'Trbovic',
        		'gender' => 'male',
        		'facebook_id' => mt_rand(),
        		'profile_picture' => 'https://scontent.xx.fbcdn.net/v/t1.0-1/c14.0.50.50/p50x50/18118555_2038480473049383_33756107051504425_n.jpg?oh=9bd9dd60bbf3a8c18af7974547486bc8&oe=59BFBDA8',
        	],
        	[
        		'email' => 'user2@mail.com',
        		'password' => '123456',
        		'birthday' => '1995-10-29',
        		'first_name' => 'Mila',
        		'last_name' => 'Kunich',
        		'gender' => 'female',
        		'facebook_id' => mt_rand(),
        		'profile_picture' => 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-9/18557332_1330208330347600_5604415580343858335_n.jpg?oh=29f4054459ac5c46a81a5f13485b8ce6&oe=59AAF29B',
        	],
        	[
        		'email' => 'user3@mail.com',
        		'password' => '123456',
        		'birthday' => '1985-01-29',
        		'first_name' => 'Emma',
        		'last_name' => 'Smithson',
        		'gender' => 'female',
        		'facebook_id' => mt_rand(),
        		'profile_picture' => 'https://scontent.fzag1-1.fna.fbcdn.net/v/t1.0-9/18582028_268048996999657_3170307703511889749_n.jpg?oh=1dd5be0a9669c1cee6a6d1b6eef90ed3&oe=59AAFCA7',
        	],
       
        ];

        foreach ($data as $key => $value) 
        {
        	$user->create($value);
        }
    }
}
