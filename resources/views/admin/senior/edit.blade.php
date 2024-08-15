@extends('layouts.master')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Senior</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
                    <li class="breadcrumb-item active">create</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Create Senior</h3>
            </div>
            <form method="POST" action="{{ route('admin.senior.update',$senior->id) }}">
                  @csrf
                  @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SeniorId<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="user_name" value="{{$senior->user_name}}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ $senior->name }}">
                            </div>
                            <div class="form-group">
                                <label>Phone<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" value="{{ $senior->phone }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- <div class="form-group">
                                <label>Password<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="password" value="{{old('password')}}">
                            </div> --}}
                            <div class="form-group">
                                <label>Amount</label>
                                <span class="badge badge-success">Max:{{ number_format(optional(auth()->user()->wallet)->balance, 2) }}</span>
                                <input type="text" class="form-control" name="amount" value="{{ $senior->wallet->balance }}">
                            </div>

                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update Senior</button>
                </div>
            </form>
        </div>

    </div>
    </div>
</section>
@endsection