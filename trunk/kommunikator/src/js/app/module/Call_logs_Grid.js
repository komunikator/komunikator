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

var today = new Date();
today.setHours(0, 0, 0, 0);
var yesterday = new Date();
yesterday.setDate((new Date()).getDate() - 1);
yesterday.setHours(0, 0, 0, 0);
function GetDateInWeek(WeekOffset) {
    if (!WeekOffset)
        WeekOffset = 0;
    var NowDate = new Date();
    var CurrentDay = NowDate.getDay();
    var LeftOffset = CurrentDay - 1 - 7 * WeekOffset;
    var RightOffset = 7 - CurrentDay + 7 * WeekOffset;
    var First = new Date(NowDate.getFullYear(), NowDate.getMonth(), NowDate.getDate() - LeftOffset);
    var Last = new Date(NowDate.getFullYear(), NowDate.getMonth(), NowDate.getDate() + RightOffset);

    return({begin: First, end: Last});
    // alert(First.getDate() + '.'+First.getMonth()+'.'+First.getFullYear()+' - ' + Last.getDate()+'.'+Last.getMonth()+'.'+Last.getFullYear());
}

var windowAbout = Ext.create('widget.window', {
    title: "About",
    width: 500,
    height: 450,
    autoHeight: true,
    autoScroll: true,
    maximizable: true, // значок «раскрыть окно на весь экран»
    modal: true, // блокирует всё, что на заднем фоне
    draggable: true, // перемещение объекта по экрану
    closeAction: 'hide',
    items: Ext.create('Ext.grid.Panel', {
        id: 'windowAboutCallSite',
        store: [[null, null]], // определили хранилище
        closeAction: 'hide', //по дефолту объект при закрытии уничтожается, с 'hide' - нет.
        enableColumnHide: false,
        columns:
                [{
                        text: app.msg.param,
                        flex: 1,
                        dataIndex: 'field1',
                        sortable: false,
                        renderer: function(value) {
                            if (app.msg[value]) {
                                return "<b>" + app.msg[value] + "</b>";
                            } else {
                                return "<b>" + value + "</b>";
                            }
                        }
                    }, {
                        text: app.msg.value,
                        flex: 1,
                        dataIndex: 'field2',
                        sortable: false
                    }
                ]
    })
});

window.openAbout = function(object) {
    Ext.getCmp('windowAboutCallSite').getStore().loadData(object);
    windowAbout.show();
};

