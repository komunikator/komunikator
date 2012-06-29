Ext.define('DIGT.Extensions_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.title = DIGT.msg.extensions;
	this.store.autorefresh = false;
	this.store.fields =  ['status', 'extensions', 'firstname', 'lastname', 'groups'];
        this.store.storeId ='extensions';
	this.viewConfig.loadMask = false;
        this.callParent(arguments);
   }
})
