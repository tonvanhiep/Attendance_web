@extends('user.layout')

@section('title')
    User - Attendance
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user/style.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4">
        <form action="{{ route('user.attendance.list') }}" method="GET" class="my-2">
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
        <form class="p-2" id="show-form" method="POST" action="{{ 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] }}">
            @csrf
            <label for="show-number">Show</label>
            <input id="input-show" type="text" list="nrows" size="10" formtarget="" name="show" value="{{ $pagination['perPage'] }}">
                <!-- <i class="fa-solid fa-chevron-down"></i> -->
                <datalist id="nrows">
                    <option value="25"></option>
                    <option value="50" selected></option>
                    <option value="100"></option>
                    <option value="200"></option>
                </datalist>
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
