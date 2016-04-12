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
var provider_img={
    'voip.mtt.ru':'images/providers/youmagicpro.png',
    'mangosip.ru':'images/providers/MangoTel.png',
    'multifon.ru':'images/providers/megafon.png'
};

var providersList =
        {
            providers: [
                {
                    id: 'megafon',
                    name_ru: 'Мегафон',
                    img: 'images/providers/megafon.png',
                    host: '193.201.229.35',
                    domain: 'multifon.ru',
                    ref_link: 'http://multifon.ru/help'
                },
                {
                    id: 'MangoTel',
                    name_ru: 'Манго Телеком',
                    img: 'images/providers/MangoTel.png',
                    host: 'mangosip.ru',
                    ref_link: 'http://www.mango-office.ru/shop/tariffs/vpbx?p=400000034'
                },
                {
                    id: 'youmagicpro',
                    name_ru: 'Youmagic.pro',
                    img: 'images/providers/youmagicpro.png',
                    host: 'voip.mtt.ru',
                    ref_link: 'https://youmagic.pro/ru/services/mnogokanalnyj-nomer?aid=3643'
                }
            ]
        };
 
function getProvidersList1() {
    for (var key in providersList.providers) {
        if (providersList.providers[key].ref_link){
            $('#provider_choose > .collection').append('<li class="collection-item">'+
            '<div class="left povider_logo_cont">'+
                '<img src="'+providersList.providers[key].img+'" alt="'+providersList.providers[key].name_ru+'" url="'+providersList.providers[key].host+'" ref="'+providersList.providers[key].ref_link+'" class="provider_logo">'+
            '</div>'+
            '<span class="title provider_name">'+providersList.providers[key].name_ru+'</span>'+
        '</li>');
        }else {
            $('#provider_choose > .collection').append('<li class="collection-item">'+
                '<div class="left povider_logo_cont">'+
                    '<img src="'+providersList.providers[key].img+'" alt="'+providersList.providers[key].name_ru+'" url="'+providersList.providers[key].host+'" class="provider_logo">'+
                '</div>'+
                '<span class="title provider_name">'+providersList.providers[key].name_ru+'</span>'+
            '</li>');
        }
       
    }
};

function getImgSipConnection(host) {
    if (host) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].host == host) {
                return providersList.providers[key].img;
            }
        }
    }
    return;
}

