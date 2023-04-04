@extends('admin.layout')


@section('title')
    Timesheet
@endsection


@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/report.css');}}">
@endpush


@section('content')
    <h3 class="i-name">Timesheet</h3>

    <form class="filter">

        <div class="filter-depart">
            <label for="office">Office</label>
            <div class="filter-input">
                <input type="text" list="office" name="office" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
                <!-- <i class="fa-solid fa-chevron-down"></i> -->
            </div>
            <datalist id="office">
                @foreach ($office as $item)
                    <option value="{{ $item->office_name }}"></option>
                @endforeach
            </datalist>

            <label for="depart" style="margin-left: 30px">Department</label>
            <div class="filter-input">
                <input type="text" list="departs" name="department" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
                <!-- <i class="fa-solid fa-chevron-down"></i> -->
            </div>
            <datalist id="departs"></datalist>
        </div>

        <div class="filter-date">
            <label for="start-date">From date</label>
            <div class="filter-input">
                <input type="date" name="from" value="{{ $condition['from'] }}" max="{{ $condition['today'] }}" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
            </div>
            <label for="end-date" style="margin-left: 50px">To date</label>
            <div class="filter-input">
                <input type="date" name="to" value="{{ $condition['to'] }}" max="{{ $condition['today'] }}" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
            </div>
            <div class="get-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="submit" value="Filter" style="background:none; color:white">
            </div>
        </div>
    </form>

    <div class="tool-board">
        <form class="show">
            <label for="show-text">Show</label>
            <div class="show-input">
                <input type="text" list="nrows" size="10" name="show-text">
                <!-- <i class="fa-solid fa-chevron-down"></i> -->
            </div>
            <datalist id="nrows">
                <option value="25"></option>
                <option value="50"></option>
                <option value="75"></option>
                <option value="100"></option>
            </datalist>
        </form>
        <ul class="print">
            <li><a href="#">CSV</a></li>
            <li><a href="#">PDF</a></li>
            <li><a href="#">PRINT</a></li>
        </ul>
    </div>

    <p id="url-pagination" hidden>{{ 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']}}</p>
    <div id="content">
        @include('admin.pagination.timesheet')
    </div>
@endsection
