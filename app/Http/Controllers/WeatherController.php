<?php

namespace App\Http\Controllers;

use App\Models\Mountain;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function show(Request $request, Mountain $mountain): JsonResponse
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $apiKey = config('services.openweathermap.key');

        $data = Cache::remember("weather.mountain.{$mountain->id}", now()->addMinutes(30), function () use ($mountain, $apiKey) {
            if (! $apiKey) {
                return ['temp' => '22°', 'wind' => 'Wind: 10 km/h', 'icon' => '01d', 'description' => 'Clear'];
            }

            $coords = $this->parseCoordinates($mountain->coordinates);

            $response = Http::withoutVerifying()->timeout(5)->get('https://api.openweathermap.org/data/2.5/weather', [
                'lat' => $coords['lat'],
                'lon' => $coords['lng'],
                'units' => 'metric',
                'appid' => $apiKey,
            ]);

            if (! $response->successful()) {
                return ['temp' => '22°', 'wind' => 'Wind: 10 km/h', 'icon' => '01d', 'description' => 'Clear'];
            }

            $body = $response->json();

            return [
                'temp' => round($body['main']['temp']).'°',
                'wind' => 'Wind: '.round($body['wind']['speed']).' km/h',
                'icon' => $body['weather'][0]['icon'] ?? '01d',
                'description' => $body['weather'][0]['main'] ?? 'Clear',
            ];
        });

        return response()->json($data);
    }

    public function forecast(Request $request, Mountain $mountain): JsonResponse
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $apiKey = config('services.openweathermap.key');

        $data = Cache::remember("weather.forecast.{$mountain->id}", now()->addHour(), function () use ($mountain, $apiKey) {
            if (! $apiKey) {
                return $this->mockForecastData();
            }

            $coords = $this->parseCoordinates($mountain->coordinates);

            $response = Http::withoutVerifying()->timeout(8)->get('https://api.openweathermap.org/data/2.5/forecast', [
                'lat' => $coords['lat'],
                'lon' => $coords['lng'],
                'units' => 'metric',
                'appid' => $apiKey,
            ]);

            if (! $response->successful()) {
                return $this->mockForecastData();
            }

            return $response->json();
        });

        return response()->json($data);
    }

    private function mockForecastData(): array
    {
        $now = time();
        $list = [];
        $icons = ['01d', '03d', '10d'];
        $descs = ['Clear', 'Clouds', 'Rain'];

        for ($i = 0; $i < 9; $i++) {
            $list[] = [
                'dt' => $now + ($i * 10800),
                'main' => ['temp' => 22 + ($i % 3)],
                'weather' => [['icon' => $icons[$i % 3], 'main' => $descs[$i % 3]]],
            ];
        }

        return ['list' => $list];
    }

    private function parseCoordinates(string $coordinates): array
    {
        $parts = explode(',', $coordinates);

        $dmsToDecimal = function (string $dms): float {
            $dms = trim(preg_replace('/\s+/', ' ', $dms));
            if (preg_match('/(\d+)\s*deg\s*(\d+)\s*\'\s*(\d+(?:\.\d+)?)\s*"\s*([NSEW])/i', $dms, $m)) {
                $decimal = (float) $m[1] + (float) $m[2] / 60 + (float) $m[3] / 3600;

                return (strtoupper($m[4]) === 'S' || strtoupper($m[4]) === 'W') ? -$decimal : $decimal;
            }

            return 0.0;
        };

        return [
            'lat' => isset($parts[0]) && trim($parts[0]) !== '' ? $dmsToDecimal($parts[0]) : -8.2666,
            'lng' => isset($parts[1]) && trim($parts[1]) !== '' ? $dmsToDecimal($parts[1]) : 115.4166,
        ];
    }
}
