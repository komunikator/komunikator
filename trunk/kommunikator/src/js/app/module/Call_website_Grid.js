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

Ext.apply(Ext.form.field.VTypes, {
    picture: function(val, field) {
        if (val == '#e6e6e6') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Grey_button.png" >'));
            return true;
        }
        if (val == '#0044cc') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Cyan_button.png" >'));
            return true;
        }
        if (val == '#2f96b4') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Blue_button.png" >'));
            return true;
        }
        if (val == '#51a351') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Green_button.png" >'));
            return true;
        }
        if (val == '#f89406') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Yellow_button.png" >'));
            return true;
        }
        if (val == '#bd362f') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Red_button.png" >'));
            return true;
        }
        if (val == '#444444') {
            console.log(field.ownerCt.items.items[5].setValue('<img src= "js/app/images/Black_button.png" >'));
            return true;
        }

    }
});

var color = [['btn', '#e6e6e6'], ['btn btn-primary', '#0044cc'], ['btn btn-info', '#2f96b4'], ['btn btn-success', '#51a351'], ['btn btn-warning', '#f89406'], ['btn btn-danger', '#bd362f'], ['btn btn-inverse', '#444444']];

Ext.define('app.module.Call_website_Grid', {
    //id: 'ID_Call_website',
    
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
                store: Ext.create('app.Store', {
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
        {  // 'button_color' - цвет кнопки
            renderer: function(v) {
                if (v == 'btn')
                    return app.msg.gray;
                else
                if (v == 'btn btn-primary')
                    return app.msg.blue;
                else
                if (v == 'btn btn-info')
                    return app.msg.azure;
                else
                if (v == 'btn btn-success')
                    return app.msg.green;
                else
                if (v == 'btn btn-warning')
                    return app.msg.orange;
                else
                if (v == 'btn btn-danger')
                    return app.msg.red;
                else
                if (v == 'btn btn-inverse')
                    return app.msg.dark_gray;
                return v;
            },
            editor: {
                xtype         : 'combobox',
                mode          : 'local',
                groupable     : false,
                sortable      : false,
                editable      : false,
                store         : color,
                listConfig: {
                    getInnerTpl: function() {
                        var tpl = '<div class="x-combo-list-item" style="background-color:{field2};color:{field2};">{field1}</div>';
                        return tpl;
                    }
                },
                defaultValue  : 1,
                vtype         : 'picture',
                listeners: {
                    afterrender: function() {
                        this.setValue(this.defaultValue);
                    }
                }
            }
        },
        {  // 'button_code' - код кнопки
            xtype  : 'actioncolumn',
            sortable: false,
            groupable: false,
            icon   : 'js/app/images/Grey_button.png',
            
            handler: function(grid, rowIndex, colIndex) {
                
                var rec = grid.getStore().getAt(rowIndex);
                
                var sda_url = 'data.php?action=get_button_code&sda_short_name=' + rec.get('short_name') + '&sda_button_color=' + rec.get('color');
                
                
                Ext.create('widget.window', {
                    title        : app.msg.button_code,
                    width        : 600,
                    height       : 450,
                    autoHeight   : true,
                    autoScroll   : true,
                    maximizable  : true,  // значок «раскрыть окно на весь экран»
                    modal        : true,  // блокирует всё, что на заднем фоне
                    draggable    : true,  // перемещение объекта по экрану
                    
                    loader: {
                        url       : sda_url,
                        loadMask  : false,
                        autoLoad  : true,  // important
                        renderer  : 'html'  // this is also the default option, other options are data | component
                    }
                    
                }).show();
                
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


                    var repository_exists = Ext.StoreMgr.lookup('sources_exception');

                    if (repository_exists)
                        repository_exists.load();
                    else
                        console.log('ERROR: sources_exception - fail_load [code of Call_website_Grid.js]');
                }

        );
    }
});

/*  tpl: Ext.create('Ext.XTemplate',
 '<tpl for=".">',
 '<div class="x-combo-list-item" style="background-color:{field1}">{field1}</div>',
 '</tpl>'
 ),
 displayTpl1: Ext.create('Ext.XTemplate',
 '<tpl for=".">',
 '{field1}',
 '</tpl>'
 )*/
