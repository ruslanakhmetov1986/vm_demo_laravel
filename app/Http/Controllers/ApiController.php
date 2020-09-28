<?php

namespace App\Http\Controllers;

use App\Services\VmService;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    private $vmService;

    public function __construct()
    {
        $this->vmService = new VmService();
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->vmService->index()
        ]);
    }

    public function pay(Request $request)
    {

        $result = $this->vmService->moveCoinToVM($request->all());

        return response()->json([
            'success' => $result,
            'data' => $this->vmService->index()
        ]);

    }

    public function buy(Request $request){

        $result = $this->vmService->buy($request->all());

        return response()->json([
            'success' => $result,
            'data' => $this->vmService->index()
        ]);
    }

    public function getChange(){

        $result = $this->vmService->getChange();

        return response()->json([
            'success' => $result,
            'data' => [],
        ]);
    }

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
