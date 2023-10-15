<?php

namespace App\Http\Controllers\Auth;
 
use Illuminate\Routing\Controller; 
use App\Http\Requests\Auth\LoginRequest; 
use Elyerr\ApiResponse\Events\LoginEvent;
use Elyerr\ApiResponse\Events\LogoutEvent;
use Elyerr\ApiResponse\Assets\JsonResponser;

class AuthorizationController extends Controller
{
    use JsonResponser;

    public function __construct()
    {
        $this->middleware('guest')->only('store');
        $this->middleware('auth')->only('destroy');
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

        $token = request()->user()->createToken($_SERVER['HTTP_USER_AGENT']);

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
