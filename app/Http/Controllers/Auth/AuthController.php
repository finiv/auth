<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRegistrationRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return RedirectResponse
     */
    public function redirectToProvider(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google login callback
     *
     * @return JsonResponse
     */
    public function handleProviderCallback(): JsonResponse
    {
        $googleUser = Socialite::driver('google')->user();
        $email = $googleUser->getEmail();
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
            ]);
        }

        $token = $user->createToken('access_token')->plainTextToken;

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
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('access_token')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    /**
     * Login via email
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function loginViaEmail(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

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
