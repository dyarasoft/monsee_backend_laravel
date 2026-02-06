<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    /**
     * Membangun respon sukses.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Membangun respon error.
     *
     * @param string|null $message
     * @param int $statusCode
     * @param mixed|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(?string $message = null, int $statusCode, $errors = null): JsonResponse
    {
        $response = [
            'status_code' => $statusCode,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}

