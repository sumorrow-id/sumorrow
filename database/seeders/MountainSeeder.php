<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Province;
use App\Models\Mountain;
use App\Models\MountainImage;
use App\Models\Basecamp;

class MountainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/mountains_seeder.json');

        if(!File::exists($jsonPath)) {
            $this->command->error('json file does not exist at ' . $jsonPath);
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        // 1. Seed Provinces
        $this->command->info('seeding... ' . count($data['provinces']) . ' provinces');
        foreach ($data['provinces'] as $provinceData) {
            Province::firstOrCreate(['name' => $provinceData['name']]);
        }

        // Mapping Province Name to ID for Mountain insert
        $provincesMap = Province::pluck('id', 'name')->toArray();

        // 2. Seed Mountains, Basecamps, and Images
        $this->command->info('seeding... ' . count($data['mountains']) . ' mountains');

        foreach ($data['mountains'] as $mountain) {
            $provinceId = $provincesMap[$mountain['province']] ?? null;

            if (!$provinceId) {
                $this->command->warn("Province {$mountain['province']} not found, skipping {$mountain['name']}.");
                continue;
            }

            // Create or Update Mountain
            $mountainModel = Mountain::updateOrCreate(
                ['name' => $mountain['name']], // Using name as the unique identifier
                [
                    'province_id'      => $provinceId,
                    'elevation_masl'   => $mountain['elevation_masl'],
                    'length_km'        => $mountain['length_km'] ?? 0,
                    'elevation_gain_m' => $mountain['elevation_gain_m'] ?? 0,
                    'coordinates'      => $mountain['coordinates'],
                    'description'      => $mountain['description'],
                    'is_active'        => $mountain['is_active'] ?? false,
                    'closed_since'     => $mountain['closed_since'] ?? null,
                    'difficulty'       => $mountain['difficulty'] ?? 'moderate',
                    'avg_rating'       => $mountain['avg_rating'] ?? 0,
                ]
            );

            // Create or Update Mountain Images
            if (!empty($mountain['images'])) {
                foreach ($mountain['images'] as $imgData) {
                    $sourceUrl = str_starts_with($imgData['image_url'], 'http')
                        ? $imgData['image_url']
                        : null;

                    MountainImage::updateOrCreate(
                        [
                            'mountain_id' => $mountainModel->id,
                            'position'    => $imgData['position'],
                        ],
                        [
                            'image_url'  => $imgData['image_url'],
                            'source_url' => $sourceUrl,
                            'is_cover'   => $imgData['is_cover'] ?? false,
                        ]
                    );
                }
            }

            // Create or Update Basecamps
            if (!empty($mountain['basecamps'])) {
                foreach ($mountain['basecamps'] as $bcData) {
                    Basecamp::updateOrCreate(
                        [
                            'mountain_id' => $mountainModel->id,
                            'name'        => $bcData['name'],
                        ]
                    );
                }
            }
        }
        
        $this->command->info('Seeding finished successfully.');
    }
}
