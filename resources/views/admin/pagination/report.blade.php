<div class="board">
    <table width="100%" class="table table-hover" style="margin-bottom: 0px">
        <thead>
            <tr>
                <td>Date</td>
                <td>Time</td>
                <td>Name</td>
                <td>ID</td>
                <td>Office</td>
                <td>Department</td>
                <td>Comment</td>
                <td>Status</td>
                {{-- <td>Check</td> --}}
            </tr>
        </thead>
        <tbody>
        @foreach ($list as $item )
            @php $dt = new DateTime($item->created_at); @endphp
            <tr>
                <td>
                    <p class="fw-bold">{{ $dt->format('d/m/Y') }}</p>
                </td>
                <td>
                    <p>{{ $dt->format('H:i:s') }}</p>
                </td>
                <td>
                    <p>{{ $item->last_name . ' ' . $item->first_name }}</p>
                </td>
                <td>
                    <p>{{ $item->id }}</p>
                </td>
                <td class="office">
                    <p>{{ $item->office_name }}</p>
                </td>
                <td class="department">
                    <p>{{ $item->department }}</p>
                </td>
                <td>
                    <p class="fw-bold">{{ $item->comment }}</p>
                </td>
                {{-- <td>{{ $item->status }}</td>
                <td>
                    <input type="checkbox" id="check-status" value="1" @if ($item->status == 1)checked @endif>
                </td> --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.components.pagination')
