app.source_tip = function(values){
    if (Ext.isObject(values) && values.id && values.name)
        return values.id.length==2?app.msg.group+"&nbsp;"+values.name:(values.id.length==3?app.msg.extension+"&nbsp;"+values.id:(app.msg[values.name]?app.msg[values.name]:values.name));
    return null;
}

//values.id.length==2?app.msg.group+"&nbsp;"+values.id:(values.id.length==3?app.msg.extension+"&nbsp;"+values.id:(app.msg[values.name]?app.msg[values.name]:values.name))

Ext.define('app.module.Keys_Grid', {
    extend : 'app.Grid',
    store_cfg:{ 
        fields : ['id','status','key', 'destination','description'],
        storeId : 'keys'
    },
    columns_renderer :
    function(value, metaData, record, rowIndex, colIndex, store) {
        if (colIndex==2 && app.msg[value])
        {
            return app.msg[value];
        }
        return value;
    },
    columns : [
    {
        hidden: true
    },

    { 
        editor :  {
            xtype: 'combobox',
            store: [['online',app.msg['online']?app.msg['online']:'online'],['offline',app.msg['offline']?app.msg['offline']:'offline']],
            //displayField: 'group',
            //valueField: 'group',
            value :'online',	
            queryMode: 'local'
            ,
            allowBlank: false 
        }

    } ,
        

    { 
        editor :  {
            xtype: 'textfield',
            regex: /^\d$/
            ,
            allowBlank: false 
        }
    },

    { 
        editor :  app.get_Source_Combo({
            allowBlank: false
        })

    /*{
            xtype: 'combobox',
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
        } */

    } ,
    { 
        editor :  {
            xtype: 'textfield'
        }
    }
    ], 
    columns_renderer : function(value, metadata, record, rowIndex, colIndex, store) {
        if (colIndex == 1 || colIndex == 3){
            metadata.tdAttr = 'data-qtip="' + app.msg[value]?app.msg[value]:value + '"';
            return app.msg[value]?app.msg[value]:value;
        }
        return value;		
    },
    initComponent : function () {
        this.callParent(arguments); 
    }
})
