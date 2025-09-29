<?php

namespace App\Traits;

trait ApiResponser
{
    /**
     * Build a success response.
     *
     * @param  int  $statusCode
     * @param  string|null  $message
     * @param  object|array  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(int $statusCode, ?string $message = null, object|array $data = [])
    {
        return response()->json([
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Build an error response.
     *
     * @param  int  $statusCode
     * @param  string|null  $message
     * @param  object|array  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(int $statusCode, ?string $message = null, object|array $data = [])
    {
        return response()->json([
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}

