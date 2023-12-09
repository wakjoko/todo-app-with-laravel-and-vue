<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => [
                'required',
                'string',
                'max:255',
                'email:strict,rfc,dns,spoof',
                'unique:'.User::class
            ],
            'password' => ['required', Password::defaults()],
        ];
    }
}
