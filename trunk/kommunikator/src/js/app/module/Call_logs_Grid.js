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
    var Last = new Date(NowDate.getFullYear(), NowDate.getMonth(), NowDate.getDate() + RightOffset)
    return({begin: First, end: Last});
    // alert(First.getDate() + '.'+First.getMonth()+'.'+First.getFullYear()+' - ' + Last.getDate()+'.'+Last.getMonth()+'.'+Last.getFullYear());
}

Ext.define('app.module.Call_logs_Grid', {
    extend: 'app.Grid',
    export: true,
    store_cfg: {
        autoLoad: false,
        fields: ['id', {
                name: 'time',
                type: 'date',
                dateFormat: app.date_format
            }, 'type', 'caller', 'called', 'duration', 'gateway', 'status'],
        storeId: 'call_logs',
        sorters: [{
                direction: 'DESC',
                property: 'time'
            }]
    },
    columns: [
        {
            xtype: 'rownumberer',
            width: 50,
            sortable: false
        },
        {
            width: 120,
            groupable: false,
            xtype: 'datecolumn',
            format: app.date_format
        },
        {
            renderer: app.msg_renderer
        },
        {
            width: 160
        },
        {
            width: 160
        },
        {
            //align: 'right',
            //width: 160,
            renderer: app.dhms
        },
        {
        },
        {
            renderer: app.msg_renderer
        }
    ],
    requires: 'Ext.ux.grid.FiltersFeature',
    features: [/*
     {
     //groupHeaderTpl: 'Subject: {name}',
     ftype: 'groupingsummary'
     },*/
        {
            ftype: 'grouping',
            hideGroupedHeader: true
        }, {
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
                            local: true,
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

            //console.log(this.store.storeId);
            //this.store.guaranteeRange(0, app.pageSize-1);
            if (app['lang'] == 'ru')
                app.Loader.load(['js/app/locale/filter.ru.js']);
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
                                    var week = GetDateInWeek(-1);
                                    get_grid_filter('time').setValue(
                                            {after: week.begin, on: null, before: week.end}
                                    );
                                    get_grid_filter('time').setActive(true, false);
                                }
                            }, {
                                xtype: 'button',
                                text: app.msg.cur_week ? app.msg.cur_week : 'Current week',
                                handler: function() {
                                    var week = GetDateInWeek();
                                    get_grid_filter('time').setValue(
                                            {after: week.begin, on: null, before: week.end}
                                    );
                                    get_grid_filter('time').setActive(true, false);
                                }
                            },
                            '|',
                            {
                                xtype: 'button',
                                text: app.msg.yesterday ? app.msg.yesterday : 'Уesterday',
                                handler: function() {
                                    get_grid_filter('time').setValue({on: yesterday});
                                    get_grid_filter('time').setActive(true, false);
                                }
                            }, {
                                xtype: 'button',
                                text: app.msg.today ? app.msg.today : 'Today',
                                handler: function() {
                                    get_grid_filter('time').setValue({on: today});
                                    get_grid_filter('time').setActive(true, false);
                                }
                            }

                        ]
                    });

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
    }
})
