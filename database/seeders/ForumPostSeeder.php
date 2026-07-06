<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ForumPostSeeder extends Seeder
{
    /**
     * Seed global forum posts (community_id = null) shown on the Community
     * forum feed and the home-page community showcase. Idempotent via
     * firstOrCreate, same convention as the catalog seeders.
     */
    public function run(): void
    {
        // Demo authors — created only when missing so the seeder also works
        // on a fresh database (migrate:fresh --seed).
        $authors = collect([
            ['username' => 'rakadewa', 'email' => 'raka.dewa@example.com'],
            ['username' => 'sintapuspita', 'email' => 'sinta.puspita@example.com'],
            ['username' => 'bimasenja', 'email' => 'bima.senja@example.com'],
        ])->map(fn (array $author): User => User::firstOrCreate(
            ['email' => $author['email']],
            [
                'username' => $author['username'],
                'password_hash' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        ))->values();

        // Images are bundled public assets picked to match each post's topic,
        // so the forum feed itself shows the photo (not just the home cards).
        $posts = [
            ['tag' => 'Hiking Stories', 'image' => 'images/dummymountain/rinjani.png', 'body' => 'Sunrise dari puncak Rinjani kemarin benar-benar membayar lunas 7 jam trekking. Danau Segara Anak terlihat jelas dari Plawangan Sembalun!'],
            ['tag' => 'Tips and Trick', 'image' => 'images/dummymountain/batur.png', 'body' => 'Tips buat pendaki pemula: mulai pendakian jam 3 pagi untuk kejar sunrise, bawa headlamp cadangan, dan istirahat tiap 30 menit.'],
            ['tag' => 'Gear & Equipment', 'image' => 'images/profile/gear.jpeg', 'body' => 'Review singkat sleeping bag bulu angsa vs sintetis untuk suhu di bawah 5 derajat: bulu angsa lebih ringan, tapi sintetis lebih tahan lembab di musim hujan.'],
            ['tag' => 'Safety & Survival', 'image' => 'images/dummymountain/semeru.png', 'body' => 'Cuaca di Semeru sedang tidak menentu minggu ini. Selalu cek info dari basecamp Ranu Pani dan jangan memaksakan summit kalau kabut turun.'],
            ['tag' => 'Hiking Stories', 'image' => 'images/dummymountain/prau.png', 'body' => 'Pendakian pertama ke Prau via Patak Banteng: 3 jam santai, golden sunrise dengan lautan awan, dan hamparan bukit Teletubbies yang ikonik.'],
            ['tag' => 'Tips and Trick', 'image' => 'images/hero/hero-2.png', 'body' => 'Cara packing carrier 60L yang benar: barang berat dekat punggung, sleeping bag paling bawah, jas hujan dan snack di top lid biar gampang diambil.'],
            ['tag' => 'Gear & Equipment', 'image' => 'images/hero/hero-3.png', 'body' => 'Sepatu trail running vs sepatu gunung mid-cut untuk trek Merbabu: kalau kering enak trail running, tapi pergelangan kaki lebih aman pakai mid-cut.'],
            ['tag' => 'Safety & Survival', 'image' => 'images/hero/hero-4.png', 'body' => 'Pertolongan pertama untuk hipotermia ringan: ganti baju basah, beri minuman hangat manis, dan masukkan penderita ke sleeping bag secepatnya.'],
        ];

        foreach ($posts as $index => $data) {
            $author = $authors[$index % $authors->count()];

            $post = Post::firstOrCreate(
                ['body' => $data['body'], 'community_id' => null],
                ['author_id' => $author->id, 'title' => '']
            );

            // PostTag lowercases keywords on save; query with the same casing.
            $post->tags()->firstOrCreate(['keyword' => strtolower($data['tag'])]);

            $post->images()->firstOrCreate(['image_url' => $data['image']], ['position' => 1]);

            // Likes from the other demo authors so counts look alive.
            $post->likes()->syncWithoutDetaching(
                $authors->where('id', '!=', $post->author_id)->pluck('id')->all()
            );
        }
    }
}
