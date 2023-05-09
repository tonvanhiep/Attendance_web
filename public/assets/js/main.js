var boxNoti = document.getElementById('list-notifi')
var boxAcc = document.getElementById('list-acc')
var down = false
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
function toggleNotifi() {
    if (down) {
        boxNoti.style.visibility = 'hidden'
        boxNoti.style.opacity = 0
        down = false
    }
    else {
        boxNoti.style.opacity = 1;
        boxNoti.style.visibility = 'visible'
        boxAcc.style.visibility = 'hidden'
        boxAcc.style.opacity = 0
        down = true;
    }
}

function toggleAcc() {
    if (down) {
        boxAcc.style.visibility = 'hidden'
        boxAcc.style.opacity = 0
        down = false
    }
    else {
        boxAcc.style.opacity = 1;
        boxAcc.style.visibility = 'visible'
        boxNoti.style.visibility = 'hidden'
        boxNoti.style.opacity = 0
        down = true;
    }
}

function pagination(page = 1)
{
    var show = document.getElementById('input-show');
    $.ajax ({
        type: 'POST',
        cache: false,
        url: document.getElementById('url-pagination').textContent,
        data: {
            "page": page,
            "show": show == null ? 50 : show.value
        },
        success: function(data) {
            document.getElementById('content').innerHTML = data;
            jQuery(document).ready(function($) {
                $(".clickable-row").click(function() {
                    window.location = $(this).data("href");
                });
            });
        },
        error: function(data) {},
    });

}



var inputShow = document.getElementById('input-show')
if (inputShow != null) {
    inputShow.onchange = function(){
        $('#show-form').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: document.getElementById('show-form').action,
                data: formData,
                cache:false,
                contentType: false,
                processData: false,
                success: (data) => {
                    document.getElementById('content').innerHTML = data;
                    jQuery(document).ready(function($) {
                        $(".clickable-row").click(function() {
                            window.location = $(this).data("href");
                        });
                    });
                },
                error: function(data){}
            });
        });
    };
}