function getNameProvConnection(host) {
    if (host) {
        for (var key in providersList.providers) {
            if (providersList.providers[key].host == host) {
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
                return providersList.providers[key].host;
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
#voice_choose, #prev_button").hide();

    $("#prev_button").hide();
    $("#current_connections").show();
    $("#header_title").text("Ваши текущие Sip подключения");
    $("#header_decription").text("Вы можете отредактировать ваши Sip подключения, или добавить новые");
    $("#page_1").show();
}
function getProvidersList() {

    $.ajax({
        url: '/kommunikator/data.php?action=get_gateways',
        method: 'get',
        success: function (response) {
            var status, provider_switch, provider_login, provider_id, img_src, domain;
            providers = JSON.parse(response);
            console.log(providers);
            console.log('visible_total ' + providers['visible_total']);
            var i = 0;
            for (i; i < providers['visible_total']; i++) {
                
                provider_id = providers['data'][i][0]; //0 - id провайдера
                
                domain = providers['data'][i][11];
                img_src = provider_img[domain];
                if(!img_src){
                    var res = domain.indexOf('mangosip.ru');
                    if(res){
                        img_src = 'images/providers/MangoTel.png';
                    }else{
                        img_src = 'images/providers/unkonwn.png';
                    }
                }
                
                provider_login = providers['data'][i][4]; //4 - username провайдера
                
                status = providers['data'][i][1]; //1 - id статуса провайдера
                
                if (status === 'online') 
                    provider_switch = 'checked';

                if (status !== 'online' && status !== 'offline')
                    status = 'none-status';

                $("#current_connections > .collection").append(
                        '<li class="collection-item with_del valign-wrapper">' +
                        '<div id='+provider_id+' style="display:none;"></div>' +
                        '<div class="click_area valign-wrapper">' +
                        '<div class="povider_logo_cont">' +
                        '<img src="'+img_src+'" alt="tata" class="provider_logo">' +
                        '</div>' +
                        '<span class="title accaunt_uri valign" password="5555">'+provider_login+'</span>' +
                        '</div>' +
                        '<div class="right_cont valign-wrapper">' +
                        '<div class="switch">' +
                        '<label title="Подключить аккаунт"><input type="checkbox" '+provider_switch+'><span class="lever"></span></label>' +
                        '</div>' +
                        '<div class="indicator ' + status + '_color">' + provider_status[status] + '</div>' +
                        '<div class="edit_btn_cont click_area"><a href="javascript:void(0)" class="btn-flat grey-text">РЕДАКТИРОВАТЬ</a></div>' +
                        '</div>' +
                        '<a href="javascript:void(0)" class="del_btn">' +
                        '<img src="images/delete.png" alt="del" class="currents_icon del_icon">' +
                        '</a>' +
                        '</li>'
                        );
            }
        }
    });
}

$(document).ready(function () {
    $('select').material_select();
    init_master();
    getProvidersList();
getProvidersList1();
    $("#provider_choose > .collection > .collection-item").on('click', function () {
        $(this).parent().children(".active_item").removeClass("active_item");
        if ($(this).hasClass("active_item")) {
            $(this).removeClass("active_item");
        } else {
            $(this).addClass("active_item");
            if ($(this).parent().parent().attr("id") == "provider_choose") {
                $("#provider_choose").hide();
                $("#done_button").show();
                $("#header_title").html("Настройки Sip подключения");
                $(".img_provider > img").attr("src", $("#provider_choose .collection-item.active_item .provider_logo").attr('src'));
                $("#enter_login_password > div > form > span").show();
                $("#enter_login_password > div > form > span > a").attr("href", $("#provider_choose > .collection > .collection-item.active_item > div > img").attr("ref"));
                $("#header_decription").html("Введите данные вашего Sip аккаунта");
                $("#enter_login_password").show();
                $("#page_3").show();
            }
        }
    });
//

    $("#add_conn_btn").on('click', function () {
        $("#current_connections").hide();
        $("#header_title").text("Выбор Sip провайдера");
        $("#header_decription").text("Выберите вашего Sip провайдера");
        $("#done_button").hide();
        $("#prev_button").show();
        $("#provider_choose").show();
        $("#page_2").show();
    });

    $("#prev_button").on('click', function () {
        if ($("#current_connections").is(":visible")) {
            $("#done_button > a").text("Закрыть");
            $("#work_mode > .collection > .collection-item").removeClass("active_item");
            $("#current_connections").hide();
            $("#prev_button").hide();
            $("#page_1").hide();
            $("#done_button").show();
            $("#work_mode").show();
            $("#header_title").text("Мастер настроек");
        } else if ($("#provider_choose").is(":visible")) {
            $("#provider_choose").hide();
            $("#done_button").show();
            $("#prev_button").hide();
            $("#provider_choose > ul > .active_item").removeClass("active_item");
            $("#current_connections").show();
            $("#page_2").hide();
            $("#header_title").text("Ваши текущие Sip подключения");
            $("#header_decription").text("Вы можете отредактировать ваши Sip подключения, или добавить новые");
            $.ajax({
                url: '/gateways',
                method: 'get',
                success: function (response) {
                    var size = 0;
                    for (var i = 10; i < response.data[0].length; i++) {
                        if (response.data[0][i] != null) {
                            size++
                        }
                    }
                    var data = response.data[0];
                    for (var i = 0; i < size; i++) {
                        if (data[i] == 0) {
                            $("#conn_" + i + " > div > .indicator").css("color", "gray").text("Отключён");
                        } else if (data[i] == 2) {
                            $("#conn_" + i + " > div > .indicator").css("color", "red").text("Ошибка регистрации");
                        } else if (data[i] == 1) {
                            $("#conn_" + i + " > div > .indicator").css("color", "green").text("Подключён");
                        }
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    myAlert(textStatus, errorThrown);
                }
            });
        } else if ($("#enter_login_password").is(":visible")) {
            $("#enter_login_password").hide();
            $("#done_button").hide();
            $("#enter_login_password > div > form").trigger('reset');
            $("#provider_choose").show();
            $("#page_3").hide();
            $("#header_title").text("Выбор Sip провайдера");
            $("#header_decription").text("Выберите вашего Sip провайдера");
        } else if ($("#edit_connection").is(":visible")) {
            $("#edit_connection").hide();
            $("#prev_button").hide();
            $("#done_button").show();
            $("#page_1").show();
            $("#header_title").text("Ваши текущие Sip подключения");
            $("#header_decription").text("Вы можете отредактировать ваши Sip подключения, или добавить новые");
            from_elem.removeClass("active_item");
            from_elem.children(".right_cont").removeClass("active_item");
            $("#edit_connection > div >form").trigger('reset');
            $("#current_connections").show();
            $.ajax({
                url: '/gateways',
                method: 'get',
                success: function (response) {
                    var size = 0;
                    for (var i = 10; i < response.data[0].length; i++) {
                        if (response.data[0][i] != null) {
                            size++
                        }
                    }
                    var data = response.data[0];
                    for (var i = 0; i < size; i++) {
                        if (data[i] == 0) {
                            $("#conn_" + i + " > div > .indicator").css("color", "gray").text("Отключён");
                        } else if (data[i] == 2) {
                            $("#conn_" + i + " > div > .indicator").css("color", "red").text("Ошибка регистрации");
                        } else if (data[i] == 1) {
                            $("#conn_" + i + " > div > .indicator").css("color", "green").text("Подключён");
                        }
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    myAlert(textStatus, errorThrown);
                }
            });
        }
    });

    $("#done_button").on('click', function () {

        if ($("#enter_login_password").is(":visible")) {
            if ($("#enter_login").val() && $("#enter_password").val()) {

                $("#work_mode > .collection > .collection-item").removeClass("active_item");
                $("#enter_login_password").hide();
                $("#prev_button").hide();
                $("#page_1, #page_2, #page_3").hide();
                var prov_name = $("#provider_choose > ul > li.collection-item.active_item > div > img").attr("alt");
                var prov_img = $("#provider_choose > ul > li.collection-item.active_item > div > img").attr("src");
                var prov_url = $("#provider_choose > ul > li.collection-item.active_item > div > img").attr("url");
                $("#current_connections > .collection").append(
                        '<li id="conn_' + cur_acc_list.length + '" class="collection-item with_del valign-wrapper">' +
                        '<div class="click_area valign-wrapper">' +
                        '<div class="povider_logo_cont">' +
                        '<img src="' + prov_img + '" alt="' + prov_name + '" class="provider_logo">' +
                        '</div>' +
                        '<span class="title accaunt_uri valign" password="' + $("#enter_password").val() + '">' + $("#enter_login").val() + '</span>' +
                        '</div>' +
                        '<div class="right_cont valign-wrapper">' +
                        '<div class="switch">' +
                        '<label title="Подключить аккаунт"><input type="checkbox"><span class="lever"></span></label>' +
                        '</div>' +
                        '<div class="indicator">Отключён</div>' +
                        '<div class="edit_btn_cont click_area"><a href="javascript:void(0)" class="btn-flat grey-text">РЕДАКТИРОВАТЬ</a></div>' +
                        '</div>' +
                        '<a href="javascript:void(0)" class="del_btn">' +
                        '<img src="images/delete.png" alt="del" class="currents_icon del_icon">' +
                        '</a>' +
                        '</li>'
                        );

                $("#current_connections > .collection > .collection-item:last-child > .del_btn").on('click', function () {
                    var tmp_id = $(this).parent().attr("id").substr(5);
                    var del_index = tmp_id;
                    $("#conn_" + del_index).remove();
                    cur_acc_list.splice(del_index, 1);
                    $.ajax({
                        url: '/resourceData/settings',
                        method: 'get',
                        success: function (response) {
                            var data = jQuery.parseJSON(response.data[0].value);
                            data.sipAccounts.splice(del_index, 1);
                            response.data[0].create = false;
                            response.data[0].name = 'config/config';
                            response.data[0].value = JSON.stringify(data, null, 4);
                            $.ajax({
                                url: "/resourceData/update",
                                method: 'put',
                                data: response.data[0],
                                success: function (response) {
                                    $.get("http://" + hostname + ":" + port + "/resourceData/settings", function () {
                                        var next_ind = parseInt(del_index) + 1;
                                        var iterator = $("#conn_" + next_ind);
                                        while (tmp_id != cur_acc_list.length) {
                                            console.log(iterator.next().attr("id"));
                                            iterator.attr("id", "conn_" + tmp_id);
                                            iterator = iterator.next();
                                            tmp_id++;
                                        }

                                    });
                                },
                                error: function (XMLHttpRequest, textStatus, errorThrown) {
                                    myAlert(textStatus, errorThrown);
                                }
                            });
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            myAlert(textStatus, errorThrown);
                        }
                    });

                });

                $('#current_connections > .collection > .collection-item:last-child input[type="checkbox"]').on("change", function () {
                    var tmp_id = $(this).parent().parent().parent().parent().attr("id").substr(5);
                    if ($(this).prop('checked')) {
                        cur_acc_list[tmp_id].disable = 0;
                        $(this).parent().attr("title", "Отключить аккаунт");
                    } else {
                        cur_acc_list[tmp_id].disable = 1;
                        $(this).parent().attr("title", "Подключить аккаунт");
                    }
                    $(this).prop('disabled', true);
                    var checkbox = $(this);
                    $.ajax({
                        url: '/resourceData/settings',
                        method: 'get',
                        success: function (response) {
                            var data = jQuery.parseJSON(response.data[0].value);
                            data.sipAccounts[tmp_id].disable = cur_acc_list[tmp_id].disable;
                            response.data[0].create = false;
                            response.data[0].name = 'config/config';
                            response.data[0].value = JSON.stringify(data, null, 4);
                            $.ajax({
                                url: "/resourceData/update",
                                method: 'put',
                                data: response.data[0],
                                success: function () {
                                    checkbox.prop('disabled', false);
                                },
                                error: function (XMLHttpRequest, textStatus, errorThrown) {
                                    myAlert(textStatus, errorThrown);
                                }
                            });
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            myAlert(textStatus, errorThrown);
                        }
                    });
                    $.ajax({
                        url: '/gateways',
                        method: 'get',
                        success: function (response) {
                            var size = 0;
                            for (var i = 10; i < response.data[0].length; i++) {
                                if (response.data[0][i] != null) {
                                    size++
                                }
                            }
                            var data = response.data[0];
                            for (var i = 0; i < size; i++) {
                                if (data[i] == 0) {
                                    $("#conn_" + i + " > div > .indicator").css("color", "gray").text("Отключён");
                                } else if (data[i] == 2) {
                                    $("#conn_" + i + " > div > .indicator").css("color", "red").text("Ошибка регистрации");
                                } else if (data[i] == 1) {
                                    $("#conn_" + i + " > div > .indicator").css("color", "green").text("Подключён");
                                }
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            myAlert(textStatus, errorThrown);
                        }
                    });
                });
                $("#current_connections > .collection > .collection-item:last-child .click_area").on('click', function () {
                    from_elem = $(this).parent();
                    if (from_elem.hasClass('right_cont')) {
                        from_elem = from_elem.parent();
                        $(this).parent().removeClass("active_item");
                    }
                    $(this).parent().parent().children(".active_item").removeClass("active_item");
                    if ($(this).parent().hasClass("active_item")) {
                        $(this).parent().removeClass("active_item");
                    } else {
                        $(this).parent().addClass("active_item");
                    }
                    $("#current_connections").hide();
                    $("#page_1").hide();
                    var from_uri = from_elem.children(".click_area").children(".accaunt_uri").text();
                    var from_pass = from_elem.children(".click_area").children(".accaunt_uri").attr("password");
                    $("#login + label").addClass("active");
                    $("#login").val(from_uri);
                    $("#password + label").addClass("active");
                    $("#password").val(from_pass);
                    $(".img_provider > img").attr("src", from_elem.children(".click_area").children(".povider_logo_cont").children().attr('src'));
                    $("#header_title").html("Редактирование Sip подключения<br/>" + from_elem.children(".click_area").children(".accaunt_uri").text());
                    $("#header_decription").text("Измените данные и нажмите сохранить");
                    $("#prev_button").show();
                    $("#done_button").hide();
                    $("#edit_connection").show();
                });
                $.ajax({
                    url: '/resourceData/settings',
                    method: 'get',
                    success: newSipConnection
                });
                $("#work_mode").show();
                $("#header_title").text("Мастер настроек");
                $("#sip_sett").click();
            } else {
                myAlert("Внимание", "Поля логин и пароль должны быть заполнены!");
            }
        } else if ($("#current_connections").is(":visible")) {
            $("#done_button > a").text("Закрыть");
            $("#work_mode > .collection > .collection-item").removeClass("active_item");
            $("#current_connections").hide();
            $("#prev_button").hide();
            $("#page_1").hide();
            $("#done_button").show();
            $("#work_mode").show();
            $("#header_title").text("Мастер настроек");

        } else if ($("#work_mode").is(":visible")) {
            window.location = '../../';
        }
    });

    $(".del_btn").on('click', function () {
        var tmp_id = $(this).parent().attr("id").substr(5);
        var del_index = tmp_id;
        $("#conn_" + del_index).remove();
        // if ($("#current_connections > ul").height() < document.documentElement.clientHeight/1.82) { 
        //     $("#current_connections").css("overflow-y","hidden");
        // }else{
        //     $("#current_connections").css("overflow-y","scroll");
        // }
        cur_acc_list.splice(del_index, 1);
        $.ajax({
            url: '/resourceData/settings',
            method: 'get',
            success: function (response) {
                var data = jQuery.parseJSON(response.data[0].value);
                data.sipAccounts.splice(del_index, 1);
                response.data[0].create = false;
                response.data[0].name = 'config/config';
                response.data[0].value = JSON.stringify(data, null, 4);
                $.ajax({
                    url: "/resourceData/update",
                    method: 'put',
                    data: response.data[0],
                    success: function (response) {
                        $.get("http://" + hostname + ":" + port + "/resourceData/settings", function () {
                            var next_ind = parseInt(del_index) + 1;
                            var iterator = $("#conn_" + next_ind);
                            console.log(iterator);
                            while (tmp_id != cur_acc_list.length) {
                                iterator.attr("id", "conn_" + tmp_id);
                                iterator = iterator.next();
                                tmp_id++;
                            }
                        });
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        myAlert(textStatus, errorThrown);
                    }
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                myAlert(textStatus, errorThrown);
            }
        });

    });

    $('input[type="checkbox"]').on("change", function () {
        var tmp_id = $(this).parent().parent().parent().parent().attr("id").substr(5);
        if ($(this).prop('checked')) {
            cur_acc_list[tmp_id].disable = 0;
            $(this).parent().attr("title", "Отключить аккаунт");
        } else {
            cur_acc_list[tmp_id].disable = 1;
            $(this).parent().attr("title", "Подключить аккаунт");
        }
        $(this).prop('disabled', true);
        var checkbox = $(this);
        $.ajax({
            url: '/resourceData/settings',
            method: 'get',
            success: function (response) {
                var data = jQuery.parseJSON(response.data[0].value);
                data.sipAccounts[tmp_id].disable = cur_acc_list[tmp_id].disable;
                response.data[0].create = false;
                response.data[0].name = 'config/config';
                response.data[0].value = JSON.stringify(data, null, 4);
                $.ajax({
                    url: "/resourceData/update",
                    method: 'put',
                    data: response.data[0],
                    success: function () {
                        checkbox.prop('disabled', false);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        myAlert(textStatus, errorThrown);
                    }
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                myAlert(textStatus, errorThrown);
            }
        });
        $.ajax({
            url: '/gateways',
            method: 'get',
            success: function (res) {
                var size = 0;
                for (var i = 10; i < res.data[0].length; i++) {
                    if (res.data[0][i] != null) {
                        size++
                    }
                }
                var data = res.data[0];
                for (var i = 0; i < size; i++) {
                    if (data[i] == 0) {
                        $("#conn_" + i + " > div > .indicator").css("color", "gray").text("Отключён");
                    } else if (data[i] == 2) {
                        $("#conn_" + i + " > div > .indicator").css("color", "red").text("Ошибка регистрации");
                    } else if (data[i] == 1) {
                        $("#conn_" + i + " > div > .indicator").css("color", "green").text("Подключён");
                    }
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                myAlert(textStatus, errorThrown);
            }
        });
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


function createConnections() {
    var img_src, img_alt;
    for (var i = 0; i < cur_acc_list.length; i++) {
        img_src = getImgSipConnection(cur_acc_list[i].host);
        img_alt = getNameProvConnection(cur_acc_list[i].host);
        if (cur_acc_list[i].disable == 1) {
            if (img_src && img_alt) {
                $("#current_connections > .collection").append(
                        '<li id="conn_' + i + '" class="collection-item with_del valign-wrapper">' +
                        '<div class="click_area valign-wrapper">' +
                        '<div class="povider_logo_cont">' +
                        '<img src="' + img_src + '" alt="' + img_alt + '" class="provider_logo">' +
                        '</div>' +
                        '<span class="title accaunt_uri valign" password="' + cur_acc_list[i].password + '">' + cur_acc_list[i].user + '</span>' +
                        '</div>' +
                        '<div class="right_cont valign-wrapper">' +
                        '<div class="switch">' +
                        '<label title="Подключить аккаунт"><input type="checkbox"><span class="lever"></span></label>' +
                        '</div>' +
                        '<div class="indicator">Отключён</div>' +
                        '<div class="edit_btn_cont click_area"><a href="javascript:void(0)" class="btn-flat grey-text">РЕДАКТИРОВАТЬ</a></div>' +
                        '</div>' +
                        '<a href="javascript:void(0)" class="del_btn">' +
                        '<img src="images/delete.png" alt="del" class="currents_icon del_icon">' +
                        '</a>' +
                        '</li>'
                        );
            } else {
                $("#current_connections > .collection").append(
                        '<li id="conn_' + i + '" class="collection-item with_del valign-wrapper">' +
                        '<div class="click_area valign-wrapper">' +
                        '<div class="povider_logo_cont">' +
                        '<img src="images/favicon.png" alt="provider" class="provider_logo">' +
                        '</div>' +
                        '<span class="title accaunt_uri valign" password="' + cur_acc_list[i].password + '">' + cur_acc_list[i].user + '</span>' +
                        '</div>' +
                        '<div class="right_cont valign-wrapper">' +
                        '<div class="switch">' +
                        '<label title="Подключить аккаунт"><input type="checkbox"><span class="lever"></span></label>' +
                        '</div>' +
                        '<div class="indicator">Отключён</div>' +
                        '<div class="edit_btn_cont click_area"><a href="javascript:void(0)" class="btn-flat grey-text">РЕДАКТИРОВАТЬ</a></div>' +
                        '</div>' +
                        '<a href="javascript:void(0)" class="del_btn">' +
                        '<img src="images/delete.png" alt="del" class="currents_icon del_icon">' +
                        '</a>' +
                        '</li>'
                        );
            }
        } else {
            if (img_src && img_alt) {
                $("#current_connections > .collection").append(
                        '<li id="conn_' + i + '" class="collection-item with_del valign-wrapper">' +
                        '<div class="click_area valign-wrapper">' +
                        '<div class="povider_logo_cont">' +
                        '<img src="' + img_src + '" alt="' + img_alt + '" class="provider_logo">' +
                        '</div>' +
                        '<span class="title accaunt_uri valign" password="' + cur_acc_list[i].password + '">' + cur_acc_list[i].user + '</span>' +
                        '</div>' +
                        '<div class="right_cont valign-wrapper">' +
                        '<div class="switch">' +
                        '<label title="Отключить аккаунт"><input type="checkbox" checked><span class="lever"></span></label>' +
                        '</div>' +
                        '<div class="indicator">Отключён</div>' +
                        '<div class="edit_btn_cont click_area"><a href="javascript:void(0)" class="btn-flat grey-text">РЕДАКТИРОВАТЬ</a></div>' +
                        '</div>' +
                        '<a href="javascript:void(0)" class="del_btn">' +
                        '<img src="images/delete.png" alt="del" class="currents_icon del_icon">' +
                        '</a>' +
                        '</li>'
                        );
            } else {
                $("#current_connections > .collection").append(
                        '<li id="conn_' + i + '" class="collection-item with_del valign-wrapper">' +
                        '<div class="click_area valign-wrapper">' +
                        '<div class="povider_logo_cont">' +
                        '<img src="images/favicon.png" alt="provider" class="provider_logo">' +
                        '</div>' +
                        '<span class="title accaunt_uri valign" password="' + cur_acc_list[i].password + '">' + cur_acc_list[i].user + '</span>' +
                        '</div>' +
                        '<div class="right_cont valign-wrapper">' +
                        '<div class="switch">' +
                        '<label title="Отключить аккаунт"><input type="checkbox" checked><span class="lever"></span></label>' +
                        '</div>' +
                        '<div class="indicator">Отключён</div>' +
                        '<div class="edit_btn_cont click_area"><a href="javascript:void(0)" class="btn-flat grey-text">РЕДАКТИРОВАТЬ</a></div>' +
                        '</div>' +
                        '<a href="javascript:void(0)" class="del_btn">' +
                        '<img src="images/delete.png" alt="del" class="currents_icon del_icon">' +
                        '</a>' +
                        '</li>'
                        );
            }
        }
    }




    $(".close_icon").on('click', function () {
        $("#prev_button").click();
    });
    $(".click_area").on('click', function () {
        $("#current_connections").hide();
        $("#page_1").hide();
        from_elem = $(this).parent();
        if (from_elem.hasClass('right_cont')) {
            from_elem = from_elem.parent();
            $(this).parent().removeClass("active_item");
        }
        $(this).parent().parent().children(".active_item").removeClass("active_item");
        if ($(this).parent().hasClass("active_item")) {
            $(this).parent().removeClass("active_item");
        } else {
            $(this).parent().addClass("active_item");
        }
        var from_uri = from_elem.children(".click_area").children(".accaunt_uri").text();
        var from_pass = from_elem.children(".click_area").children(".accaunt_uri").attr("password");
        $("#login + label").addClass("active");
        $("#login").val(from_uri);
        $("#password + label").addClass("active");
        $("#password").val(from_pass);
        $(".img_provider > img").attr("src", from_elem.children(".click_area").children(".povider_logo_cont").children().attr('src'));
        $("#header_title").html("Редактирование Sip подключения<br/>" + from_elem.children(".click_area").children(".accaunt_uri").text());
        $("#header_decription").text("Измените данные и нажмите сохранить");
        $("#prev_button").show();
        $("#done_button").hide();
        $("#edit_connection").show();
    });

    $("#save_conn_btn").on('click', function () {
        if ($("#login").val() && $("#password").val()) {
            $("#edit_connection").hide();
            $("#page_1").show();
            $("#prev_button").hide();
            $("#done_button").show();
            $("#prev_button").hide();
            $("#header_title").text("Ваши текущие Sip подключения");
            $("#header_decription").text("Вы можете отредактировать ваши Sip подключения, или добавить новые");
            from_elem.removeClass("active_item");
            from_elem.children(".right_cont").removeClass("active_item");
            var from_uri = from_elem.children(".click_area").children(".accaunt_uri").text();

            $.ajax({
                url: '/resourceData/settings',
                method: 'get',
                success: recordSipConnection
            });
            from_elem.children(".click_area").children(".accaunt_uri").text($("#login").val());
            from_elem.children(".click_area").children(".accaunt_uri").attr("password", $("#password").val());
            $.ajax({
                url: '/gateways',
                method: 'get',
                success: function (res) {
                    var size = 0;
                    for (var i = 10; i < res.data[0].length; i++) {
                        if (res.data[0][i] != null) {
                            size++
                        }
                    }
                    var data = res.data[0];
                    for (var i = 0; i < size; i++) {
                        if (data[i] == 0) {
                            $("#conn_" + i + " > div > .indicator").css("color", "gray").text("Отключён");
                        } else if (data[i] == 2) {
                            $("#conn_" + i + " > div > .indicator").css("color", "red").text("Ошибка регистрации");
                        } else if (data[i] == 1) {
                            $("#conn_" + i + " > div > .indicator").css("color", "green").text("Подключён");
                        }
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    myAlert(textStatus, errorThrown);
                }
            });

        } else {
            myAlert("Внимание", "Поля логин и пароль должны быть заполнены!");
        }
    });

    $("#page_1").on('click', function () {
        if ($("#provider_choose").is(":visible")) {
            $("#prev_button").click();
        } else if ($("#enter_login_password").is(":visible")) {
            $("#prev_button").click();
            $("#prev_button").click();
        }
    });
    $("#page_2").on('click', function () {
        if ($("#enter_login_password").is(":visible")) {
            $("#prev_button").click();
        }
    });
    $("#del_connection_btn").on('click', function () {
        var result = confirm("Вы уверены что хотите удалить запись?")
        if (result) {
            from_elem.children('.del_btn').click();
            $("#prev_button").click();
        }
    });

}

function newSipConnection(response) {
    var data = jQuery.parseJSON(response.data[0].value);
    var login = $("#enter_login").val();
    var pass = $("#enter_password").val();
    var host = $("#provider_choose > ul > li.collection-item.active_item > div > img").attr("url");
    var domain = getDomainSipConnection($("#provider_choose > ul > li.collection-item.active_item > div > img").attr("alt"));
    var sipAccount = {
        host: host,
        expires: 60,
        user: login,
        password: pass,
        disable: 1
    };
    if (domain) {
        sipAccount['domain'] = domain;
    }
    ;
    cur_acc_list[cur_acc_list.length] = sipAccount;
    var idSipRecord = cur_acc_list.length - 1;
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
            $.get("http://" + hostname + ":" + port + "/resourceData/settings", function () {
                $("#provider_choose > ul > .active_item").removeClass("active_item");
                $("#enter_login_password > div > form").trigger('reset');
            });
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            myAlert(textStatus, errorThrown);
        }
    });
}

function recordSipConnection(response) {
    var data = jQuery.parseJSON(response.data[0].value);
    var login = $("#login").val();
    var pass = $("#password").val();
    var host = getHostSipConnection(from_elem.children(".click_area").children().children().attr("alt"));
    var domain = getDomainSipConnection(from_elem.children(".click_area").children().children().attr("alt"));
    var idSipRecord = from_elem.attr('id').substr(5);
    var sipAccount = {
        host: host,
        expires: 60,
        user: login,
        password: pass,
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

    sipAccount['disable'] = data.sipAccounts[idSipRecord].disable;

    //     sipAccount['disable'] = 1;
    // }

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
            $.get("http://" + hostname + ":" + port + "/resourceData/settings", function () {
                from_elem.removeClass("active_item");
                $("#edit_connection > div >form").trigger('reset');
            });
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            myAlert(textStatus, errorThrown);
        }
    });
}
