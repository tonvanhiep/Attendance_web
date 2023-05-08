@extends('admin.layout')


@section('title')
    Timesheet
@endsection


@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/report.css');}}">
@endpush


@section('content')
    <h3 class="i-name"> </h3>

    <form class="filter">
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

    {{-- <div class="tool-board">
        <ul class="print">
            <li><a href="{{ route('admin.attendance.exportcsv') . (isset($_SERVER['QUERY_STRING']) == true ? ('?' . $_SERVER['QUERY_STRING']) : '') }}">CSV</a></li>
            <li><a href="{{ route('admin.attendance.exportpdf') . (isset($_SERVER['QUERY_STRING']) == true ? ('?' . $_SERVER['QUERY_STRING']) : '') }}">PDF</a></li>
            <li><a href="#">PRINT</a></li>
        </ul>
    </div> --}}

    <div id="content">
        <h5 style="text-align: center">Overview</h5>
        <div class="board">
            <table width="100%" class="table table-hover" style="margin-bottom: 0px">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>ID</td>
                        <td>Office</td>
                        <td>Department</td>
                        <td>Present</td>
                        <td>Late</td>
                        <td>Early</td>
                        <td>Off</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody>
                    <tr class='clickable-row' data-href='{{ route('admin.timesheet.detail', ['id' => $overview['id']]) }}'>
                        <td class="name">
                            <p class="fw-bold">{{ $overview['last_name'] . ' ' . $overview['first_name'] }}</p>
                        </td>
                        <td class="id">
                            <p>{{ $overview['id'] }}</p>
                        </td>
                        <td class="office">
                            <p>{{ $overview['office_name'] }}</p>
                        </td>
                        <td class="department">
                            <p>{{ $overview['department'] }}</p>
                        </td>
                        <td class="present">
                            <p>{{ $overview['present'] }}</p>
                        </td>
                        <td class="late">
                            <p>{{ $overview['late'] }}</p>
                        </td>
                        <td class="early">
                            <p>{{ $overview['early'] }}</p>
                        </td>
                        <td class="off">
                            <p>{{ $overview['off'] }}</p>
                        </td>
                        <td class="total">
                            <p>{{ $overview['total'] }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h5 style="text-align: center">Detail</h5>
        <div class="board">
            <table width="100%" class="table table-hover" style="margin-bottom: 0px">
                <thead>
                    <tr>
                        <td></td>
                        <td>Date</td>
                        <td>Check In</td>
                        <td>Check Out</td>
                        <td>Result</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($arrTimesheetDetail as $key => $item)
                        <tr @if ($item['day_off']) style="color: lightgrey" @endif>
                            <td class="weekday">
                                <p @if (!$item['day_off']) class="fw-bold" @endif>{{ $item['weekday'] }}</p>
                            </td>
                            <td class="date">
                                <p @if (!$item['day_off']) class="fw-bold" @endif>{{ $key }}</p>
                            </td>
                            <td class="check-in">
                                <p>{{ $item['check_in'] }}</p>
                            </td>
                            <td class="check-out">
                                <p>{{ $item['check_out'] }}</p>
                            </td>
                            <td class="result">
                                <p>{{ $item['status'] }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script>
        jQuery(document).ready(function($) {
            $(".clickable-row").click(function() {
                window.location = $(this).data("href");
            });
        });
    </script>
@endpush
