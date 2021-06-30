<?php

namespace Hellen\TwoFactorAuth\Contracts;

interface TwoFactorProvider
{
    
    public function enabled($user);
    public function register($user): void;

    public function unregister($user);
    public function verify($user, string $token);
}
