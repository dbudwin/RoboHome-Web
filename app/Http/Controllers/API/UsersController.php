<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Common\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function publicId(Request $request): JsonResponse
    {
        $currentUser = $request->user();

        return response()->json(['public_id' => $currentUser->public_id]);
    }
}
