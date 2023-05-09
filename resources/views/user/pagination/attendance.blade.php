<h5 style="text-align: center">Overview</h5>
<div class="col" style="float: none">
    <table class="table bg-white rounded shadow-sm  table-hover">
        <thead>
            <tr>
                {{-- <th scope="col" width="50">Number</th> --}}
                <td scope="col">Name</td>
                <td scope="col">ID</td>
                <td scope="col">Office</td>
                <td scope="col">Department</td>
                <td scope="col">Present</td>
                <td scope="col">Late</td>
                <td scope="col">Early</td>
                <td scope="col">Off</td>
                <td scope="col">Total</td>
            </tr>
        </thead>
        <tbody>
            <tr>
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

<div class="col" style="float: none; margin-left: 0px;">
    <table class="table bg-white rounded shadow-sm  table-hover">
        <thead>
            <tr>
                {{-- <th scope="col" width="50">Number</th> --}}
                <td scope="col"></td>
                <td scope="col">Date</td>
                <td scope="col">Check In</td>
                <td scope="col">Check Out</td>
                <td scope="col">Result</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($arrTimesheetDetail as $key => $item)
                <tr @if ($item['day_off']) style="color: lightgrey" @endif
                class='clickable-row' data-href='{{ route('user.attendance.detail') . ($key == date('Y-m-d') ? '' : '?date=' . $key) }}'>
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

@include('user.components.pagination')

@push('js')
    <script>
        jQuery(document).ready(function($) {
            $(".clickable-row").click(function() {
                window.location = $(this).data("href");
            });
        });
    </script>
@endpush

