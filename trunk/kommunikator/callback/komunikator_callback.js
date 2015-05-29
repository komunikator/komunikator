var DCB = {
    debug:false
};

(function () {

    c_uservisit = 'digt_callback_user_visit';
    c_urlhistory = 'digt_callback_url_history';
    c_actsomepagevisit = 'digt_callback_act_some_pagevisit';
    c_actexitvisit = 'digt_callback_act_exitvisit';
    c_numberpage = 'digt_callback_act_numberpage';
    c_scrollingvisit = 'digt_callback_act_scrollingvisit';
    c_createorder = 'digt_callback_createorder';

    function addListener(obj, type, listener) {
        if (obj.addEventListener) {
            obj.addEventListener(type, listener, false);
            return true;
        } else if (obj.attachEvent) {
            obj.attachEvent('on' + type, listener);
            return true;
        }
        return false;
    }

    DCB.metrica = function () {     // scrolling 
        addListener(window.parent.window, 'scroll', function () {
            var sT = Number($(window.parent.window).scrollTop());
            var cH = Number($(window.parent.document).height()-$(window.parent.window).height());
	    if (DCB.debug == true) console.log('sT='+sT+' cH='+cH);
            if (sT == cH)
            {
                if (DCB.incCookie(c_scrollingvisit) > 1)   // сработает N раз ==1 - сработает 1 раз 
                {
                    DCB.Create_order(msg_on_metrica);
                    if (DCB.debug == true) console.log('скролл до конца страницы');
                }
            }
        });
    };

    DCB.setCookie = function (cookie_name, cookie_value, cookie_expires) // установка cookie
    {
        var c_exp = 1;
        if (cookie_expires)
            c_exp = cookie_expires;
        $.cookie.json = true;
        $.cookie(cookie_name, cookie_value, {expires: c_exp});
    };

    DCB.getCookie = function (cookie_name)  // получение cookie
    {
        $.cookie.json = true;
        return $.cookie(cookie_name);
    };

    DCB.checkCookie = function (cookie_name, cookie_value) // проверка куки
    {
        $.cookie.json = true;
        if (cookie = $.cookie(cookie_name))
            if (cookie_value)
                if (cookie == cookie_value)
                    return true
                else
                    return false;
            else
                return true;
        return false;
    };

    DCB.incCookie = function (cookie_name, cookie_expires)    //  инкремент куки
    {
        $.cookie.json = true;
        var cookie = DCB.getCookie(cookie_name);
        if (!cookie)
        {
            DCB.setCookie(cookie_name, 1, cookie_expires);
            return 1;
        }
        var cookie_n = Number(cookie) + 1;
        DCB.setCookie(cookie_name, cookie_n, cookie_expires);
        return cookie_n;
    };

    DCB.createObject = function (propName, propValue)
    {
        this[propName] = propValue;
    };

    DCB.user_visit = function ()                   // проверка посещения сайта клиентом
    {
        var ref_domain = window.parent.document.referrer == '' ? '' : window.parent.document.referrer.split('/')[2];
        var my_domain = location.hostname;
        if (DCB.incCookie(c_uservisit) > 1)
            if (ref_domain != my_domain)
                DCB.Create_order(msg_on_user_visit);
    };

    var timestamp = new Date().getTime().toString();
    var urlhistory;

    DCB.check_urlhistory = function ()           // проверка history url

    {
        if (!urlhistory)
            urlhistory = [];
        urlhistory.push({'timestamp': timestamp, 'url': window.location.toString()});
        DCB.setCookie(c_urlhistory, urlhistory);

        if (urlhistory.length > 0)         // проверка посещения определенной страницы сайта 
            if (urlhistory[urlhistory.length - 1].url == specificurl)    // http://localhost/index.html
                if (DCB.incCookie(c_actsomepagevisit) == 1) // >1 - показывать N раз 
                {
                    DCB.Create_order(msg_on_specificpage);
                    if (DCB.debug == true) console.log('это определенная страница');
                }
    };



    DCB.check_numberpage = function () {    // проверка кол-ва посещенных страниц

        var number_key = urlhistory.length;
        if (number_key >= number_page)
        {
            if (DCB.incCookie(c_numberpage) == 1)            //  if(DCB.incCookie(c_numberpage) > 1) 
            {
                DCB.Create_order(msg_on_check_urlhistory);
                if (DCB.debug == true) console.log('это количество посещенных страниц');
            }
        }
    };

    DCB.user_activity2 = function () {     // проверка активности пользователя по скролу, нажатию на клавишу, движению мыши 

        act_time = ua_seconds;          // время проведения клиента на сайте 
        act_threshold = 5;
        act_threshold_f = true;

        act_timer = setTimeout(function ()
        {
            clearTimeout(act_timer);
            if (act_threshold_f == false)
            {
                DCB.Create_order(msg_on_user_activity2);
                if (DCB.debug == true) console.log('activ');
            }
        }, 1000 * act_time);

        $(window.parent.document).bind('mousemove keydown scroll', function ()
        {
            act_threshold_f = false;
            act_th_timer = setTimeout(function ()
            {
                act_threshold_f = true;
                clearTimeout(act_th_timer);
            }, 1000 * act_threshold);
        });
    };

    DCB.user_exit = function ()  // уход со страницы сайта 
    {

        window.onbeforeunload = function () {
            if (DCB.incCookie(c_actexitvisit) == 2)
            {
                return msg_on_user_exit;
                if (DCB.debug == true) console.log('уход со страницы');
            }
        };
    };

    DCB.selectcolor = function () // выбор цвета кнопки 
    {

        if ($('.icon_box').get(0))
        {
            $('.icon_box').css('background', '#' + color_hex_before);
            addListener($('.icon_box').get(0), 'mouseover', function () {
                $('.icon_box').css('background', '#' + color_hex_after);
            });
            addListener($('.icon_box').get(0), 'mouseout', function () {
                $('.icon_box').css('background', '#' + color_hex_before);
            });
        }
        if (color_hex_before == "ffffff" || color_hex_after == "ffffff") {
            $('.icon1').css('color', '#000000');
        }
    };

    DCB.checkbrowser = function () // определение типа браузера
    {
        var user = detect.parse(navigator.userAgent);
        if (DCB.debug == true) console.log(user.browser.family + user.browser.version + user.os.name);
        if (user.browser.family === 'IE') {

            $('.some_background').css('margin-top', '-20px');
            $('.some_background').css('height', '16px');
        }
    };

    DCB.setFramePosSize = function (x,y,width,height)
    {
        window.parent.document.getElementById('komunikatorCallbackFrame').width=width;
        window.parent.document.getElementById('komunikatorCallbackFrame').height=height;
        window.parent.document.getElementById('komunikatorCallbackFrame').style.left=x;
        window.parent.document.getElementById('komunikatorCallbackFrame').style.top=y;
    };

    DCB.correctScreen = function ()           // определение размеров экрана
    {
		var client_w = $(window.parent.document.documentElement).width();
		var client_h = $(window.parent.document.documentElement).height();
		var win_h;
		var win_w;
		if (window.parent.document.compatMode === 'BackCompat') {
			win_h = window.parent.document.body.clientHeight;
			win_w = window.parent.document.body.clientWidth;
		} else {
			win_h = window.parent.document.documentElement.clientHeight;
			win_w = window.parent.document.documentElement.clientWidth;
		}
			if ($('.icon_box').get(0))
			{
				if ($('.icon_box').css('display') == 'none')
			{
			DCB.setFramePosSize(0,0,win_w,win_h);
			} else
			{
			DCB.setFramePosSize(win_w-150,win_h-150,74,74);
			}
		}
    };

    DCB.begin = function () {
        $("head").prepend("<style type=\"text/css\">" +
            "@font-face {\n" +
            "\tfont-family: \"FontAwesome\";\n" +
            "\tfont-weight: normal;\n" +
            "\tfont-style: normal;\n" +
            "\tsrc: url('"+dcb_id_server+"/callback/font-awesome-4.3.0/fonts/fontawesome-webfont.eot?v=4.3.0');\n" +
            "\tsrc: url('"+dcb_id_server+"/callback/font-awesome-4.3.0/fonts/fontawesome-webfont.eot?#iefix&v=4.3.0') format('embedded-opentype'),  url('"+dcb_id_server+"/callback/font-awesome-4.3.0/fonts/fontawesome-webfont.woff2?v=4.3.0') format('woff2'),  url('"+dcb_id_server+"/callback/font-awesome-4.3.0/fonts/fontawesome-webfont.woff?v=4.3.0') format('woff'),  url('"+dcb_id_server+"/callback/font-awesome-4.3.0/fonts/fontawesome-webfont.ttf?v=4.3.0') format('truetype'),  url('"+dcb_id_server+"/callback/font-awesome-4.3.0/fonts/fontawesome-webfont.svg?v=4.3.0#fontawesomeregular') format('svg');\n" +
            "}\n" +
            "</style>");
        $(document).ready(function () {

            $('body').append('<div id="dcb_id" class="dcb"></div>');
            $('#dcb_id').append('<div id="circle" class="icon_box" onClick="DCB.Create_order(undefined,true);"><i class="ball icon1 fa fa-phone fa-3x"></i>' +
                    '</div><div style="display: none;"><div class="box-modal" id="win_order_7503523488"><div class="mod_header_7894788111"><div class="box-modal_close arcticmodal-close">X</div></div>' +
                    '<div class="mod_body_1427621553" id="win_order_content_9268377087"></div><div class="mod_footer_2196269136" id="podpic_komunikator_1749966526"><div class="text_silka_komunicator_9989142638">Работает на технологии</div>' +
                    '<a href="http://komunikator.ru/" target="_blank"><div class="some_background"></div></a></div></div>');

            urlhistory = DCB.getCookie(c_urlhistory);

            DCB.selectcolor();   // приоритет вызова функций 
            DCB.correctScreen();
            DCB.checkbrowser();
            if (on_metrica == true)
                DCB.metrica();
            if (on_user_activity2 == true)
                DCB.user_activity2();
            if (on_check_urlhistory == true)
                DCB.check_urlhistory();
            if (on_user_visit == true)
                DCB.user_visit();
            if (on_check_numberpage == true)
                DCB.check_numberpage();
            if (on_user_exit == true)
                DCB.user_exit();
            addListener(window.parent.window, 'resize', function () {
                DCB.correctScreen();
            });
            addListener(window.parent.window, 'scroll', function () {
                DCB.correctScreen();
            });
            addListener(window.parent.window, 'load', function () {
                DCB.correctScreen();
            });
            DCB.CheckWorkTime();
        });
    };


    DCB.kf = function () {   // готовность загрузки js css 
        DCB.k--;
        if (DCB.k == 0)
        {
            DCB.begin();
        }
    };
    
    DCB.includeJS = function (f_url)   // подгрузка js
    {
        var js = document.createElement('script');
        js.type = 'text/javascript';
        js.async = false;
        js.src = f_url;
        js.onload = DCB.kf;
        js.onreadystatechange = function () {
            if (this.readyState == 'complete')
                DCB.kf();
        }
        var jsx = document.getElementsByTagName('script')[0];
        jsx.parentNode.insertBefore(js, jsx);
        return js;
    };
    
    DCB.includeCSS = function (f_url)    // подгрузка css
    {
        var css = document.createElement("link");
        css.rel = "stylesheet";
        css.type = "text/css";
        css.href = f_url;
        css.onload = DCB.kf;
        css.onreadystatechange = function () {
            if (this.readyState == 'complete')
                DCB.kf();
        }
        document.getElementsByTagName("head")[0].appendChild(css);
        return css;
    };

    var cancel_order = true;
    var jsonpCallback_datasuccess = 'false';        // значение параметра success возвращаемое через jsonpCallback()
    var jsonpCallback_done = 'false';               // true - ф-ция jsonpCallback() выполнилась; false - не выполнялась
    var Call_us_6760835097_disabled = false;
    var jsonpCallback_warning = '';

    DCB.Cancel_order = function ()         // отмена заказа звонка
    {
        cancel_order = true;
        $('.icon_box').css('display', 'block');
	DCB.correctScreen();		   // корректируем iFrame
    };

    DCB.Create_order_checkcookie = function ()   // блокировка всплывающих окон по таймеру 

    {
        if (DCB.checkCookie(c_createorder) == true)
            return false;
        var c_createorder_exp = new Date();
        var c_createorder_exp_seconds = time_popup_blocker;           //5sec

        c_createorder_exp.setTime(c_createorder_exp.getTime() + (c_createorder_exp_seconds * 1000));
        if (DCB.debug == true) console.log(c_createorder_exp);
        DCB.setCookie(c_createorder, 'true', c_createorder_exp)
        return true;
    };

    DCB.Create_order = function (co_text, force)
    {
        // проверка частоты вызова ф-ции
        if (!force)
            if (DCB.Create_order_checkcookie() == false)
                return;
        // защита от повторного вызова
        if (cancel_order == false)
            return;
        cancel_order = false;
        // отрисовка
        $('.icon_box').css('display', 'none');          // прячем кнопку
	DCB.correctScreen();				// корректируем iFrame
        $('#win_order_7503523488').arcticmodal({// показываем модальное окно   
            afterClose: function (data, el) {
                if (DCB.debug == true) console.log(data);
                DCB.Cancel_order();
            }
        });
	DCB.correctScreen();
        $('#win_order_content_9268377087').empty();    // очистка контекста модального окна

        if (co_text == undefined)
            co_text = 'Хотите, мы вам перезвоним за ' + dcb_sec + ' секунд?';      // замена текста в мод.окне

        $('#win_order_content_9268377087').append('<div id="zagolovok_order_0353271466" class="text_zagolovka_order_4043482234">' + co_text + '</div>' +
                '<div style="display:inline-block;width:100%"><input type="text" name="Number" id="Number_calling_2240965432" size="35" maxlength="25" placeholder="Введите ваш номер" class="text_message_2563964469">' +
                '<input type="button" value="Звоните!" id="Call_us_6760835097" class="button_calling_1712953875" onClick="DCB.Show_timer();"' + (Call_us_6760835097_disabled ? ' disabled' : '') + '></div>' +
                '<div id="calling_free_5164231155" ><div class="text_call_free_4537679586">Звонок бесплатный</div><div id="ahtyng_5031613510" class="trevoga_9107808614"></div><div id="Help_us_window_0685353415" style="display: none"><div class="help_federation_number_text_0597947849" id="Help_us_text_9868532398"></div></div></div>');
        DCB.button_calling_color(!Call_us_6760835097_disabled);
        DCB.button_calling_print_time;
        $('#podpic_komunikator_1749966526').css('display', 'block');     // логотип комуникатора
    };

    DCB.Show_timer = function ()                // показать таймер 
    {
        if (cancel_order == true)
            return; // проверяем не закрыли ли окно заказа звонка
        var phone = document.getElementById('Number_calling_2240965432').value;
        if (phone == "")
        {
            $('#ahtyng_5031613510').empty();
            $('#ahtyng_5031613510').append('Пожалуйста, введите номер.');

        } else
        {
            var re1 = phone.replace(/[\s-]+/g, '');         // проверка на валидность набора номера                      
	    var numregexp = /^(8|\+7)(\d{10,15})$/;
            var valid = DCB.valid1(re1, numregexp);
            if (valid == true)
            {
		re1 = re1.replace(numregexp,"7$2");
                DCB.zapret();                               // запрет на нажатии кнопки заказа звонка
                // меняем форму ввода номера на таймер
                $('#win_order_content_9268377087').empty();
                $('#win_order_content_9268377087').append('<div class="text_zagolovka_order_4043482234">Мы вам уже звоним!</div><div id="timer_9109060427" class="cntSeparator_8087290461"></div>');
                DCB.countdown_init();
                DCB.countdown();

                $.jsonp({url: ""+ dcb_id_server + "/service/data.php?action=order_call&number=" + re1 + "&callback=DCB.jsonpCallback&call_back_id=" + call_back_id});
            } else
            {
                $('#ahtyng_5031613510').empty();
                $('#ahtyng_5031613510').append('Пожалуйста, введите номер в федеральном формате.');
                $('#Help_us_text_9868532398').empty();
                $('#Help_us_text_9868532398').append('Номера в федеральном формате<br>+7-XXX-XXX-XX-XX<br> 8-XXX-XXX-XX-XX</br>');

                $('#Number_calling_2240965432').on({//вкл. всплывающей подсказки по набору номера
                    mouseover: function ()
                    {
                        $('#Help_us_window_0685353415').css('display', 'block');
                    },
                    mouseout: function ()
                    {
                        $('#Help_us_window_0685353415').css('display', 'none');
                    }
                });
            }
        }
    };

    DCB.valid1 = function (param, regex)
    {
        var valid = regex.test(param);
        return valid;
    };

    DCB.countdown_init = function ()         // переменные таймера по заказу звонка
    {
        min = 0;
        sec = dcb_sec;
        milisec = 0;
        jsonpCallback_datasuccess = 'false';
        jsonpCallback_done = 'false';
    };

    DCB.countdown = function ()        // таймер 
    {
        if (cancel_order == true)
        {
            clearInterval(inter);
            return;
        }
        milisec--;
        if (milisec < 0) {
            milisec = 99;
            sec--;
        }

        if (sec < 0) {
            sec = 59;
            min--;
        }

        time = (min < 10 ? "0" + min : min) + ":" +
                (sec < 10 ? "0" + sec : sec) + "," +
                (milisec < 10 ? "0" + milisec : milisec) + " сек";

        if ($('#timer_9109060427'))
            $('#timer_9109060427').html(time);

        if ((min <= 0 && sec <= 0 && milisec <= 0) || jsonpCallback_done == 'true')
        {
            clearInterval(inter);
            // меняем таймер на текст "извините"   

            if (DCB.debug == true) console.log('jsonpCallback_datasuccess=' + jsonpCallback_datasuccess);
            if (DCB.debug == true) console.log('jsonpCallback_done=' + jsonpCallback_done);
            if (DCB.debug == true) console.log('jsonpCallback_warning=' + jsonpCallback_warning);
            $('#win_order_content_9268377087').empty();

            if (jsonpCallback_datasuccess == 'false' && jsonpCallback_done == 'true')
            {
                // ответ от сервера пришел, но звонок совершить сейчас невозможно  
                $('#win_order_content_9268377087').append('<div class="text_zagolovka_order_4043482234">Извините, похоже никого нет в офисе</div><div class="perezvon_7957356058" id="auto_otvet_perezvon_0661029074"><br>Мы обязательно перезвоним Вам в течении суток</br></div>');
            }
            if (jsonpCallback_datasuccess == 'true')
            {
                // мы вам звоним, все в порядке
                $('#win_order_content_9268377087').empty();
                $('#win_order_content_9268377087').append('<div class="text_zagolovka_order_4043482234">Спасибо за использование нашего сервиса</div><div class="podrobnee_6300426980" id="yznai_o_technologii_5324782904">Узнайте <a href="http://komunikator.ru/" target="_blank">подробнее</a> о технологиях</div><a href="http://komunikator.ru/" target="_blank"><div class="bolshoi_komunicator_5316051287"></div></a>');
                $('#podpic_komunikator_1749966526').css('display', 'none');
            }
            if (jsonpCallback_datasuccess == 'false' && jsonpCallback_done == 'false')
            {
                // время вышло и ответ от сервера не пришел 
                $('#win_order_content_9268377087').empty();
                $('#win_order_content_9268377087').append('<div class="text_zagolovka_order_4043482234"><p>Error 404 not found</p></div>');
            }
        } else
            inter = setTimeout(DCB.countdown, 10);
    };

    DCB.jsonpCallback = function (data) {  // запрос на сервер по заказу звонка 
        if (DCB.debug == true) console.log(data.success);
        jsonpCallback_done = 'true';
        jsonpCallback_datasuccess = data.success;
        jsonpCallback_warning = data.warning;

        if (data.warning)                    // критические ошибки 
            if (DCB.debug == true) console.log(data.warning);
    };

    var countdownTimer, seconds, minutes;

    DCB.zapret = function () {
        seconds = dcb_seconds;  // timer 30 sec     
        minutes = 0;
        countdownTimer = setInterval(DCB.secondPassed, 1000);
        $('#Call_us_6760835097').attr("disabled", true);
        Call_us_6760835097_disabled = true;
    };

    DCB.button_calling_color = function (enabled)  // true - темная, false - светло-серая (неактивна)
    {
        if (enabled)
        {
            $('.button_calling_1712953875').css('background-color', '#484848');
        }
        else
        {
            $('.button_calling_1712953875').css('background-color', '#BFBFBF');
        }
    };

    DCB.button_calling_print_time = function ()
    {
        if ($("#Call_us_6760835097") !== undefined && Call_us_6760835097_disabled)
            $("#Call_us_6760835097").val((minutes < 10 ? '0' + minutes : minutes) + ':' + (seconds < 10 ? '0' + seconds : seconds));
    };

    DCB.secondPassed = function () {         // таймер кнопки звноите!
        DCB.button_calling_color(false);
        if (seconds == 0)
            minutes--;
        else
            seconds--;
        DCB.button_calling_print_time();
        minutes = Math.round((seconds - 30) / 60);
        if (seconds <= 0 && minutes <= 0)
        {
            if ($("#Call_us_6760835097") !== undefined)
                $("#Call_us_6760835097").val('Звоните!');
            clearInterval(countdownTimer);
            $('#Call_us_6760835097').attr("disabled", false);
            Call_us_6760835097_disabled = false;
            DCB.button_calling_color(true);
        }
    };

    DCB.jsonpCallbackStatus = function (data) {  // проверка статуса рабочего времени 
        if (DCB.debug == true) console.log(data.status);
        if (data.status = true)
        {
            $('.btn_callback_8403736779').css('display', 'block');
        } else {
            $('.btn_callback_8403736779').css('display', 'none');
        }
    };

    DCB.CheckWorkTime = function () {
        $.jsonp({url: "" + dcb_id_server + "/service/data.php?action=get_work_status&callback=DCB.jsonpCallbackStatus"});
        setTimeout(DCB.CheckWorkTime, 60000);
    };

    // Внедряем объекты
    DCB.k = 9 - 1;
    digt_callback_url = dcb_id_server + '/callback';
    DCB.includeCSS(digt_callback_url + "/order_calling_style.css");
    DCB.includeCSS(digt_callback_url + "/font-awesome-4.3.0/css/font-awesome.css");
    DCB.includeCSS(digt_callback_url + "/js/arcticmodal/jquery.arcticmodal-0.3.css");
    DCB.includeCSS(digt_callback_url + "/js/arcticmodal/themes/komunikator.css");
    DCB.includeJS(digt_callback_url + "/js/jquery.min.js");
    DCB.includeJS(digt_callback_url + "/js/arcticmodal/jquery.arcticmodal-0.3.min.js");
    DCB.includeJS(digt_callback_url + "/js/jquery.jsonp-2.4.0.js");
    DCB.includeJS(digt_callback_url + "/js/detect.js");
    DCB.includeJS(digt_callback_url + "/js/jquery.cookie.js");
})();
 
