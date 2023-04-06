@extends('user.layout')

@section('title')
    User - Attendance
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4">
        <form action="{{ route('user.attendance.list') }}" method="GET" class="my-5">
            @csrf
            <br>
            <div class="row p-2">
                <div class="container">
                    <div class="form-group row">
                        <label for="date" class="col-form-label col-sm-1">From</label>
                        <div class="col-sm-3">
                            <input type="date" name="fromDate" id="fromDate" class="form-control input-sm" value="{{ $request->input('fromDate') }}">
                        </div>
                        <label for="date" class="col-form-label col-sm-1">To</label>
                        <div class="col-sm-3">
                            <input type="date" name="toDate" id="toDate" class="form-control input-sm" value="{{ $request->input('toDate') }}">
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-primary" name="seach" title="search">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
