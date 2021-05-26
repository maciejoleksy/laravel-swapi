<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
