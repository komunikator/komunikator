
var hostname = window.location.hostname;
var port = window.location.port;
var idSipRecord = null;
var idProvider = null;
var editData = null;

window.onload = function () {

    $.get("http://" + hostname + ":" + port + "/resourceData/settings", displaySipTable);
    $('#button-login-ok').on('click', function () {
        $('#user-login-form').hide();
        $('#user-status-form').show();
    });

    $('#refresh-button').on('click', function () {
        $.get("http://" + hostname + ":" + port + "/resourceData/settings", displaySipTable);
    });

};


function displaySipTable(returnedData) {
    console.log(returnedData);
    if (returnedData && returnedData.data && returnedData.data[0] && returnedData.data[0].value) {
        var data = jQuery.parseJSON(returnedData.data[0].value);

        var tbody = document.getElementById("list-sip-connection").getElementsByTagName("TBODY")[0];
        if (tbody.rows.length > 0) {
            while (tbody.rows[0]) {
                tbody.deleteRow(0);
            }
        }

        if (data.sipAccounts.length > 0) {
            for (var i = 0; i < data.sipAccounts.length; i++) {
                var domain = data.sipAccounts[i].host;

                if (data.sipAccounts[i].domain) {
                    domain = data.sipAccounts[i].domain;
                }
                var nameProvider = data.sipAccounts[i].user + "@" + domain;
                var row = document.createElement("TR");
                var td = document.createElement("TD");
                td.appendChild(document.createTextNode(nameProvider));
                row.setAttribute('oncontextmenu', 'return menu(1, event, this);');
                row.appendChild(td);
                tbody.appendChild(row);
            }
        }

    }
}

function actionSipConnection() {
    if (editData) {
        recordSipConnection(editData);
    } else {
        $.ajax({
            url: '/resourceData/settings',
            method: 'get',
            success: recordSipConnection
        });
    }
}

function recordSipConnection(response) {
    var data = jQuery.parseJSON(response.data[0].value);
    var login = $("#userSipName").val();
    var pass = $("#userSipPassword").val();
    var host = getHostSipConnection(idProvider);
    var domain = getDomainSipConnection(idProvider);
    var sipAccount = {
        host: host,
        expires: 60,
        user: login,
        password: pass,
        disable: 1
    };

    if (!host) {
        if (data && data.sipAccounts &&
            data.sipAccounts[idSipRecord] &&
            data.sipAccounts[idSipRecord].host) {

            sipAccount['host'] = data.sipAccounts[idSipRecord].host;
        }
    }

    if (domain) {
        sipAccount['domain'] = domain;
    } else {
        if (data && data.sipAccounts &&
            data.sipAccounts[idSipRecord] &&
            data.sipAccounts[idSipRecord].domain) {

            sipAccount['domain'] = data.sipAccounts[idSipRecord].domain;
        }
    }

    //choose add or edit
    (idSipRecord === null) ?
            data.sipAccounts[data.sipAccounts.length] = sipAccount :
            data.sipAccounts[idSipRecord] = sipAccount;

    response.data[0].create = false;
    response.data[0].name = 'config/config';
    //save in the proper format
    response.data[0].value = JSON.stringify(data, null, 4);

    $.ajax({
        url: "/resourceData/update",
        method: 'put',
        data: response.data[0],
        success: function (response) {
            $.get("http://" + hostname + ":" + port + "/resourceData/settings", displaySipTable);
        }
    });
    idSipRecord = null;
    idProvider = null;
    editData = null;
    $("#userSipName").val('');
    $("#userSipPassword").val('');
}

function editAction()
{
    $("#choice-page").hide();
    $("#next_button").removeClass("next_text");
    $("#next_button").addClass("ready_text");
    $.ajax({
        url: '/resourceData/settings',
        method: 'get',
        success: function (response) {
            editData = response;
            var data = jQuery.parseJSON(response.data[0].value);
            $("#userSipName").val(data.sipAccounts[idSipRecord].user);
            $("#userSipPassword").val(data.sipAccounts[idSipRecord].password);
            $("#enterData-page").show();
            $("#user-master-form").dialog("open");
        }
    });
}
;


function deleteSipAccount()
{
    $("#question-shour-form").dialog("open");
}

function showText(text)
{
    alert("You entered " + text);
}
/*
function displaySipTable1(returnedData)//старый вариант
{
    max кол-во подключений 10. Возвращается массив с 20-ью значениями
      первые 10 - состояние, вторые - название
    var color;
    var tbody = document.getElementById("list-sip-connection").getElementsByTagName("TBODY")[0];
    if (tbody.rows.length > 0) {
        console.log(tbody);
        while (tbody.rows[0]) {
            tbody.deleteRow(0);
        }
    }

    for (var i = 10; i < 20; i++) {
        if (returnedData.data[0][i] != null) {
            console.log(returnedData.data[0][i]);
            color = (returnedData.data[0][i - 10] == 1) ? 'green' : 'red';
            var row = document.createElement("TR");
            var td = document.createElement("TD");
            var td1 = document.createElement("TD");
            var circle = document.createElement("div");
            circle.setAttribute('class', 'circle_' + color);
            td1.appendChild(circle);
            td.appendChild(document.createTextNode(returnedData.data[0][i]));
            td1.setAttribute('class', 'sip-col1');
            td.setAttribute('class', 'sip-col2');
            row.setAttribute('style', 'color: ' + color + ';');
            row.setAttribute('oncontextmenu', 'return menu(1, event, this);');
            //    td.setAttribute('oncontextmenu', 'return menu(1, event);');
            row.appendChild(td1);
            row.appendChild(td);
            tbody.appendChild(row);
        }
    }
}*/

function defPosition(event) {
    var x = y = 0;
    if (document.attachEvent != null) {
        x = window.event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
        y = window.event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
    } else if (!document.attachEvent && document.addEventListener) {
        x = event.clientX + window.scrollX;
        y = event.clientY + window.scrollY;
    } else {
    }
    return {x: x, y: y};
}

function menu(type, evt, obj) {
    // Блокируем всплывание события contextmenu
    evt = evt || window.event;
    evt.cancelBubble = true;

    idSipRecord = obj.rowIndex;
    // Показываем собственное контекстное меню
    var menu = document.getElementById("contextMenuId");
    var html = "";
    switch (type) {
        case (1) :
            html = "<table>";
            html += "<tr><td id='edit-sip-menu' class='edit_text' onclick='editAction();'></td></tr>";
            html += "<tr><td id='delete-sip-menu' class='delete_text' onclick='deleteSipAccount();'></td></tr>";
            html += "</table>";
            break;
        default :
            break;
    }
    if (html) {

        menu.innerHTML = html;
        menu.style.top = defPosition(evt).y + "px";
        menu.style.left = defPosition(evt).x + "px";
        menu.style.display = "";
    }
    // Блокируем всплывание стандартного браузерного меню
    return false;
}

// Закрываем контекстное при клике левой или правой кнопкой по документу
// Функция для добавления обработчиков событий
function addHandler(object, event, handler, useCapture) {
    if (object.addEventListener) {
        object.addEventListener(event, handler, useCapture ? useCapture : false);
    } else if (object.attachEvent) {
        object.attachEvent('on' + event, handler);
    } else
        alert("Add handler is not supported");
}
addHandler(document, "contextmenu", function () {
    document.getElementById("contextMenuId").style.display = "none";
});
addHandler(document, "click", function () {
    document.getElementById("contextMenuId").style.display = "none";
});