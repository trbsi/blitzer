<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* $this->call(UsersTable::class);
         $this->call(TagsTable::class);
         $this->call(PinsTable::class);*/
         $this->call(MessagesTable::class);
    }
}
