<?php
namespace App\Exceptions;

use App\Http\Controllers\Controller;
class Exception extends \Exception
{
    public static $tracerSpan;
    public function __construct($message, $code)
    {
        if (!empty(Controller::$tracerSpan)) {
            Controller::$tracerSpan->close();
        }

        Exception::$tracerSpan = app('Tracing')->startActiveSpan('Exception', [
            'tags' => [
                'Code' => $code,
                'Message' => $message
            ]
        ]);

        $this->message = $message;
        $this->code = $code;
    }
}
