<div class="board" style="overflow-x:auto;">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="margin: 30px;" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <table width="100%" class="table table-hover" style="margin-bottom: 0px">
        <thead>
            <tr>
                <td>Name</td>
                <td>ID</td>
                <td>Email</td>
                <td>Date of birth</td>
                <td>Office</td>
                <td>Department</td>
                <td>Position</td>
                <td>Join date</td>
                <td>Address</td>
                <td>Status</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            @foreach ($list as $item)
                <tr>
                    <td class="name">
                        <p class="fw-bold">{{ $item->last_name }} {{ $item->first_name }}</p>
                    </td>
                    <td class="id">
                        <p>{{ $item->id }}</p>
                    </td>
                    <td class="email">
                        <p>{{ $item->email }}</p>
                    </td>
                    <td class="birth">
                        <p>{{ $item->birth_day }}</p>
                    </td>
                    <td class="office">
                        <p>{{ $item->office_name }}</p>
                    </td>
                    <td class="depart">
                        <p>{{ $item->department }}</p>
                    </td>
                    <td class="position">
                        <p>{{ $item->position }}</p>
                    </td>
                    <td class="date">
                        <p>{{ $item->join_day }}</p>
                    </td>
                    <td class="address">
                        <p>{{ $item->address }}</p>
                    </td>
                    <td class="status">
                        <p>{{ statusEmployeeMean($item->status) }}</p>
                    </td>
                    <td class="edit"><a href="{{ route('admin.staff.edit', ['id' => $item->id]) }}">Edit</a></td>
                </tr>
            @endforeach

        </tbody>
    </table>
</div>

@include('admin.components.pagination')
