Ext.define('app.Playlist_Grid', {
    extend : 'app.Grid',
    store_cfg : {
        fields : ['id', 'playlist', 'in_use'],
        storeId : 'playlist'
    },
    columns : [
    {
        hidden: true
    },
    { 
        editor :  {
            xtype: 'textfield'
        }
    }
    ],
    initComponent : function () {
        this.callParent(arguments);
    }
})
