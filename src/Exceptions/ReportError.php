<?php

namespace Elyerr\ApiResponse\Exceptions;

use Elyerr\ApiResponse\Assets\JsonResponser;
use Exception;
use Illuminate\Support\Facades\View;

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
     * render exception
     * @param mixed $request
     * @return response|View|\Elyerr\ApiResponse\Assets\Json
     */
    public function render($request)
    {
        return $request->wantsJson() ? $this->message($this->message, $this->code) :
            $this->report_error();

    }

    /**
     * Report the error
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function report_error()
    {
        return View::exists('error.report') ?
            view('error.report', ['code' => $this->code, 'message' => $this->message]) :
            response($this->message, $this->code);
    }
}
