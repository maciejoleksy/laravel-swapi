<?php

namespace App\Contracts\Helpers;

class ApiResponse
{
    public function success(array $data = array())
    {
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function error($status, $message = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $status);
    }
}
