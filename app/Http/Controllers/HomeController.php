<?php

namespace App\Http\Controllers;

use App\Enums\TransactionName;
use App\Models\Admin\UserLog;
use App\Models\SeamlessTransaction;
use App\Models\User;
use App\Services\WalletService;
use App\Settings\AppSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('Admin');
        $role = $user->roles->pluck('title');

        $getUserCounts = function ($roleTitle) use ($isAdmin, $user) {
            return User::whereHas('roles', function ($query) use ($roleTitle) {
                $query->where('title', '=', $roleTitle);
            })->when(! $isAdmin, function ($query) use ($user) {
                $query->where('agent_id', $user->id);
            })->count();
        };

        $totalBalance = DB::table('users')->join('wallets', 'wallets.user_id', '=', 'users.id')
            ->where('agent_id', Auth::id())->select(DB::raw('SUM(wallets.balance) as balance'))->first();

        $deposit = $user->transactions()->with('targetUser')
            ->select(DB::raw('SUM(transactions.amount) as amount'))
            ->where('transactions.type', 'deposit')
            ->first();

        $withdraw = $user->transactions()->with('targetUser')
            ->select(DB::raw('SUM(transactions.amount) as amount'))
            ->where('transactions.type', 'withdraw')
            ->first();

        $agent_count = $getUserCounts('Agent');
        $player_count = $getUserCounts('Player');

        return view('admin.dashboard', compact(
            'agent_count',
            'player_count',
            'user',
            'deposit',
            'withdraw',
            'totalBalance',
            'role'
        ));
    }

    public function balanceUp(Request $request)
    {
        abort_if(
            Gate::denies('admin_access'),
            Response::HTTP_FORBIDDEN,
            '403 Forbidden |You cannot  Access this page because you do not have permission'
        );
        $request->validate([
            'balance' => 'required|numeric',
        ]);

        app(WalletService::class)->deposit($request->user(), $request->balance, TransactionName::CapitalDeposit);

        return back()->with('success', 'Add New Balance Successfully.');
    }

    public function logs($id)
    {
        $logs = UserLog::with('user')->where('user_id', $id)->get();

        return view('admin.login_logs.index', compact('logs'));
    }
}
