Ext.define('Adminka.view.Viewport', {
	extend: 'Ext.container.Viewport',
	style:'padding:2px;',
	layout: 'border',
	requires: [
	    'Adminka.view.network.Edit',
        'Adminka.view.Navigation',
        'Adminka.view.Sysinfo',
	],
	items: [{
		id: 'right_panel',
		region: 'center',
		autoHeight: true,
		border: false,
		margins: '0 0 5 0',
		layout: 'card',
		items: [{
			id: 'networkedit',
			xtype: 'networkedit'
		},{
			id: 'sysinfo',
			xtype: 'sysinfo'
		}],
		buttons: [{
			text: 'Save',
			action: 'save'
		},{
			text: 'Cancel',
			action: 'cancel'
		}]
	}, {
		id: 'left_menu',
		region: 'west',
		collapsible: true,
		width: 150,
		xtype: 'navmenu'
	}]	
});
