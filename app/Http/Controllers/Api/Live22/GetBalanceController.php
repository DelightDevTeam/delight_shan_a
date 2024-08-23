<?php

namespace App\Http\Controllers\Api\Live22;

use Illuminate\Http\Request;
use App\Services\GameService;
use App\Http\Controllers\Controller;

class GetBalanceController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function getBalance(Request $request)
    {
        $authToken = $request->input('auth_token');
        
        $response = $this->gameService->getBalance($authToken);

        return response()->json($response);
    }
}
