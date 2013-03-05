Ext.apply(Ext.form.field.VTypes, {
    mailVal: function(val, field) {
        if (/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/.test(val))
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

    }/*,
    {
        text: app.msg.load,
        handler:function() {
            var form = this.ownerCt.ownerCt.getForm();
                form.load();
        }			

    }*/],

    initComponent : function () {
         
        this.items = [
        {
            xtype: 'fieldset',
            title: app.msg.mailevents,
            defaultType: 'checkboxfield',
            items: [
                {
                    boxLabel  : app.msg.mailevent_incoming_gate,
                    name      : 'mailevent',
                    inputValue: '1',
                    id        : 'mailevent1'
                }, {
                    boxLabel  : app.msg.mailevent_incoming,
                    name      : 'mailevent',
                    inputValue: '2',
                    id        : 'mailevent2'
                }, {
                    boxLabel  : app.msg.mailevent_outgoing,
                    name      : 'mailevent',
                    inputValue: '3',
                    id        : 'mailevent3'
                }, {
                    boxLabel  : app.msg.mailevent_internal,
                    name      : 'mailevent',
                    inputValue: '4',
                    id        : 'mailevent4'
                }
            ]
        },{
            xtype: 'fieldset',
            border: true,
            items: [
            {
                xtype: 'textfield',
                name : 'email',
                fieldLabel: app.msg.email,
                vtype: 'mailVal'
            },
            {
                xtype: 'textareafield',
                anchor: '100%',
                name : 'mail1',
                fieldLabel: app.msg.mail1                
            },
            {
                xtype: 'textareafield',
                anchor: '100%',
                name : 'mail2',
                fieldLabel: app.msg.mail2
            }]
        }];
        this.callParent(arguments);
        var form = this.getForm();
        form.load();
    }
})
