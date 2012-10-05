Ext.define('DIGT.Extensions_Grid', {
    requires: 'DIGT.Groups_Grid',
    extend : 'DIGT.Grid',
    initComponent : function () {
	//this.title = DIGT.msg.extensions;
	//`console.log(Ext.StoreMgr.lookup('groups'));
	this.store.autorefresh = false;
	this.store.fields = ['id','status', 'extension', 'firstname', 'lastname', 'group'];
        this.store.storeId ='extensions';
	this.viewConfig.loadMask = false;
	this.columns = [
	{hidden: true},
	{},
	{ editor :  {
                xtype: 'textfield',
 		regex: /^\d{3}$/
		//,
                //allowBlank: false 
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
                xtype: 'textfield'
	}},
	{ editor :  {
    		xtype: "combobox",
    		store: Ext.StoreMgr.lookup('groups'),
    		displayField: "group",
    		valueField: "group",
    		queryMode: "remove"
	}}
	];
        this.callParent(arguments); 
   }
})
