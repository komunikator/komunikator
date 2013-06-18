Ext.define('app.Call_Forwarding', {
    extend: 'Ext.window.Window',
    // alias : 'widget.login',
    id: 'CallForwarding',
    autoShow: true,
    width: 330,
    height: 220,
    layout: 'border',
    border: false,
    modal: true,
    closable: true, //убирает крестик, закрывающий окно
    resizable: false, // нельзя изменить размеры окна
    draggable: false, //перемещение объекта по экрану

    initComponent: function() {
        this.items = [{
                id: 'call_forwarding',
                title: app.msg.forward, //получаем название титула окна
                region: 'center', //расположена форма по центру
                xtype: 'form',
                //   url: 'data.php',
                method: 'POST',
                bodyStyle: 'padding:10px; background: transparent;border-top: 0px none;',
                labelWidth: 75,
                defaultType: 'textfield',
                items: [
                    {
                        fieldLabel: app.msg.always,
                        name: 'forward',
                        id: 'forward',
                        height: 20
                    },
                    {
                        fieldLabel: app.msg.forward_busy,
                        name: 'forward_busy',
                        id: 'forward_busy',
                        height: 20
                    }, {
                        fieldLabel: app.msg.forward_noanswer,
                        name: 'forward_noanswer',
                        id: 'forward_noanswer',
                        height: 20
                    },
                    {
                        fieldLabel: app.msg.noanswer_timeout,
                        name: 'noanswer_timeout',
                        id: 'noanswer_timeout',
                        height: 20,
                        editor: {
                            xtype: 'textfield',
                            regex: /^\d{1,3}$/
                        }
                    }
                ]
            }
        ];
        this.buttons = [{
                id: 'update_pass',
                text: app.msg.OK,
                handler: function() {

                }
            }];


        this.callParent(arguments);
    }
});