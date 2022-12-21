<?php

namespace App\Service;

use App\Entity\CityWeather;
use App\Service\OpenWeatherApiService\OpenWeatherApiService;
use App\Service\WeatherApiService\WeatherApiService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class WeatherService
{
    private OpenWeatherApiService $openWeatherApiService;
    private WeatherApiService $weatherApiService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        OpenWeatherApiService $openWeatherApiService,
        WeatherApiService $weatherApiService,
        EntityManagerInterface $entityManager
    ) {
        $this->openWeatherApiService = $openWeatherApiService;
        $this->weatherApiService = $weatherApiService;
        $this->entityManager = $entityManager;
    }

    public function getWeather(string $city, string $country = null): array
    {
        $cache = $this->checkCache($city);
        if ($cache) {
            return $cache;
        }
        $tempCollection = new WeatherCollection($city);
        $response = [
            'avgTemp' => 0,
            'city' => $city,
            'error' => false,
        ];
        try {
            $w1 = $this->openWeatherApiService->getWeather($city, $country);
            $tempCollection->addTemp($w1['temp']);
            $w2 = $this->weatherApiService->getWeather($city, $country);
            $tempCollection->addTemp($w2['temp']);
        } catch (\RuntimeException $exception) {
            $response['error'] = true;
            $response['message'] = $exception->getMessage();
        }
        $response['avgTemp'] = $tempCollection->getAvgTemp();
        $this->logWeatherSearch($city, $response['avgTemp']);
        $this->cacheLastSearch($city, $response['avgTemp']);

        return $response;

    }

    private function logWeatherSearch(string $city, float $avgTemp): void
    {
        $cityWeather = new CityWeather();
        $cityWeather->setCity($city)
            ->setTemp($avgTemp)
            ->setInsertTime(new DateTime());

        $this->entityManager->persist($cityWeather);
        $this->entityManager->flush();
    }

    private function cacheLastSearch(string $city, float $avgTemp): void
    {
        $cache = new FilesystemAdapter();
        $lastData = $cache->getItem('last_data');
        $lastData->set(['city' => $city, 'avgTemp' => $avgTemp, 'error' => false]);
        $cache->save($lastData);
        $cache->get('last_data', function (ItemInterface $item) {
            $item->expiresAfter(100);

            return true;
        }, 0);
    }

    private function checkCache(string $city): array
    {
        $cache = new FilesystemAdapter();
        $lastData = $cache->getItem('last_data');
        if ($lastData->isHit()) {
            $lastData = $lastData->get();
            if ($lastData['city'] === $city) {
                return $lastData;
            }
        }

        return [];
    }


}
