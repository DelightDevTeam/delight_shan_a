<?php

namespace App\Http\Controllers\Api\Live22;

use App\Models\Product;
use App\Models\GameList;
use App\Enums\StatusCode;
use Illuminate\Http\Request;
use App\Services\GameService;
use App\Traits\HttpResponses;
use App\Models\Admin\GameType;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GameLoginRequest;

class GameLoginController extends Controller
{
    use HttpResponses;
    protected $gameService;

    // Inject the GameService into the controller
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function Gamelogin(GameLoginRequest $request)
{
    // Log incoming request data
    Log::info('Received game login request', $request->all());

    // Validate the required parameters
    $validated = $request->validate([
        'product_id' => 'required|integer',
        'game_type_id' => 'required|integer',
        'game_code' => 'required|string'
    ]);

    try {
        $response = $this->gameService->gameLogin(
            (int) $request->product_id,
            (int) $request->game_type_id,
            $request->game_code,
            (bool) $request->input('launch_demo', false)
        );

        return $this->success('Launch Game success', $response);
    } catch (\Throwable $e) {
        Log::error('Error processing game login', [
            'error' => $e->getMessage()
        ]);
        return $this->error('Game login failed', StatusCode::InternalServerError->value);
    }
}


    // public function Gamelogin(GameLoginRequest $request)
    // {
    //     $response = $this->gameService->gameLogin(
    //         $request->product_id,
    //         $request->game_type_id,
    //         $request->game_code,
    //         $request->input('launch_demo', false)
    //     );

    //     return $this->success('Launch Game success', $response);
    // }

    public function getGameList($productId, $gameTypeId)
    {
        $gamelist = Gamelist::where('product_id', $productId)->where('game_type_id', $gameTypeId)->get();

        return $this->success($gamelist);
    }

    public function getGameType()
    {
        $gameTypes = GameType::all();

        return $this->success($gameTypes);
    }

    public function getProductType($productId)
    {
        $products = Product::with('gameTypes')->where('id', $productId)->get();

        return $this->success($products);
    }

    public  function GetHasDemo()
    {
        $gameList = GameList::where('has_demo', 1)->get();

        return $this->success($gameList);
    }
}
