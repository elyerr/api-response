<?php

namespace App\Http\Controllers\Auth;

use Error;
use Illuminate\Http\Request;
use App\Exceptions\ReportError;
use App\Http\Controllers\GlobalController; 

class TokensController extends GlobalController
{

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Muestra una lista de tokens pertenecientes al usuario autenticado
     *
     * @param \Illuminate\Http\Request $request
     * @return Json
     */
    public function index(Request $request)
    {
        $tokens = $request->user()->tokens;

        return $this->showAll($tokens, 200);
    }

    /**
     * Crea un nuevo TOken
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Json
     */
    public function store(Request $request)
    {
        $token = $request->user()->createToken($request->user()->email . "|" . $_SERVER['HTTP_USER_AGENT']);

        return response()->json(['token' => 'Bearer ' . $token->plainTextToken], 201);
    }

    /**
     * @param \Illiminate\Http\Request $request
     * @return Json
     */
    public function destroyAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->message('Los Tokens fueron revocados.', 200);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Integer $id
     * @return Json
     */
    public function destroy(Request $request, $id)
    {
        try {

            $token = $request->user()->tokens()->where('id', $id)->first();

            $token->delete();

            return $this->message('El token ha sido revocado.', 201);

        } catch (Error $e) {

            throw new ReportError("Error al procesar la petición", 404);
        }
    }
}
