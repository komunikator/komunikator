Ext.define('app.module.Dial_plans_Grid', {
    extend: 'app.Grid',
    store_cfg:
            {
                fields: ['id', 'dial_plan', 'priority', 'prefix', 'gateway', 'nr_of_digits_to_cut', 'position_to_start_cutting', 'nr_of_digits_to_replace', 'digits_to_replace_with', 'position_to_start_replacing', 'position_to_start_adding', 'digits_to_add'],
                storeId: 'dial_plans'
            },
    viewConfig: {
        loadMask: false
    },
    columns: [
        {
            hidden: true
        },
        {
            width: 120,
            editor: {
                xtype: 'textfield',
            //   regex: /^[a-zA-Zа-яА-Я0-9_-]{1,16}$/,
                allowBlank: false
            }
        },
        {
            editor: {
                xtype: 'textfield',
                regex: /^\d{1,2}$/,
                allowBlank: false
            }
        },
        {
            editor: {
                xtype: 'textfield',
                regex: /^\+?\d{1,10}$/,
                allowBlank: false
            }
        },
        {
            text: app.gateway,
            editor: {
                xtype: 'combobox',
                store: Ext.StoreMgr.lookup('gateways') ?
                        Ext.StoreMgr.lookup('gateways') :
                        Ext.create('app.Store', {
                    autorefresh: false,
                    fields: ['id', 'status', 'enabled', 'gateway', 'server', 'username', 'password', 'description', 'protocol', 'ip_transport', 'authname', 'domain', 'callerid'],
                    storeId: 'gateways'
                }),
                //queryCaching: false,
                //triggerAction: 'query',
                displayField: 'gateway',
                valueField: 'gateway',
                allowBlank: false,
                queryMode: 'local',
                listeners: {
                    afterrender: function() {
                        this.store.load();
                    }
                }
            }

        },
        //'nr_og_digits_to_cut','position_to_start_cutting','nr_of_digits_to_replace','digits_to_replace_with','position_to_start_replacing','position_to_start_adding','digits_to_add'
        {
            text: '- N',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '- START',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '<> N',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '<> START',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '<>',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '+ START',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        },
        {
            text: '+',
            editor: {
                xtype: 'textfield',
                regex: /^(\d+|)$/
            },
            width: 90
        }
    ],
    initComponent: function() {
        this.callParent(arguments);
    }
})
