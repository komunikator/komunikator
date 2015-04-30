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


var windowSettings = Ext.create('widget.window', {
    id: 'window_about1',
    title: "Настройки",
    width: 800,
    height: 500,
    autoHeight: true,
    autoScroll: true,
    maximizable: true, // значок «раскрыть окно на весь экран»
    modal: true, // блокирует всё, что на заднем фоне
    draggable: true, // перемещение объекта по экрану
    closeAction: 'hide',
    items: Ext.create('Ext.grid.Panel', {
        id: 'windowSettingsCallBack',
        store: [[null, null, null, null, null]], // определили хранилище
        closeAction: 'hide', //по дефолту объект при закрытии уничтожается, с 'hide' - нет.

        plugins: [{
                ptype: 'rowediting',
                clicksToEdit: 2
            }],
        columns:
                [
                    {
                        width: 200,
                        text: app.msg.param,
                        dataIndex: 'field1',
                        sortable: false,
                        renderer: function(value) {
                            if (app.msg[value]) {
                                //return app.msg[value];
                                return '<div style="white-space:normal !important; height:40px; text-align:  left">' + app.msg[value] + '</div>';
                            } else {
                                return '<div style="white-space:normal !important; height:40px; text-align:  left">' + value + '</div>';

                            }
                        }
                    }, {
                        text: "Вкл/Выкл",
                        dataIndex: 'field2',
                        width: 60,
                        renderer: app.checked_render,
                        editor: {
                            xtype: 'checkbox',
                            style: {
                                textAlign: 'center'
                            }
                        }
                    },
                    {
                        width: 100,
                        dataIndex: 'field3',
                        sortable: false,
                        renderer: function(value) {
                            return '<div style="white-space:normal !important; height:25px; text-align:  center">' + value + '</div>';
                        }
                    },
                    {
                        width: 150,
                        text: app.msg.stipulation,
                        dataIndex: 'field4',
                        sortable: false,
                        editor: {
                            xtype: 'textfield'
                        },
                        renderer: function(value) {
                            return '<div style="white-space:normal !important; height:25px; text-align:  center">' + value + '</div>';
                        }
                    }, {width: 250,
                        text: "текст сообщения",
                        dataIndex: 'field5',
                        sortable: false,
                        editor: {
                            xtype: 'textfield'
                        },
                        renderer: function(value) {
                            return '<div style="white-space:normal !important; height:25px; text-align:  center">' + value + '</div>';
                        }
                    }
                ], height: 390

    }),
    buttons: [
        {
            id: 'change_forw11',
            text: app.msg.save,
            handler: function() {
                var change_forward = Ext.getCmp('windowSettingsCallBack');
                var array = [];
                var e = 1;

                Ext.getCmp('windowSettingsCallBack').getStore().each(function(model) {
                    var key = e + "";
                    var obj = {};
                    obj[key] = model.data;
                    array.push(obj);
                    e++;
                });

                var string = JSON.stringify(array);
                change_forward.body.mask();
                var record = Ext.getCmp('call_back_grid').getSelectionModel().getLastSelected();
                record.set("settings", string);

                Ext.getCmp('window_about1').close();
                Ext.getCmp('call_back_grid').store.dirtyMark = true;
                Ext.getCmp('call_back_grid').store.sync();

                change_forward.body.unmask();
            }
        },
        {
            text: app.msg.cancel,
            handler: function() {
                windowSettings.close();
            }
        }
    ]
            // this.callParent(arguments);

});

window.openSettings = function(object) {
    var value = unescape(object);
    var call_site_params = [];
    var obj = JSON.parse(value);

    for (var i = 0; i < 8; i++)
    {
        var st = [];
        for (var key in obj[i]) {
            for (var key1 in obj[i][key])
            {
                st.push(obj[i][key][key1]);
            }
        }
        st.push(i + "");
        call_site_params.push(st);

    }

    Ext.getCmp('windowSettingsCallBack').getStore().loadData(call_site_params);
    windowSettings.show();
};
Ext.define('app.module.Call_back_Grid', {
    extend: 'app.Grid',
    store_cfg: {
        fields: ['id', 'destination', 'name_site', 'callthrough_time', 'description', 'settings', 'button_code'],
        storeId: 'call_back'
    },
    columns: [
        {// 'id'
            hidden: true
        },
        {// 'destination' - назначение
            editor: {
                xtype: 'combobox',
                store: Ext.create('app.Store', {
                    fields: ['id', 'name'],
                    storeId: 'sources_exception'
                }),
                editable: false,
                displayField: 'name',
                valueField: 'id',
                queryMode: 'local',
                allowBlank: false
            }

        },
        {// 'name_site'  - описание
            editor: {
                xtype: 'textfield'
            }
        },
        {// 'callthrough time'
            width: 120,
            groupable: false,
            editor: {
                xtype: 'textfield',
                regex: /^\d{2,3}$/,
                allowBlank: false
            }
        },
        {// 'description'  - описание
            width: 250,
            editor: {
                xtype: 'textfield'
            }
        },
        {// 'settings'         
            width: 150,
            renderer: app.button_settings
        },
        {// 'button_code' - код кнопки
            xtype: 'actioncolumn',
            sortable: false,
            groupable: false,
            icon: 'js/app/images/Grey_button.png',
            handler: function(grid, rowIndex, colIndex) {
                var rec = grid.getStore().getAt(rowIndex);
                app.request(
                        {
                            action: 'get_call_back_code',
                            id: rec.get('id'),
                            destination: rec.get('destination'),
                            name_site: rec.get('name_site'),
                            callthrough_time: rec.get('callthrough_time')
                        },
                function(result) {
                    Ext.create('widget.window', {
                        title: app.msg.button_code,
                        width: 600,
                        height: 450,
                        autoHeight: true,
                        autoScroll: true,
                        maximizable: true, // значок «раскрыть окно на весь экран»
                        modal: true, // блокирует всё, что на заднем фоне
                        draggable: true, // перемещение объекта по экрану
                        html: '<pre>' + result.data + '</pre>'
                    }).show();
                }
                );
            }
        }
    ],
    initComponent: function() {
        this.callParent(arguments);
        this.store.on('load',
                function(store, records, success) {

                    var grid = Ext.getCmp(this.storeId + '_grid');  // поиск объекта по ID
                    if (grid && !this.autoLoad)
                        grid.ownerCt.body.unmask();     // «серый» экран – блокировка действий пользователя
                    this.Total_sync();                  // количество записей
                    this.dirtyMark = false;             // измененных записей нет
                    if (!success && store.storeId) {
                        store.removeAll();
                        if (store.autorefresh != undefined)
                            store.autorefresh = false;
                        console.log('ERROR: ' + store.storeId + ' fail_load [code of Call_back_Grid.js]');
                    }
                    var repository_exists = Ext.StoreMgr.lookup('sources_exception');
                    if (repository_exists)
                        repository_exists.load();
                    else
                        console.log('ERROR: sources_exception - fail_load [code of Call_back_Grid.js]');
                }
        );
    }
});
