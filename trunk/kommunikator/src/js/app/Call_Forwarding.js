Ext.define('app.Call_Forwarding', {
    extend: 'Ext.window.Window',
    
    // alias : 'widget.login',
    id: 'CallForwarding',
    //  autoShow: true,
    width: 400,
    height: 300,
    layout: 'fit',
   // border: false,
    modal: false,
    closable: true, //убирает крестик, закрывающий окно
    resizable: false, // нельзя изменить размеры окна
    draggable: true, //перемещение объекта по экрану
    storeId: 'user_forwarding',
  //  closeAction: 'hide',
 
              
                title: app.msg.forward, //получаем название титула окна
                 
               
  initComponent : function () {
       this.items= [ Ext.create('app.module.Farwarding_Panel', {
            
            })
            ],
         /* buttons : [{
                id: 'close_forward',
                text: app.msg.close,
                handler: function() { Ext.getCmp('CallForwarding').close(); }
                                   
                               
                    }]*/
      this.buttons = [
           {  text: app.msg.save,
             /* handler  : function() {
                    var fn = function(btn) {
                      
                            app.request(
                            {
                                action : 'change_forwardd'
                            },
                            function(result) {
                                if (!result.message)
                                    box.hide();
                            // console.log(result)
                            });
                        
                    };
                    Ext.MessageBox.show({
                        title    : app.msg.performing_actions,  
                        msg      : app.msg.change_redirect,  
                        buttons  : Ext.MessageBox.YESNOCANCEL,
                        fn       : fn,
                       // animEl   : 'mb4',
                        icon     : Ext.MessageBox.QUESTION
                    });

                }*/
            
            
             },
            
            {
                text: app.msg.cancel,
                scope: this,
                handler: this.close
            }
        ];
    this.callParent(arguments);             
  }  
      
});