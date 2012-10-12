Ext.define('app.Extensions_Panel', {
    extend : 'Ext.Panel',
    title: app.msg.extensions,
    defaults:{
	border:false,
        style:'padding:15px;'
    },
    bodyStyle:'padding:10px;',
    layout:'card',
    items: [
        {
            title:app.msg.groups,
            items:Ext.create('app.Groups_Grid')
        }, 
        {
            title:app.msg.extensions, 
            items:Ext.create('app.Extensions_Grid')
        }
    ],
    initComponent : function () {
	this.tbar = [];
        for (var item in this.items) {
            this.tbar.push({
                handler: eval ('(function(){return function(c){c.ownerCt.ownerCt.getLayout().setActiveItem('+item+')} })()'),
                text: this.items[item].title
            });
        };
        this.callParent(arguments);
    }    
});