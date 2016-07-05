

$(document).ready(function () {
    // the "href" attribute of .modal-trigger must specify the modal ID that wants to be triggered
    $('.modal-trigger').leanModal();
});

function showNotification(theTitle, theBody, theIcon) {
    var icon = (theIcon) ? theIcon : 'images/logo.png';
    var title = (theTitle) ? theTitle : '';
    var options = {
        body: theBody,
        icon: icon
    };
    var notification = new Notification(title, options);
    window.setTimeout(function () {
        notification.close();
    }, 5000);
}

function restore_options()
{
    var extention, service, password, click2call, openform;
    document.title = chrome.i18n.getMessage('options');
    extention = localStorage['extension'] ? localStorage['extension'] : '';
    password = localStorage['password'] ? localStorage['password'] : '';
    service = localStorage['service'] ? localStorage['service'] : '';
    document.getElementById('nameProject').innerHTML = chrome.i18n.getMessage('name_project');

    $('#labelExtension').text(chrome.i18n.getMessage('extension'));
    $('#labelPassword').text(chrome.i18n.getMessage('password'));
    $('#labelService').text(chrome.i18n.getMessage('service'));
    $('#labelClick2call').text(chrome.i18n.getMessage('click2call'));
    $('#labelBugReport').text(chrome.i18n.getMessage('bug_report'));
    $('#_save').text(chrome.i18n.getMessage('save'));
    $('#_exit').text(chrome.i18n.getMessage('exit'));

    var toggle = (localStorage['status'] == 'online') ? true : false;
    $('#statusView').prop('checked', toggle);


    if (extention !== '') {
        $('#extension').val(extention);
        $('label[for=extension]').addClass('active');
    }
    if (password !== '') {
        $('#password').val(password);
        $('label[for=password]').addClass('active');
    }
    if (service !== '') {
        $('#service').val(service);
        $('label[for=service]').addClass('active');
        localStorage['service_url'] = "http://" + localStorage['service'] + "/service/data.php";
    }

    click2call = localStorage['click2call'] == 'true' ? true : false;
    $('#click2call').prop('checked', click2call);
    openform = localStorage['openform'] == 'true' ? true : false;
    $('#openform').prop('checked', openform);

}

function save_options()
{
    var extention = $('#extension').val(),
            password = $('#password').val(),
            service = $('#service').val(),
            click2call = $('#click2call').prop('checked'),
            openform = $('#openform').prop('checked');

    localStorage['openform'] = openform;
    localStorage['click2call'] = click2call;

    if (extention == '' || password == '' || service == '') {
        $('#status').val(chrome.i18n.getMessage('connection_details'));
        if (service == '') {
            $('#statusView').prop('checked', false)
            localStorage.removeItem('service');
            localStorage.removeItem('session_id');
        }
    } else {
        localStorage['extension'] = extention;
        localStorage['password'] = password;
        localStorage['service'] = service;
        localStorage['service_url'] = "http://" + service + "/service/data.php";

        get_session_id(function (msg) {
            $('#status').val(msg);
            setTimeout(function () {
                $('#status').val('');
                chrome.extension.sendRequest({
                    'do': "update"
                });
            }, 1750);
        });
    }
}

function exit() {
    window.close();
}
;

$(window).load(function () {

    restore_options();

    $('#_save').on('click', function () {
        save_options();
    });

    $("#_exit").on("click", function () {
        exit();
    });

    $('#statusView').on('click', function () {
        if ($('#statusView').prop('checked')) {
            save_options();
        } else {
            localStorage.removeItem('session_id');
        }
    });

});

window.addEventListener('storage', function (event) {

    console.log("changes in localStorage: key - " + event.key + "; new value - " + event.newValue + ";");

    if (event.key == 'status') {
        if (event.newValue == 'online') {
            $('#statusView').prop('checked', true);
        } else if (event.newValue == 'offline') {
            $('#statusView').prop('checked', false);
        } else {
            $('#status').val('Error: status - ' + event.newValue);
        }
    }
});