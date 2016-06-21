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
        fields: ['id', 'caller_number', 'caller_group', 'type', 'gateway', 'called_number', 'called_group', 'enabled', 'description'],
        storeId: 'call_records'
    },
    columnLines: true,
    columns: [
        {// 'id'
            hidden: true
        },
        {
            //  'caller'
            text: app.msg.caller,
            columns: [{width: 150,
                    text: app.msg.number,
                    dataIndex: 'caller_number',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.StoreMgr.lookup('extensions_list') ?
                                Ext.StoreMgr.lookup('extensions_list') :
                                Ext.create('app.Store', {
                            fields: ['id', 'name'],
                            storeId: 'extensions_list'
                        }),
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'name',
                        //задает внешний вид выпадающего комбобокса
                        tpl: Ext.create('Ext.XTemplate',
                                '<tpl for=".">',
                                '<tpl if="name != \'*\'">',
                                '<div class="x-boundlist-item" style="min-height: 22px">{name}</div>',
                                '<tpl elseif="name == \'*\'">',
                                '<div class="x-boundlist-item" style="min-height: 22px">', app.msg.All, '</div>',
                                '</tpl>',
                                '</tpl>'
                                ),
                        //задает внешний вид редактируемого поля
                        displayTpl: Ext.create('Ext.XTemplate',
                                '<tpl for=".">',
                                '<tpl if="name != \'*\'">',
                                '{name}',
                                '<tpl elseif="name == \'*\'">',
                                app.msg.All,
                                '</tpl>',
                                '</tpl>'
                                ),
                        listeners: {
                            change:
                                    function(f, new_val) {
										console.log(f.ownerCt.items.items[1].value);
									    console.log('new_val='+new_val);
                                        if (new_val == null && f.ownerCt.items.items[1].value == null) {
                                            f.ownerCt.items.items[1].setValue('*');
                                        }
                                        return true;
                                    }
                        }

                    },
                    renderer: function(v) {
                        return  (v == '*') ? app.msg.All : v;
                    }
                },
                {// 'group'
                    width: 150,
                    header: app.msg.group,
                    dataIndex: 'caller_group',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.StoreMgr.lookup('groups_extended') ?
                                Ext.StoreMgr.lookup('groups_extended') :
                                Ext.create('app.Store', {
                            fields: ['id', 'group', 'description', 'extension'],
                            storeId: 'groups_extended'
                        }),
                        queryMode: 'local',
                        valueField: 'group',
                        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                        // настройка combobox под «себя»
                        // «нормальное» отображение пустых полей в выпадающем списке
                        displayField: 'group',
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
                        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

                        editable: false,
                        listeners: {
                            afterrender: function() {
                                this.store.load();
                            }
                        }
                    }
                }]

        },
        {
            hidden: true
        },
        {// 'type'
            width: 125,
            editor: {
                xtype: 'combobox',
                store: type,
                queryMode: 'local',
                //displayField: 'id',
                valueField: 'id',
                editable: false,
                tpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '<tpl if="id == \'*\'">',
                        '<div class="x-boundlist-item" style="min-height: 22px">', app.msg.all_calls, '</div>',
                        '<tpl elseif="id == 1">',
                        '<div class="x-boundlist-item" style="min-height: 22px">', app.msg.outgoing_calls, '</div>',
                        '<tpl elseif="id == 2">',
                        '<div class="x-boundlist-item" style="min-height: 22px">', app.msg.incoming_calls, '</div>',
                        '<tpl elseif="id == 3">',
                        '<div class="x-boundlist-item" style="min-height: 22px">', app.msg.internal_calls, '</div>',
                        '</tpl>',
                        '</tpl>'
                        ),
                //задает внешний вид редактируемого поля
                displayTpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '<tpl if="id == \'*\'">',
                        app.msg.all_calls,
                        '<tpl elseif="id == 1">',
                        app.msg.outgoing_calls,
                        '<tpl elseif="id == 2">',
                        app.msg.incoming_calls,
                        '<tpl elseif="id == 3">',
                        app.msg.internal_calls,
                        '</tpl>',
                        '</tpl>'
                        )

            },
            renderer: function(v) {
                if (v == '*') {
                    return app.msg.all_calls
                }
                else if (v == '1') {
                    return app.msg.outgoing_calls
                }
                else if (v == '2') {
                    return app.msg.incoming_calls
                }
                else if (v == '3') {
                    return app.msg.internal_calls
                }

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
                editable: false,
                tpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '<tpl if="name == \'*\'">',
                        '<div class="x-boundlist-item" style="min-height: 22px">', app.msg.All, '</div>',
                        '<tpl else>',
                        '<div class="x-boundlist-item" style="min-height: 22px">', '{name}', '</div>',
                        '</tpl>',
                        '</tpl>'
                        ),
                //задает внешний вид редактируемого поля
                displayTpl: Ext.create('Ext.XTemplate',
                        '<tpl for=".">',
                        '<tpl if="name != \'*\'">',
                        '{name}',
                        '<tpl elseif="name == \'*\'">',
                        app.msg.All,
                        '</tpl>',
                        '</tpl>'
                        )
            },
            renderer: function(v) {
                return  (v == '*') ? app.msg.All : v;
            }

        },
        {
            //  'called'
            text: app.msg.called,
            columns: [{width: 150,
                    text: app.msg.number,
                    dataIndex: 'called_number',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.StoreMgr.lookup('extensions_list') ?
                                Ext.StoreMgr.lookup('extensions_list') :
                                Ext.create('app.Store', {
                            fields: ['id', 'name'],
                            storeId: 'extensions_list'
                        }),
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'name',
                        //задает внешний вид выпадающего комбобокса
                        tpl: Ext.create('Ext.XTemplate',
                                '<tpl for=".">',
                                '<tpl if="name != \'*\'">',
                                '<div class="x-boundlist-item" style="min-height: 22px">{name}</div>',
                                '<tpl elseif="name == \'*\'">',
                                '<div class="x-boundlist-item" style="min-height: 22px">', app.msg.All, '</div>',
                                '</tpl>',
                                '</tpl>'
                                ),
                        //задает внешний вид редактируемого поля
                        displayTpl: Ext.create('Ext.XTemplate',
                                '<tpl for=".">',
                                '<tpl if="name != \'*\'">',
                                '{name}',
                                '<tpl elseif="name == \'*\'">',
                                app.msg.All,
                                '</tpl>',
                                '</tpl>'
                                ),
                        listeners: {
                            change:
                                    function(f, new_val) {
                                        if (new_val == null && f.ownerCt.items.items[6].value == null) {
                                            f.ownerCt.items.items[6].setValue('*');
                                        }
                                        return true;
                                    }
                        }
                    },
                    renderer: function(v) {
                        return  (v == '*') ? app.msg.All : v;
                    }
                },
                {// 'group'
                    width: 150,
                    header: app.msg.group,
                    dataIndex: 'called_group',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.StoreMgr.lookup('groups_extended') ?
                                Ext.StoreMgr.lookup('groups_extended') :
                                Ext.create('app.Store', {
                            fields: ['id', 'group', 'description', 'extension'],
                            storeId: 'groups_extended'
                        }),
                        queryMode: 'local',
                        valueField: 'group',
                        displayField: 'group',
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
                }]
        },
        {
            hidden: true
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
        {// 'description'
            width: 300,
            editor: {
                xtype: 'textfield'
            }
        }
    ],
    viewConfig: {
        stripeRows: true
    }
});

