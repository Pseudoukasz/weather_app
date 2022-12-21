<?php

namespace App\Service\WeatherApiService;

use App\Api\OpenWeatherApi\OpenWeatherApiRequest;
use App\Api\WeatherApi\WeatherApiRequest;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class WeatherApiService
{
    private WeatherApiRequest $weatherApiRequest;

    public function __construct(WeatherApiRequest $weatherApiRequest)
    {
        $this->weatherApiRequest = $weatherApiRequest;
    }

    public function getWeather(string $city, string $country = null): array
    {
        $weatherData = $this->weatherApiRequest->getWeather($city, $country);
        if ($weatherData['error']) {
            throw new RuntimeException($weatherData['message']);
        }

        return [
            'temp' => $weatherData['current']['temp_c'],
            'city' => $weatherData['location']['name'],
            'error' => false,
        ];
    }
}
