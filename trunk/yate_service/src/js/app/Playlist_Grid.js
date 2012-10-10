Ext.define('app.Playlist_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	this.store_cfg.autorefresh = undefined;  
	this.store_cfg.fields = ['id', 'playlist', 'in_use'];
        this.store_cfg.storeId ='playlist';
	this.viewConfig.loadMask = false;
	this.columns = [
	{
            hidden: true
        }
	];
        this.callParent(arguments);
   }
})
