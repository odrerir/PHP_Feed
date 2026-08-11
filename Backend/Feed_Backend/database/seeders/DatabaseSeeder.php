<?php
namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory(10)->create();
        $users->each(function (User $user) use ($users) {
            Post::factory(rand(1, 5))->create(['user_id' => $user->id])
                ->each(function (Post $post) use ($users) {
                    $users->random(rand(2, 6))->each(
                        fn (User $liker) => $post->likes()->firstOrCreate(['user_id' => $liker->id])
                    );
                    $users->random(rand(0, 4))->each(
                        fn (User $commenter) => $post->comments()->create([
                            'user_id' => $commenter->id,
                            'content' => fake()->sentence(),
                        ])
                    );
                });

            $users->except($user->id)->random(rand(0, 6))->each(
                fn (User $target) => $user->following()->syncWithoutDetaching($target->id)
            );
        });
    }
}