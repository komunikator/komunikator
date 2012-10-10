Ext.define('app.Conferences_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
        //this.title = app.msg.extensions;
        this.store_cfg.autorefresh = false;
        this.store_cfg.fields = ['id','conference', 'number', 'participants'];
        this.store_cfg.storeId ='conferences';
        this.viewConfig.loadMask = false;
        this.columns = [
        {
            hidden: true
        },
        { 
            editor :  {
                xtype: 'textfield'
            }
        },

        { 
            editor :  {
                xtype: 'textfield'
            }
        },
	{}
        ];
        this.callParent(arguments); 
    }
})
