@extends('layouts.master')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Agent Lists</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                    <li class="breadcrumb-item active">Agent List</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card-header">
                    <a href="{{ route('admin.agent.create')}}" class="btn btn-primary">Create</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <table id="mytable" class="table table-bordered table-hover">
                            <thead>
                           <tr>
                               <th>User Name</th>
                               <th>Game Name</th>
                               <th>Bet Amount</th>
                               <th>Valid Bet Amount</th>
                               <th>Payout</th>
                               <th>Win/Lose</th>
                               <th>Result Type</th>
                           </tr>
                       </thead>
                       <tbody>
                           @forelse($results as $result)
                           <tr>
                               {{-- <td>{{ $result->user_name }}</td>
                               <td>{{ $result->game_name }}</td>
                               <td>{{ $result->bet_amount }}</td>
                               <td>{{ $result->valid_bet_amount }}</td>
                               <td>{{ $result->payout }}</td>
                               <td>{{ $result->win_lose }}</td>
                               <td>{{ $result->result_type }}</td> --}}
                                <td>{{ $result->user_name }}</td>
                                <td>{{ $result->game_name }}</td>
                                <td>{{ number_format($result->total_bet_amount, 4) }}</td>
                                <td>{{ number_format($result->total_valid_bet_amount, 4) }}</td>
                                <td>{{ number_format($result->total_payout, 4) }}</td>
                                <td>{{ number_format($result->total_win_lose, 4) }}</td>
                                <td>{{ $result->result_type }}</td>
                           </tr>
                           @empty
                           <tr>
                               <td colspan="7">No game results found.</td>
                           </tr>
                           @endforelse
                       </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</section>
@endsection
