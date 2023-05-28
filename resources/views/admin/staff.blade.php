@extends('admin.layout')


@section('title')
    Staff List
@endsection


@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/staff.css');}}">
    <link rel="stylesheet" href="{{asset('assets/css/report.css');}}">
@endpush


@section('content')
    <h3 class="i-name">Staff List</h3>

    <form class="filter">

        <div class="filter-depart" style="display: flex; flex-wrap: wrap;">
            <label for="office">Office</label>
            <div class="filter-input">
                <select name="office" style="font-style: 14px; padding: 5px 10px; border-radius:5px; min-width:150px;">
                    <option value="">All</option>
                    @foreach ($office as $item)
                        <option value="{{ $item->id }}" @if ($condition['office'] == $item->id) selected @endif>{{ $item->office_name }}</option>
                    @endforeach
                </select>
            </div>

            <label for="depart" style="margin-left: 30px">Department</label>
            <div class="filter-input">
                <select name="depart" style="font-style: 14px; padding: 5px 10px; border-radius:5px; min-width:150px;">
                    <option value="">All</option>
                    {{-- @foreach ($office as $item)
                        <option value="{{ $item->office_name }}"></option>
                    @endforeach --}}
                </select>
            </div>
        </div>

        <div class="filter-date" style="display: flex; flex-wrap: wrap;">
            <label for="status">Status</label>
            <div class="status">
                <select name="status" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
                    <option value="" selected>All</option>
                    <option value="1" @if ($condition['status'] == 1) selected @endif>Active</option>
                    <option value="2" @if ($condition['status'] == 2) selected @endif>Maternity Leave</option>
                    <option value="0" @if ($condition['status'] == 0) selected @endif>Quit job</option>
                </select>
            </div>

            <label for="sort" style="margin-left: 50px">Sort By</label>
            <div class="status">
                <select name="sort" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
                    <option value="1" @if ($condition['sort'] == 1) selected @endif>ID Z->A</option>
                    <option value="2" @if ($condition['sort'] == 2) selected @endif>ID A->Z</option>
                    <option value="3" @if ($condition['sort'] == 3) selected @endif>Name A->Z</option>
                    <option value="4" @if ($condition['sort'] == 4) selected @endif>Name Z->A</option>
                </select>
            </div>

            <div class="get-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="submit" value="Filter" style="background:none; color:white">
            </div>
        </div>
    </form>

    <div class="tool-board" style="display: flex; flex-wrap: wrap;">
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
            <li><a href="{{ route('admin.staff.exportcsv') . (isset($_SERVER['QUERY_STRING']) == true ? ('?' . $_SERVER['QUERY_STRING']) : '') }}">CSV</a></li>
            <li><a href="{{ route('admin.staff.exportpdf') . (isset($_SERVER['QUERY_STRING']) == true ? ('?' . $_SERVER['QUERY_STRING']) : '') }}">PDF</a></li>
            <li><a href="#">PRINT</a></li>
        </ul>
        <div class="btn-add">
            <i class="fa-solid fa-plus"></i>
            <a href="{{route('admin.staff.create')}}">New Employee</a>
        </div>
    </div>

    <div class="search-table">
        <div>
            <form>
                <button type="submit" style="background: none"><i class="fa-solid fa-magnifying-glass"></i></button>
                <input type="text" placeholder="Search" name="search" size="10" value={{ $condition['search'] }}>
            </form>

        </div>
    </div>

    <p id="url-pagination" hidden>{{ 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']}}</p>
    <div id="content">
        @include('admin.pagination.staff')
    </div>
@endsection
