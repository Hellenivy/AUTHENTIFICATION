<?php

namespace MichaelDzjap\TwoFactorAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifySMSToken extends FormRequest
{

    public function authorize()
    {
        if ($this->session()->has('two-factor:auth')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'token' => 'required|string|numeric',
        ];
    }
}
