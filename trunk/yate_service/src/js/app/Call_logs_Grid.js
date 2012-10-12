Ext.define('app.Call_logs_Grid', {
    extend : 'app.Grid',
    store_cfg:{                                    
        fields : [{
            name:'time', 
            type:'date', 
            dateFormat:app.date_format
        }, 'caller', 'called', 'duration', 'status'],
        storeId : 'call_logs'
    },
    columns : [{
        width:120,
        xtype: 'datecolumn',
        format:app.date_format
    //filterable: true
    }],
    requires: 'Ext.ux.grid.FiltersFeature',
    features : [ { 
        ftype : 'filters' ,
        //autoReload: false, //don't reload automatically
        local: false, //only filter locally
        encode : true,
        filters:
        [{
            type: 'date',
            dateFormat : app.php_date_format,
            dataIndex: 'time'
        }, {
            type: 'string',
            dataIndex: 'caller'
        },{
            type: 'string',
            dataIndex: 'called'
        },{
            type: 'numeric',
            dataIndex: 'duration'
        },{
            type: 'string',
            dataIndex: 'status'
        }]
    } ],
    initComponent : function () {
        app.Loader.load(['js/ux/grid/css/GridFilters.css','js/ux/grid/css/RangeMenu.css']);
        this.listeners = {
            afterrender: function(){/*this.store.load();*/

                this.store.guaranteeRange(0, app.pageSize-1);
                if (app['lang'] == 'ru')
                    app.Loader.load(['js/app/locale/filter.ru.js']);
            }

        };  
        /*
        this.columns_renderer = 
        function(value, metaData, record, rowIndex, colIndex, store) {
        //    if (colIndex==1) 
	//	return Ext.util.Format.date(new Date(value*1000), 'd.m.12 H:i:s');;
            return value; 
        }
*/
        this.callParent(arguments);
    }
})
