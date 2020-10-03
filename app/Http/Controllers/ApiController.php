<?php

namespace App\Http\Controllers;

use App\Services\VmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    /**
     * All VM logic
     */
    private $vmService;

    public function __construct()
    {
        $this->vmService = new VmService();
    }

    /**
     * @return JsonResponse
     * @api {get}
     * @apiDescription gen index data
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->vmService->index()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @api {post}
     * @apiDescription pay drink
     */
    public function pay(Request $request): JsonResponse
    {

        $result = $this->vmService->moveCoinToVM($request->all());

        return response()->json([
            'success' => $result,
            'data' => $this->vmService->index()
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @api {post}
     * @apiDescription buy drink
     */
    public function buy(Request $request): JsonResponse
    {

        $result = $this->vmService->buy($request->all());

        return response()->json([
            'success' => $result,
            'data' => $this->vmService->index()
        ]);
    }

    /**
     * @return JsonResponse
     * @api {get}
     * @apiDescription get change from VM
     */
    public function getChange(): JsonResponse
    {

        $result = $this->vmService->getChange();

        return response()->json([
            'success' => $result,
            'data' => [],
        ]);
    }

    /**
     * @return JsonResponse
     * @api {get}
     * @apiDescription reset to VM demo data
     */
    public function resetDemoData(): JsonResponse
    {

        $result = $this->vmService->resetDemoData();

        if ($result == true) {
            return response()->json([
                'success' => true,
                'data' => $this->vmService->index()
            ]);
        }
    }
}
