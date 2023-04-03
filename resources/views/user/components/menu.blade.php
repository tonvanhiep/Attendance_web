<div class="bg-white" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">
        <img src="{{ asset('assets/img/logoBK.png') }}" style="width:150px;height:auto" alt="">HCMUT
    </div>
    <div class="list-group list-group-flush my-3">
        <a href="{{ route('user.home') }}"
            class="list-group-item list-group-item-action bg-transparent second-text active"><i
                class="fas fa-home me-2"></i></i>Home</a>
        <a href="{{ route('user.attendance.list') }}"
            class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                class="fas fa-project-diagram me-2"></i>Attendance</a>
        <a href="{{ route('user.report.list') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                class="fas fa-paperclip me-2"></i>Reports</a>
        <a href="#" class="list-group-item list-group-item-action bg-transparent second-text fw-bold"><i
                class="fas fa-comment-dots me-2"></i>Chat</a>
        <a href="{{ route('user.logout') }}"
            class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i
                class="fas fa-power-off me-2"></i>Logout</a>
    </div>
</div>
