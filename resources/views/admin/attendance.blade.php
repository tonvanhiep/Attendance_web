@extends('admin.layout')


@section('title')
    Attendance
@endsection


@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/report.css');}}">
    <link rel="stylesheet" href="{{asset('assets/css/attendance.css');}}">
@endpush


@section('content')
    <h3 class="i-name">Real-time Attendance</h3>

    <form class="filter">

        <div class="filter-depart">
            <label for="depart">Office</label>
            <div class="filter-input">
                <select name="office" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
                    <option value="">All</option>
                    @foreach ($office as $item)
                        <option value="{{ $item->id }}" @if ($condition['office'] == $item->id) selected @endif>{{ $item->office_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="filter-date">
            <label for="start-date">From date</label>
            <div class="filter-input">
                <input type="date" name="from" value="{{ $condition['from'] }}" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
            </div>
            <label for="end-date" style="margin-left: 50px">To date</label>
            <div class="filter-input">
                <input type="date" name="to" value="{{ $condition['to'] }}" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
            </div>
            <div class="get-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="submit" value="Filter" style="background:none; color:white">
            </div>
        </div>
    </form>

    <div class="tool-board">
        <form id="show-form" class="show" method="POST" action="{{ 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']}}">
            <label for="show-text">Show</label>
            <div class="show-input">
                <input id="input-show" type="text" list="nrows" size="10" formtarget="" name="show" value="{{ $pagination['perPage'] }}">
                <!-- <i class="fa-solid fa-chevron-down"></i> -->
                <datalist id="nrows">
                    <option value="25"></option>
                    <option value="50" selected></option>
                    <option value="100"></option>
                    <option value="200"></option>
                </datalist>
            </div>
        </form>
        <ul class="print">
            <li><a href="{{ route('admin.attendance.exportcsv') . (isset($_SERVER['QUERY_STRING']) == true ? ('?' . $_SERVER['QUERY_STRING']) : '') }}">CSV</a></li>
            <li><a href="{{ route('admin.attendance.exportpdf') . (isset($_SERVER['QUERY_STRING']) == true ? ('?' . $_SERVER['QUERY_STRING']) : '') }}">PDF</a></li>
            <li><a href="#">PRINT</a></li>
        </ul>
    </div>
    <p id="url-pagination" hidden>{{ 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']}}</p>
    <div id="content">
        @include('admin.pagination.attendance')
    </div>

@endsection