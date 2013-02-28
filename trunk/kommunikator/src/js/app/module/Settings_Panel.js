Ext.define('app.module.Settings_Panel', {
    extend : 'Ext.form.Panel',	
    url:'data.php',
    // autoLoad: true,
    frame: true,
    waitMsgTarget: true,

    fieldDefaults: {
        labelAlign: 'right',
        labelWidth: 85,
        msgTarget: 'side'
    },  
    items: [{
        xtype: 'fieldset',
        items: [{
            xtype: 'textfield',
            fieldLabel: 'ID',
            name: 'username'
        }, {
            xtype: 'textfield',
            fieldLabel: 'CODE',
            name: 'password'
        }, {
            xtype: 'textfield',
            fieldLabel: 'COUNTRY',
            name: 'lastLogin'
        }]
    }],   
    initComponent : function () {
        this.callParent(arguments); 
    }
})
