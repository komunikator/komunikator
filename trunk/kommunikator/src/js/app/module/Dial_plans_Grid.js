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

Ext.define('app.module.Dial_plans_Grid', {
    extend: 'app.Grid',
    store_cfg:
            {
                fields: ['id', 'dial_plan', 'priority', 'prefix', 'gateway', 'nr_of_digits_to_cut', 'position_to_start_cutting', 'nr_of_digits_to_replace', 'digits_to_replace_with', 'position_to_start_replacing', 'position_to_start_adding', 'digits_to_add'],
                storeId: 'dial_plans'
            },
    viewConfig: {
        loadMask: false
    },
    columns: [
        {
            hidden: true
        },
        {
            width: 120,
            editor: {
                xtype: 'textfield',
            //   regex: /^[a-zA-Zа-яА-Я0-9_-]{1,16}$/,
                allowBlank: false
            }
        },
        {
            editor: {
                xtype: 'textfield',
                regex: /^\d{1,2}$/,
                allowBlank: false
            }
        },
        {
            editor: {
                xtype: 'textfield',
                regex: /^\+?\d{1,10}$/,
                allowBlank: false
            }
        },
        {
            text: app.gateway,
            editor: {
                xtype: 'combobox',
                store: Ext.StoreMgr.lookup('gateways') ?
                        Ext.StoreMgr.lookup('gateways') :
                        Ext.create('app.Store', {
                    autorefresh: false,
                    fields: ['id', 'status', 'enabled', 'gateway', 'server', 'username', 'password', 'description', 'protocol', 'ip_transport', 'authname', 'domain', 'callerid'],
                    storeId: 'gateways'
                }),
                //queryCaching: false,
                //triggerAction: 'query',
                displayField: 'gateway',
                valueField: 'gateway',
                allowBlank: false,
                queryMode: 'local',
                listeners: {
                    afterrender: function() {
                        this.store.load();
                    }
                }
            }

        },
        //'nr_og_digits_to_cut','position_to_start_cutting','nr_of_digits_to_replace','digits_to_replace_with','position_to_start_replacing','position_to_start_adding','digits_to_add'
        {
            text: '- N',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '- START',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '<> N',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '<> START',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '<>',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '+ START',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '+',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        }
    ],
    initComponent: function() {
        this.callParent(arguments);
    }
})