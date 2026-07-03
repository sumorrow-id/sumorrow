<?php

return [
    'developers_label' => 'Pengembang',
    'hero_description' => 'API HTTP baca-saja untuk katalog gunung Sumorrow — provinsi, gunung, gambar gunung, basecamp, dan penilaian gunung.',

    'base_url_label' => 'URL Dasar',
    'format_label' => 'Format',
    'format_value' => 'Hanya JSON',
    'auth_label' => 'Autentikasi',
    'rate_limit_label' => 'Batas Permintaan',

    'on_this_page' => 'Di halaman ini',
    'nav_authentication' => 'Autentikasi',
    'nav_rate_limiting' => 'Pembatasan Permintaan',
    'nav_envelopes' => 'Amplop Respons',
    'nav_query_params' => 'Parameter Kueri',
    'nav_endpoints' => 'Endpoint',
    'nav_errors' => 'Kode Kesalahan',
    'nav_roadmap' => 'Peta Jalan',

    'authentication_desc' => 'Setiap permintaan harus menyertakan salah satu dari: cookie sesi Sanctum (SPA same-origin, setelah login web), atau personal access token yang dikirim sebagai header Bearer.',
    'authentication_401_note' => 'Kode 401 dikembalikan untuk kredensial yang hilang atau tidak valid:',

    'rate_limiting_desc' => 'Batas diberlakukan per identitas dan dikelompokkan per menit. Setiap respons menyertakan header standar di bawah ini.',
    'audience_label' => 'Audiens',
    'limit_label' => 'Batas',
    'authenticated_user' => 'Pengguna terautentikasi',
    'authenticated_user_limit' => '30 permintaan / menit (per ID pengguna)',
    'unauthenticated' => 'Tidak terautentikasi',
    'unauthenticated_limit' => '10 permintaan / menit (per IP) — tetap mengembalikan 401',
    'header_bucket_size' => 'Ukuran bucket',
    'header_requests_remaining' => 'Sisa permintaan dalam jendela waktu',
    'header_retry_seconds' => 'Detik untuk menunggu (pada 429)',

    'envelope_list' => 'Daftar (dipaginasi)',
    'envelope_single' => 'Sumber daya tunggal',
    'envelope_error' => 'Amplop kesalahan',
    'envelope_validation' => 'Validasi (422)',

    'query_params_intro_1' => 'mendukung parameter kueri berikut.',
    'query_params_intro_2' => 'menerima',
    'query_params_default_note' => '(default 50, maks 50).',
    'param_label' => 'Param',
    'type_label' => 'Tipe',
    'notes_label' => 'Catatan',

    'code_label' => 'Kode',
    'when_label' => 'Kapan',
    'error_401_desc' => 'Kredensial Sanctum hilang / tidak valid',
    'error_404_desc' => 'Sumber daya tidak ditemukan, atau jalur API tidak cocok',
    'error_422_desc' => 'Validasi parameter kueri gagal',
    'error_429_desc' => 'Batas permintaan terlampaui — lihat',

    'roadmap_desc' => 'Endpoint tulis (POST / PATCH / DELETE) belum tersedia. Item di bawah ini direncanakan, dibatasi untuk penulisan khusus admin di revisi mendatang.',
];
