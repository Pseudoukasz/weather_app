<?php

namespace App\Controller;

use App\Form\LocationType;
use App\Repository\CityWeatherRepository;
use App\Service\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    private WeatherService $weatherService;
    private CityWeatherRepository $cityWeatherRepository;

    public function __construct(WeatherService $weatherService, CityWeatherRepository $cityWeatherRepository)
    {
        $this->weatherService = $weatherService;
        $this->cityWeatherRepository = $cityWeatherRepository;
    }

    #[Route('/weather', name: 'app_weather')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LocationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $weatherData = $this->weatherService->getWeather($formData['city'], $formData['country']);
        }
        $lastSearched = $this->cityWeatherRepository->findBy([], ['id' => 'DESC'], 5);

        return $this->render('weather/weather.html.twig', [
            'form' => $form->createView(),
            'weatherData' => $weatherData ?? [],
            'lastSearched' => $lastSearched

        ]);
    }
















}
