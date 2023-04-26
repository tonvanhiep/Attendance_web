Echo.channel('laravel_database_attendance')
.listen('Attendance', (data) => {
    var countWaitingConfirm = document.getElementById("count-watting-confirm");

    console.log(data)
    if (data.waiting_confirm > 0) {
        countWaitingConfirm.innerHTML='<span style="font-size: 10px" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">' + (data.waiting_confirm < 100 ? data.waiting_confirm : '99+') + '</span>';

        if(window.location.pathname == "/admin/attendance") {
            pagination(document.getElementsByClassName('is-active')[0].innerText);

            // const queryString = window.location.search;
            // const urlParams = new URLSearchParams(queryString);
            // const status = urlParams.get('status')
            // const office = urlParams.get('office')
            // const from = urlParams.get('from')
            // const to = urlParams.get('to')
            // console.log(status);
            // console.log(data.attendance.status);

            // if((status == data.attendance.status || status == '' || status == 0 || (status == null && data.attendance.status == 1)))
            //     pagination(document.getElementsByClassName('is-active')[0].innerText);
        }
    }
    else if (data.waiting_confirm <= 0) {
        child = countWaitingConfirm.childNodes;
        countWaitingConfirm.removeChild(child[1]);
    }
})
