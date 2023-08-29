<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRegistrationRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\Auth\EmailService;
use App\Services\Auth\GoogleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly GoogleService $googleService,
        private readonly EmailService $emailService,
    ) {}

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return RedirectResponse
     */
    public function redirectToProvider(): RedirectResponse
    {
        return $this->googleService->redirectToProvider();
    }

    /**
     * Google login callback
     *
     * @return JsonResponse
     */
    public function handleProviderCallback(): JsonResponse
    {
        $token = $this->googleService->handleProviderCallback();

        return response()->json(['token' => $token]);
    }

    /**
     * Register via email
     *
     * @param EmailRegistrationRequest $request
     * @return JsonResponse
     */
    public function registerViaEmail(EmailRegistrationRequest $request): JsonResponse
    {
        $token = $this->emailService->register(
            $request->input('name'),
            $request->input('email'),
            Hash::make($request->input('password'))
        );

        return response()->json(['token' => $token]);
    }

    /**
     * Login via email
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function loginViaEmail(Request $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $token = $this->emailService->createToken(Auth::user());
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Auth failed'], 401);
    }

    /**
     * No auth response
     *
     * @return JsonResponse
     */
    public function notAuthResponse(): JsonResponse
    {
        return response()->json(['message' => 'Please register in app.'], 401); // can be lang
    }
}
