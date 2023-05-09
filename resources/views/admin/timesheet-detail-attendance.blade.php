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
        <label for="start-date">Date</label>
        <div class="filter-input">
            <input type="date" name="date" value="{{ $condition['from'] }}" max="{{ $condition['today'] }}" style="font-style: 14px; padding: 5px 10px; border-radius:5px">
        </div>
        <div class="get-btn">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="submit" value="Filter" style="background:none; color:white">
        </div>
    </div>
</form>

<div id="content">
    <div class="board">
        <table width="100%" class="table table-hover" style="margin-bottom: 0px">
            <thead>
                <tr>
                    <td>Time</td>
                    <td>Timekeeper</td>
                    <td>Office</td>
                    <td>Result Recogination</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceList as $item)
                    @php $dt = new DateTime($item->timekeeping_at); @endphp
                    <tr class='clickable-row'
                    data-href='{{ route('admin.attendance.detail', ['id' => $item->attendance_id]) }}'>
                        <td class="time">
                            <p class="fw-bold">{{ $dt->format('H:i:s') }}</p>
                        </td>
                        <td class="timekeeper">
                            <p>{{ $item->timekeeper_name }}</p>
                        </td>
                        <td class="office">
                            <p>{{ $item->office_name }}</p>
                        </td>
                        <td class="face-recognition">
                            <a href="{{ asset('storage/image-checkin/' . $item->face_image) }}"> Image</a>
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

