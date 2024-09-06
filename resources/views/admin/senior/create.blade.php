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
                    <li class="breadcrumb-item active">create senior</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card">
             <div class="card-header">
                <h3 class="card-title">
                    <span>
                   <a href="{{ route('admin.senior.index') }}" class="btn btn-success">
                       <i class="fas fa-arrow-left" style="font-size: 20px;"></i> Back
                   </a>
            </span>
            </h3>
            </div>
            <form action="{{route('admin.senior.store')}}" method="POST">
                @csrf
                <div class="card-body mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SeniorId<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="user_name" value="{{$user_name}}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{old('name')}}">
                            </div>
                            <div class="form-group">
                                <label>Phone<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone" value="{{old('phone')}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="password" value="{{old('password')}}">
                            </div>
                            <div class="form-group">
                                <label>Amount</label>
                                <span class="badge badge-success">Max:{{ number_format(optional(auth()->user()->wallet)->balance, 2) }}</span>
                                <input type="text" class="form-control" name="amount" value="{{old('amount')}}">
                            </div>

                        </div>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>

    </div>
    </div>
</section>
@endsection
