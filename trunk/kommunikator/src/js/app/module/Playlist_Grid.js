Ext.define('app.module.Playlist_Grid', {
    extend : 'app.Grid',
    load_after_edit: true,
    store_cfg : {
        fields : ['id', 'playlist', 'in_use'],
        storeId : 'playlists'
    },
    columns : [
    {
        hidden: true
    },
    { 
        editor :  {
            xtype: 'textfield',
            allowBlank: false
        }
    },
    { 
        renderer : app.checked_render,
        editor :  {
            xtype: 'checkbox',
            listeners:{
                change: function(c,n,o){
                //var store = Ext.StoreMgr.lookup('playlists');
                //	store.load();
                /*Ext.each(store.getRange(),function(record){
                        record.set({
                            in_use:!n
                        });
                        console.log(record);
                    });
		*/
                }
            },	
            style: {
                textAlign: 'center'
            } 
        }
    }
    ],
    initComponent : function () {
        this.callParent(arguments);
    }
})
