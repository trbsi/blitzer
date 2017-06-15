<?php

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Tag $tags)
    {
        $data =
            [
                [
                    'tag' => '#walkingdownthestreet',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#buysomeweed',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#runningaround',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#party',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#drink',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#bored',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#letsgoswim',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#ridingabike',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#businessmeeting',
                    'popularity' => rand(1, 100),
                ],
                [
                    'tag' => '#groupmeetup',
                    'popularity' => rand(1, 100),
                ],
            ];

        foreach ($data as $key => $value) {
            $tags->create($value);
        }
    }
}
