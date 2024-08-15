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
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Senior Id</label>
                  <input type="text" class="form-control" readonly>
                  </div>
                <div class="form-group">
                  <label>Name</label>
                  <input type="text" class="form-control">
                </div>
                <div class="form-group">
                  <label>Phone</label>
                  <input type="text" class="form-control">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Password</label>
                    <input type="text" class="form-control">
                </div>
                <div class="form-group">
                  <label>PaymentType</label>
                    <select name="" id="" class="form-control"></select>
                </div>
                <div class="form-group">
                    <label for=""></label>
                </div>
              </div>
            </div>

          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            Visit <a href="https://select2.github.io/">Select2 documentation</a> for more examples and information about
            the plugin.
          </div>
        </div>
      </div>
    </section>
@endsection
