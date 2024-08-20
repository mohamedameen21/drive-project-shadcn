<?php

namespace App\Http\Controllers;

use App\Http\Responses\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DemoUserController extends Controller
{
    public function storeDemoUserToken(Request $request)
    {
        $token = Str::random(40);
        $user = User::where('email', 'demouser@gmail.com')->first() ?? null;

        Cache::put('login_token_' . $token, [
            'user_id' => $user->id,
        ], now()->addYear(1));

        $signedUrl = URL::temporarySignedRoute(
            'authenticate',
            now()->addYear(1),
            ['token' => $token]
        );

        dd($signedUrl);
    }

    public function authenticateDemoUser(Request $request) {
        $token = request()->route('token');

        $tokenData = Cache::get('login_token_' . $token);

        if (!$tokenData) {
            return redirect()->route('login');
        }

        $user = User::find($tokenData['user_id']);

        if (!$user) {
            return redirect()->route('login');
        }

        auth()->login($user);

        return redirect()->route('myFiles');
    }
}
