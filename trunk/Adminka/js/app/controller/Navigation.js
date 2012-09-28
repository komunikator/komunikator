Ext.define('Adminka.controller.Navigation', {
	extend: 'Ext.app.Controller',

	refs: [{ ref: 'rightPanel', selector: '#right_panel'}],

	init: function() {
		this.control({
			'#left_menu': {
				itemclick: this.itemClick
			}
		});
	},

	itemClick: function(view, rec, item, index, eventObj) {
		if (rec.get('leaf') && rec.get('view'))
		{
			var panel = this.getRightPanel();
			panel.layout.setActiveItem(rec.get('view'));
		}
	}
});
