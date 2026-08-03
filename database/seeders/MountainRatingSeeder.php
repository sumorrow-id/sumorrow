<?php

namespace Database\Seeders;

use App\Models\Mountain;
use App\Models\MountainRating;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MountainRatingSeeder extends Seeder
{
    private const MOUNTAIN = 'Kawi Butak';

    /**
     * Reviewer accounts live under a reserved TLD (RFC 6761), so the addresses
     * can never reach a real inbox.
     */
    private const REVIEWER_EMAIL_DOMAIN = 'sumorrow.test';

    /**
     * Score, reviewer name, and review text — one entry per rater.
     *
     * Hard-coded rather than generated on purpose: this seeder also runs on the
     * deployed VM, where `composer install --no-dev` leaves no Faker — model
     * factories and the faker helper are both unavailable there.
     *
     * @var list<array{int, string, string}>
     */
    private const REVIEWS = [
        [5, 'Arif Setiawan', 'Jalur via Sirah Kencong rapi dan penanda arahnya jelas. Cocok untuk pendakian dua hari satu malam.'],
        [4, 'Dewi Lestari', 'Vegetasinya rimbun jadi adem sepanjang perjalanan. Sayang sinyal hilang total di atas pos 2.'],
        [5, 'Bagus Prakoso', 'Sunrise dari puncak Butak juara banget. Sabananya luas dan enak buat ngecamp.'],
        [3, 'Rina Wulandari', 'Trek lumayan monoton sampai pos 3, baru menarik setelah masuk area sabana.'],
        [4, 'Fajar Nugroho', 'Sumber air ada di pos 2, jadi tidak perlu bawa air terlalu banyak dari basecamp.'],
        [5, 'Sinta Maharani', 'Salah satu gunung paling under-rated di Malang. Sepi, bersih, dan viewnya lengkap.'],
        [4, 'Yoga Pratama', 'Pendakian tipis-tipis buat pemula, tapi tetap siapkan fisik karena tanjakannya panjang.'],
        [2, 'Nadia Rahmawati', 'Waktu saya naik jalurnya becek parah dan banyak pohon tumbang, kurang terawat.'],
        [5, 'Reza Firmansyah', 'Camp di Cemoro Kandang enak banget, angin tidak terlalu kencang dan tanahnya datar.'],
        [4, 'Putri Anggraini', 'Petugas basecamp ramah dan briefingnya jelas. Registrasi cepat, tidak ribet.'],
        [3, 'Doni Kurniawan', 'Ramai kalau long weekend, area camp sampai penuh. Datang di hari biasa lebih nyaman.'],
        [5, 'Maya Safitri', 'Pemandangan Arjuno-Welirang dari punggungan Kawi kelihatan jelas saat cuaca cerah.'],
        [4, 'Hendra Gunawan', 'Estimasi 6 jam sampai puncak realistis kalau jalan santai dan sering istirahat.'],
        [5, 'Laras Ayu', 'Sabana Butak luas banget, serasa bukan di Jawa Timur. Wajib coba minimal sekali.'],
        [3, 'Bayu Aditya', 'Jalur turun via Panderman cukup licin, sebaiknya bawa trekking pole.'],
        [4, 'Intan Permata', 'Cocok buat latihan sebelum naik gunung yang lebih tinggi. Elevasinya lumayan menguras.'],
        [5, 'Rizal Hakim', 'Bintang malam hari di sabana bersih tanpa polusi cahaya. Pengalaman terbaik tahun ini.'],
        [4, 'Tari Oktaviani', 'Basecamp menyediakan tempat parkir aman dan warung yang buka sampai malam.'],
        [2, 'Galih Saputra', 'Sampah masih banyak di area camp. Semoga pengelola lebih tegas soal ini.'],
        [5, 'Winda Kusuma', 'Kombinasi hutan pinus, sabana, dan puncak berbatu bikin jalurnya tidak membosankan.'],
        [4, 'Anton Wijaya', 'Suhu malam sekitar 10 derajat, sleeping bag standar masih cukup hangat.'],
        [3, 'Sari Melati', 'Air di pos 2 kadang kering saat kemarau, lebih aman bawa cadangan dari bawah.'],
        [5, 'Dimas Ramadhan', 'Track menuju puncak Kawi lebih menantang dari Butak, tapi worth it.'],
        [4, 'Novi Handayani', 'Simaksi murah dan prosesnya online, tinggal tunjukkan bukti di basecamp.'],
        [5, 'Iqbal Maulana', 'Baru pertama naik dan langsung jatuh cinta. Pasti balik lagi tahun depan.'],
        [4, 'Citra Ningsih', 'Jalur cukup jelas, tapi tetap bawa peta offline karena ada beberapa percabangan.'],
        [3, 'Wahyu Santoso', 'Puncaknya sempit dan tertutup pepohonan, view terbaik justru ada di sabana.'],
        [5, 'Ratna Dewi', 'Sunset dari Butak dengan latar Gunung Kelud benar-benar tidak bisa dilupakan.'],
        [4, 'Adi Nurcahyo', 'Pendakian aman untuk kelompok kecil. Kami bertiga tidak menemui kendala berarti.'],
        [2, 'Fitri Amalia', 'Cuaca berubah drastis siang hari, kabut tebal sampai jarak pandang cuma 5 meter.'],
        [5, 'Bimo Sasongko', 'Alamnya masih asri, sempat ketemu lutung dan beberapa jenis burung endemik.'],
        [4, 'Ayu Widiastuti', 'Ada shelter darurat di dekat pos 3, membantu banget waktu hujan tiba-tiba.'],
        [5, 'Krisna Adiputra', 'Trek panjang tapi landai, ramah untuk pendaki yang bawa carrier berat.'],
        [3, 'Mega Puspita', 'Jarak antar pos cukup jauh, siapkan mental untuk trek monoton di tengah.'],
        [4, 'Rendra Halim', 'Pemandangan kebun teh di awal jalur jadi bonus yang menyenangkan.'],
        [5, 'Tika Ardhani', 'Salah satu sabana terbaik di Jawa. Pagi-pagi tertutup embun, cantik sekali.'],
        [4, 'Surya Pambudi', 'Pengelola rutin patroli, jadi terasa lebih aman dibanding beberapa gunung lain.'],
        [3, 'Vina Rosalina', 'Toilet di basecamp perlu perbaikan, selebihnya fasilitas sudah memadai.'],
        [5, 'Eko Susanto', 'Ideal untuk pendakian tektok kalau start subuh dan fisik dalam kondisi prima.'],
        [4, 'Lia Kartika', 'Jalur berbatu di dekat puncak butuh kehati-hatian, terutama saat basah.'],
        [5, 'Panji Wibowo', 'Bawa teman yang baru pertama naik dan dia langsung ketagihan. Recommended.'],
        [4, 'Hana Kirana', 'Angin di sabana kencang malam hari, pilih spot camp yang terlindung semak.'],
        [2, 'Teguh Prasetyo', 'Antrian registrasi lama saat musim libur, siapkan waktu ekstra di basecamp.'],
        [5, 'Dinda Larasati', 'Dari puncak bisa lihat Semeru, Kelud, dan Arjuno sekaligus. Luar biasa.'],
        [4, 'Rio Andrian', 'Total naik turun 12 jam santai termasuk istirahat panjang di sabana.'],
        [3, 'Sekar Ayuningtyas', 'Kurang cocok kalau cari tantangan teknis, jalurnya relatif aman dan landai.'],
        [5, 'Ilham Ramdani', 'Gunung favorit saya untuk healing. Sepi, dingin, dan pemandangannya lengkap.'],
        [4, 'Nita Zahra', 'Sinyal masih dapat di beberapa titik sabana, lumayan untuk kabar ke rumah.'],
        [5, 'Agus Salim', 'Perpaduan hutan lebat dan padang rumput terbuka bikin fotonya bagus semua.'],
        [4, 'Ratih Kumala', 'Pendakian menyenangkan, hanya perlu waspada dengan jalur licin setelah hujan.'],
    ];

    public function run(): void
    {
        $mountain = Mountain::where('name', self::MOUNTAIN)->first();

        if (! $mountain) {
            $this->command->warn(self::MOUNTAIN.' not found — run MountainSeeder first.');

            return;
        }

        $total = count(self::REVIEWS);

        foreach (self::REVIEWS as $index => [$score, $name, $review]) {
            $reviewer = $this->reviewer($name, $index);

            MountainRating::updateOrCreate(
                ['user_id' => $reviewer->id, 'mountain_id' => $mountain->id],
                ['score' => $score, 'review' => $review],
            )->forceFill(['created_at' => now()->subDays($total - $index)])->save();
        }

        // The DB trigger keeps avg_rating in sync, but refresh the row we hold
        // so a seeder run leaves the model consistent on every driver.
        $mountain->update(['avg_rating' => $mountain->ratings()->avg('score')]);

        $this->command->info("Seeded {$total} ratings for ".self::MOUNTAIN.'.');
    }

    /**
     * Find or create the account behind a seeded review.
     *
     * Reviewers are dedicated accounts keyed by email, so re-running never
     * duplicates them and never pins a review on a real member who did not
     * write it. They carry no password, so nobody can sign in as them.
     */
    private function reviewer(string $name, int $index): User
    {
        $username = Str::slug($name, '.');
        $email = $username.'@'.self::REVIEWER_EMAIL_DOMAIN;

        if (User::where('username', $username)->where('email', '!=', $email)->exists()) {
            $username .= $index;
        }

        return User::firstOrCreate(['email' => $email], [
            'username' => $username,
            'password_hash' => null,
            'email_verified_at' => now(),
        ]);
    }
}
