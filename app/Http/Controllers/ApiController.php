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
     * @api {get}
     * @apiDescription gen index data
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->vmService->index()
        ]);
    }

    /**
     * @api {post}
     * @apiDescription pay drink
     * @param Request $request
     * @return JsonResponse
     */
    public function pay(Request $request)
    {

        $result = $this->vmService->moveCoinToVM($request->all());

        return response()->json([
            'success' => $result,
            'data' => $this->vmService->index()
        ]);

    }

    /**
     * @api {post}
     * @apiDescription buy drink
     * @param Request $request
     * @return JsonResponse
     */
    public function buy(Request $request){

        $result = $this->vmService->buy($request->all());

        return response()->json([
            'success' => $result,
            'data' => $this->vmService->index()
        ]);
    }

    /**
     * @api {get}
     * @apiDescription get change from VM
     * @return JsonResponse
     */
    public function getChange(){

        $result = $this->vmService->getChange();

        return response()->json([
            'success' => $result,
            'data' => [],
        ]);
    }

    /**
     * @api {get}
     * @apiDescription reset to VM demo data
     * @return JsonResponse
     */
    public function resetDemoData(){

        $result = $this->vmService->resetDemoData();

        if($result == true){
            return response()->json([
                'success' => true,
                'data' => $this->vmService->index()
            ]);
        }
    }
}
