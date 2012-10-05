Ext.define('Adminka.view.Navigation', {
	extend: 'Ext.tree.Panel',
	alias : 'widget.navmenu',
	fields: ['text', 'view'],
	title : 'Navigation',
	rootVisible: false,
	
	initComponent: function() {
		this.root = {
	       children: [{ 
	    	   text: 'Network Settings', leaf: true, view: 'networkedit'
	       },{ 
	    	   text: 'System', expanded: true,
	    	   children: [{
	    		   text: 'Info', leaf: true, view: 'sysinfo'
	    	   }, {
	    		   text: 'Reboot', leaf: true
	    	   }]
	       }]
	    };
		this.callParent(arguments);
	}
});