var type = Ext.create('Ext.data.Store', {
    fields: ['id', 'type'],
    data: [
        {"id": "*", 'type': app.msg.all_calls},
        {"id": "1", 'type': app.msg.outgoing_calls},
        {"id": "2", 'type': app.msg.incoming_calls},
        {"id": "3", 'type': app.msg.internal_calls}
    ]
});


Ext.define('app.module.Call_Record_Grid', {
    extend: 'app.Grid',
    store_cfg: {
        fields: ['id', 'caller', 'type', 'gateway', 'number', 'group', 'enabled', 'description'],
        storeId: 'call_records'
    },
    columnLines: true,
    columns: [
        {// 'id'
            hidden: true
        },
        {
            width: 175,
            editor: {
                xtype: 'combobox',
                store: Ext.create('app.Store', {
                    fields: ['id', 'name'],
                    storeId: 'ext_groups'
                }),
                queryMode: 'local',
                displayField: 'name',
                valueField: 'id',
                editable: true
            }


        }, {// 'type'
            width: 125,
            editor: {
                xtype: 'combobox',
                store: type,
                queryMode: 'local',
                displayField: 'type',
                valueField: 'id',
                editable: false
            }
        },
        {// 'gateway'
            width: 150,
            editor: {
                xtype: 'combobox',
                store: Ext.create('app.Store', {
                    fields: ['id', 'name'],
                    storeId: 'gateway_list'
                }),
                queryMode: 'local',
                displayField: 'name',
                valueField: 'id',
                editable: false
            }

        },
        {
            //  'called'
            text: app.msg.called,
            columns: [{width: 150,
                    text: app.msg.number,
                    dataIndex: 'number',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.create('app.Store', {
                            fields: ['id', 'name'],
                            storeId: 'extensions_list'
                        }),
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id'
                                // editable: true,
                                /*  listeners: {
                                 afterrender: function() {
                                 this.store.load();
                                 }}*/
                    }
                },
                {// 'group'
                    width: 150,
                    header: app.msg.group,
                    dataIndex: 'group',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.create('app.Store', {
                            fields: ['id', 'name'],
                            storeId: 'groups_list'
                        }),
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        editable: false
                    }
                }]
        },
        {
            hidden: true
        },
        {// 'enabled'
            width: 90,
            renderer: app.checked_render,
            editor: {
                xtype: 'checkbox',
                style: {
                    textAlign: 'center'
                }
            }
        },
        {// 'description'
            width: 300,
            editor: {
                xtype: 'textfield'
            }
        }

    ], viewConfig: {
        stripeRows: true
    }
});

