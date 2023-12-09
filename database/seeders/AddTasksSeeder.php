<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AddTasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Tag::truncate();
        Task::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        $faker = new Generator();

        User::factory()
            ->has(
                Task::factory()
                    ->has(
                        Tag::factory()->count($faker->numberBetween(1,5)),
                        'tags'
                    )
                    ->count($faker->numberBetween(30,70)),
                'tasks'
                )
            ->count(10)
            ->createQuietly();
    }
}
