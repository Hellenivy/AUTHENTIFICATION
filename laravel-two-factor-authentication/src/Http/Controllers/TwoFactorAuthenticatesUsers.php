<?php

namespace Hellen\TwoFactorAuth\Http\Controllers;

use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Hellen\TwoFactorAuth\Contracts\TwoFactorProvider;
use Hellen\TwoFactorAuth\Events\TwoFactorAuthenticated;
use Hellen\TwoFactorAuth\Exceptions\TokenAlreadyProcessedException;
use Hellen\TwoFactorAuth\Exceptions\TokenExpiredException;
use Hellen\TwoFactorAuth\Exceptions\TokenInvalidException;
use Hellen\TwoFactorAuth\Http\Requests\VerifySMSToken;

trait TwoFactorAuthenticatesUsers
{
    use RedirectsUsers, ThrottlesTwoFactorAuths;

    /**   
     * @return 
     */
    public function showTwoFactorForm()
    {
        return view('twofactor-auth::form');
    }
    public function verifyToken(VerifySMSToken $request)
    {
        if ($this->hasTooManyTwoFactorAuthAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        try {
            $result = $this->attemptTwoFactorAuth($request);
        } catch (TokenInvalidException $exception) {
            $result = false;
        } catch (TokenExpiredException $exception) {
            return $this->sendKillTwoFactorAuthResponse($request);
        } catch (TokenAlreadyProcessedException $exception) {
            return $this->sendKillTwoFactorAuthResponse($request);
        }

        if ($result) {
            return $this->sendTwoFactorAuthResponse($request);
        }

        return $this->handleFailedAttempt($request);
    }

    /**
     * @param  
     * @return 
     */
    protected function attemptTwoFactorAuth(Request $request)
    {
        $user = config('twofactor-auth.model')::findOrFail(
            $request->session()->get('two-factor:auth')['id']
        );

        if (resolve(TwoFactorProvider::class)->verify($user, $request->input('token'))) {
            auth()->login($user);   // If SMS code validation passes, login user

            return true;
        }

        return false;
    }

    /**
     *
     *
     * @param  
     * @return 
     */
    protected function sendTwoFactorAuthResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearTwoFactorAuthAttempts($request);

        $request->session()->forget('two-factor:auth');

        $user = $request->user();

        event(new TwoFactorAuthenticated($user));

        return $this->authenticated($request, $user)
            ?: redirect()->intended($this->redirectPath());
    }

 
    protected function authenticated(Request $request, $user)
    {
        //
    }


    protected function handleFailedAttempt(Request $request)
    {
        $this->incrementTwoFactorAuthAttempts($request);

        if ($path = $this->redirectAfterFailurePath()) {
            return redirect()->to($path)->withErrors([
                'token' => __('twofactor-auth::twofactor-auth.failed'),
            ]);
        }

        return $this->sendFailedTwoFactorAuthResponse($request);
    }


    protected function redirectAfterFailurePath(): ?string
    {
        if (method_exists($this, 'redirectToAfterFailure')) {
            return $this->redirectToAfterFailure();
        }

        if (property_exists($this, 'redirectToAfterFailure')) {
            return $this->redirectToAfterFailure;
        }

        return null;
    }


    protected function sendFailedTwoFactorAuthResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'token' => [__('twofactor-auth::twofactor-auth.failed')],
        ]);
    }

    /**
     *
     *
     * @param  
     * @return 
     */
    protected function sendKillTwoFactorAuthResponse(Request $request)
    {
        $errors = ['token' => __('twofactor-auth::twofactor-auth.expired')];

        if ($request->expectsJson()) {
            return response()->json($errors, 401);
        }

        return redirect()->to('/login')->withErrors($errors);
    }

 
    public function username(): string
    {
        return 'email';
    }
}
