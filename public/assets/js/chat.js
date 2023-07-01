const btnSendMessage = document.getElementById('btn-send-message')
const contentMessage = document.getElementById('content-message')
const chatBox = document.getElementById('div-chat-box')
const month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const urlMyAvatar = document.getElementsByClassName('my-avatar')[0].src
const arrPathName = window.location.pathname.split('/')
const groupID = arrPathName[arrPathName.length - 1];
const btnReturnSearch = document.getElementById('btn-return-search');
const inpSearch = document.getElementById('inp-search');
const divSearch = document.getElementById('div-search');
const divListMess = document.getElementById('div-list-message');
const listMess = document.getElementById('list-message-group');
const divListSearch = document.getElementById('div-list-search');

Echo.private('user.' + document.getElementById('id-user').innerText)
.listen('Chat', (data) => {
    console.log(data)
    addMessToListGroup(data.message, false);
    if (chatBox != null) {
        if (arrPathName[3] == data.message.id_receiver) {
            addMessToChatBox(data.message, '', false)
            markReaded(data.message.id_receiver, data.message.id)
        }
        chatBox.scrollTop = chatBox.scrollHeight
    };
})



inpSearch.onfocus = function () {
    btnReturnSearch.hidden = false
    divListMess.hidden = true
    divListSearch.hidden = false
    divSearch.style.width = "90%"
}

if (contentMessage != null) contentMessage.onfocus = unsearch

btnReturnSearch.onclick = unsearch

function unsearch() {
    btnReturnSearch.hidden = true
    divListMess.hidden = false
    divListSearch.hidden = true
    divSearch.style.width = "100%"
}

function addMessToListGroup(data, isSender = true) {
    var group = ''
    var messGroup = document.getElementById('list-mess-group-' + data.id_receiver)
    if (messGroup == null) {
        group = '<li class="p-2 border-bottom message-group">' +
            '<a href="" class="d-flex justify-content-between">' +
                '<div class="d-flex flex-row">' +
                    '<div>' +
                        '<img src="' + (isSender == true || data.type != 1 ? data.avatar_group : data.avatar) + '" alt="avatar" class="d-flex align-self-center me-3" style="width: 60px; height:60px; border-radius:100px">' +
                        '<span class="badge bg-success badge-dot"></span>' +
                    '</div>' +
                    '<div class="pt-1">' +
                        '<p class="fw-bold mb-0">' + (isSender == true || data.type != 1 ? data.name_group : data.name_sender) + '</p>' +
                            '<p id="list-mess-group-' + data.id_receiver + '" class="small text-muted" style="font-style: italic"></p>' +
                    '</div>' +
                '</div>' +
                '<div class="pt-1">' +
                    '<p id="time-mess-group-' + data.id_receiver + '" class="small text-muted mb-1 date-time"></p>' +
                '</div>' +
            '</a>' +
        '</li>';
        listMess.innerHTML += group
    }
    messGroup = document.getElementById('list-mess-group-' + data.id_receiver)
    if (isSender) {
        messGroup.innerText = 'You:' + data.content.replaceAll('</br>', ' ')
        messGroup.style = "font-style: italic; font-weight: normal"
    }
    else {
        messGroup.innerText = data.content.replaceAll('</br>', ' ')
        messGroup.style = "font-weight: bold; font-style: normal"
        var divInfoMessGroup = document.getElementById('div-info-group-' + data.id_receiver)

        if(divInfoMessGroup.children.length == 1) {
            divInfoMessGroup.innerHTML += '<span id="mess-unread-group-' + data.id_receiver + '" class="badge bg-danger rounded-pill float-end">0</span>'
        }
        divInfoMessGroup.children[1].innerText = parseInt(divInfoMessGroup.children[1].innerText) + 1
    }
    document.getElementById('time-mess-group-' + data.id_receiver).innerText = data.created_at

    $('#list-message-group .message-group').sort(sortDescending).appendTo('#list-message-group');
}

