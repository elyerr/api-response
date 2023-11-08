<?php

namespace Elyerr\ApiResponse\Exceptions;

use Elyerr\ApiResponse\Assets\JsonResponser;
use Exception; 
use Illuminate\Support\Facades\View;

class ReportError extends Exception
{
    use JsonResponser;

    public $message;
    public $code;

    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return $request->wantsJson() ? $this->message($this->message, $this->code) :
        $this->report_error();

    }

    /**
     * reporta el error en una vista report de la carpeta error
     * @return \Illuminate\Support\Facades\View | response
     *
     */
    public function report_error()
    {
        return View::exists('error.report') ?
        view('error.report', ['code' => $this->code, 'message' => $this->message]) :
        response($this->message, $this->code);
    }

}
