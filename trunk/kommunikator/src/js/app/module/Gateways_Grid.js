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

app.Loader.load('js/ux/css/CheckHeader.css');

Ext.define('app.module.Gateways_Grid', {
    extend: 'app.Grid',
    restart_button: true,
    store_cfg: {
        autorefresh: false,
        fields: ['id', 'status', 'enabled', 'gateway', 'server', 'username', 'password', 'description', 'protocol', 'ip_transport', 'authname', 'domain', 'callerid'],
        storeId: 'gateways'
    },
    columns: [
        {// 'id'
            hidden: true
        },
        {// 'status'
            width: 60,
            renderer: app.online_offline_renderer
        },
        {// 'enabled'
            width: 90,
            renderer: app.checked_render,
            editor: {
                xtype: 'checkbox',
                style: {
                    textAlign: 'center'
                }
            }
        },
        {// 'gateway'
            width: 125,
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },
        {// 'server'
            width: 125,
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },
        {// 'username'
            width: 125,
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },
        {// 'password'
            width: 70,
            sortable: false,
            renderer: function() {
                return '***';
            },
            editor: {
                xtype: 'textfield',
                inputType: 'password',
                allowBlank: false
            }
        },
        {// 'description'
            width: 150,
            editor: {
                xtype: 'textfield'
            }
        },
        {// 'protocol'
            width: 80,
            editor: {
                xtype: 'combobox',
                mode: 'local',
                triggerAction: 'all',
                store: [
                    ['sip', 'sip']
                ],
                editable: false,
                allowBlank: false
            }
        },
        {// 'ip_transport'
            width: 80,
            editor: {
                xtype: 'combobox',
                mode: 'local',
                triggerAction: 'all',
                store: [
                    ['UDP', 'UDP']/*,
                     ['TLS', 'TLS'],
                     ['TCP', 'TCP']*/
                ],
                editable: false,
                allowBlank: false
            }
        },
        {// 'authname'
            width: 125,
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },
        {// 'domain'
            width: 150,
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },
        {// 'callerid'
            width: 125,
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        }
    ],
    initComponent: function() {
        this.callParent(arguments);
    }
});