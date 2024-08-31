<?php

namespace App\Http\Controllers\Api\Live22;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameLoginRequest;
use App\Models\Admin\GameType;
use App\Models\GameList;
use App\Models\Product;
use App\Services\GameService;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $response = $this->gameService->gameLogin(
            $request->game_code,
            $request->input('launch_demo', false)
        );

        return $this->success('Launch Game success', $response);
    }

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
