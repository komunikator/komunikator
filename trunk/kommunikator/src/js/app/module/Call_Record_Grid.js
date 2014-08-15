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
var type = Ext.create('Ext.data.Store', {
    fields: ['id', 'type'],
    data: [
        {"id": "*", 'type': app.msg.all_calls},
        {"id": "1", 'type': app.msg.outgoing_calls},
        {"id": "2", 'type': app.msg.incoming_calls},
        {"id": "3", 'type': app.msg.internal_calls}
    ]
});


Ext.define('app.module.Call_Record_Grid', {
    extend: 'app.Grid',
    store_cfg: {
        fields: ['id', 'caller', 'type', 'gateway', 'called'],
        storeId: 'call_record'
    },
    columns: [
        {// 'id'
            hidden: true
        },
        {// 'caller'
            width: 175,
            editor: {
                xtype: 'combobox',
                store: Ext.create('app.Store', {
                    fields: ['id', 'name'],
                    storeId: 'ext_groups'
                }),
                queryMode: 'local',
                displayField: 'name',
                valueField: 'name',
                editable: true,
            }
            // editor : app.get_Source_Combo({})
        },
        {// 'type'
            width: 125,
            editor: {
                xtype: 'combobox',
                store: type,
                queryMode: 'local',
                displayField: 'type',
                valueField: 'type',
                editable: false
            }
        },
        {// 'gateway'
            width: 150,
            editor: {
                xtype: 'combobox',
                store: Ext.create('app.Store', {
                    fields: ['id', 'name'],
                    storeId: 'gateway_list'
                }),
                queryMode: 'local',
                displayField: 'name',
                valueField: 'name',
                editable: true
            }
            /*editor: {
             xtype: 'combobox',
             store: Ext.StoreMgr.lookup('gateways') ? Ext.StoreMgr.lookup('gateways') :
             Ext.create('app.Store', {
             autorefresh: false,
             fields: ['id', 'status', 'enabled', 'gateway', 'server', 'username', 'password', 'description', 'protocol', 'ip_transport', 'authname', 'domain', 'callerid'],
             storeId: 'gateways'
             }),
             displayField: 'gateway',
             valueField: 'gateway',
             queryMode: 'local',
             listeners: {
             afterrender: function() {
             this.store.load();
             }
             }
             }*/
        },
        {// 'called'
            header: app.msg.called,
            dataIndex: 'called',
            headers: [
                {
                    text: app.msg.number,
                    dataIndex: 'number'
                },
                {
                    header: app.msg.group,
                    dataIndex: 'group'
                }

            ]
        },
        /*  {// 'called'
         width: 300,
         columns: [{
         text: 'number',
         width: 150,
         editor: {
         xtype: 'combobox',
         store: Ext.create('app.Store', {
         fields: ['id', 'name'],
         storeId: 'sources_exception'
         }),
         queryMode: 'local',
         displayField: 'name',
         valueField: 'name',
         editable: true,
         emptyText : 'all'
         },
         
         },
         {
         text: 'group',
         width: 150
         }]
         }*/
    ],
    initComponent: function() {
        this.columns[4] = {
            header: app.msg.called,
            dataIndex: 'called',
            groupable: false,
            sortable: false,
            menuDisabled: true,
            columns: [
                {// 'number'
                    width: 150,
                    text: app.msg.number,
                    dataIndex: 'number',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.create('app.Store', {
                            fields: ['id', 'name'],
                            storeId: 'extensions_list'
                        }),
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'name',
                        editable: true,
                        listeners: {
                            afterrender: function() {
                                this.store.load();
                            }
                        }
                    }
                },
                {// 'group'
                    width: 150,
                    header: app.msg.group,
                    dataIndex: 'group',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.create('app.Store', {
                            fields: ['id', 'group', 'description', 'extension'],
                            storeId: 'groups_extended'
                        }),
                        queryMode: 'local',
                        valueField: 'group',
                        tpl: Ext.create('Ext.XTemplate',
                                '<tpl for=".">',
                                '<div class="x-boundlist-item" style="min-height: 22px">{group}</div>',
                                '</tpl>'
                                ),
                        displayTpl: Ext.create('Ext.XTemplate',
                                '<tpl for=".">',
                                '{group}',
                                '</tpl>'
                                ),
                        editable: false,
                        listeners: {
                            afterrender: function() {
                                this.store.load();
                            }
                        }
                    }
                }
            ]
        };
        this.callParent(arguments);
    }
});

