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

Ext.define('app.module.Extensions_Grid', {
    extend: 'app.Grid',
    store_cfg: {
        autorefresh: false,
        loadmask: true,
        fields: ['id', 'status', 'extension', 'password', 'firstname', 'lastname', 'address', 'group_name', 'priority', 'forward', 'forward_busy', 'forward_noanswer', 'noanswer_timeout'],
        storeId: 'extensions',
        sorters: [{
                direction: 'DESC',
                property: 'group_name'
            }]
    },
    not_create_column: true,
    columns: [
        {// 'id'
            hidden: true  // hidden: false <- не работает т.к. было скрыто раньше
        },
        {// 'status'
            width: 60
        },
        {// 'extension'
            width: 120,
            groupable: false,
            editor: {
                xtype: 'textfield',
                regex: /^\d{3}$/,
                allowBlank: false
            }
        },
        {// 'password'
            width: 70,
            sortable: false,
            groupable: false,
            renderer: function() {
                return '***';
            },
            editor: {
                xtype: 'textfield',
                inputType: 'password',
                regex: /^\d{3,10}$/,
                allowBlank: false
            }
        },
        {// 'firstname'
            editor: {
                xtype: 'textfield'
            }
        },
        {// 'lastname'
            editor: {
                xtype: 'textfield'
            }
        },
        {// 'address'
            width: 150,
            groupable: false,
            editor: {
                vtype: 'email',
                xtype: 'textfield'
            }
        },
        {// Группа ( Группа и Приоритет )
            header: app.msg.group,
            dataIndex: 'group',
            headers: [
                {
                    text: app.msg.group,
                    dataIndex: 'group_name'
                },
                {
                    header: app.msg.priority,
                    dataIndex: 'priority'
                }

            ]
        },
        {// Переадресация ( Всегда, Номер занят, Нет ответа, Таймаут (сек) )
            header: app.msg.forward,
            dataIndex: 'forward',
            headers: [
                {
                    header: app.msg.number,
                    dataIndex: 'forward'
                },
                {
                    header: app.msg.forward_busy,
                    dataIndex: 'forward_busy'
                },
                {
                    header: app.msg.forward_noanswer,
                    dataIndex: 'forward_noanswer'
                },
                {
                    header: app.msg.noanswer_timeout,
                    dataIndex: 'noanswer_timeout',
                    editor: {
                        xtype: 'textfield',
                        regex: /^\d{1,3}$/
                    }
                }
            ]
        }
    ],
    requires: 'Ext.ux.grid.FiltersFeature',
    features: [
        {
            ftype: 'grouping'/*,            
             groupByText    : '???????????? ?? ????? ????',
             showGroupsText : '?????????? ?? ???????'*/
        },
        {
            ftype: 'filters',
            // autoReload: true,  // don't reload automatically
            local: false, // only filter locally
            encode: true,
            filters: [
                {
                    encode: 'encode',
                    local: true,
                    type: 'list',
                    options: [
                        ['online', app.msg['registered']],
                        ['offline', app.msg['unregistered']],
                        ['busy', app.msg['busy']]
                    ],
                    dataIndex: 'status'
                }, {
                    type: 'string',
                    dataIndex: 'extension'
                }, {
                    type: 'string',
                    dataIndex: 'firstname'
                }, {
                    type: 'string',
                    dataIndex: 'lastname'
                }, {
                    type: 'string',
                    dataIndex: 'address'
                }, {
                    type: 'string',
                    dataIndex: 'group_name'/*,
                     encode: 'encode',
                     local: true,
                     type: 'list',
                     store: Ext.StoreMgr.lookup('groups'),
                     labelField: 'group',
                     valueField: 'group'  // not work, need 'id'
                     */
                }
            ]

                    /*
                     type: 'boolean',
                     // type: 'string',
                     yesText: app.msg.online,  // default
                     noText: app.msg.online,  // default
                     dataIndex: 'status'
                     */
        }
    ],
    columns_renderer: app.online_offline_renderer,
    initComponent: function() {
        app.Loader.load(['js/ux/grid/css/GridFilters.css', 'js/ux/grid/css/RangeMenu.css']);

        this.listeners.beforerender = function() {
            if (app['lang'] == 'ru')
                app.Loader.load(['js/app/locale/filter.ru.js']);
        };

        this.columns[7] = {
            header: app.msg.group,
            dataIndex: 'group',
            groupable: false,
            sortable: false,
            menuDisabled: true,
            columns: [
                {// 'group_name'
                    width: 120,
                    sortable: true,
                    groupable: true,
                    text: app.msg.group,
                    dataIndex: 'group_name',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.StoreMgr.lookup('groups_extended') ?
                                Ext.StoreMgr.lookup('groups_extended') :
                                Ext.create('app.Store', {
                            fields: ['id', 'group', 'description', 'extension'],
                            storeId: 'groups_extended'
                        }),
                        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                        // если хранилище создается только в 1ом месте, 
                        // то код выглядит так:
                        /*
                         store: Ext.create('app.Store', {
                         fields: ['id', 'group', 'description', 'extension'],
                         storeId: 'groups_extended'
                         }),*/
                        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                        queryMode: 'local',
                        valueField: 'group',
                        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                        // настройка combobox под «себя»
                        // «нормальное» отображение пустых полей в выпадающем списке

                        // displayField  : 'group', <- заменено кодом ниже

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
                            },
                            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  
                            // при изменении значения в поле "Группа"
                            // сбрасывается значение поля "Приоритет"
                            change: function(f, new_val) {
                                f.ownerCt.items.items[8].setValue(null);
                            }
                            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -    
                        }
                    }
                },
                {// 'priority'
                    width: 100,
                    sortable: true,
                    groupable: false,
                    header: app.msg.priority,
                    dataIndex: 'priority',
                    editor: {
                        xtype: 'numberfield',
                        minValue: 1
                    }
                }
            ]
        };

        this.forward_editor = {
            xtype: 'combobox',
            editable: true,
            regex: new RegExp('(^\\d{2,11}$)|(^' + app.msg.voicemail + '$)'),
            store: sda_storage_for_forwarding,
            queryMode: 'local',
            valueField: 'abbr',
            tpl: Ext.create('Ext.XTemplate',
                    '<tpl for=".">',
                    '<div class="x-boundlist-item" style="min-height: 22px">{name}</div>',
                    '</tpl>'
                    ),
            displayTpl: Ext.create('Ext.XTemplate',
                    '<tpl for=".">',
                    '{name}',
                    '</tpl>'
                    )

        };

        this.forward_renderer = function(value) {
            if (value == 'vm')
                return app.msg.voicemail;
            return value;
        };

        this.columns[8] = {
            text: app.msg.forward,
            groupable: false,
            sortable: false,
            menuDisabled: true,
            defaults: {
                editor: this.forward_editor,
                renderer: this.forward_renderer,
                menuDisabled: true,
                groupable: false
            },
            columns: [
                {// 'forward'
                    header: app.msg.always,
                    dataIndex: 'forward',
                    width: 120
                },
                {// 'forward_busy'
                    header: app.msg.forward_busy,
                    dataIndex: 'forward_busy',
                    width: 120
                },
                {// 'forward_noanswer'
                    header: app.msg.forward_noanswer,
                    dataIndex: 'forward_noanswer',
                    width: 120
                },
                {// 'noanswer_timeout'
                    header: app.msg.noanswer_timeout,
                    dataIndex: 'noanswer_timeout',
                    width: 90,
                    editor: {
                        xtype: 'textfield',
                        regex: /^\d{1,3}$/
                    }
                }
            ]
        };

        this.callParent(arguments);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // при внесении изменений в хранилище groups
        // повторная загрузка (обновление записей) хранилища groups_extended

        this.store.on('load',
                function(store, records, success) {

                    var grid = Ext.getCmp(this.storeId + '_grid');  // поиск объекта по ID
                    if (grid && !this.autoLoad)
                        grid.ownerCt.body.unmask();  // «серый» экран – блокировка действий пользователя
                    this.Total_sync();  // количество записей
                    this.dirtyMark = false;  // измененных записей нет
                    if (!success && store.storeId) {
                        store.removeAll();
                        if (store.autorefresh != undefined)
                            store.autorefresh = false;
                        console.log('ERROR: ' + store.storeId + ' fail_load [code of Extensions_Grid.js]');
                    }

                    var repository_exists = Ext.StoreMgr.lookup('extensions_list');

                    if (repository_exists) {
                        repository_exists.load();
                        console.log('!!!!!!!!!');
                    }
                    else
                        console.log('ERROR: extensions_list - fail_load [code of Extensions_Grid.js]');
                }

        );
        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    }
});


var sda_storage_for_forwarding = Ext.create('Ext.data.Store', {
    fields: ['abbr', 'name'],
    data: [
        {"abbr": "", "name": ""},
        {"abbr": "vm", "name": app.msg.voicemail}
    ]
});