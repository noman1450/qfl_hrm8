<?php

namespace App\Http\Responses;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Controllers\Controller;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     */
    public function toResponse($request)
    {
        auth()->user()->update([
            'last_login_at' => now()->toDateTimeString(),
            'last_login_ip' => $request->getClientIp()
        ]);

        resolve(Controller::class)->recordActivity(
             1,
             'System Login',
             auth()->user()->name.'-'.auth()->user()->email,
             auth()->id(),
             'users'
        );

        return redirect()->intended(
            RouteServiceProvider::HOME
        );
    }
}
