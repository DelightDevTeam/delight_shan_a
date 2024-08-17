<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\Http\Requests\PlayerRequest;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PlayerController extends Controller
{
    private const PLAYER_ROLE = 5;

    public function index(): View
    {
        if (! Gate::allows('player_index')) {
            abort(403);
        }

        $query = User::query()->roleLimited()->with('wallet');

        $users = $query->hasRole(self::PLAYER_ROLE)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.player.index', compact('users'));
    }

    public function create(): View
    {
        $user_name = $this->generateRandomString();

        return view('admin.player.create', compact('user_name'));
    }

    // public function store(PlayerRequest $request): RedirectResponse
    // {
    //     if (! Gate::allows('player_create')) {
    //         abort(403);
    //     }

    //     $agent = Auth::user();

    //     $inputs = $request->validated();

    //     if (isset($inputs['amount']) && $inputs['amount'] > $agent->wallet->balance) {
    //         throw ValidationException::withMessages([
    //             'amount' => 'Insufficient balance for transfer.',
    //         ]);
    //     }
    //     $userPrepare = array_merge(
    //         $inputs,
    //         [
    //             'password' => Hash::make($inputs['password']),
    //             'agent_id' => Auth::id(),
    //             'type' => UserType::Agent,
    //         ]
    //     );

    //     $player = User::create($userPrepare);
    //     $player->roles()->sync(self::PLAYER_ROLE);

    //     if (isset($inputs['amount'])) {
    //         app(WalletService::class)->transfer($agent, $player, $inputs['amount'], TransactionName::CreditTransfer);
    //     }

    //     return redirect()->route('admin.player.index')
    //         ->with('success', 'Player created successfully');
    // }

    public function store(PlayerRequest $request): RedirectResponse
    {
        // Check for authorization
        if (! Gate::allows('player_create')) {
            abort(403);
        }

        $agent = Auth::user();
        $inputs = $request->validated();

        // Check if the agent has sufficient balance
        if (isset($inputs['amount']) && $inputs['amount'] > $agent->wallet->balance) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient balance for transfer.',
            ]);
        }

        // Prepare the data to create a user locally
        $userPrepare = array_merge(
            $inputs,
            [
                'password' => Hash::make($inputs['password']),
                'agent_id' => Auth::id(),
                'type' => UserType::Agent,
            ]
        );

        // Create the player in the local database
        $player = User::create($userPrepare);
        $player->roles()->sync(self::PLAYER_ROLE);

        // Transfer amount if provided
        if (isset($inputs['amount'])) {
            app(WalletService::class)->transfer($agent, $player, $inputs['amount'], TransactionName::CreditTransfer);
        }

        // Integrate the CreatePlayer API call
        $this->createPlayerInExternalSystem($player->user_name);

        return redirect()->route('admin.player.index')
            ->with('success', 'Player created successfully');
    }

    // private function createPlayerInExternalSystem(string $playerId)
    // {
    //     // Retrieve values from the config/game.php file
    //     $operatorId = config('game.api.operator_code'); // Get Operator ID from config/game.php
    //     $secretKey = config('game.api.secret_key');     // Get Secret Key from config/game.php
    //     $apiUrl = config('game.api.url').'CreatePlayer'; // Get API URL from config/game.php and append the endpoint
    //     $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');

    //     // Generate the signature using MD5 hashing
    //     $signature = md5('CreatePlayer'.$requestDateTime.$operatorId.$secretKey.$playerId);

    //     // Prepare the data to be sent in the request
    //     $data = [
    //         'OperatorId' => $operatorId,
    //         'RequestDateTime' => $requestDateTime,
    //         'Signature' => $signature,
    //         'PlayerId' => $playerId,
    //     ];

    //     try {
    //         // Send the request
    //         $response = Http::withHeaders([
    //             'Content-Type' => 'application/json',
    //             'Accept' => 'application/json',
    //         ])->post($apiUrl, $data);

    //         if ($response->successful()) {
    //             return $response->json(); // Return the JSON response from the API
    //         }

    //         return response()->json([
    //             'error' => 'API request failed',
    //             'details' => $response->body(),
    //         ], $response->status());
    //     } catch (\Throwable $e) {
    //         // Handle unexpected exceptions
    //         return response()->json([
    //             'error' => 'An unexpected error occurred',
    //             'exception' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    private function createPlayerInExternalSystem(string $playerId)
    {
        // Retrieve values from the config/game.php file
        $operatorId = config('game.api.operator_code'); // Get Operator ID from config/game.php
        $secretKey = config('game.api.secret_key');     // Get Secret Key from config/game.php
        $apiUrl = config('game.api.url').'CreatePlayer'; // Get API URL from config/game.php and append the endpoint
        $requestDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');

        // Generate the signature using MD5 hashing
        $signature = md5('CreatePlayer'.$requestDateTime.$operatorId.$secretKey.$playerId);

        // Prepare the data to be sent in the request
        $data = [
            'OperatorId' => $operatorId,
            'RequestDateTime' => $requestDateTime,
            'Signature' => $signature,
            'PlayerId' => $playerId,
        ];

        // Log the request details for debugging
        Log::debug('CreatePlayer API request', [
            'url' => $apiUrl,
            'data' => $data,
        ]);

        try {
            // Send the request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($apiUrl, $data);

            // Log the response status and body
            Log::debug('CreatePlayer API response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                return $response->json(); // Return the JSON response from the API
            }

            return response()->json([
                'error' => 'API request failed',
                'details' => $response->body(),
            ], $response->status());
        } catch (\Throwable $e) {
            // Log the exception details
            Log::error('CreatePlayer API error', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Handle unexpected exceptions
            return response()->json([
                'error' => 'An unexpected error occurred',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id): View
    {
        if (! Gate::allows('player_edit')) {
            abort(403);
        }

        $player = User::find($id);

        return view('admin.player.edit', compact('player'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        if (! Gate::allows('player_edit')) {
            abort(403);
        }

        $user = User::find($id);
        $user->update([
            'user_name' => $request->user_name,
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->back()
            ->with('success', 'Player Updated successfully');
    }

    public function destroy(string $id)
    {
        //
    }

    public function ban($id): RedirectResponse
    {
        $user = User::find($id);
        $user->update(['status' => $user->status == 1 ? 2 : 1]);

        return redirect()->back()->with(
            'success',
            'User '.($user->status == 1 ? 'activated' : 'banned').' successfully'
        );
    }

    private function generateRandomString(): string
    {
        $randomNumber = mt_rand(10000000, 99999999);

        return 'SKM'.$randomNumber;
    }
}
