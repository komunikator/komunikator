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

Ext.apply(Ext.form.field.VTypes, {
    picture: function(val, field) {
        if (val == '#0095C6') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Blue_button.png" >'));
            return true;
        }
        if (val == '#F48033') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Orange_button.png" >'));
            return true;
        }
        if (val == '#C60000') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Red_button.png" >'));
            return true;
        }
        if (val == '#009E00') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Green_button.png" >'));
            return true;
        }
        if (val == '#FFCC4D') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Yellow_button.png" >'));
            return true;
        }
        if (val == '#6169CC') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Purple_button.png" >'));
            return true;
        }
        if (val == '#DE57A4') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Pink_button.png" >'));
            return true;
        }
        if (val == '#A0C152') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Lightgreen_button.png" >'));
            return true;
        }
        if (val == '#3C3C3C') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Black_button.png" >'));
            return true;
        } 
        if (val == '#CECECE') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Grey_button.png" >'));
            return true;
        }         

    }
});

var color = [['komunikator_btn-info komunikator_btn_blue', '#0095C6'], ['komunikator_btn-info komunikator_btn_orange', '#F48033'], ['komunikator_btn-info komunikator_btn_red', '#C60000'], ['komunikator_btn-info komunikator_btn_green', '#009E00'], ['komunikator_btn-info komunikator_btn_yellow', '#FFCC4D'], ['komunikator_btn-info komunikator_btn_purple', '#6169CC'], ['komunikator_btn-info komunikator_btn_pink', '#DE57A4'], ['komunikator_btn-info komunikator_btn_lightgreen', '#A0C152'], ['komunikator_btn-info komunikator_btn_black', '#3C3C3C'], ['komunikator_btn-info komunikator_btn_grey', '#CECECE']];

Ext.define('app.module.Call_website_Grid', {
    extend: 'app.Grid',
    store_cfg: {
        fields: ['id', 'description', 'destination', 'short_name', 'color', 'button_code'],
        storeId: 'call_button'
    },
    columns: [
        {// 'id'
            hidden: true
        },
        {// 'description'  - описание
            editor: {
                xtype: 'textfield'
            }
        },
        {// 'destination' - назначение
            editor: {
                xtype: 'combobox',
                store: Ext.StoreMgr.lookup('sources_exception') ?
                        Ext.StoreMgr.lookup('sources_exception') :
                        Ext.create('app.Store', {
                    fields: ['id', 'name'],
                    storeId: 'sources_exception'
                }),
                editable: false,
                displayField: 'name',
                valueField: 'name',
                queryMode: 'local',
                allowBlank: false
            }
        },
        {// 'short_name' - псевдоним
            editor: {
                xtype: 'textfield',
                allowBlank: false
            }
        },
        {// 'button_color' - цвет кнопки
            renderer: function(v) {
                if (v == 'komunikator_btn-info komunikator_btn_blue')
                    return app.msg.blue;
                else
                if (v == 'komunikator_btn-info komunikator_btn_orange')
                    return app.msg.orange;
                else
                if (v == 'komunikator_btn-info komunikator_btn_red')
                    return app.msg.red;
                else
                if (v == 'komunikator_btn-info komunikator_btn_green')
                    return app.msg.green;
                else
                if (v == 'komunikator_btn-info komunikator_btn_yellow')
                    return app.msg.yellow;
                else
                if (v == 'komunikator_btn-info komunikator_btn_purple')
                    return app.msg.purple;
                else
                if (v == 'komunikator_btn-info komunikator_btn_pink')
                    return app.msg.pink;
                else
                if (v == 'komunikator_btn-info komunikator_btn_lightgreen')
                    return app.msg.green;     
                else
                if (v == '')
                    return app.msg.black;
                else
                if (v == '')
                    return app.msg.grey;            
                return v;
            },
            editor: {
                xtype: 'combobox',
                mode: 'local',
                groupable: false,
                sortable: false,
                editable: false,
                store: color,
                listConfig: {
                    getInnerTpl: function() {
                        var tpl = '<div class="x-combo-list-item" style="background-color:{field2};color:{field2};">{field1}</div>';
                        return tpl;
                    }
                },
                defaultValue: 1,
                vtype: 'picture',
                listeners: {
                    afterrender: function() {
                        this.setValue(this.defaultValue);
                    }
                }
            }
        },
        {// 'button_code' - код кнопки
            xtype: 'actioncolumn',
            sortable: false,
            groupable: false,
            icon: 'js/app/images/Grey_button.png',
            handler: function(grid, rowIndex, colIndex) {
                var rec = grid.getStore().getAt(rowIndex);
                // var sda_url = 'data.php?action=get_button_code&sda_short_name=' + rec.get('short_name') + '&sda_button_color=' + rec.get('color');
                app.request(
                        {
                            action: 'get_button_code',
                            sda_short_name: rec.get('short_name'),
                            sda_button_color: rec.get('color')
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
                        console.log('ERROR: ' + store.storeId + ' fail_load [code of Call_website_Grid.js]');
                    }
                    
                }
        );
    }
});
