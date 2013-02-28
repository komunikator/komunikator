app.Source_Combo = {
    xtype: 'combo',
    store: Ext.StoreMgr.lookup('sources')?
    Ext.StoreMgr.lookup('sources'):
    Ext.create('app.Store',{
        fields : ['id', 'name'],   
        storeId : 'sources'
    }),
    //queryCaching: false,
    tpl: Ext.create('Ext.XTemplate',
        '<tpl for=".">',
        '<div class="x-boundlist-item" data-qtip="{[app.source_tip(values)]}">{[app.msg[values.name]?app.msg[values.name]:values.name]}</div>',
        '</tpl>'
        ),
    // template for the content inside text field
    displayTpl: Ext.create('Ext.XTemplate',
        '<tpl for=".">',
        '{[app.msg[values.name]?app.msg[values.name]:values.name]}',
        '</tpl>'
        ),
    editable: true,	
    displayField: 'name',
    valueField: 'name',
    queryMode: 'remove'
}
