<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $sourceDir = database_path('seeders/assets/posts');
        $files = glob($sourceDir.'/*.{jpg,jpeg,png}', GLOB_BRACE);

        $source = $files[array_rand($files)];
        $filename = 'posts/'.Str::uuid().'.'.pathinfo($source, PATHINFO_EXTENSION);

        Storage::disk('public')->put($filename, file_get_contents($source));

        return [
            'user_id'    => User::factory(),
            'caption'    => fake()->optional()->sentence(),
            'media_path' => $filename,
        ];
    }
}