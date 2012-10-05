Ext.define('DIGT.Playlist_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.store.autorefresh = false;  
	this.store.fields = ['id', 'playlist', 'in_use'];
        this.store.storeId ='playlist';
	this.viewConfig.loadMask = false;
	this.columns = [
	{
            hidden: true
        }
	];
        this.callParent(arguments);
   }
})
