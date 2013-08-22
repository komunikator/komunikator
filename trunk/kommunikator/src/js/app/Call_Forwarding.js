Ext.define('app.Call_Forwarding', {
    extend: 'Ext.window.Window',
    id: 'CallForwarding',
    width: 400,
    height: 300,
    layout: 'fit',
    border: false,
    modal: true, //блокирует всё, что на заднем фоне
    closable: true, //убирает крестик, закрывающий окно
    resizable: false, // нельзя изменить размеры окна
    draggable: false, //перемещение объекта по экрану
    title: app.msg.forward, //получаем название титула окна

    initComponent: function() {
        this.items = [Ext.create('app.module.Forwarding_Panel', {
            })
        ],
                this.buttons = [
            {
                id: 'change_forw',
                text: app.msg.save,
                handler: function() {
                    var change_forward = Ext.getCmp('ForwardingPanel');
                    if (change_forward.getForm().isValid()) {
                        change_forward.body.mask();
                        app.request(
                                change_forward.getForm().getValues(),
                                function(result) {
                                    Ext.getCmp('CallForwarding').close();
                                }, function(result) {
                            change_forward.body.unmask();
                        }, true);
                    }
                }
            },
            {
                text: app.msg.cancel,
                scope: this,
                handler: this.close
            }
        ];
        this.callParent(arguments);
    }
}
);