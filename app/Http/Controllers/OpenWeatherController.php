<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\OpenWeather\OpenWeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpenWeatherController extends Controller
{
    public function __construct(private readonly OpenWeatherService $service) {}

    /**
     * Get user home page
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAndCacheWeatherData();

        return response()->json(['user' => UserResource::make($request->user()), 'main' => $data]);
    }
}
