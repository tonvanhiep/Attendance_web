<section id="menu">
    <div class="logo">
        <img src="{{asset('assets/img/logoBK.png');}}" alt="">
        <h2>HCMUT</h2>
    </div>
    <ul class="items" style="padding-left: 0px;">
        <li style="{{ $page == 'dashboard' ? 'border-left: 4px solid #fff;' : '' }}"><i class="fa-solid fa-gauge"></i><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li style="{{ $page == 'staff' ? 'border-left: 4px solid #fff;' : '' }}"><i class="fa-regular fa-user"></i><a href="{{route('admin.staff.list')}}">Staff List</a></li>
        <li @if ($page == 'attendance') style="border-left: 4px solid #fff;" @endif>
            <i id="count-watting-confirm" class="fa-regular fa-clock position-relative">
                @if (isset($waitConfirm))
                    <span style="font-size: 10px" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                        {{ $waitConfirm <= 99 ? $waitConfirm : '99+' }}
                    </span>
                @endif
            </i>
            <a href="{{route('admin.attendance.list')}}">Attendance</a>
        </li>
        <li style="{{ $page == 'timesheet' ? 'border-left: 4px solid #fff;' : '' }}"><i class="fa-regular fa-file-lines"></i><a href="{{route('admin.timesheet.list')}}">Timesheet</a></li>
        <li style="{{ $page == 'report' ? 'border-left: 4px solid #fff;' : '' }}"><i class="fa-regular fa-file-lines"></i><a href="{{route('admin.report.list')}}">Report</a></li>
    </ul>
</section>