Ext.define('app.module.Call_logs_Grid', {
    extend: 'app.Grid',
    export: true,
    store_cfg: {
        autoLoad: false,
        fields: ['id', {
                name: 'time',
                type: 'date',
                dateFormat: app.date_format
            }, 'type', 'caller', 'called', 'duration', 'gateway', 'status', 'record', 'download'],
        storeId: 'call_history',
        sorters: [{
                direction: 'DESC',
                property: 'time'
            }]
    },
    columns: [
        {// 'id'
            width: 50,
            xtype: 'rownumberer',
            sortable: false
        },
        {// {'time' + 'date'}
            width: 125,
            xtype: 'datecolumn',
            format: app.date_format,
            groupable: false
        },
        {// 'type'
            width: 125,
            renderer: app.msg_renderer
        },
        {// 'caller'
            width: 175
        },
        {// 'called'
            width: 175
        },
        {// 'duration'
            width: 100,
            renderer: app.dhms
        },
        {// 'gateway'
            width: 150,
            renderer: function(value, metadata, record) {
                var value = unescape(value); //инфо о звонке с сайта записывается в Base64, эта строка выводит в норм виде
                if (value !== '') {
                    try {
                        JSON.parse(value);
                    } catch (e) {
                        return value;
                    }
                    var call_site_params = [];
                    Ext.call_site_hint = '';
                    var obj = JSON.parse(value);
                    for (var key in obj) {
                        for (var key1 in obj[key]) {
                            if (app.msg[key1]) {
                                var val = app.msg[key1];
                            } else {
                                var val = key1;
                            };
                            Ext.call_site_hint = Ext.call_site_hint + val + " : " + obj[key][key1] + "<br/>";
                            call_site_params.push([key1, obj[key][key1]]);
                        }
                    }
                    metadata.tdAttr = 'data-qtip="' + Ext.call_site_hint + '"';//выводим подсказку при наведении 
                    Ext.call_site_hint = call_site_params;      //собрали массив для данных в выводимом окне при клике          
                    return '<img src="js/app/images/about.png" alt="About" onclick=openAbout(Ext.call_site_hint) style = "cursor: pointer">';
                }
            }
        },
        {// 'status'
            width: 150,
            renderer: app.msg_renderer
        },
        {//record
            sortable: false,
            width: 320,
            xfilter: {},
            renderer: function(value) {
                if (value)
                    value = '<audio style="width: 300px;display: block;-webkit-box-sizing: border-box; height: 30px;white-space: normal !important;\n\
                         line-height: 13px;border-collapse: separate;border-color: gray;" \n\
                         type="audio/wav" src="records/' + value + '.wav?dc_=' + new Date().getTime() + '" controls autobuffer></audio>';
                return value;
            }
        },
        {
            sortable: false,
            width: 125,
            renderer: function(value) {
                if (value)
                    value = '<a TARGET="_blank" href="records/' + value + '.wav">' + app.msg.download + '</a>';
                return value;
            }
        }
    ],
    requires: 'Ext.ux.grid.FiltersFeature',
    features: [
        /*
         {
         //groupHeaderTpl: 'Subject: {name}',
         ftype: 'groupingsummary'
         },
         */
        {
            ftype: 'grouping',
            hideGroupedHeader: true
        },
        {
            ftype: 'filters',
            //autoReload: true,//false,//true,  //don't reload automatically
            local: false, //only filter locally
            encode: true,
            filters:
                    [{
                            type: 'date',
                            dateFormat: app.php_date_format,
                            dataIndex: 'time',
                            active: true,
                            value: {
                                //after: new Date(),
                                //before: new Date(new Date().getTime() + 24 * 60 * 60 * 1000)
                                on: new Date()
                                        //   before: new Date ()
                            }
                        }, {
                            encode: 'encode',
                            local: true,
                            type: 'list',
                            options: [['internal', app.msg['internal']], ['incoming', app.msg['incoming']], ['outgoing', app.msg['outgoing']]],
                            dataIndex: 'type'
                        }, {
                            type: 'string',
                            dataIndex: 'caller'
                        }, {
                            type: 'string',
                            dataIndex: 'called'
                        }, {
                            type: 'numeric',
                            dataIndex: 'duration'
                        }, {
                            type: 'string',
                            dataIndex: 'gateway'
                        }/*, {
                         type: 'string',
                         dataIndex: 'status'
                         }*/]
        }],
    //viewConfig:{loadMask :true},
    initComponent: function() {
        app.Loader.load(['js/ux/grid/css/GridFilters.css', 'js/ux/grid/css/RangeMenu.css']);
        this.listeners.beforerender = function() {
//console.log(this.store.storeId);
//this.store.guaranteeRange(0, app.pageSize-1);
            if (app['lang'] == 'ru')
                app.Loader.load(['js/app/locale/filter.ru.js']);
        };
        /*
         this.columns_renderer = 
         function(value, metaData, record, rowIndex, colIndex, store) {
         //    if (colIndex==1) 
         //	return Ext.util.Format.date(new Date(value*1000), 'd.m.12 H:i:s');;
         return value; 
         }
         */
        /*
         onRefresh : function(){
         var me = this; 
         me.body.mask(Ext.view.AbstractView.prototype.loadingText);
         this.store.load({
         callback: function(){
         me.body.unmask()
         }
         });
         
         */
        this.callParent(arguments);
        Ext.ux.grid.filter.DateFilter.override({
            init: function() {
                this.callOverridden();
                this.on('update', this.updateValues);
            },
            updateValues: function() {
                var me = this, key, picker;
                for (key in me.fields) {
                    if (me.fields[key].checked) {
                        picker = me.getPicker(key);
                        me.values[key] = picker.getValue();
                    }
                }
            }
        });
        var me = this;
        var get_grid_filter = function(name) {
            return me.filters.getFilter(name);
        };
        this.addDocked(
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            xtype: 'button',
                            text: app.msg.last_week ? app.msg.last_week : 'Last week',
                            handler: function() {
//me.filters.resumeEvents();
//me.filters.reload()
                                var week = GetDateInWeek(-1);
                                get_grid_filter('time').setActive(false, false);
                                get_grid_filter('time').setValue(
                                        {after: week.begin, before: week.end}
                                );
                                get_grid_filter('time').setActive(true, false);
                            }
                        }, {
                            xtype: 'button',
                            text: app.msg.cur_week ? app.msg.cur_week : 'Current week',
                            handler: function() {
                                var week = GetDateInWeek();
                                get_grid_filter('time').setActive(false, false);
                                get_grid_filter('time').setValue(
                                        {after: week.begin, before: week.end}
                                );
                                get_grid_filter('time').setActive(true, false);
                            }
                        },
                        '|',
                        {
                            xtype: 'button',
                            text: app.msg.yesterday ? app.msg.yesterday : 'Уesterday',
                            handler: function() {
                                var d = new Date();
                                d.setDate(d.getDate() - 1);
                                get_grid_filter('time').setActive(false, false);
                                get_grid_filter('time').setValue({on: d});
                                get_grid_filter('time').setActive(true, false);
                            }
                        }, {
                            xtype: 'button',
                            text: app.msg.today ? app.msg.today : 'Today',
                            handler: function() {
                                get_grid_filter('time').setActive(false, false);
                                get_grid_filter('time').setValue({on: new Date()});
                                get_grid_filter('time').setActive(true, false);
                            }
                        }

                    ]
                });
    }
})