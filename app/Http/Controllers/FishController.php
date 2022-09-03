<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Fish;
use Illuminate\Http\JsonResponse;

class FishController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() :JsonResponse
    {
        return response()->json(Fish::get(), 200);
        // return response()->json([], 200);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) :JsonResponse
    {
        $fish = Fish::find($id);
        if(is_null($fish)) return response()->json(null, 404);

        return response()->json($fish, 200);
    }
}