@extends('layouts.master')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Banner Text</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="{{route('admin.bannerText.create')}}" class="btn btn-success">Create</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-widget="expandable-table" aria-expanded="false">
                                <td class="text-sm font-weight-normal">{{ $bannerText->id }}</td>
                                <td>
                                    {{$bannerText->text}}
                                </td>
                                <td class="project-action">
                                    <a href="{{ route('admin.bannerText.edit', $bannerText->id) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i>Edit</a>
                                    <a href="{{ route('admin.bannerText.edit', $bannerText->id) }}" class="btn btn-danger btn-sm" onclick="event.preventDefault();
                                                     document.getElementById('bannerText-form').submit();"><i class="fas fa-trash"></i>Delete</a>
                                    <form id="bannerText-form" action="{{ route('admin.bannerText.destroy', $bannerText->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection