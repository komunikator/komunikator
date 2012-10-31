Ext.define('Adminka.controller.Network', {
	extend: 'Ext.app.Controller',

	refs: [
	    { ref: 'networkEdit', selector: '#networkedit' },
	    { ref: 'fields', selector: '#networkedit > fieldset' }
	],
	stores: ['Network'],
	init: function() {
		this.control({
			'viewport button[action=save]': {
				click: function() {
					var form = this.getNetworkEdit().getForm();
					if (form.isValid()) {
						form.submit({
							url: 'network.php',
	                    	success: function() {
		                    	Ext.MessageBox.show({ title:'Выполнено', msg: 'Изменения сохранены',
		                    		icon: Ext.MessageBox.INFO, buttons: Ext.MessageBox.OK});
	                    	},
	                    	failure: function(fp, o) {
		                		var json = Ext.decode(o.response.responseText);
		                		Ext.MessageBox.show({title: 'Ошибка!', msg: json.message,
	                				icon: Ext.MessageBox.ERROR, buttons: Ext.MessageBox.OK});
	                    	}
						});
					}
				}
			},
			'#networkedit radiogroup': {
				change: this.networkTypeChange
			}
		});
	},
	
	networkTypeChange: function(field, newVal, oldVal) {
		if (field.getValue().type > 0) {
			this.getFields().enable();
		} else {
			this.getFields().disable();
		}
	},
	
	onLaunch: function() {
		var me = this;
		this.getNetworkStore().on('load', function() {
			me.getNetworkEdit().getForm().loadRecord(me.getNetworkStore().getAt(0));
		});
	}
});
