<?php

namespace App\Http\Controllers\Api\Live22;

use Illuminate\Http\Request;
use App\Services\GameService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GameLoginController extends Controller
{
    protected $gameService;

    // Inject the GameService into the controller
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    // Method to handle game login
    public function login(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'game_code' => 'required|string',
            'launch_demo' => 'sometimes|boolean',
        ]);

        // Get the authenticated user's ID
        $playerId = Auth::user()->id;
        
        // Call the gameLogin method from the GameService
        $response = $this->gameService->gameLogin(
            $playerId,
            $validated['game_code'],
            $request->ip(), // Automatically get the client's IP address
            $request->input('launch_demo', false) // Default to false if not provided
        );

        // Return the response
        return response()->json($response);
    }
}
