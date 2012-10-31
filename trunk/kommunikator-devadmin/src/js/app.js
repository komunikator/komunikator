Ext.Loader.setConfig({enabled:true});
Ext.tip.QuickTipManager.init();

Ext.apply(Ext.form.field.VTypes, {
	ipVal: function(val, field) {
		if (/^([1-9][0-9]{0,1}|1[013-9][0-9]|12[0-689]|2[01][0-9]|22[0-3])([.]([1-9]{0,1}[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])){2}[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-4])$/.test(val))
		{
			return true;
		}
		return false;
	},
	ipValText: 'Invalid IP Address',
	ipMask: function(val, field) {
		if (/^(128|192|224|24[08]|25[245].0.0.0)|(255.(0|128|192|224|24[08]|25[245]).0.0)|(255.255.(0|128|192|224|24[08]|25[245]).0)|(255.255.255.(0|128|192|224|24[08]|252))$/.test(val))
		{
			return true;
		}
		return false;
	},
	ipMaskText: 'Invalid Network Mask'
});

Ext.application({
    name: 'Adminka',
    appFolder: 'js/app',
    // automatically create an instance of AM.view.Viewport
    autoCreateViewport: true,
    controllers: ['Network', 'Navigation']
});
