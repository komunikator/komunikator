Ext.define('app.LoginWindow', {
    extend: 'Ext.window.Window',
    alias: 'widget.login',
    id: 'loginWindow',
    autoShow: true,
    width: 300,
    height: 140,
    layout: 'border',
    border: false,
    modal: true,
    closable: false, //убирает крестик, закрывающий окно
    resizable: false, // нельзя изменить размеры окна
    draggable: false, //перемещение объекта по экрану

    initComponent: function() {
        this.items = [/*{
         region : 'north',
         title: app.msg.auth_title
         //height : 52,
         //bodyCls : 'app_header'
         }, */{
                id: 'login_form',
                title: app.msg.auth_title, //получаем название титула окна
                region: 'center', //расположена форма по центру
                xtype: 'form',
                url: 'data.php',
                method: 'POST',
                bodyStyle: 'padding:10px; background: transparent;border-top: 0px none;',
                labelWidth: 75,
                defaultType: 'textfield',
                items: [{
                        fieldLabel: app.msg.login,
                        name: 'user',
                        id: 'usr',
                        allowBlank: false
                    }, {
                        fieldLabel: app.msg.password,
                        name: 'password',
                        inputType: 'password',
                        id: 'pwd',
                        allowBlank: false,
                        listeners:
                                {
                                    specialkey: function(t, e) {
                                        var login_button = Ext.getCmp('login_button');
                                        if (e.getKey() == e.ENTER && !login_button.disabled) {
                                            e.stopEvent();
                                            login_button.handler();
                                        }
                                    }
                                }
                    }, {
                        name: 'action',
                        value: 'auth',
                        hidden: true
                    }, {
                        name: 'time_offset',
                        value: new Date().getTimezoneOffset(),
                        hidden: true
                    }
                ]
            }
        ];

        this.buttons = [{
                id: 'login_button',
                text: app.msg.OK,
                handler: function() {
                    var login_form = Ext.getCmp('login_form');
                    if (login_form.getForm().isValid()) {
                        login_form.body.mask();
                        app.request(
                                login_form.getForm().getValues(),
                                function(result) {

                                    login_form.getForm().reset();
                                    Ext.getCmp('loginWindow').hide();
                                    if (result['user'])
                                        app.main(result['user'], null);
                                    if (result['extension'])
                                        app.main(null, result['extension']);
                                    //app.View;

                                    login_form.body.unmask();
                                }, function(result) {

                            login_form.body.unmask();
                        });
                    }
                }
            }];
        this.callParent(arguments);
    }
});