<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Fish;
use Illuminate\Http\JsonResponse;

class FishController extends Controller
{
    private $fishModel;

    public function __construct()
    {
        $this->fishModel = new Fish;
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) :JsonResponse
    {
        if ($request->name) $this->fishModel->where("name", "like", "%{$request->name}%");

        return response()->json($this->fishModel->get(), 200);
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