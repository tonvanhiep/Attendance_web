<div class="col" style="float: none">
    <table class="table bg-white rounded shadow-sm  table-hover">
        <thead>
            <tr>
                {{-- <th scope="col" width="50">Number</th> --}}
                <th scope="col">DayofWeek</th>
                <th scope="col">Date</th>
                <th scope="col">Timekeeper</th>
                <th scope="col">Check_in</th>
                <th scope="col">Check_out</th>
                <th scope="col">Face_img</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dayOfWeekArr as $item)
                <tr>
                    {{-- <th scope="row">{{ $count++ }}</th> --}}
                    <td>{{ $item['dayOfWeek'] }}</td>
                    <td>{{ $item['date'] }}</td>
                    <td>{{ $item['timekeeper_name'] }}</td>
                    <td>{{ $item['check_in'] }}</td>
                    <td>{{ $item['check_out'] }}</td>
                    <td><a href="{{ asset('storage/image-checkin/'.$item['face_image']) }}">{{ $item['face_image'] }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- @include('user.components.pagination') --}}
