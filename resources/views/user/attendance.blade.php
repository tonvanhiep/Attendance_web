@extends('user.layout')

@section('title')
    User - Attendance
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex">
            <div class="mr-auto p-2 col-md-4 mb-3">
                <label for="validationServer01">From date</label>
                <input type="date" class="form-control">
            </div>
            <div class="p-2 col-md-4 mb-3">
                <label for="validationServer02">To date</label>
                <input type="date" class="form-control">
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> </button>
            </div>
        </div>
        <form class="p-2" method="POST" action="{{ 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] }}">
            <label for="show-number">Show</label>
            <select class="custom-select" id="show-number">
                <option selected>Choose...</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
            </select>
        </form>

        <div class="row my-5">
            <h3 class="fs-4 mb-3 text-uppercase">Table Attendance</h3>
            <p id="url-pagination" hidden>{{ 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] }}</p>
            <div id="content">
                @include('user.pagination.attendance')
            </div>
        </div>
    </div>

@endsection
