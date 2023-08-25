<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;

class WeatherRepository
{
    /**
     * @param $key
     * @param $data
     * @return void
     */
    public function saveWeatherData($key, $data): void
    {
        Cache::set($key, json_encode($data));
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getWeatherData($key): mixed
    {
        $data = Cache::get($key);
        return $data ? json_decode($data, true) : null;
    }

    /**
     * @param $cacheKey
     * @return void
     */
    public function forgetWeatherData($cacheKey): void
    {
        Cache::forget($cacheKey);
    }
}
