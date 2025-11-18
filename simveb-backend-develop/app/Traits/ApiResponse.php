<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponse
{
    protected function createdResponse($data)
    {
        return response()->json($data, ResponseAlias::HTTP_CREATED);
    }

    protected function successResponse($data)
    {
        return response()->json($data, ResponseAlias::HTTP_OK);
    }

    protected function errorResponse($message, $code): JsonResponse
    {
        return response()->json(['message' => $message], $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function formatedResponse(array $args)
    {
        $success = $args[0];
        $result = $args[1];

        if ($success) {
            return $this->successResponse($result);
        } else {
            return $this->errorResponse($result['message'], $result['code']);
        }
    }
}
