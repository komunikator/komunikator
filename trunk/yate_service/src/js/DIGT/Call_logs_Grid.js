Ext.define('DIGT.Call_logs_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
        //this.title = DIGT.msg.call_logs;
        this.store.fields = ['time', 'caller', 'called', 'duration', 'status'];
        this.store.storeId ='call_logs';
	this.columns = [{width:120}];
        this.listeners = {
	afterrender: function(){/*this.store.load();*/this.store.guaranteeRange(0, DIGT.pageSize-1);}
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
        //this.store.autoLoad = true;
    }
})
