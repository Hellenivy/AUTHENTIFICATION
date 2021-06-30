<?php

namespace Hellen\TwoFactorAuth\Providers;

use Exception;
use MessageBird\Client;
use MessageBird\Exceptions\RequestException;
use MessageBird\Objects\Verify;
use Hellen\TwoFactorAuth\Contracts\SMSToken;
use Hellen\TwoFactorAuth\Contracts\TwoFactorProvider;
use Hellen\TwoFactorAuth\Exceptions\TokenAlreadyProcessedException;
use Hellen\TwoFactorAuth\Exceptions\TokenExpiredException;
use Hellen\TwoFactorAuth\Exceptions\TokenInvalidException;

class MessageBirdVerify extends BaseProvider implements TwoFactorProvider, SMSToken
{

    private $client;

    /**
     *
     *
     * @param 
     * @return 
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function register($user): void
    {
        //
    }

    public function unregister($user)
    {
        $result = $this->client->verify->delete($user->getTwoFactorAuthId());
        $user->setTwoFactorAuthId(null);

        return $result;
    }

    public function verify($user, string $token)
    {
    
        try {
            $result = $this->client->verify->verify($user->getTwoFactorAuthId(), $token);
        } catch (RequestException $exception) {
            $message = $exception->getMessage();

            if ($message === 'Token should between 6 and 10 characters' || $message === 'The token is invalid.') {
                throw new TokenInvalidException($message);
            }

            if ($message === 'The token has expired.') {
                throw new TokenExpiredException($message);
            }

            if ($message === 'The token has already been processed.') {
                throw new TokenAlreadyProcessedException($message);
            }

            // Re-throw exception if there was no match
            throw $exception;
        }

        if ($result->getStatus() === Verify::STATUS_VERIFIED) {
            return true;
        }

        return false;
    }


    public function sendSMSToken($user): void
    {
        if (! $user->getMobile()) {
            throw new Exception("No mobile phone number found for user {$user->id}.");
        }

        $verify = new Verify;
        $verify->recipient = $user->getMobile();

        $result = $this->client->verify->create(
            $verify,
            config('twofactor-auth.providers.messagebird.options')
        );

        $user->setTwoFactorAuthId($result->getId());
    }
}
