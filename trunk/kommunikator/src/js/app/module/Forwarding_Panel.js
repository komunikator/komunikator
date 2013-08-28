Ext.define('app.module.Forwarding_Panel', {
    extend: 'Ext.form.Panel',
    url: 'data.php?action=get_forwarding',
    id: 'ForwardingPanel',
    style: 'padding:40px;',
    frame: true,
    waitMsgTarget: true,
    width: 400,
    height: 600,
   
    initComponent: function() {

        this.items = [{
                id: 'change_forward',
                xtype: 'fieldset',
                border: true,
                items: [
                    { id: 'id',
                        xtype: 'textfield',
                        name: 'id',
                        hidden: true
                    },
                    {
                        fieldLabel: app.msg.always,
                        name: 'forward',
                        id: 'forward',
                        height: 20,
                        xtype: 'combobox',
                        mode: 'local',
                        editable: true,
                        triggerAction: 'all',
                        regex: new RegExp('(^\\d{1,11}$)|(^' + app.msg.voicemail + '$)'),
                        store: [
                            ['vm', app.msg.voicemail],
                        ],
                        listeners:
                                {
                                    specialkey: function(t, e) {
                                        var change_forw = Ext.getCmp('change_forw');
                                        if (e.getKey() == e.ENTER && !change_forw.disabled) {
                                            e.stopEvent();
                                            change_forw.handler();
                                        }
                                    }
                                }
                    }, {
                        fieldLabel: app.msg.forward_busy,
                        name: 'forward_busy',
                        id: 'forward_busy',
                        height: 20,
                        xtype: 'combobox',
                        mode: 'local',
                        editable: true,
                        triggerAction: 'all',
                        regex: new RegExp('(^\\d{1,11}$)|(^' + app.msg.voicemail + '$)'),
                        store: [
                            ['vm', app.msg.voicemail],
                        ]
                    }, 
                            {
                        fieldLabel: app.msg.forward_noanswer,
                        name: 'forward_noanswer',
                        id: 'forward_noanswer',
                        height: 20,
                        xtype: 'combobox',
                        mode: 'local',
                        editable: true,
                        triggerAction: 'all',
                        regex: new RegExp('(^\\d{1,11}$)|(^' + app.msg.voicemail + '$)'),
                        store: [
                            ['vm', app.msg.voicemail],
                        ]
                    }, 
                            {
                        xtype: 'textfield',
                        fieldLabel: app.msg.noanswer_timeout,
                        name: 'noanswer_timeout',
                        id: 'noanswer_timeout',
                        height: 20,
                        editor: {
                            xtype: 'numberfield',
                            minValue: 1
                        }
                    },
                    {
                        xtype: 'textfield',
                        name: 'action',
                        value: 'update_extensions',
                        hidden: true
                    }]
            }];


        this.callParent(arguments);
        var form = this.getForm();
        form.load();
    }
});
