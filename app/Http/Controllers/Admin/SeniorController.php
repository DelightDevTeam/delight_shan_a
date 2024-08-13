<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionName;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\SeniorRequest;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class SeniorController extends Controller
{
    private const  SENIOR_ROLE = 2;

    public function index()
    {
        $query = User::query()->roleLimited()->with('wallet');

        $users = $query->hasRole(self::SENIOR_ROLE)
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.senior.index', compact('users'));
    }

    public function create()
    {
        $user_name = $this->generateRandomString();

        return view('admin.senior.create', compact('user_name'));
    }

    public function store(SeniorRequest $request)
    {

        $admin = Auth::user();
        dd($admin->balanceFloat);
        $inputs = $request->validated();

        if (isset($inputs['amount']) && $inputs['amount'] > $admin->balanceFloat) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient balance for transfer.',
            ]);
        }
        $userPrepare = array_merge(
            $inputs,
            [
                'password' => Hash::make($inputs['password']),
                'agent_id' => Auth::id(),
                'type' => UserType::Senior,
            ]
        );

        $senior = User::create($userPrepare);
        $senior->roles()->sync(self::SENIOR_ROLE);

        if (isset($inputs['amount'])) {
            app(WalletService::class)->transfer($admin, $senior, $inputs['amount'], TransactionName::CreditTransfer);
        }

        return redirect()->back()
            ->with('success', 'Senior created successfully');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function ban($id)
    {
        $user = User::find($id);
        $user->update(['status' => $user->status == 1 ? 2 : 1]);

        return redirect()->back()->with(
            'success',
            'User ' . ($user->status == 1 ? 'activated' : 'banned') . ' successfully'
        );
    }

    private function generateRandomString()
    {
        $randomNumber = mt_rand(10000000, 99999999);

        return 'SKM'.$randomNumber;
    }
}
