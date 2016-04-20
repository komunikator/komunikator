var hostname = window.location.hostname;
var port = window.location.port;
var cur_acc_list = [];
var from_elem;
var providers;
var provider_status = {
    offline: 'Отключён',
    online: 'Подключён',
    'none-status': 'Ошибка регистрации'
};

var create_provider = {};
var provider_img = {
    'voip.mtt.ru': 'images/providers/youmagicpro.png',
    'mangosip.ru': 'images/providers/MangoTel.png',
    'multifon.ru': 'images/providers/megafon.png'
};
//var Ext = window.Ext;
console.log(window.Ext);
var providersList =
        {
            providers: [
                {
                    id: 'megafon',
                    name_ru: 'Мегафон',
                    img: 'images/providers/megafon.png',
                    server: '193.201.229.35',
                    domain: 'multifon.ru',
                    ref_link: 'http://multifon.ru/help'
                },
                {
                    id: 'MangoTel',
                    name_ru: 'Манго Телеком',
                    img: 'images/providers/MangoTel.png',
                    server: '81.88.86.11',
                    //domain: 'mangosip.ru',
                    domain: '***.mangosip.ru',
                    ref_link: 'http://www.mango-office.ru/shop/tariffs/vpbx?p=400000034'
                },
                {
                    id: 'youmagicpro',
                    name_ru: 'Youmagic.pro',
                    img: 'images/providers/youmagicpro.png',
                    server: '80.75.132.66',
                    domain: 'voip.mtt.ru',
                    ref_link: 'https://youmagic.pro/ru/services/mnogokanalnyj-nomer?aid=3643'
                }
            ]
        };

var empty_provider = {
    id: null,
    status: "",
    enabled: true,
    gateway: "",
    server: "",
    username: "",
    password: "",
    description: "",
    protocol: "sip",
    ip_transport: "UDP",
    authname: "",
    domain: "",
    callerid: ""
};
var edit_provider = {};

function getProvidersList1() {
    for (var key in providersList.providers) {
        $('#provider_choose > .collection').append('<li class="collection-item provider-item" id="' + key + '_provider">' +
                '<div class="left povider_logo_cont">' +
                '<img src="' + providersList.providers[key].img + '" alt="' + providersList.providers[key].name_ru + '" url="' + providersList.providers[key].server + '" ref="' + providersList.providers[key].ref_link + '" class="provider_logo">' +
                '</div>' +
                '<span class="title provider_name">' + providersList.providers[key].name_ru + '</span>' +
                '</li>');
    }
}
;

function getImgSipConnection(server) {
    if (server) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].server == server) {
                return providersList.providers[key].img;
            }
        }
    }
    return;
}

function getNameProvConnection(server) {
    if (server) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].server == server) {
                return providersList.providers[key].name_ru;
            }
        }
    }
    return;
}

function getHostSipConnection(name_ru) {
    if (name_ru) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].name_ru == name_ru) {
                return providersList.providers[key].server;
            }
        }
    }
    return;
}

function getDomainSipConnection(name_ru) {
    if (name_ru) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].name_ru == name_ru) {
                return providersList.providers[key].domain;
            }
        }
    }
    return;
}

