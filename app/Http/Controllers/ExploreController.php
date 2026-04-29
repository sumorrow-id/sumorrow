<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExploreController extends Controller
{
    public function dummy()
    {
        $dummyMountains = [
            [
                'name' => 'Gunung Rinjani',
                'elevation' => '3726',
                'image' => asset('images/dummymountain/rinjani.png'),
                'desc' => 'Gunung berapi tertinggi kedua di Indonesia yang terletak di Pulau Lombok. Terkenal dengan keindahan Danau Segara Anak di kalderanya yang memukau para pendaki.',
                'tags' => ['Nusa Tenggara', 'Expert', 'Volcano']
            ],
            [
                'name' => 'Gunung Semeru',
                'elevation' => '3676',
                'image' => asset('images/dummymountain/semeru.png'),
                'desc' => 'Puncak tertinggi di Pulau Jawa, Mahameru. Gunung ini menawarkan lanskap sabana Ranu Kumbolo yang ikonik dan medan pasir yang menantang di puncaknya.',
                'tags' => ['Java', 'Expert', 'Volcano']
            ],
            [
                'name' => 'Gunung Prau',
                'elevation' => '2565',
                'image' => asset('images/dummymountain/prau.png'),
                'desc' => 'Pilihan favorit para pendaki pemula. Gunung Prau memiliki area camp terluas dengan pemandangan golden sunrise terbaik se-Asia Tenggara menghadap Sindoro-Sumbing.',
                'tags' => ['Java', 'Beginner', 'Nature']
            ],
            [
                'name' => 'Gunung Kerinci',
                'elevation' => '3805',
                'image' => asset('images/dummymountain/kerinci.png'),
                'desc' => 'Gunung berapi tertinggi di Indonesia, terletak di perbatasan Jambi dan Sumatera Barat. Dikelilingi oleh hutan hujan tropis lebat Taman Nasional Kerinci Seblat.',
                'tags' => ['Sumatra', 'Expert', 'Volcano']
            ],
            [
                'name' => 'Gunung Batur',
                'elevation' => '1717',
                'image' => asset('images/dummymountain/batur.png'),
                'desc' => 'Gunung berapi aktif di Kintamani, Bali. Pendakian yang relatif singkat dan ramah pemula, sangat populer untuk mengejar panorama matahari terbit yang magis.',
                'tags' => ['Bali', 'Beginner', 'Volcano']
            ],
            [
                'name' => 'Gunung Latimojong',
                'elevation' => '3478',
                'image' => asset('images/dummymountain/latimojong.png'),
                'desc' => 'Puncak Rantemario di Gunung Latimojong adalah atap Pulau Sulawesi. Menawarkan jalur hutan lumut yang rapat dan keanekaragaman hayati endemik yang luar biasa.',
                'tags' => ['Sulawesi', 'Adventurer', 'Nature']
            ],

            [
                'name' => 'Gunung Agung',
                'elevation' => '3142',
                'image' => asset('images/dummymountain/agung.png'),
                'desc' => 'Gunung tertinggi dan paling disucikan di Pulau Bali. Menawarkan medan bebatuan vulkanik yang menantang dengan panorama kawah spektakuler di puncaknya.',
                'tags' => ['Bali', 'Expert', 'Volcano']
            ],

            [
                'name' => 'Gunung Kelud',
                'elevation' => '1731',
                'image' => asset('images/dummymountain/kelud.png'),
                'desc' => 'Gunung berapi aktif di Jawa Timur yang terkenal dengan kubah lavanya. Jalurnya sangat ramah pemula dan memiliki pemandangan kawah hijau yang eksotis.',
                'tags' => ['Java', 'Beginner', 'Volcano']
            ]
        ];

        return view('explore', compact('dummyMountains'));
    }
}
