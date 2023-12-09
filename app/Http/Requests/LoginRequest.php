<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class LoginRequest extends FormRequest
{
    public const MAX_ATTEMPTS = 5;

    public function rules()
    {
        return [
            'email' => [
                'required',
                // 'email:strict,rfc,dns,spoof',
                'exists:'.User::class,
            ],
            'password' => ['required'],
            'token_name' => ['nullable'],
            'expires_at' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $emailNotExists = Arr::has($validator->failed(), 'email.Exists');

        if ($emailNotExists) {
            $exception = new ValidationException($validator);
            $exception->status = 404;

            throw $exception;
        }

        parent::failedValidation($validator);
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticateOrFail(): void
    {
        $this->ensureIsNotRateLimited();

        if (Auth::attempt($this->only('email', 'password')) === false) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([trans('auth.failed')]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS) === false) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw new TooManyRequestsHttpException($seconds);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $email = $this->input('email');

        return Str::lower("{$email}|{$this->ip()}");
    }
}
