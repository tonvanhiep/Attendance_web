@extends('admin.layout')


@section('title')
    Report
@endsection


@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/report.css');}}">
@endpush


@section('content')
    <h3 class="i-name">Attendance Report</h3>

    <form class="filter">

        <div class="filter-depart">
            <label for="office">Office</label>
            <div class="filter-input">
                <input type="text" list="office" name="office" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
                <!-- <i class="fa-solid fa-chevron-down"></i> -->
            </div>
            <datalist id="office">
                {{-- @foreach ($office as $item)
                    <option value="{{ $item->office_name }}"></option>
                @endforeach --}}
            </datalist>

            <label for="depart" style="margin-left: 30px">Department</label>
            <div class="filter-input">
                <input type="text" list="departs" name="department" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
                <!-- <i class="fa-solid fa-chevron-down"></i> -->
            </div>
            <datalist id="departs"></datalist>

            <div class="filter-date">
                <div class="get-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="submit" value="Filter" style="background:none; color:white">
                </div>
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
                <option value="10"></option>
                <option value="15"></option>
                <option value="20"></option>
                <option value="25"></option>
            </datalist>
        </form>
        <ul class="print">
            <li><a href="#">CSV</a></li>
            <li><a href="#">PDF</a></li>
            <li><a href="#">PRINT</a></li>
        </ul>
    </div>

    <div class="board">
        <table width="100%" class="table table-hover" style="margin-bottom: 0px">
            <thead>
                <tr>
                    <th>Number</th>
                    <td>Name</td>
                    <td>ID</td>
                    <td>Content</td>
                    <td>Date</td>
                    <td>Status</td>
                </tr>
            </thead>
            <tbody>
            @foreach ($list as $item )
                <tr>
                    {{-- <td class="name">
                        <h5>Ho Viet Cuong</h5>
                    </td>
                    <td class="id">
                        <p>1912820</p>
                    </td>
                    <td class="date">
                        <p>E, may cham cong tui bi sai kia</p>
                    </td>
                    <td class="date">
                        <p>9:00:22 14-10-2022</p>
                    </td>
                    <td class="workhour">
                        <p></p>
                    </td> --}}
                    <th><h5>{{ $count++ }}</h5></th>
                    <td>{{ $item->last_name . ' ' . $item->first_name }}</td>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->comment }}</td>
                    <td>{{ $item->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
