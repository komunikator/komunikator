Ext.define('app.module.Update_Panel', {
    extend : 'Ext.Panel',
    defaults:{
        border:false,
        style:'padding:15px;'
    },
    bodyStyle:'padding:10px;',
    items  : [
    {
        width: 240,
        xtype: 'button',
        text:  app.msg.checkforupdates,
        handler: function(){
        }
    }
    ]
});