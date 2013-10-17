/*
*  | RUS | - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 

*    «Komunikator» – Web-интерфейс для настройки и управления программной IP-АТС «YATE»
*    Copyright (C) 2012-2013, ООО «Телефонные системы»

*    ЭТОТ ФАЙЛ является частью проекта «Komunikator»

*    Сайт проекта «Komunikator»: http://4yate.ru/
*    Служба технической поддержки проекта «Komunikator»: E-mail: support@4yate.ru

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

*    "Komunikator" project site: http://4yate.ru/
*    "Komunikator" technical support e-mail: support@4yate.ru

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

Ext.define('app.module.Forwarding_Panel', {
    extend: 'Ext.form.Panel',
    url: 'data.php?action=get_forwarding',
    id: 'ForwardingPanel',
    style: 'padding:40px;',
    frame: true,
    waitMsgTarget: true,
    width: 400,
    height: 600,
   
    initComponent: function() {

        this.items = [{
                id: 'change_forward',
                xtype: 'fieldset',
                border: true,
                items: [
                    { id: 'id',
                        xtype: 'textfield',
                        name: 'id',
                        hidden: true
                    },
                    {
                        fieldLabel: app.msg.always,
                        name: 'forward',
                        id: 'forward',
                        height: 20,
                        xtype: 'combobox',
                        mode: 'local',
                        editable: true,
                        triggerAction: 'all',
                        regex: new RegExp('(^\\d{1,11}$)|(^' + app.msg.voicemail + '$)'),
                        store: [
                            ['vm', app.msg.voicemail],
                        ],
                        listeners:
                                {
                                    specialkey: function(t, e) {
                                        var change_forw = Ext.getCmp('change_forw');
                                        if (e.getKey() == e.ENTER && !change_forw.disabled) {
                                            e.stopEvent();
                                            change_forw.handler();
                                        }
                                    }
                                }
                    }, {
                        fieldLabel: app.msg.forward_busy,
                        name: 'forward_busy',
                        id: 'forward_busy',
                        height: 20,
                        xtype: 'combobox',
                        mode: 'local',
                        editable: true,
                        triggerAction: 'all',
                        regex: new RegExp('(^\\d{1,11}$)|(^' + app.msg.voicemail + '$)'),
                        store: [
                            ['vm', app.msg.voicemail],
                        ]
                    }, 
                            {
                        fieldLabel: app.msg.forward_noanswer,
                        name: 'forward_noanswer',
                        id: 'forward_noanswer',
                        height: 20,
                        xtype: 'combobox',
                        mode: 'local',
                        editable: true,
                        triggerAction: 'all',
                        regex: new RegExp('(^\\d{1,11}$)|(^' + app.msg.voicemail + '$)'),
                        store: [
                            ['vm', app.msg.voicemail],
                        ]
                    }, 
                            {
                        xtype: 'textfield',
                        fieldLabel: app.msg.noanswer_timeout,
                        name: 'noanswer_timeout',
                        id: 'noanswer_timeout',
                        height: 20,
                        editor: {
                            xtype: 'numberfield',
                            minValue: 1
                        }
                    },
                    {
                        xtype: 'textfield',
                        name: 'action',
                        value: 'update_extensions',
                        hidden: true
                    }]
            }];


        this.callParent(arguments);
        var form = this.getForm();
        form.load();
    }
});