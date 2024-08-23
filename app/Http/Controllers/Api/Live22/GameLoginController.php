<?php

namespace App\Http\Controllers\Api\Live22;

use Illuminate\Http\Request;
use App\Services\GameService;
use App\Http\Controllers\Controller;

class GameLoginController extends Controller
{
    protected $gameService;

    // Inject the GameService into the controller
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    // Method to handle game login
    public function Gamelogin(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'player_id' => 'required|string',
            'game_code' => 'required|string',
            'launch_demo' => 'sometimes|boolean',
        ]);

        // Call the gameLogin method from the GameService
        $response = $this->gameService->gameLogin(
            $validated['player_id'],
            $validated['game_code'],
            $request->ip(), // Automatically get the client's IP address
            $request->input('launch_demo', true) // Default to false if not provided
        );

        // Return the response
        return response()->json($response);
    }
}
