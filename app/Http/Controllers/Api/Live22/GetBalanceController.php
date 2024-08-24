<?php 
namespace App\Http\Controllers\Api\Live22;

use App\Enums\StatusCode;
use Illuminate\Http\Request;
use App\Services\GameService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\SlotWebhookValidator;
use App\Http\Requests\SlotWebhookRequest;
use App\Services\Slot\SlotWebhookService;

class GetBalanceController extends Controller
{
    
    public function getBalance(SlotWebhookRequest $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request using the SlotWebhookValidator
            $validator = SlotWebhookValidator::make($request)->validate();

            // If validation fails, return the error response
            if ($validator->fails()) {
                return $validator->getResponse();
            }

            // Get the balance from the member's wallet
            $balance = $request->getMember()->balanceFloat;

            DB::commit();

            // Build and return the success response
            return SlotWebhookService::buildResponse(
                StatusCode::OK,
                $balance,
                $balance
            );
        } catch (\Exception $e) {
            DB::rollBack();

            // Handle any unexpected exceptions and return an error message
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    // protected $gameService;

    // public function __construct(GameService $gameService)
    // {
    //     $this->gameService = $gameService;
    // }

    // public function getBalance(Request $request)
    // {
    //     // Retrieve the PlayerId and AuthToken directly from the request
    //     $playerId = $request->input('PlayerId');
    //     $authToken = $request->input('AuthToken');

    //     if (!$playerId) {
    //         return response()->json(['error' => 'PlayerId is missing from the request.'], 400);
    //     }

    //     if (!$authToken) {
    //         return response()->json(['error' => 'AuthToken is missing from the request.'], 400);
    //     }

    //     // Retrieve the user's wallet balance if the user is authenticated
    //     $user = $request->user();
    //     if ($user && $user->wallet) {
    //         Log::info('User Wallet Balance:', ['balance' => $user->wallet->balance]);
    //     } else {
    //         Log::warning('No wallet associated with the user');
    //     }

    //     // Pass the token and PlayerId to the GameService's getBalance method
    //     $response = $this->gameService->getBalance($authToken, $playerId);

    //     $balance = $user ? $user->wallet->balance : null;

    //     // Check if the API request was successful
    //     if ($response instanceof \Illuminate\Http\JsonResponse) {
    //         $responseData = $response->getData(true);

    //         // Assuming the API response contains the 'Balance' and you want to replace it with your site's wallet balance
    //         if (isset($responseData['Balance'])) {
    //             $responseData['Balance'] = $balance;
    //         }

    //         // Build the final response to match the expected structure
    //         $finalResponse = [
    //             'Status' => 200,
    //             'Description' => 'Success',
    //             'ResponseDateTime' => now()->setTimezone('UTC')->format('Y-m-d H:i:s'),
    //             'Balance' => $responseData['Balance'] ?? null,
    //         ];

    //         return response()->json($finalResponse);
    //     }

    //     // Handle the case where the API request fails
    //     return response()->json([
    //         'error' => 'API request failed',
    //         'details' => $response->getData(true),
    //     ], 500);
    // }
}
