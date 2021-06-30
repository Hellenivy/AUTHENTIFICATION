<?php

namespace Hellen\TwoFactorAuth\Providers;

use Hellen\TwoFactorAuth\Contracts\SMSToken;
use Hellen\TwoFactorAuth\Contracts\TwoFactorProvider;

class NullProvider extends BaseProvider implements TwoFactorProvider, SMSToken
{
    /**
     * {@inheritdoc}
     */
    public function register($user): void
    {
        //
    }


    public function unregister($user)
    {
        //
    }


    public function verify($user, string $token)
    {
        return true;
    }

    public function sendSMSToken($user): void
    {
        //
    }
}
