<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TranslateController extends Controller
{
    public function getMessage()
    {
        return response()->json([
            'message' => __('messages.welcome')
        ]);
    }
}
