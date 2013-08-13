Ext.apply(Ext.form.VTypes, {
    password: function(val, field) {
        if (field.initialPassField) {
            var pwd = Ext.getCmp(field.initialPassField);
            return (val == pwd.getValue());
        }
        return true;
    },
    passwordCheckText: app.msg.warning_pwd
});

Ext.define('app.UpdatePassword', {
    extend: 'Ext.window.Window',
    //alias : 'widget.login',
    id: 'UpdatePassword',
    autoShow: true,
    width: 300,
    height: 200,
    layout: 'border',
    border: false,
    modal: true,
    closable: true, //убирает крестик, закрывающий окно
    resizable: false, // нельзя изменить размеры окна
    draggable: false, //перемещение объекта по экрану
  //closeAction: 'hide',
          
    initComponent: function() {
        this.items = [{
                id: 'update_password',
                title: app.msg.update_password, //получаем название титула окна
                region: 'center', //расположена форма по центру
                xtype: 'form',
                method: 'POST',
                bodyStyle: 'padding:10px; background: transparent;border-top: 0px none;',
                labelWidth: 100,
                defaultType: 'textfield',
                items: [/*{
                        fieldLabel: app.msg.login,
                        name: 'ExtensionChange',
                        id: 'ExtensionChange',
                        allowBlank: false
                    },*/ {
                        fieldLabel: app.msg.password,
                        name: 'pass',
                        inputType: 'password',
                        id: 'pass',
                        allowBlank: false,
                        listeners:
                                {
                                    specialkey: function(t, e) {
                                        var change_pass = Ext.getCmp('change_pass');
                                        if (e.getKey() == e.ENTER && !change_pass.disabled) {
                                            e.stopEvent();
                                            change_pass.handler();
                                        }
                                    }
                                }
                    },
                    {
                        fieldLabel: app.msg.new_password, //новый пароль
                        name: 'passwd',
                        inputType: 'password',
                        id: 'passwd',
                        allowBlank: false,
                        height: 20,
                        vtype: 'password',
                        regex: /^\d{3,10}$/
                    },
                    {
                        fieldLabel: app.msg.repeat_new_password, //повторить новый пароль
                        name: 'newpasswd',
                        inputType: 'password',
                        id: 'newpasswd',
                        allowBlank: false,
                        vtype: 'password',
                        initialPassField: 'passwd',
                        height: 20,
                        regex: /^\d{3,10}$/
                    },
                    {
                        name: 'action',
                        value: 'change_password',
                        hidden: true
                    }
                ]
            }
        ];

        this.buttons = [{
                id: 'change_pass',
                text: app.msg.save,
                handler: function() {
                    var update_password = Ext.getCmp('update_password');
                    if (update_password.getForm().isValid()) {
                        update_password.body.mask();
                        app.request(
                                update_password.getForm().getValues(),
                                function(result) {
                                    update_password.getForm().reset();
                                    Ext.getCmp('UpdatePassword').close();
                                    update_password.body.unmask();
                                }, function(result) {
                            update_password.body.unmask();
                        });
                    }
                }
            },
        {
                text: app.msg.cancel,
                scope: this,
                handler: this.close
            }];
        this.callParent(arguments);
    }
});