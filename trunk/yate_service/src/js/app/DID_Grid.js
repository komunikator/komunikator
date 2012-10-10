Ext.define('app.DID_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
        this.store_cfg.autorefresh = undefined;
        this.store_cfg.fields = ['id','did','number', 'destination','description','extension','group'];
        this.store_cfg.storeId ='dids';
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

            { 
            editor :  {
                xtype: 'textfield'
            }
            }
        ];
        this.callParent(arguments); 
    }
})
