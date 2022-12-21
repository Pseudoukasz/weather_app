<?php

namespace App\Service;

class WeatherCollection
{
    private array $tempCollection = [];
    private string $city;

    public function __construct(string $city)
    {
        $this->city = $city;
    }

    public function getCity(): float
    {
        return $this->city;
    }

    public function addTemp(float $temp): void
    {
        $this->tempCollection[] = $temp;
    }

    public function getAvgTemp(): float
    {
        if ($this->tempCollection) {
            $avg = 0;
            foreach ($this->tempCollection as $value) {
                $avg += $value;
            }

            return $avg / count($this->tempCollection);
        }

        return 0;

    }


}