function init_master() {

    $("#page_1, #page_2, #page_3").hide();
    $("#header_title").text("Мастер настроек");
    $("#finish_master_page, #provider_choose, #enter_login_password,\n\
#voice_choose, #prev_button, #edit_connection").hide();

    $("#prev_button, #done_button").hide();
    $("#current_connections").show();
    //$("#done_button > a").text("Закрыть");
    $("#header_title").text("Ваши текущие SIP подключения");
    $("#header_decription").text("Вы можете отредактировать ваши SIP подключения или добавить новые");
    $("#page_1").show();
}
function getProvidersList() {

    $.ajax({
        url: '/kommunikator/data.php?action=get_gateways',
        method: 'get',
        success: function (response) {
            var status, provider_switch = '', provider_login, provider_id, img_src;
            var provider_domain, provider_enabled;
            providers = JSON.parse(response);

            var i = 0;
            $('#current_connections > .collection >li').remove();
            for (i; i < providers['visible_total']; i++) {

                provider_id = providers['data'][i][0]; //0 - id провайдера

                provider_domain = providers['data'][i][11];// console.log(provider_domain);
                
                img_src = provider_img[provider_domain];
                if (!img_src) {
                    var res = provider_domain.indexOf('mangosip.ru'); //console.log(res);
                    img_src = (res>=0) ? 'images/providers/MangoTel.png' : 'images/providers/unkonwn.png';
                }

                provider_login = providers['data'][i][5]; //5 - username провайдера

                provider_enabled = providers['data'][i][2]; //2 - enabled провайдера(для переключателя)

                status = providers['data'][i][1]; //1 - id статуса провайдера


                provider_switch = (provider_enabled === '1') ? 'checked' : '';

                if (status !== 'online' && status !== 'offline')
                    status = 'none-status';

                $("#current_connections > .collection").append(
                        '<li class="collection-item with_del valign-wrapper">' +
                       // '<div class="click_area valign-wrapper">' +
                        '<div class="povider_logo_cont">' +
                        '<img src="' + img_src + '" alt="" class="provider_logo">' +
                        '</div>' +
                        '<span class="provider-id" style="display:none;">' + provider_id + '</span>' +
                        '<span class="title accaunt_uri valign truncate" >' + provider_login + '</span>' +
                       // '</div>' +
                       // '<div class="right_cont valign-wrapper">' +
                        '<div class="switch">' +
                        '<label title="Подключить аккаунт"><input type="checkbox" ' + provider_switch + ' class="switch_click"><span class="lever"></span></label>' +
                        '</div>' +
                        '<div class="indicator ' + status + '_color">' + provider_status[status] + '</div>' +
                        '<div class="edit_btn_cont click_area"><a href="javascript:void(0)" class="btn-flat grey-text">РЕДАКТИРОВАТЬ</a></div>' +
                    //    '</div>' +
                        '</li>'
                        );

                $(".edit_btn_cont").bind("click", function (e) {
                    $("#current_connections").hide();
                    $("#page_1").hide();
                    from_elem = $(this).parent();
//                    if (from_elem.hasClass('right_cont')) {
//                        from_elem = from_elem.parent();
//                        $(this).parent().removeClass("active_item");
//                    }
                    $(this).parent().parent().children(".active_item").removeClass("active_item");
                    if ($(this).parent().hasClass("active_item")) {
                        $(this).parent().removeClass("active_item");
                    } else {
                        $(this).parent().addClass("active_item");
                    }

                    var from_provider_id = from_elem.children(".provider-id").html(); 

                    $.ajax({
                        url: '/kommunikator/data.php?action=get_gateways',
                        method: 'get',
                        success: function (response) {
                            providers = JSON.parse(response);
                            var i = 0;
                            for (i; i < providers['visible_total']; i++) {
                                if (providers['data'][i][0] == from_provider_id) {
                                    edit_provider = providers['data'][i];
                                    var from_pass = providers['data'][i][6];
                                    var from_uri = providers['data'][i][5];

                                    $("#login + label").addClass("active");
                                    $("#login").val(from_uri);
                                    $("#password + label").addClass("active");
                                    $("#password").val(from_pass);
                                    $(".img_provider > img").attr("src", from_elem.children(".povider_logo_cont").children().attr('src'));
                                    $("#header_title").html("Редактирование SIP подключения<br/>" + from_elem.children(".accaunt_uri").text());
                                    $("#header_decription").text("Измените данные и нажмите сохранить");
                                    
                                    var provider_domain = providers['data'][i][11]; console.log(provider_domain);
                
                
                    var res = provider_domain.indexOf('mangosip.ru'); console.log(res);
                    if (res>=0) {
                        $('#domain').val(provider_domain);
            $('#label_domain').addClass('active');
            $('#domain').show();
                    }else
                    {
                        $('#domain, #label_domain').hide();
                    }
                
                                    $("#prev_button").show();
                                    $("#done_button").hide();
                                    $("#edit_connection").show();
                                }
                            }
                            if ($.isEmptyObject(edit_provider)) {
                                alert('требуется обновить список провайдеров');
                                init_master();
                                getProvidersList();
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            myAlert(textStatus, errorThrown);
                        }
                    });
                });

                $('.switch').on("click", function () {console.log(this);
                    var upd_provider = {};
                    

                    from_elem = $(this).parent();
                    // какое-то тупое, исправить
//                    if (from_elem.hasClass('right_cont')) {
//                        from_elem = from_elem.parent();
//                    }
                    var from_provider_id = from_elem.children(".provider-id").html();
                    upd_provider.id = from_provider_id;
                    //upd_provider.enabled = ($(this).children('input[type="checkbox"]').prop("checked")) ? true : false;
                    
                    upd_provider.enabled = $('.switch_click').prop("checked") ? true : false;
                    $.ajax({
                        url: "/kommunikator/data.php?action=update_gateways",
                        method: 'post',
                        processData: false,
                        contentType: 'text/plain',
                        data: JSON.stringify(upd_provider),
                        success: function (response) {
                          //  edit_provider = empty_provider;
                            init_master();
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            myAlert(textStatus, errorThrown);
                        }
                    });
                });
            }
            $("#current_connections > .collection").slideDown('fast');
        }
    });
}


 $(document).keypress(function( event ) {
      if ( event.which == 13 ) {
        if ($('#my_alert').is(":visible")){
            $("#myAlertOkBtn").click();
        }else {
            if ($("#current_connections").is(":visible") || $("#voice_choose").is(":visible") || $("#enter_login_password").is(":visible")){
                $("#done_button").click();
            }
             if ($("#edit_connection").is(":visible")){
                $("#save_conn_btn").click();
            }
        } 
      }
    });
$(document).ready(function () {
    
    
    
    //$('select').material_select();
    init_master();
    getProvidersList();
    getProvidersList1();

    $('#reset_btn').on('click', function () {
        getProvidersList();
    });

    $(".provider-item").on('click', function () {
        var item = this.id.split('_')[0]; // 
        create_provider = empty_provider;
        create_provider.domain = providersList.providers[item]["domain"];
        create_provider.server = providersList.providers[item]["server"];
        var img_src = providersList.providers[item]["img"];
        var ref_link = providersList.providers[item]["ref_link"];

        $(this).removeClass("active_item");
        $("#provider_choose").hide();
        $("#done_button").show();
        $("#header_title").html("Настройки SIP подключения");
        $(".img_provider > img").attr("src", img_src);
        $("#enter_login_password > div > form > span").show();
        $("#enter_login_password > div > form > span > a").attr("href", ref_link);
        $("#header_decription").html("Введите данные вашего SIP аккаунта");
        $("#enter_login_password").show();
        if(create_provider.server === '81.88.86.11'){
            $('#enter_domain').val(create_provider.domain);
            $('#label_enter_domain').addClass('active');
            $('#enter_domain').show();
        }
        else{
            $('#enter_domain, #label_enter_domain').hide();
        }
        $("#page_3").show();
        $("#done_button > a").text("Сохранить");
    });


    $("#add_conn_btn").on('click', function () {
        $("#current_connections").hide();
        $("#header_title").text("Выбор SIP провайдера");
        $("#header_decription").text("Выберите вашего SIP провайдера");
        $("#done_button").hide();
        $("#prev_button").show();
        $("#provider_choose").show();
        $("#page_2").show();
    });

    $("#prev_button, .close_icon").on('click', function () {
        if ($("#provider_choose").is(":visible")) {
            init_master();
            getProvidersList();

        } else if ($("#enter_login_password").is(":visible")) {
            $("#enter_login_password").hide();
            $("#current_connections").hide();
            $("#header_title").text("Выбор SIP провайдера");
            $("#header_decription").text("Выберите вашего SIP провайдера");
            $("#done_button").hide();
            $("#prev_button").show();
            $("#provider_choose").show();
            $("#page_2").show();

        } else if ($("#edit_connection").is(":visible")) {
            init_master();
            getProvidersList();
        }
    });

    $("#done_button").on('click', function () {

        if ($("#enter_login_password").is(":visible")) {
            if ($("#enter_login").val() && $("#enter_password").val()) {

                create_provider.username = $("#enter_login").val();
                create_provider.password = $("#enter_password").val();
                create_provider.gateway = $("#enter_login").val();
                create_provider.description = $("#enter_login").val();
                create_provider.authname = $("#enter_login").val();
                create_provider.callerid = $("#enter_login").val();
                if(create_provider.server === '81.88.86.11'){
                    create_provider.domain = $('#enter_domain');
                    }
                $("#page_1, #page_2, #page_3").hide();

                $.ajax({
                    url: "/kommunikator/data.php?action=create_gateways",
                    method: 'post',
                    processData: false,
                    contentType: 'text/plain',
                    data: JSON.stringify(create_provider),
                    success: function (response) {
                        create_provider = empty_provider;
                        init_master();
                        getProvidersList();
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        myAlert(textStatus, errorThrown);
                    }
                });

            } else {
                myAlert("Внимание", "Поля логин и пароль должны быть заполнены!");
            }
        } else if ($("#current_connections").is(":visible")) {console.log(5555555);
            Ext.getCmp('ProviderWizard').close();

        }
    });

    $("#save_conn_btn").on('click', function () {
        var provider_id;
        if ($("#login").val() && $("#password").val()) {
            provider_id = edit_provider[0];
            edit_provider = {};
            edit_provider.id = provider_id;
            edit_provider.password = $("#password").val();
            edit_provider.username = $("#login").val();
            edit_provider.authname = $("#login").val();
            edit_provider.callerid = $("#login").val();
            edit_provider.gateway = $("#login").val();
            if ($("#domain").is(":visible")){
                edit_provider.domain = $("#domain").val();
            }

            $.ajax({
                url: "/kommunikator/data.php?action=update_gateways",
                method: 'post',
                processData: false,
                contentType: 'text/plain',
                data: JSON.stringify(edit_provider),
                success: function (response) {
                    //  $("#current_connections > .collection").empty();
                    edit_provider = empty_provider;
                    init_master();
                    getProvidersList();
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    myAlert(textStatus, errorThrown);
                }
            });
        } else {
            myAlert("Внимание", "Поля логин и пароль должны быть заполнены!");
        }
    });

    $("#del_connection_btn").on('click', function () {
        var new_obj = {};
        var result = confirm("Вы уверены что хотите удалить запись?");
        if (result) {
            new_obj.id = edit_provider[0];
            $.ajax({
                url: "/kommunikator/data.php?action=destroy_gateways",
                method: 'post',
                processData: false,
                contentType: 'text/plain',
                data: JSON.stringify(new_obj),
                success: function (response) {
                    edit_provider = empty_provider;
                    init_master();
                    getProvidersList();
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    myAlert(textStatus, errorThrown);
                }
            });
        }
    });
});

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

var lang = getUrlVars()["lang"];
(lang == 'ru') ? docss("ru.css") : (lang == 'en') ? docss("en.css") : docss("ru.css");

function docss(name)
{
    var st = document.createElement("link");
    st.setAttribute("rel", "stylesheet");
    st.setAttribute("href", "css/" + name);
    document.getElementsByTagName("head")[0].appendChild(st)
}

function myAlert(header, text) {
    $("#my_alert > div.modal-content > h4").text(header);
    $("#my_alert > div.modal-content > p").text(text);
    $('#my_alert').openModal();
}