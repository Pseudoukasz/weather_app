<?php

namespace App\Api\WeatherApi;

use App\Api\Request;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class WeatherApiRequest extends Request
{
    public const BASE_URL = 'http://api.weatherapi.com/v1/';
    public const HEADERS = [];
    private ParameterBagInterface $container;

    public function __construct(ParameterBagInterface $container)
    {
        $this->container = $container;
        parent::__construct(self::BASE_URL, self::HEADERS);
    }

    public function getWeather(string $city, string $country = null): array
    {
        $params = $this->prepareRequestParams($city, $country);
        try {
            $req = $this->request('current.json', $params);
            $response = json_decode($req->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $response['error'] = false;
        } catch (GuzzleException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true)['error'];
            $response['error'] = true;
        }

        return $response;
    }

    private function prepareRequestParams(string $city, string $country = null): string
    {
        $apiKey = $this->container->get('WEATHER_API_KEY');

        $params = '?key='.$apiKey.'&q='.$city;
        if ($country) {
            $params .= ','.$country;
        }

        return $params;
    }

}
