Ext.define('app.Mini_active_calls_Grid', {
    extend : 'app.Grid',
    status_grid : true,
    grid_id : 'Mini_active_calls_Grid',	
    store_cfg: {
        autorefresh : false,
        fields : ['time', 'caller', 'called', 'duration', 'status'],
        storeId :'active_calls'  
    },
    initComponent : function () {
        //this.title = app.msg.active_calls;
        this.viewConfig.loadMask = false;
        this.columns = [
        {
            hidden: true
        },

        {
            width:70
        },

        {
            width:70
        },

        {
            hidden: true
        },

        {
            width:70
        }
        ];
        this.callParent(arguments);
    }
})
