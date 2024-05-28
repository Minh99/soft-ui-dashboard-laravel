<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            [
                'name' => 'Overcoming the challenge of learning',
                'description' => '',
                'slug' => 'overcoming-the-challenge-of-learning',
                'image' => '',
                'keywords' => 'learning'
            ],
            [
                'name' => 'Friendship and support',
                'description' => '',
                'slug' => 'friendship-and-support',
                'image' => '',
                'keywords' => 'friendship'
            ],
            [
                'name' => 'Discovering the mystery',
                'description' => '',
                'slug' => 'discovering-the-mystery',
                'image' => '',
                'keywords' => 'mystery'
            ],
            [
                'name' => 'Preparing for the big day',
                'description' => '',
                'slug' => 'preparing-for-the-big-day',
                'image' => '',
                'keywords' => 'big day'
            ],
            [
                'name' => 'Journey to conquer dreams',
                'description' => '',
                'slug' => 'journey-to-conquer-dreams',
                'image' => '',
                'keywords' => 'dreams'
            ],
            [
                'name' => 'Lesson about courage',
                'description' => '',
                'slug' => 'lesson-about-courage',
                'image' => '',
                'keywords' => 'courage'
            ],
            [
                'name' => 'New experience',
                'description' => '',
                'slug' => 'new-experience',
                'image' => '',
                'keywords' => 'experience'
            ],
            [
                'name' => 'The value of love',
                'description' => '',
                'slug' => 'the-value-of-love',
                'image' => '',
                'keywords' => 'love'
            ],
            [
                'name' => 'Discovering passion',
                'description' => '',
                'slug' => 'discovering-passion',
                'image' => '',
                'keywords' => 'passion'
            ],
            [
                'name' => 'Journey of self-discovery',
                'description' => '',
                'slug' => 'journey-of-self-discovery',
                'image' => '',
                'keywords' => 'self-discovery'
            ],
            [
                'name' => 'A stressful week with many exercises and group projects',
                'description' => '',
                'slug' => 'a-stressful-week-with-many-exercises-and-group-projects',
                'image' => '',
                'keywords' => 'stressful'
            ],
        ];

        DB::table('topics')->insert($topics);
    }
}
