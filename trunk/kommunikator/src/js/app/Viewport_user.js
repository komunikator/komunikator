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

Ext.define('app.Viewport_user', {
    extend: 'Ext.container.Viewport',
    style: 'padding : 2px 10px', // отступы: верх, низ - 2; право, лево - 10
    layout: 'border',
    items: [{
            region: 'north', // верх
            // autoHeight : true,
            border: false,
            margins: '0 0 5 0'
        }, {
            region: 'south', // низ
            title: '<div style="text-align : center"><p style="font-size : 8pt">' + app.msg.copyright + '</p></div>', // Телефонные системы®PBX © 2012
            border: false,
            margins: '10 0 10 0'
        }, {
            region: 'center', // центр
            layout: 'fit',
            xtype: 'tabpanel',
            id: 'main_tabpanel',
            bodyStyle: 'padding : 15px', // отступы: верх, низ, право, лево - 15

            items: [
                Ext.create('app.Card_Panel', {
                    title: app.msg.private_office, // Личный кабинет
                    items: [
                        Ext.create('app.module.Call_logs_Grid', {
                            title: app.msg.call_logs  // История звонков
                        }),
                        {
                            title: app.msg.update_password, // изменить пароль

                            handler: function() {
                                Ext.create('app.UpdatePassword').show();

                            }
                        },
                        {
                            title: app.msg.forward, // Переадресация

                            handler: function() {
                                Ext.create('app.Call_Forwarding').show();

                            }
                        },
                    ]
                }),
            ]
        }],
    initComponent: function() {
        this.items[0].title =
                '<div class="x-box-inner" style="padding-left: 10px;  padding-top:3px; padding-bottom:3px; padding-right: 10px; height: 42px">' +
                '<img class="logo" src="js/app/images/logo.png" alt="Komunikator" border="0" align="left">' +
                '<p align="right"><a href="#" onclick="app.logout(); return false">' + app.msg.logout + '</a></p>' +
                '<p align="right">' + app.msg.user + ': ' + this.extension_name + '</p>' +
                '</div>';
//alert(this.extension_name);
        this.callParent(arguments);

        Ext.TaskManager.start({
            run: function() {
                Ext.StoreMgr.each(function(item, index, length) {
                    if (item.storeId == 'statistic') {
                        if (item.autorefresh)
                            item.load();
                        // console.log(item.storeId + ":item.autorefresh-:" + item.autorefresh);
                    }
                    ;
                    if (Ext.getCmp(item.storeId + '_grid'))
                        if (app.active_store == item.storeId && item.autorefresh && !this.dirtyMark)
                            item.load();
                })
            },
            interval: app.refreshTime
        });
    }
});