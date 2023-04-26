<div class="board">
    <table width="100%" class="table table-hover" style="margin-bottom: 0px">
        <thead>
            <tr>
                <td>Name</td>
                <td>ID</td>
                <td>Office</td>
                <td>Position</td>
                <td>Status</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
                <tr>
                    <td class="person">
                        <img src="{{ asset("$item->avatar") }}" alt="avatar {{ $item->first_name }} {{ $item->last_name }}">
                        <div class="person-description">
                            <p class="fw-bold">{{ $item->last_name }} {{ $item->first_name }}</p>
                            <p>...</p></p>
                        </div>
                    </td>
                    <td class="id">
                        <p>{{ $item->id }}</p>
                    </td>
                    <td class="office">
                        <p>{{ $item->office_name }}</p>
                    </td>
                    <td class="position">
                        <p class="fw-bold">{{ $item->department }}</p>
                        <p>{{ $item->position }}</p>
                    </td>
                    <td class="status">
                        <p @if ($item->status == 2) style="background-color: darkgray" @endif class="active">{{ statusEmployeeMean($item->status) }}</p>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.components.pagination')
