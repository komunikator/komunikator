Ext.define('DIGT.Call_logs_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
        this.title = DIGT.msg.call_logs;
        this.store.fields =  ['time', 'caller', 'called', 'duration', 'status'];
        this.store.storeId ='call_logs';
        this.callParent(arguments);
        this.listeners = {
            activate: function(){
                this.un('activate', arguments.callee);
                this.store.guaranteeRange(0, DIGT.store_page-1);
            }
        }
    }
})
