<div class="col" style="float: none">
    <table class="table bg-white rounded shadow-sm  table-hover">
        <thead>
            <tr>
                <th scope="col" width="50">Number</th>
                <th scope="col">Name</th>
                <th scope="col">Employee_id</th>
                <th scope="col">Machine_id</th>
                <th scope="col">Date</th>
                <th scope="col">Check_in</th>
                <th scope="col">Check_out</th>
                <th scope="col">Face_img</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
                <tr>
                    <th scope="row">{{ $count++ }}</th>
                    <td>{{ $user->last_name.' '.$user->first_name }}</td>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->timekeeper_id }}</td>
                    <td>{{ $item->date }}</td>
                    <td>{{ $item->check_in }}</td>
                    <td>{{ $item->check_out }}</td>
                    <td>{{ $item->face_image }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- @include('user.components.pagination') --}}
