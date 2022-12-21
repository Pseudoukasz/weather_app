<?php

namespace App\Service\OpenWeatherApiService;

use App\Api\OpenWeatherApi\OpenWeatherApiRequest;
use App\Api\WeatherApi\WeatherApiRequest;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class OpenWeatherApiService
{
    private OpenWeatherApiRequest $openWeatherApiRequest;

    public function __construct(OpenWeatherApiRequest $openWeatherApiRequest)
    {
        $this->openWeatherApiRequest = $openWeatherApiRequest;
    }

    public function getWeather(string $city, string $country = null): array
    {
        $weatherData = $this->openWeatherApiRequest->getWeather($city, $country);
        if ($weatherData['error']) {
            throw new RuntimeException($weatherData['message']);
        }

        return ['temp' => $weatherData['main']['temp'], 'city' => $weatherData['name'], 'error' => false];
    }
}
