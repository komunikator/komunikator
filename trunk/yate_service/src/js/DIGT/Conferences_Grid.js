Ext.define('DIGT.Conferences_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
        //this.title = DIGT.msg.extensions;
        this.store.autorefresh = false;
        this.store.fields = ['id','conference', 'number', 'participants'];
        this.store.storeId ='conferences';
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
