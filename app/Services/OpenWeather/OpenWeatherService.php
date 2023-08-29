<?php

namespace App\Services\OpenWeather;

use App\Repositories\WeatherRepository;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\Position;
use Symfony\Component\HttpFoundation\Response;

class OpenWeatherService
{
    const OPER_WEATHER_PREFIX = 'weather_';

    /**
     * Create a new OpenWeatherService instance.
     *
     * @param Client $client
     * @param WeatherRepository $weatherRepository
     */
    public function __construct(
        private readonly Client $client,
        private readonly WeatherRepository $weatherRepository
    ){}

    /**
     * Fetch and cache weather data.
     *
     * @return array
     * @throws Exception|GuzzleException
     */
    public function getAndCacheWeatherData(): array
    {
        $location = $this->getLocation();

        $cacheKey = self::OPER_WEATHER_PREFIX . $location->countryCode . '_' . $location->regionCode;

        $cachedData = $this->weatherRepository->getWeatherData($cacheKey);

        if ($cachedData) {
            if (now() < $cachedData['expires_at']) {
                return $cachedData['data'];
            }

            $this->weatherRepository->forgetWeatherData($cacheKey);
        }

        $weatherData = $this->getWeatherData();
        $this->weatherRepository->saveWeatherData($cacheKey, ['data' => $weatherData, 'expires_at' => today()->endOfDay()]);

        return $weatherData;
    }

    /**
     * Get weather data from the OpenWeather API.
     *
     * @return array
     * @throws Exception|GuzzleException
     */
    private function getWeatherData(): array
    {
        return $this->formatedResponse($this->send());
    }

    /**
     * Send a GET request to the OpenWeather API.
     *
     * @return array
     * @throws Exception|GuzzleException
     */
    public function send(): array
    {
        $response = $this->client->get($this->getUrl());

        return $this->getResponseBody($response);
    }

    /**
     * Get the URL for the OpenWeather API request.
     *
     * @return string
     */
    private function getUrl(): string
    {
        return config('services.open_weather.url') . $this->getParameters();
    }

    /**
     * Get user Location
     *
     * @return bool|Position
     */
    private function getLocation()
    {
        return Location::get();
    }

    /**
     * Get the parameters for the OpenWeather API request.
     *
     * @return string
     */
    private function getParameters(): string
    {
        $location = $this->getLocation();

        return "?lat=$location->latitude&lon=$location->longitude&exclude=hourly,minutely,alerts&appid=" . $this->getApiKey();
    }

    /**
     * Get the API key for the OpenWeather API request.
     *
     * @return string
     */
    private function getApiKey(): string
    {
        return config('services.open_weather.api_key');
    }

    /**
     * Get the response body as an array.
     *
     * @param $response
     * @return array
     * @throws Exception
     */
    private function getResponseBody($response): array
    {
        if ($response && $response->getStatusCode() === Response::HTTP_OK) {
            $body = $response->getBody();
            return @json_decode($body, true);
        }

        throw new Exception(
            'OpenWeather bad response code '
            . $response->getStatusCode(),
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Format the weather data response.
     *
     * @param array $data
     * @return array
     */
    private function formatedResponse(array $data): array
    {
        $current = $data['current'];
        $daily = array_shift($data['daily']);

        return [
            'temp' => $current['temp'],
            'pressure' => $current['pressure'],
            'humidity' => $current['humidity'],
            'tempMin' => $daily['temp']['min'],
            'tempMax' => $daily['temp']['max'],
        ];
    }
}
