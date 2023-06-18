<?php

namespace App\GraphQL\Mutations\Auth;

use App\Events\Auth\UserLoggedIn;
use App\Exceptions\AuthenticationException;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Exceptions\ValidationException;

class Login
{
    protected string $email;

    /**
     * @param null $rootValue
     * @param array<string, mixed> $args
     * @throws AuthenticationException|ValidationException
     */
    public function resolve($rootValue, array $args): array
    {
        $credentials = Arr::only($args, ['email', 'password']);
        $this->email = Arr::get($credentials, 'email');

        $guard = Auth::guard();

        $this->ensureIsNotRateLimited();
        if (!$guard->attempt($credentials)) {
            RateLimiter::hit($this->throttleKey());
            throw new AuthenticationException("Unauthorized", "Incorrect email or password.");
        }

        RateLimiter::clear($this->throttleKey());

        /** @var User $user */
        $user = $guard->user();
        event(new UserLoggedIn($user));

        $token = $user->createToken('SPA')->accessToken;

        return [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'user' => auth()->user()
        ];
    }

    /**
     * @return string
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'. request()->ip());
    }

    /**
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $errorMsg = trans('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]);

        throw new ValidationException(['email' => $errorMsg], $errorMsg);
    }
}
