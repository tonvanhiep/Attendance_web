<div class="board">
    <table width="100%">
        <thead>
            <tr>
                <td>Name</td>
                <td>ID</td>
                <td>Office</td>
                <td>Department</td>
                <td>Late</td>
                <td>Early</td>
                <td>Off</td>
                <td>Total</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($arr as $item)
                <tr onclick="window.location='http://127.0.0.1:9000/admin/timesheet/detail/2'">
                    <td class="name">
                        <h5>{{ $item['last_name'] . ' ' . $item['first_name'] }}</h5>
                    </td>
                    <td class="id">
                        <p>{{ $item['id'] }}</p>
                    </td>
                    <td class="office">
                        <p>{{ $item['office_name'] }}</p>
                    </td>
                    <td class="department">
                        <p>{{ $item['department'] }}</p>
                    </td>
                    <td class="late">
                        <p>{{ $item['late'] }}</p>
                    </td>
                    <td class="early">
                        <p>{{ $item['early'] }}</p>
                    </td>
                    <td class="off">
                        <p>{{ $item['off'] }}</p>
                    </td>
                    <td class="total">
                        <p>{{ $item['total'] }}</p>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.components.pagination')
