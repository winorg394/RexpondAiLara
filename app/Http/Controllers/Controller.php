<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function reply($success, $message, $data = null)
    {
        return response()->json([
            'success' => $success,//true or false
            'message' => $message,
            'data' => $data,
        ]);
    }
}
