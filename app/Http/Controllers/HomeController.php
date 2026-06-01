<?php

namespace App\Http\Controllers;

use App\Models\Mountain;

class HomeController extends Controller
{
    public function index()
    {
        $mountains = Mountain::with(['province', 'images'])->get();
        
        $weatherData = $mountains->map(function ($mountain) {
            $provinceName = $mountain->province ? $mountain->province->name : 'Indonesia';
            $provinceName = str_replace('Provinsi ', '', $provinceName);

            // Fungsi pembantu untuk mengubah format "8 deg 16' 0" S" menjadi desimal
            $dmsToDecimal = function ($dmsStr) {
                $dmsStr = trim(preg_replace('/\s+/', ' ', $dmsStr));
                if (preg_match('/(\d+)\s*deg\s*(\d+)\s*\'\s*(\d+(\.\d+)?)\s*"\s*([NSEW])/i', $dmsStr, $matches)) {
                    $degrees = (float) $matches[1];
                    $minutes = (float) $matches[2];
                    $seconds = (float) $matches[3];
                    $direction = strtoupper($matches[5]);

                    $decimal = $degrees + $minutes / 60 + $seconds / 3600;
                    if ($direction === 'S' || $direction === 'W') {
                        $decimal *= -1;
                    }
                    return $decimal;
                }
                return 0;
            };

            $parts = explode(',', $mountain->coordinates);
            $lat = isset($parts[0]) && trim($parts[0]) !== '' ? $dmsToDecimal($parts[0]) : '-8.2666'; 
            $lng = isset($parts[1]) && trim($parts[1]) !== '' ? $dmsToDecimal($parts[1]) : '115.4166';

            return [
                'loc' => $mountain->name . ', ' . $provinceName,
                'url' => '/explore/' . $mountain->id,
                'lat' => $lat,
                'lng' => $lng,
                // Default fallback if API fails
                'temp' => '22&deg;',
                'wind' => 'Wind: 10 km/h',
            ];
        })->toArray();

        // Shuffle so it's different each load
        shuffle($weatherData);

        // Get 6 random mountains for the popular section
        // We will just pick 6 random from the full fetched collection
        $popularMountains = $mountains->random(min(10, $mountains->count()))->map(function ($mountain) {
            $provinceName = $mountain->province ? $mountain->province->name : '';
            $provinceName = str_replace('Provinsi ', '', $provinceName);
            
            return [
                'id' => $mountain->id,
                'name' => $mountain->name,
                'location' => $provinceName,
                'image' => $mountain->images->first() ? $mountain->images->first()->image_url : 'https://images.unsplash.com/photo-1627916538356-9a2ebfb1d200?auto=format&fit=crop&q=80&w=500',
            ];
        });

        // Get 3 random peaks for the "Choose Your Peak" section
        $randomPeaks = $mountains->random(min(3, $mountains->count()))->map(function ($mountain) {
            $provinceName = $mountain->province ? $mountain->province->name : '';
            $provinceName = str_replace('Provinsi ', '', $provinceName);
            
            return [
                'id' => $mountain->id,
                'name' => $mountain->name,
                'location' => $provinceName,
                'elevation' => $mountain->elevation_masl ? $mountain->elevation_masl . ' mdpl' : 'Unknown',
                'difficulty' => $mountain->difficulty ? ucfirst($mountain->difficulty) : 'Moderate',
                'image' => $mountain->images->first() ? $mountain->images->first()->image_url : 'https://images.unsplash.com/photo-1627916538356-9a2ebfb1d200?auto=format&fit=crop&q=80&w=600',
            ];
        });

        // API Key to pass to frontend
        $openWeatherApiKey = config('services.openweathermap.key');

        return view('home', compact('weatherData', 'openWeatherApiKey', 'popularMountains', 'randomPeaks'));
    }

    public function redirectToHome()
    {
        return redirect()->route('home');
    }
}
