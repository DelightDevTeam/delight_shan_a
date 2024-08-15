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
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PlayerController extends Controller
{
    private const  PLAYER_ROLE = 5;

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

    public function store(PlayerRequest $request): RedirectResponse
    {
        if (! Gate::allows('player_create')) {
            abort(403);
        }

        $agent = Auth::user();

        $inputs = $request->validated();

        if (isset($inputs['amount']) && $inputs['amount'] > $agent->wallet->balance) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient balance for transfer.',
            ]);
        }
        $userPrepare = array_merge(
            $inputs,
            [
                'password' => Hash::make($inputs['password']),
                'agent_id' => Auth::id(),
                'type' => UserType::Agent,
            ]
        );

        $player = User::create($userPrepare);
        $player->roles()->sync(self::PLAYER_ROLE);

        if (isset($inputs['amount'])) {
            app(WalletService::class)->transfer($agent, $player, $inputs['amount'], TransactionName::CreditTransfer);
        }

        return redirect()->route('admin.player.index')
            ->with('success', 'Player created successfully');
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
            'User ' . ($user->status == 1 ? 'activated' : 'banned') . ' successfully'
        );
    }

    private function generateRandomString(): string
    {
        $randomNumber = mt_rand(10000000, 99999999);

        return 'SKM'.$randomNumber;
    }
}
