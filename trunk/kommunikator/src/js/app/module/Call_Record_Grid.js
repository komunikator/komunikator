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
        fields: ['id', 'caller_number', 'caller_group', 'type', 'gateway', 'called_number', 'called_group', 'enabled', 'description'],
        storeId: 'call_records'
    },
    columnLines: true,
    columns: [
        {// 'id'
            hidden: true
        },
        {
            //  'caller'
            text: app.msg.caller,
            columns: [{width: 150,
                    text: app.msg.number,
                    dataIndex: 'caller_number',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.create('app.Store', {
                            fields: ['id', 'name'],
                            storeId: 'extensions_list'
                        }),
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'name'
                    }
                },
                {// 'group'
                    width: 150,
                    header: app.msg.group,
                    dataIndex: 'caller_group',
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
        {// 'type'
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
                    dataIndex: 'called_number',
                    editor: {
                        xtype: 'combobox',
                        store: Ext.create('app.Store', {
                            fields: ['id', 'name'],
                            storeId: 'extensions_list'
                        }),
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'name'
                    }
                },
                {// 'group'
                    width: 150,
                    header: app.msg.group,
                    dataIndex: 'called_group',
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

