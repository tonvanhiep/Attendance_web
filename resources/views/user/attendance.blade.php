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
            <br>
            <div class="row p-2">
                <div class="container">
                    <div class="form-group row">
                        <label for="date" class="col-form-label col-sm-1">From</label>
                        <div class="col-sm-3">
                            <input type="date" name="from" id="from" class="form-control input-sm"value="{{ $condition['from'] }}" max="{{ $condition['today'] }}">
                        </div>
                        <label for="date" class="col-form-label col-sm-1">To</label>
                        <div class="col-sm-3">
                            <input type="date" name="to" id="to" class="form-control input-sm" value="{{ $condition['to'] }}" max="{{ $condition['today'] }}">
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

        <div class="row my-5">
            <p id="url-pagination" hidden>{{ 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] }}</p>
            <div id="content">
                @include('user.pagination.attendance')
            </div>
        </div>
    </div>
@endsection
