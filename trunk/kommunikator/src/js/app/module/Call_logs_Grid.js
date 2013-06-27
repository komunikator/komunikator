var today = new Date(); 
today.setHours(0, 0, 0, 0);
var tomorrow = new Date();  
tomorrow.setDate((new Date()).getDate() + 1);
tomorrow.setHours(0, 0, 0, 0);

Ext.define('app.module.Call_logs_Grid', {
    extend : 'app.Grid',
    store_cfg:{
        autoLoad: false,
        fields : ['id',{
            name:'time', 
            type:'date', 
            dateFormat:app.date_format
        }, 'caller', 'called', 'duration', 'status'],
        storeId : 'call_logs',
        sorters : [{
            direction: 'DESC',
            property: 'time'	
        }]
    },
    columns : [
    {
        xtype: 'rownumberer',
        width: 50,
        sortable: false
    },
    {
        width:120,
        groupable: false, 
        xtype: 'datecolumn',
        format:app.date_format
    },
    {
        width:160
    },
    {
        width:160
    },
    {},
    {
        renderer: function(value) {
            if (app.msg[value])
                value = app.msg[value];
            return value
        }

    }
    ],
    requires: 'Ext.ux.grid.FiltersFeature',
    features : [{
        ftype:'grouping',
        hideGroupedHeader :true
    }, { 
        ftype : 'filters' ,
        //autoReload: true,//false,//true,  //don't reload automatically
        local: false, //only filter locally
        encode : true,
        filters:
        [{
            type: 'date',
            dateFormat : app.php_date_format,
            dataIndex: 'time',
            active: true,
            value: {
                //after: new Date(),
                //before: new Date(new Date().getTime() + 24 * 60 * 60 * 1000)
              on: new Date()
           //   before: new Date ()
            }
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
    //viewConfig:{loadMask :true},
    initComponent : function () {
        app.Loader.load(['js/ux/grid/css/GridFilters.css','js/ux/grid/css/RangeMenu.css']);
        this.listeners.beforerender = function(){
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
    }
})
