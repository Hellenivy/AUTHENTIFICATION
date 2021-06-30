<?php

namespace Hellen\TwoFactorAuth\Http\Controllers;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait ThrottlesTwoFactorAuths
{
    use ThrottlesLogins;

    protected function hasTooManyTwoFactorAuthAttempts(Request $request)
    {
        return self::hasTooManyLoginAttempts($request);
    }


    protected function incrementTwoFactorAuthAttempts(Request $request): void
    {
        self::incrementLoginAttempts($request);
    }


    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $message = __('twofactor-auth::twofactor-auth.throttle', ['seconds' => $seconds]);

        $errors = ['token' => $message];

        if ($request->expectsJson()) {
            return response()->json($errors, 429);
        }

        return redirect()->to('/login')
            ->withInput(
                Arr::only($request->session()->get('two-factor:auth'), [$this->username(), 'remember'])
            )
            ->withErrors($errors);
    }

    protected function clearTwoFactorAuthAttempts(Request $request): void
    {
        self::clearLoginAttempts($request);
    }
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->session()->get('two-factor:auth')[$this->username()]).'|'.$request->ip();
    }
}
