<?php

namespace App\Http\Controllers\ApiAdministrator\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdministratorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response|ResponseFactory|JsonResponse
    {
        /** @var int $size */
        $size = $request->input('size') ?? 25;
        /** @var string $search */
        $search = $request->input('search') ?? '';

        $administrators = Administrator::searchCriteria($search)->paginate($size);

        return response()->json($administrators);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response|ResponseFactory|JsonResponse
    {
        return response()->json();
    }

    /**
     * Display the specified resource.
     */
    public function show(Administrator $administrator): Response|ResponseFactory|JsonResponse
    {
        return response()->json();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Administrator $administrator): Response|ResponseFactory|JsonResponse
    {
        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Administrator $administrator): Response|ResponseFactory|JsonResponse
    {
        return response()->json();
    }
}
