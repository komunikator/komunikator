Ext.define('app.UpdatePassword', {
    extend : 'Ext.window.Window',
   // alias : 'widget.login',
    id : 'UpdatePassword',
    autoShow : true,
    width : 300,
    height : 180,
    layout : 'border',
    border : false,
    modal : true,
    closable : true,        //убирает крестик, закрывающий окно
    resizable : false,       // нельзя изменить размеры окна
    draggable : false,       //перемещение объекта по экрану

    initComponent : function () {
        this.items = [/*{
            region : 'north',
            title: app.msg.auth_title
        //height : 52,
        //bodyCls : 'app_header'
        }, */{
            id : 'update_password',
            title: app.msg.update_password,	//получаем название титула окна
            region : 'center',          //расположена форма по центру
            xtype : 'form',
         //   url: 'data.php',
            method: 'POST',
            bodyStyle : 'padding:10px; background: transparent;border-top: 0px none;',
            labelWidth : 75,
            defaultType : 'textfield',
            items : [ {
                fieldLabel : app.msg.password,
                name : 'pass',
                inputType : 'pass',
                id : 'pass',
                allowBlank : false,
                height : 20
          
            }, {
                fieldLabel : app.msg.new_password,
                name : 'newpassword',//
                inputType : 'newpassword',//
                id : 'pwdn',//
                allowBlank : false,
                height : 20
            },
                    
                    {
                name : 'action',
                value: 'auth',
                hidden: true
            }, {
                name : 'time_offset',
                value:  new Date().getTimezoneOffset(),
                hidden: true
            }	
            ]
        }
        ];
                this.buttons = [{
            id : 'update_pass',
            text : app.msg.OK,
            handler: function(){
            
            }
        }];


        this.callParent(arguments);
    }
});