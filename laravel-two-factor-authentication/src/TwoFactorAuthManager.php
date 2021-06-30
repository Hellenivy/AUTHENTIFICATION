<?php

namespace Hellen\TwoFactorAuth;

use Illuminate\Support\Manager;
use MessageBird\Client;
use Hellen\TwoFactorAuth\Contracts\TwoFactorProvider;
use Hellen\TwoFactorAuth\Providers\MessageBirdVerify;
use Hellen\TwoFactorAuth\Providers\NullProvider;

class TwoFactorAuthManager extends Manager
{

    public function provider(string $driver = null): TwoFactorProvider
    {
        return $this->driver($driver);
    }

    protected function createMessageBirdDriver(): TwoFactorProvider
    {
        return new MessageBirdVerify(
            new Client($this->config['twofactor-auth.providers.messagebird.key'])
        );
    }

    protected function createNullDriver(): TwoFactorProvider
    {
        return new NullProvider;
    }
    public function getDefaultDriver(): string
    {
        return $this->config['twofactor-auth.default'];
    }

    public function setDefaultDriver(string $name): void
    {
        $this->config['twofactor-auth.default'] = $name;
    }
}
