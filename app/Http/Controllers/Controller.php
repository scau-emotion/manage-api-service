<?php

namespace App\Http\Controllers;

use App\Exceptions\IDLException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new IDLException(json_encode($validator->errors()->getMessages()), '500000');
        }

        return $this->extractInputFromRules($request, $rules);
    }

    public function response($data = '')
    {
        $format = [
            'code' => 0,
            'message' => '',
            'data' => $data
        ];

        return $format;
    }

}
