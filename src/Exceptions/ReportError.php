<?php

namespace Elyerr\ApiResponse\Exceptions;

use Exception; 
use Elyerr\ApiResponse\Assets\JsonResponser;

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
        return  $this->message($this->message, $this->code);
    }
}
