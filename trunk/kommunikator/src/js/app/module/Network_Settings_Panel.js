Ext.apply(Ext.form.field.VTypes, {
    ipVal: function(val, field) {
        if (/^([1-9][0-9]{0,1}|1[013-9][0-9]|12[0-689]|2[01][0-9]|22[0-3])([.]([1-9]{0,1}[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])){2}[.]([1-9][0-9]{0,1}|1[0-9]{2}|2[0-4][0-9]|25[0-4])$/.test(val))
        {
            return true;
        }
        return false;
    },
    ipValText: app.msg.invalid_ip_address,
    ipMask: function(val, field) {
        if (/^(128|192|224|24[08]|25[245].0.0.0)|(255.(0|128|192|224|24[08]|25[245]).0.0)|(255.255.(0|128|192|224|24[08]|25[245]).0)|(255.255.255.(0|128|192|224|24[08]|252))$/.test(val))
        {
            return true;
        }
        return false;
    },
    ipMaskText: app.msg.invalid_netmask
});

Ext.define('app.module.Network_Settings_Panel', {
    extend : 'Ext.form.Panel',	
    url:'data.php?action=net_settings',
    //autoLoad: true,
    style:'padding:40px;',
    frame: true,
    waitMsgTarget: true,

    fieldDefaults: {
        labelAlign: 'right',
        labelWidth: 100,
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
            var form = this.ownerCt.ownerCt.getForm();
                form.load();
        }			

    }],

    initComponent : function () {
         
        this.items = [
        {
            xtype: 'radiogroup',
            columns: 1,
            listeners:{
                change:	function(field, newVal, oldVal) {
                    if (field.getValue().type) {
                    //field.enable();
                    } else {
                //field.ownerCt.getComponent('type').disable();
                }
                }

            },
            items: [
            {
                boxLabel: app.msg.auto_dhcp, 
                name:'type', 
                inputValue: 0, 
                checked: true
            },

            {
                boxLabel: app.msg.static_ip,
                itemId:'type', 
                name:'type', 
                inputValue:1
            }
            ]
        },{
            xtype: 'fieldset',
            border: true,
            //disabled: true,
            items: [
            {
                xtype: 'textfield',
                name:'dev',
                hidden:true
            },

            {
                xtype: 'textfield',
                name : 'ipaddress',
                fieldLabel: app.msg.ip_address,
                vtype: 'ipVal'
            },
            {
                xtype: 'textfield',
                name : 'ipmask',
                fieldLabel: app.msg.netmask,
                vtype: 'ipMask'
            },
            {
                xtype: 'textfield',
                name : 'ipgateway',
                fieldLabel: app.msg.gateway,
                vtype: 'ipVal',
                allowBlank: true
            }]
        }];
        this.callParent(arguments);
        var form = this.getForm();
        form.load();
    }
})
