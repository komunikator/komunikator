Ext.apply(Ext.form.field.VTypes, {
    mailVal: function(val, field) {
        if (/^(\w([-_+.']?\w+)+@(\w(-*\w+)+\.)+[a-zA-Z]{2,4}[,;])*\w([-_+.']?\w+)+@(\w(-*\w+)+\.)+[a-zA-Z]{2,4}$/.test(val))
        {
            return true;
        }
        return false;
    },
    mailValText: app.msg.invalid_email,
});

Ext.define('app.module.Mail_Settings_Panel', {
    extend : 'Ext.form.Panel',	
    url:'data.php?action=mail_settings',
    //autoLoad: true,
    style:'padding:40px;',
    autoScroll:true,
    frame: true,
    waitMsgTarget: true,

    fieldDefaults: {
        labelAlign: 'right',
        labelWidth: 260,
        msgTarget: 'side'
    },
    buttons: [{
        monitorValid: true,
        formBind:true, 
        text: app.msg.save,
        //action: 'save',
        handler:function() {
            var form = this.ownerCt.ownerCt.getForm();
            console.log(form.getValues());
            if (form.isValid()) {
                form.submit();
            }
        }			

    },
    {
        text: app.msg.load,
        handler:function() {
            //console.log('test');
            var form = this.ownerCt.ownerCt.getForm();            
                form.load();
        }			

    }],

    initComponent : function () {
         
        this.items = [
        {
            xtype: 'fieldset',
            title: app.msg.mailevents,
            defaultType: 'checkboxfield',
            defaults: {
                    inputValue: 'true',
                    uncheckedValue: 'false'
            },
            items: [
                {
                    boxLabel  : app.msg.mailevent_incoming_gate,
                    name      : 'incoming_trunk'
                }, {
                    boxLabel  : app.msg.mailevent_incoming,
                    name      : 'incoming_call'
                }, {
                    boxLabel  : app.msg.mailevent_outgoing,
                    name      : 'outgoing_call'
                }, {
                    boxLabel  : app.msg.mailevent_internal,
                    name      : 'internal_call'
                }
            ]
        },{
            xtype: 'fieldset',
            border: true,
            title: app.msg.mail_nofications,
            items: [
            {
                xtype: 'textfield',
                anchor: '100%',                
                name : 'from',
                fieldLabel: app.msg.from,
                vtype: 'email'
            },
            {
                xtype: 'textfield',
                anchor: '100%',                
                name : 'fromname',
                fieldLabel: app.msg.fromname,
            },            
            {
                xtype: 'textfield',
                anchor: '100%',                
                name : 'email',
                fieldLabel: app.msg.email,
                vtype: 'mailVal',
                emptyText: app.msg.example_email
            },
            {
                xtype: 'textareafield',
                anchor: '100%',
                name : 'incoming_call_text',
                fieldLabel: app.msg.mail1                
            },
            {
                xtype: 'textareafield',
                anchor: '100%',
                name : 'incoming_trunk_text',
                fieldLabel: app.msg.mail2
            },
            {
                xtype: 'fieldset',
                border: true,
                title: app.msg.mail_subject,
                items: [
                {
                    xtype: 'textfield',
                    anchor: '100%',
                    name:  'incoming_subject',
                    fieldLabel: app.msg.mail_incoming_subject
                },            
                {
                    xtype: 'textfield',
                    anchor: '100%',
                    name:  'outgoing_subject_call_not_accepted',
                    fieldLabel: app.msg.mail_outgoing_subject_call_not_accepted
                },            
                {
                    xtype: 'textfield',
                    anchor: '100%',
                    name:  'outgoing_subject_fax_not_accepted',
                    fieldLabel: app.msg.mail_outgoing_subject_fax_not_accepted
                },            
                {
                    xtype: 'textfield',
                    anchor: '100%',
                    name:  'ioutgoing_subject_call_accepted',
                    fieldLabel: app.msg.mail_outgoing_subject_call_accepted
                },
                {
                    xtype: 'textfield',
                    anchor: '100%',
                    name:  'outgoing_subject_fax_accepted',
                    fieldLabel: app.msg.mail_outgoing_subject_fax_accepted
                }
                ]
            }
        ]
        }];
        this.callParent(arguments);
        var form = this.getForm();
        form.load();
    }
});
