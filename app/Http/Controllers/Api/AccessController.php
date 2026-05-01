<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function __invoke(Request $request, AccessService $access): JsonResponse
    {
        return response()->json($access->check($request->user()));
    }
}
