Ext.define('DIGT.LoginWindow', {
    extend : 'Ext.window.Window',
    alias : 'widget.login',
    id : 'loginWindow',
    autoShow : true,
    width : 300,
    height : 140,
    layout : 'border',
    border : false,
    modal : true,
    closable : false,
    resizable : false,
    draggable : false,

    initComponent : function () {
        this.items = [/*{
            region : 'north',
            title: DIGT.msg.auth_title
        //height : 52,
        //bodyCls : 'app_header'
        }, */{
            id : 'login_form',
	    title: DIGT.msg.auth_title,	
            region : 'center',
            xtype : 'form',
            url: 'data.php',
            method: 'POST',
            bodyStyle : 'padding:10px; background: transparent;border-top: 0px none;',
            labelWidth : 75,
            defaultType : 'textfield',
            items : [{
                fieldLabel : DIGT.msg.login,
                name : 'user',
                id : 'usr',
                allowBlank : false
            }, {
                fieldLabel : DIGT.msg.password,
                name : 'password',
                inputType : 'password',
                id : 'pwd',
                allowBlank : false,
                listeners:
                {
                    specialkey:function(t, e){
                        var login_button = Ext.getCmp('login_button');
                        if(e.getKey() == e.ENTER && !login_button.disabled){
                            e.stopEvent();
                            login_button.handler();
                        }
                    }
                }
            }, {
                name : 'action',
                value: 'auth',
                hidden: true
            }

            ]
        }
        ];

        this.buttons = [{
            id : 'login_button',
            text : DIGT.msg.OK,
            handler: function(){
                var login_form = Ext.getCmp('login_form');
                if (login_form.getForm().isValid()){
                    login_form.body.mask();
                    DIGT.request(
                        login_form.getForm().getValues(),
                        function(result){
                            login_form.getForm().reset();
                            Ext.getCmp('loginWindow').hide();
			    DIGT.main(result['user']);
		            //Ext.getCmp('Digt.container').onShowFn(result['user']);
                            login_form.body.unmask();
                        },function(result){login_form.body.unmask();});
                }
            }
        }];
        this.callParent(arguments);
    }
});