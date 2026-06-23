<?php

namespace Tests\Feature;

use App\Models\Mountain;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeMountain(): Mountain
    {
        $province = Province::create(['name' => 'Nusa Tenggara Barat']);

        return Mountain::create([
            'province_id' => $province->id,
            'name' => 'Rinjani',
            'elevation_masl' => 3726,
            'length_km' => 12.0,
            'elevation_gain_m' => 2000,
            'coordinates' => '8 deg 24\' 51" S, 116 deg 27\' 28" E',
            'description' => 'A test mountain.',
            'is_active' => true,
            'difficulty' => 'hard',
            'avg_rating' => 4.5,
        ]);
    }

    // -------------------------------------------------------------------------
    // show — signature is enforced inside the controller
    // -------------------------------------------------------------------------

    public function test_weather_show_rejects_unsigned_requests(): void
    {
        $mountain = $this->makeMountain();

        $response = $this->getJson(route('weather.show', $mountain));

        $response->assertStatus(403);
    }

    public function test_weather_show_returns_data_for_signed_requests(): void
    {
        $mountain = $this->makeMountain();

        $url = URL::signedRoute('weather.show', ['mountain' => $mountain->id]);

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure(['temp', 'wind', 'icon', 'description']);
    }

    // -------------------------------------------------------------------------
    // forecast
    // -------------------------------------------------------------------------

    public function test_weather_forecast_rejects_unsigned_requests(): void
    {
        $mountain = $this->makeMountain();

        $response = $this->getJson(route('weather.forecast', $mountain));

        $response->assertStatus(403);
    }

    public function test_weather_forecast_returns_data_for_signed_requests(): void
    {
        $mountain = $this->makeMountain();

        $url = URL::signedRoute('weather.forecast', ['mountain' => $mountain->id]);

        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure(['list']);
    }
}
