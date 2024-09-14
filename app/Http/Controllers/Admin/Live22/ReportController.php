<?php

namespace App\Http\Controllers\Admin\Live22;

use App\Http\Controllers\Controller;
use App\Models\Admin\GameResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
{
    // Get game results grouped by user_id, ordered by id desc, including user name
     $results = GameResult::select(DB::raw('MAX(game_results.id) as id'), 'game_results.user_id', 'users.name as user_name', 'game_results.bet_amount', 'game_results.valid_bet_amount', 'game_results.payout', 'game_results.win_lose', 'game_results.game_name', 'game_results.result_type')
        ->join('users', 'game_results.user_id', '=', 'users.id')
        ->groupBy('game_results.user_id', 'users.name', 'game_results.bet_amount', 'game_results.valid_bet_amount', 'game_results.payout', 'game_results.win_lose', 'game_results.game_name', 'game_results.result_type')
        ->orderBy('id', 'desc')
        ->get();


    // Pass the results to the view
    return view('admin.live_22.report.index', compact('results'));
}

}