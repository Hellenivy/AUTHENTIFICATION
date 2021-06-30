<?php

namespace Hellen\TwoFactorAuth;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

trait TwoFactorAuthenticable
{

    public function getMobile(): string
    {
        return $this->mobile;
    }


    public function twoFactorAuth(): HasOne
    {
        return $this->hasOne(
            \Hellen\TwoFactorAuth\Models\TwoFactorAuth::class, 'user_id', $this->getKeyName()
        );
    }

   
    public function setTwoFactorAuthId(string $id): void
    {
        $enabled = config('twofactor-auth.enabled', 'user');

        if ($enabled === 'user') {
            $this->twoFactorAuth->update(['id' => $id]);
        }

        if ($enabled === 'always') {
            $this->upsertTwoFactorAuthId($id);
        }
    }
    public function getTwoFactorAuthId(): string
    {
        return $this->twoFactorAuth->id;
    }
    private function upsertTwoFactorAuthId(string $id): void
    {
        DB::transaction(function () use ($id) {
            $attributes = ['id' => $id];

            if (! $this->twoFactorAuth()->exists()) {
                $this->twoFactorAuth()->create($attributes);
            } else {
                $this->twoFactorAuth->update($attributes);
            }
        });
    }
}
