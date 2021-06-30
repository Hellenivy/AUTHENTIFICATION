<?php

namespace Hellen\TwoFactorAuth\Providers;

abstract class BaseProvider
{
    /**
     * 
     *
     * @param  
     * @return 
     */
    public function enabled($user)
    {
        $enabled = config('twofactor-auth.enabled', 'user');

        if ($enabled === 'user') {
            return ! is_null($user->twoFactorAuth);
        }

        return $enabled === 'always';
    }
}
