Ext.define('Adminka.controller.Network', {
	extend: 'Ext.app.Controller',

	refs: [
	    { ref: 'networkEdit', selector: '#networkedit' },
	    { ref: 'fields', selector: '#networkedit > fieldset' }
	],
	
	init: function() {
		this.control({
			'viewport button[action=save]': {
				click: function() {
					var form = this.getNetworkEdit().getForm();
					if (form.isValid()) {
						//form.submit();
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
	}
});
