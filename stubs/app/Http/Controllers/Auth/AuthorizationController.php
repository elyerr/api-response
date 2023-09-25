<?php

namespace App\Http\Controllers\Auth;

use App\Events\Tokens\LoginEvent;
use App\Events\Tokens\LogoutEvent;
use Illuminate\Routing\Controller;
use App\Http\Requests\Auth\LoginRequest; 

class AuthorizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->only('store');
        $this->middleware('auth:sanctum')->only('destroy');
    }
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $token = request()->user()->createToken($request->email ."|". $_SERVER['HTTP_USER_AGENT']);

        LoginEvent::dispatch(request()->user());

        return response()->json(['data' => [
            'Authorization' => "Bearer " . $token->plainTextToken,
        ]], 201);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        request()->user()->currentAccessToken()->delete(); 

        LogoutEvent::dispatch(request()->user());

        return $this->message('La sesion ha sido cerrada.', 200);
    }
}
