<?php

namespace App\Http\Controllers;

use App\Exceptions\IDLException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    static $tracerSpan;

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {

        $span = app('Tracing')->startActiveSpan('IDL Validation', [
            'tags' => [
                'Rules' => json_encode($rules)
            ]
        ]);

        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new IDLException(json_encode($validator->errors()->getMessages()), '500000');
        }

        $result =  $this->extractInputFromRules($request, $rules);

        $span->close();

        return $result;
    }

    public function response($data = '')
    {
        $format = [
            'code' => 0,
            'message' => '',
            'data' => $data
        ];

        Controller::$tracerSpan->close();
        return $format;
    }

    public function __construct(Request $request)
    {
        Controller::$tracerSpan = app('Tracing')->startActiveSpan('Controller', [
            'tags' => [
                'Controller' => get_class($this),
                'Request' => json_encode($request->all()),
                'Uri' => $request->getUri(),
                'Path' => $request->getPathInfo()
            ]
        ]);
    }

}
