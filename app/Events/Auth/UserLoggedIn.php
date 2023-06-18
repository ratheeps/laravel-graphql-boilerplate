<?php

namespace App\Events\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

class UserLoggedIn
{
    /**
     * @var Authenticatable
     */
    public Authenticatable $user;

    /**
     * UserLoggedIn constructor.
     *
     * @param  Authenticatable  $user
     */
    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}
