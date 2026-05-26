<?php

namespace Database\Seeders;

use App\Models\Mountain;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password_hash' => bcrypt('password'),
            ]);
        }

        $semeru = Mountain::where('name', 'Semeru')->first();
        $merapi = Mountain::where('name', 'Merapi')->first();

        // 1. Post for Semeru
        if ($semeru) {
            $post = Post::updateOrCreate(
                [
                    'author_id' => $user->id,
                    'title' => 'Conquering Semeru',
                    'mountain_id' => $semeru->id,
                ],
                [
                    'climbing_date' => '2026-05-14',
                    'duration_days' => 2,
                    'body' => 'It was a great experience reaching the summit of Mahameru.',
                ]
            );

            // Add images if not exists
            if ($post->images()->count() == 0) {
                $post->images()->createMany([
                    ['image_url' => 'https://images.unsplash.com/photo-1542220152-36c84c4e7235?q=80&w=300&fit=crop', 'position' => 1],
                    ['image_url' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=300&fit=crop', 'position' => 2],
                    ['image_url' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=300&fit=crop', 'position' => 3],
                    ['image_url' => 'https://images.unsplash.com/photo-1522079184545-d8cf01b1cb91?q=80&w=300&fit=crop', 'position' => 4],
                    ['image_url' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=300&fit=crop', 'position' => 5],
                    ['image_url' => 'https://images.unsplash.com/photo-1522079184545-d8cf01b1cb91?q=80&w=300&fit=crop', 'position' => 6],
                    ['image_url' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=300&fit=crop', 'position' => 7],
                    ['image_url' => 'https://images.unsplash.com/photo-1522079184545-d8cf01b1cb91?q=80&w=300&fit=crop', 'position' => 8],
                ]);
            }
        }

        // 2. Post for Merapi
        if ($merapi) {
            $post2 = Post::updateOrCreate(
                [
                    'author_id' => $user->id,
                    'title' => 'A day in Merapi',
                    'mountain_id' => $merapi->id,
                ],
                [
                    'climbing_date' => '2026-01-22',
                    'duration_days' => 1,
                    'body' => 'The New Selo Trail is challenging but rewarding.',
                ]
            );

            // Add images if not exists
            if ($post2->images()->count() == 0) {
                 $post2->images()->createMany([
                    ['image_url' => 'https://images.unsplash.com/photo-1542281286-9e0a16bb7366?q=80&w=300&fit=crop', 'position' => 1],
                    ['image_url' => 'https://images.unsplash.com/photo-1600298882283-40b4dcb8b211?q=80&w=300&fit=crop', 'position' => 2],
                    ['image_url' => 'https://images.unsplash.com/photo-1516900448138-898720b93707?q=80&w=300&fit=crop', 'position' => 3],
                    ['image_url' => 'https://images.unsplash.com/photo-1628126235206-5260b9ea6441?q=80&w=300&fit=crop', 'position' => 4],
                 ]);
            }
        }
    }
}
