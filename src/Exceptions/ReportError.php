<?php

namespace Elyerr\ApiResponse\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Elyerr\ApiResponse\Assets\JsonResponser;

class ReportError extends Exception
{
    use JsonResponser;

    /**
     * message
     * @var string
     */
    public $message;

    /**
     * code
     * @var
     */
    public $code;

    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * Render exception
     * @param mixed $request
     * @return mixed|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function render($request)
    {
        $user = Auth::user();

        $logData = [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => optional($request->route())->getName(),
            'error' => $this->message,
            'code' => $this->code,
            'trace' => $this->getTraceAsString(),
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ];

        Log::error('Exception captured', $logData);

        if ($request->wantsJson()) {
            return $this->message($this->message, $this->code);
        }

        throw new Exception(__($this->message), $this->code);
    }
}
