Ext.define('app.Dial_plans_Grid', {
    extend : 'app.Grid',
    initComponent : function () {
	this.store_cfg.autorefresh = false;
	this.store_cfg.fields = ['id','dial_plan', 'priority', 'prefix', 'gateway_id', 'nr_og_digits_to_cut','position_to_start_cutting','nr_of_digits_to_replace','digits_to_replace_with','position_to_start_replacing','position_to_start_adding','digits_to_add'];
        this.store_cfg.storeId ='dial_plans';
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
