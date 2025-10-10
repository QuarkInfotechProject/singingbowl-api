<?php

namespace Modules\Shared\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserBaseController extends Controller
{
    public function successResponse($message = null, $data = array(), $statusCode = 200, $responseCode= 0, $headers = array(), $format= 'json')
    {
        return $this->response($message, $data, $statusCode, $responseCode, $headers, $format);
    }

    public function errorResponse($message = null, $data = array(), $statusCode = 500, $responseCode = -1, $headers = array(), $format = 'json')
    {
        return $this->response($message, $data, $statusCode, $responseCode, $headers, $format);
    }


    public function response($message = null, $res = array(), $statusCode = 200, $responseCode = 0, $headers = array(), $format = 'json')
    {
        $data['code'] = $responseCode;
        if ($message) {
            $data['message'] = $message;
        }
        if (!empty($res)) {
            $data['data'] = $res;
        }
        if ($format == 'json') {
            return new JsonResponse($data, $statusCode, $headers, false);
        }
        return new Response();
    }
}