function addMessToChatBox(data, status = '', isSender = true) {
    if (isSender) {
        chatBox.innerHTML = chatBox.innerHTML + '<div class="d-flex flex-row justify-content-end">' +
        '<div style="max-width: 80%; margin-left:auto">' +
            '<p class="small p-2 me-3 mb-1 text-white rounded-3 bg-primary">' + data.content + '</p>' +
            '<div style="display: flex; justify-content: space-between;">' +
                '<p style="display: inline" class="small me-3 mb-3 rounded-3 text-muted">' + data.created_at + '</p>' +
                '<p style="display: inline; ' + (status == 'Error occur!' ? 'color:red !important;' : '') + '" class="small me-3 mb-3 rounded-3 text-muted">' + status + '</p>' +
            '</div>' +
        '</div>' +
        '<img class="my-avatar" src="' + data.avatar + '" alt="avatar 1" style="width: 45px; height: 45px; border-radius:100px">' +
        '</div>';
    }
    else {
        chatBox.innerHTML = chatBox.innerHTML + '<div class="d-flex flex-row justify-content-start">' +
        '<img src="' + data.avatar + '" alt="avatar 1" style="width: 45px; height: 45px; border-radius:100px">' +
        '<div style="max-width: 80%;">' +
            '<p class="small p-2 ms-3 mb-1 rounded-3" style="background-color: #f5f6f7;">' + data.content + '</p>' +
            '<p class="small ms-3 mb-3 rounded-3 text-muted float-end">' + data.created_at + '</p>' +
        '</div>' +
        '</div>';
    }
}

var idTimeout = -1;
inpSearch.oninput = function () {
    if (setTimeout != -1) {
        clearTimeout(idTimeout);
        idTimeout = -1;
    }
    idTimeout = setTimeout(searchName, 650, inpSearch.value);
}

if (chatBox != null) chatBox.scrollTop = chatBox.scrollHeight;
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function sendMessage(content) {
    if (idTimeout != -1) {
        idTimeout = -1;
    }
    const tgian = new Date;
    let status = 'Error occur!';
    let message;
    try {
        $.ajax ({
            type: 'POST',
            cache: false,
            url: document.getElementById('url-store-message').textContent,
            async:false,
            data: {
                "id_receiver": groupID,
                "content": content,
                "time": tgian.getTime(),
                "is_private": window.location.pathname.split('/')[3] == 'u' ? 1 : 0
            },
            success: function(data) {
                status = '';
                message = data.message;
                addMessToListGroup(data.message);
            },
            error: function(data) {},
        });
    } catch (error) {}
    addMessToChatBox(message, status);
}

if (btnSendMessage != null) {
    btnSendMessage.onclick = function () {
        let content = contentMessage.value;
        if (!(content == "" || content == null)) {
            contentMessage.value = null;
            content = content.trim().replaceAll("\n", "</br>");

            sendMessage(content);
        }
        chatBox.scrollTop = chatBox.scrollHeight;
    }

}

function sortDescending(a, b) {
    var time1  = $(a).find(".date-time").text();
    time1 = time1.split(' ')
    date1 = time1[0].split('-');
    hour1 = time1[1].split(':');
    date1 = new Date(date1[2], date1[1] -1, date1[0], hour1[0], hour1[1], hour1[2]);

    var time2  = $(b).find(".date-time").text();
    time2 = time2.split(' ')
    date2 = time2[0].split('-');
    hour2 = time2[1].split(':');
    date2 = new Date(date2[2], date2[1] -1, date2[0], hour2[0], hour2[1], hour2[2]);

    return date1 < date2 ? 1 : -1;
}

function searchName($name) {
    try {
        $name = $name.trim()
        if($name == null || $name == '') return

        $.ajax ({
            type: 'POST',
            cache: false,
            url: window.location.origin + '/user/chat/search',
            data: {
                "name": $name
            },
            success: function(data) {
                var html = '';
                data.employee.forEach(element => {
                    html = html +
                    '<li class="p-2 border-bottom message-group">' +
                        '<a href="' + window.location.origin + '/user/chat/u/' + element.id + '" class="d-flex justify-content-between">' +
                            '<div class="d-flex flex-row">' +
                                '<div>' +
                                    '<img src="' + element.avatar + '" alt="avatar" class="d-flex align-self-center me-3" style="width: 60px; height:60px; border-radius:100px">' +
                                    '<span class="badge bg-success badge-dot"></span>' +
                                '</div>' +
                                '<div class="pt-1">' +
                                    '<p class="fw-bold mb-0">' + element.full_name + '</p>' +
                                '</div>' +
                            '</div>' +
                        '</a>' +
                    '</li>';
                });
                document.getElementById('list-message-search').innerHTML = html
            },
            error: function(data) {},
        });
    } catch (error) {}
}

function markReaded(group_id, mess_id) {
    try {
        $.ajax ({
            type: 'POST',
            cache: false,
            url: window.location.origin + '/user/chat/markreaded',
            data: {
                "id_group": group_id,
                "id_message": mess_id
            },
            success: function(data) {
                var divInfoMessGroup = document.getElementById('div-info-group-' + group_id)
                divInfoMessGroup.removeChild(divInfoMessGroup.children[1])
                document.getElementById('list-mess-group-' + group_id).style = "font-weight:normal"
            },
            error: function(data) {},
        });
    } catch (error) {}
}
