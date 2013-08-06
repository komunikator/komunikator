

Ext.define('app.module.Forwarding_Panel', {
    extend : 'Ext.form.Panel',	
    url:'data.php?action=get_user_forwarding',
    //autoLoad: true,
    id:'FarwardingPanel',
    style:'padding:40px;',
    frame: true,
    waitMsgTarget: true,
width:400,
    height: 300,
    //  closeAction: 'hide',
  


    initComponent : function () {
         
        this.items = [
      {
          id: 'update_forward',
            xtype: 'fieldset',
            border: true,
            //disabled: true,
            items: [{ 
                       // url: 'data.php?action=get_user_forwarding',
                                xtype: 'textfield',
                                fieldLabel: app.msg.always,
                                name: 'change_forward',
                                id: 'forward',
                                height: 20,
                                 //  value:'лаборант',
                              /*         listeners:
                                {
                                    specialkey: function(t, e) {
                                        var change_pass = Ext.getCmp('change_forwarddd');
                                        if (e.getKey() == e.ENTER && !change_pass.disabled) {
                                            e.stopEvent();
                                            change_pass.handler();
                                        }
                                    }
                                }*/
                            }, {
                                xtype: 'textfield',
                                fieldLabel: app.msg.forward_busy,
                                name: 'change_forward_busy',
                                id: 'forward_busy',
                                height: 20,
                             //  value:'forward_busy',
                            }, {
                                xtype: 'textfield',
                                fieldLabel: app.msg.forward_noanswer,
                                name: 'change_forward_noanswer',
                                id: 'forward_noanswer',
                                height: 20,
                            }, {
                                xtype: 'textfield',
                                fieldLabel: app.msg.noanswer_timeout,
                                name: 'change_noanswer_timeout',
                                id: 'noanswer_timeout',
                                height: 20,
                                editor: {
                                    xtype: 'textfield',
                                    regex: /^\d{1,3}$/
                                }
                            }]
        }];
  
       
        this.callParent(arguments);
        var form = this.getForm();
        form.load();
    }
});
