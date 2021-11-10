<?php

namespace App\Traits;

trait ApiTrait
{
    // Return Success
    public function success($message = null, $data = null)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ]);
    }

    // Return Fail
    public function fail($message = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ]);
    }
}
