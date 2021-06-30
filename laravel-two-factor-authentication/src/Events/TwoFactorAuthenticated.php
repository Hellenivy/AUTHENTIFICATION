<?php

namespace Hellen\TwoFactorAuth\Events;

use Illuminate\Queue\SerializesModels;

class TwoFactorAuthenticated
{
    use SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
