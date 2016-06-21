/*
 *  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
 *    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
 *    Copyright (C) 2012-2013, ООО «Телефонные системы»
 
 *    ЭТОТ ФАЙЛ является частью проекта «Komunikator»
 
 *    Сайт проекта «Komunikator»: http://komunikator.ru/
 *    Служба технической поддержки проекта «Komunikator»: E-mail: support@komunikator.ru
 
 *    В проекте «Komunikator» используются:
 *      исходные коды проекта «YATE», http://yate.null.ro/pmwiki/
 *      исходные коды проекта «FREESENTRAL», http://www.freesentral.com/
 *      библиотеки проекта «Sencha Ext JS», http://www.sencha.com/products/extjs
 
 *    Web-приложение «Komunikator» является свободным и открытым программным обеспечением. Тем самым
 *  давая пользователю право на распространение и (или) модификацию данного Web-приложения (а также
 *  и иные права) согласно условиям GNU General Public License, опубликованной
 *  Free Software Foundation, версии 3.
 
 *    В случае отсутствия файла «License» (идущего вместе с исходными кодами программного обеспечения)
 *  описывающего условия GNU General Public License версии 3, можно посетить официальный сайт
 *  http://www.gnu.org/licenses/ , где опубликованы условия GNU General Public License
 *  различных версий (в том числе и версии 3).
 
 *  | ENG | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 
 *    "Komunikator" is a web interface for IP-PBX "YATE" configuration and management
 *    Copyright (C) 2012-2013, "Telephonnyie sistemy" Ltd.
 
 *    THIS FILE is an integral part of the project "Komunikator"
 
 *    "Komunikator" project site: http://komunikator.ru/
 *    "Komunikator" technical support e-mail: support@komunikator.ru
 
 *    The project "Komunikator" are used:
 *      the source code of "YATE" project, http://yate.null.ro/pmwiki/
 *      the source code of "FREESENTRAL" project, http://www.freesentral.com/
 *      "Sencha Ext JS" project libraries, http://www.sencha.com/products/extjs
 
 *    "Komunikator" web application is a free/libre and open-source software. Therefore it grants user rights
 *  for distribution and (or) modification (including other rights) of this programming solution according
 *  to GNU General Public License terms and conditions published by Free Software Foundation in version 3.
 
 *    In case the file "License" that describes GNU General Public License terms and conditions,
 *  version 3, is missing (initially goes with software source code), you can visit the official site
 *  http://www.gnu.org/licenses/ and find terms specified in appropriate GNU General Public License
 *  version (version 3 as well).
 
 *  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
 */

Ext.define('app.LoginWindow', {
    extend: 'Ext.window.Window',
    alias: 'widget.login',
    id: 'loginWindow',
    autoShow: true,
    width: 300,
    height: 140,
    layout: 'border',
    border: false,
    modal: true,
    closable: false, //убирает крестик, закрывающий окно
    resizable: false, // нельзя изменить размеры окна
    draggable: false, //перемещение объекта по экрану

    initComponent: function() {
        this.items = [/*{
         region : 'north',
         title: app.msg.auth_title
         //height : 52,
         //bodyCls : 'app_header'
         }, */{
                id: 'login_form',
                title: app.msg.auth_title, //получаем название титула окна
                region: 'center', //расположена форма по центру
                xtype: 'form',
                url: 'data.php',
                method: 'POST',
                bodyStyle: 'padding:10px; background: transparent;border-top: 0px none;',
                labelWidth: 75,
                defaultType: 'textfield',
                items: [{
                        fieldLabel: app.msg.login,
                        name: 'user',
                        id: 'usr',
                        allowBlank: false
                    }, {
                        fieldLabel: app.msg.password,
                        name: 'password',
                        inputType: 'password',
                        id: 'pwd',
                        allowBlank: false,
                        listeners:
                                {
                                    specialkey: function(t, e) {
                                        var login_button = Ext.getCmp('login_button');
                                        if (e.getKey() == e.ENTER && !login_button.disabled) {
                                            e.stopEvent();
                                            login_button.handler();
                                        }
                                    }
                                }
                    }, {
                        name: 'action',
                        value: 'auth',
                        hidden: true
                    }, {
                        name: 'time_offset',
                        value: new Date().getTimezoneOffset(),
                        hidden: true
                    }
                ]
            }
        ];

        this.buttons = [{
                id: 'login_button',
                text: app.msg.OK,
                handler: function() {
                    var login_form = Ext.getCmp('login_form');
                    if (login_form.getForm().isValid()) {
                        login_form.body.mask();
                        app.request(
                                login_form.getForm().getValues(),
                                function(result) {

                                    login_form.getForm().reset();
                                    Ext.getCmp('loginWindow').hide();
                                    if (result['user'])
                                        app.main(result['user'], null);
                                    if (result['extension'])
                                        app.main(null, result['extension']);
                                    //app.View;

                                    login_form.body.unmask();
                                }, function(result) {

                            login_form.body.unmask();
                        });
                    }
                }
            }];
        this.callParent(arguments);
    }
});