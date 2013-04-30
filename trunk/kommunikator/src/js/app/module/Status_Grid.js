app.grid_show = function(storeId) {
    var grid = Ext.getCmp(storeId+'_grid');
    if (grid) {
        grid.ownerCt.ownerCt.setActiveTab(grid.ownerCt.ownerCt.items.indexOf(grid.ownerCt));
        grid.ownerCt.layout.setActiveItem(grid.ownerCt.items.indexOf(grid));
        grid.getStore().load();
        grid.fireEvent('activate',grid);
    }
};		

Ext.define('app.module.Status_Grid', {
    // title : 'day statistic',
    extend       : 'app.Grid',
    border       : false,
    style        : 'padding : 15px',
    status_grid  : true,
    grid_id      : 'Status_Grid',
    
    store_cfg    : {
        autorefresh  : true,
        fields       : ['name', 'value'],
        storeId      : 'statistic'  
    },
    
    height       : 500,
    hideHeaders  : true,
    
    columns      : [
    {
        width : 120
    },
    { 
        flex : 1
        // width : 50
    }],

    columns_renderer : function(value, metadata, record, rowIndex, colIndex, store) {
        if (colIndex == 0 || colIndex == 1)
            if (app.msg[value])
                value = app.msg[value];
        
        if (colIndex == 1) {
            if (record.data.name == 'status') {
                var color = (value==app.msg['online']) ? 'green' : 'red';
                return '<span style="color:'+color+';">'+value+'</span>';
            }
            if (record.data.name == 'cpu_use') {
                var color = (parseFloat(value.replace(/^([\d\.]+)\s%$/,"$1"))<app.critical_cpu) ? 'green' : 'red';
                return '<span style="color:'+color+';">'+value+'</span>';
            }
        };
        
        if (colIndex == 0) {
            if (record.data.name =='day_total_calls'){
                return '<a href="#" onclick="app.grid_show('+"'call_logs'"+');">'+value+'</a>';
            };
            if (record.data.name =='active_calls'){
                return '<a href="#" onclick="app.grid_show('+"'active_calls'"+');">'+value+'</a>';
            };
            if (record.data.name =='active_gateways'){
                return '<a href="#" onclick="app.grid_show('+"'gateways'"+');">'+value+'</a>';
            }
        }
        
        return value;
    },
            
    initComponent : function() {
        this.callParent(arguments); 
    }
})
