<div class="board">
    @if (isset($waitConfirm) && $waitConfirm > 0)
        <div class="alert alert-warning" role="alert">
            There are many requests waiting for confirmation.
            @if ($condition['status'] != 2)
            <a href="{{ route('admin.attendance.list') . '?status=2' }}" class="alert-link">Go to and Confirm</a>
            @endif
        </div>
    @endif

    <table width="100%" class="table table-hover">
        <thead>
            <tr>
                <td>Name</td>
                <td>ID</td>
                <td>Office</td>
                <td>Timkeeper</td>
                <td>Date</td>
                <td>Time</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
                @php $dt = new DateTime($item->timekeeping_at); @endphp
                <tr
                    @if ($condition['status'] == 0)
                        @switch($item->status)
                            @case(2)
                                style="color: #ffc107"
                                @break
                            @case(3)
                                style="color: #dc3545"
                                @break
                            @default

                        @endswitch
                    @endif
                    class='clickable-row' data-href='{{ route('admin.attendance.detail', ['id' => $item->attendance_id]) }}'>
                    <td class="name">
                        <p class="fw-bold">{{ $item->last_name }} {{ $item->first_name }}</p>
                    </td>
                    <td class="id">
                        <p>{{ $item->id }}</p>
                    </td>
                    <td class="office">
                        <p>{{ $item->office_name }}</p>
                    </td>
                    <td class="timekeeper">
                        <p>{{ $item->timekeeper_name }}</p>
                    </td>
                    <td class="date">
                        <p>{{ $dt->format('d/m/Y') }}</p>
                    </td>
                    <td class="time">
                        <p>{{ $dt->format('H:i:s') }}</p>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.components.pagination')
