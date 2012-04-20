var SettingsScreen = {
	onShow: function(isFirstShow) {
		SettingsScreen.updateField('account');
		SettingsScreen.updateField('password');
	},
	
	updateField: function(itemName) {
		var value = Settings.getItem(itemName);
		if (!value) value = '';
		$('settings-' + itemName).value = value;
	},
	
	onHide: function() {
		var changed = this.save();
		
		//if (changed) {
		//	KPlugin.disconnect();
		//}
	},

	save: function() {
		var changed = false;
	
		changed = this.saveItem('account') || changed;
		changed = this.saveItem('password') || changed;
		
		return changed;
	},
	
	saveItem: function(itemName) {
		var oldValue = Settings.getItem(itemName);
		var newValue = $('settings-' + itemName).value;
		
		Settings.setItem(itemName, newValue);
		
		return !(oldValue == newValue);
	}
};
