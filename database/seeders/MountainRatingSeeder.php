<?php

namespace Database\Seeders;

use App\Models\Mountain;
use App\Models\MountainRating;
use App\Models\User;
use Illuminate\Database\Seeder;

class MountainRatingSeeder extends Seeder
{
    private const MOUNTAIN = 'Kawi Butak';

    /**
     * Score + review pairs, one per rater. The list length decides how many
     * users end up rating the mountain.
     *
     * @var list<array{int, string}>
     */
    private const REVIEWS = [
        [5, 'Jalur via Sirah Kencong rapi dan penanda arahnya jelas. Cocok untuk pendakian dua hari satu malam.'],
        [4, 'Vegetasinya rimbun jadi adem sepanjang perjalanan. Sayang sinyal hilang total di atas pos 2.'],
        [5, 'Sunrise dari puncak Butak juara banget. Sabananya luas dan enak buat ngecamp.'],
        [3, 'Trek lumayan monoton sampai pos 3, baru menarik setelah masuk area sabana.'],
        [4, 'Sumber air ada di pos 2, jadi tidak perlu bawa air terlalu banyak dari basecamp.'],
        [5, 'Salah satu gunung paling under-rated di Malang. Sepi, bersih, dan viewnya lengkap.'],
        [4, 'Pendakian tipis-tipis buat pemula, tapi tetap siapkan fisik karena tanjakannya panjang.'],
        [2, 'Waktu saya naik jalurnya becek parah dan banyak pohon tumbang, kurang terawat.'],
        [5, 'Camp di Cemoro Kandang enak banget, angin tidak terlalu kencang dan tanahnya datar.'],
        [4, 'Petugas basecamp ramah dan briefingnya jelas. Registrasi cepat, tidak ribet.'],
        [3, 'Ramai kalau long weekend, area camp sampai penuh. Datang di hari biasa lebih nyaman.'],
        [5, 'Pemandangan Arjuno-Welirang dari punggungan Kawi kelihatan jelas saat cuaca cerah.'],
        [4, 'Estimasi 6 jam sampai puncak realistis kalau jalan santai dan sering istirahat.'],
        [5, 'Sabana Butak luas banget, serasa bukan di Jawa Timur. Wajib coba minimal sekali.'],
        [3, 'Jalur turun via Panderman cukup licin, sebaiknya bawa trekking pole.'],
        [4, 'Cocok buat latihan sebelum naik gunung yang lebih tinggi. Elevasinya lumayan menguras.'],
        [5, 'Bintang malam hari di sabana bersih tanpa polusi cahaya. Pengalaman terbaik tahun ini.'],
        [4, 'Basecamp menyediakan tempat parkir aman dan warung yang buka sampai malam.'],
        [2, 'Sampah masih banyak di area camp. Semoga pengelola lebih tegas soal ini.'],
        [5, 'Kombinasi hutan pinus, sabana, dan puncak berbatu bikin jalurnya tidak membosankan.'],
        [4, 'Suhu malam sekitar 10 derajat, sleeping bag standar masih cukup hangat.'],
        [3, 'Air di pos 2 kadang kering saat kemarau, lebih aman bawa cadangan dari bawah.'],
        [5, 'Track menuju puncak Kawi lebih menantang dari Butak, tapi worth it.'],
        [4, 'Simaksi murah dan prosesnya online, tinggal tunjukkan bukti di basecamp.'],
        [5, 'Baru pertama naik dan langsung jatuh cinta. Pasti balik lagi tahun depan.'],
        [4, 'Jalur cukup jelas, tapi tetap bawa peta offline karena ada beberapa percabangan.'],
        [3, 'Puncaknya sempit dan tertutup pepohonan, view terbaik justru ada di sabana.'],
        [5, 'Sunset dari Butak dengan latar Gunung Kelud benar-benar tidak bisa dilupakan.'],
        [4, 'Pendakian aman untuk kelompok kecil. Kami bertiga tidak menemui kendala berarti.'],
        [2, 'Cuaca berubah drastis siang hari, kabut tebal sampai jarak pandang cuma 5 meter.'],
        [5, 'Alamnya masih asri, sempat ketemu lutung dan beberapa jenis burung endemik.'],
        [4, 'Ada shelter darurat di dekat pos 3, membantu banget waktu hujan tiba-tiba.'],
        [5, 'Trek panjang tapi landai, ramah untuk pendaki yang bawa carrier berat.'],
        [3, 'Jarak antar pos cukup jauh, siapkan mental untuk trek monoton di tengah.'],
        [4, 'Pemandangan kebun teh di awal jalur jadi bonus yang menyenangkan.'],
        [5, 'Salah satu sabana terbaik di Jawa. Pagi-pagi tertutup embun, cantik sekali.'],
        [4, 'Pengelola rutin patroli, jadi terasa lebih aman dibanding beberapa gunung lain.'],
        [3, 'Toilet di basecamp perlu perbaikan, selebihnya fasilitas sudah memadai.'],
        [5, 'Ideal untuk pendakian tektok kalau start subuh dan fisik dalam kondisi prima.'],
        [4, 'Jalur berbatu di dekat puncak butuh kehati-hatian, terutama saat basah.'],
        [5, 'Bawa teman yang baru pertama naik dan dia langsung ketagihan. Recommended.'],
        [4, 'Angin di sabana kencang malam hari, pilih spot camp yang terlindung semak.'],
        [2, 'Antrian registrasi lama saat musim libur, siapkan waktu ekstra di basecamp.'],
        [5, 'Dari puncak bisa lihat Semeru, Kelud, dan Arjuno sekaligus. Luar biasa.'],
        [4, 'Total naik turun 12 jam santai termasuk istirahat panjang di sabana.'],
        [3, 'Kurang cocok kalau cari tantangan teknis, jalurnya relatif aman dan landai.'],
        [5, 'Gunung favorit saya untuk healing. Sepi, dingin, dan pemandangannya lengkap.'],
        [4, 'Sinyal masih dapat di beberapa titik sabana, lumayan untuk kabar ke rumah.'],
        [5, 'Perpaduan hutan lebat dan padang rumput terbuka bikin fotonya bagus semua.'],
        [4, 'Pendakian menyenangkan, hanya perlu waspada dengan jalur licin setelah hujan.'],
    ];

    public function run(): void
    {
        $mountain = Mountain::where('name', self::MOUNTAIN)->first();

        if (! $mountain) {
            $this->command->warn(self::MOUNTAIN.' not found — run MountainSeeder first.');

            return;
        }

        $needed = count(self::REVIEWS);
        $users = User::take($needed)->get();

        if ($users->count() < $needed) {
            $users = $users->concat(User::factory()->count($needed - $users->count())->create());
        }

        foreach (self::REVIEWS as $index => [$score, $review]) {
            MountainRating::updateOrCreate(
                ['user_id' => $users[$index]->id, 'mountain_id' => $mountain->id],
                ['score' => $score, 'review' => $review],
            )->forceFill(['created_at' => now()->subDays($needed - $index)])->save();
        }

        // The DB trigger keeps avg_rating in sync, but refresh the row we hold
        // so a seeder run leaves the model consistent on every driver.
        $mountain->update(['avg_rating' => $mountain->ratings()->avg('score')]);

        $this->command->info("Seeded {$needed} ratings for ".self::MOUNTAIN.'.');
    }
}
