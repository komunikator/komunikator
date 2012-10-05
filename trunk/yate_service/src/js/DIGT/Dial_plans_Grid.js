Ext.define('DIGT.Dial_plans_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
	this.store.autorefresh = false;
	this.store.fields = ['id','dial_plan', 'priority', 'prefix', 'gateway_id', 'nr_og_digits_to_cut','position_to_start_cutting','nr_of_digits_to_replace','digits_to_replace_with','position_to_start_replacing','position_to_start_adding','digits_to_add'];
        this.store.storeId ='dial_plans';
	this.viewConfig.loadMask = false;
	this.columns = [
	{hidden: true},
	{},
	{},
	{},
	{},
	{},
	{},
	{},
	{},
	{},
	{},
	{}
	];
        this.callParent(arguments); 
   }
})
