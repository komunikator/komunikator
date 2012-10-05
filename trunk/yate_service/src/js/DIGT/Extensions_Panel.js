Ext.define('DIGT.Extensions_Panel', {
    extend : 'Ext.Panel',
    title: DIGT.msg.extensions,
    defaults:{
	border:false,
        style:'padding:15px;'
    },
    bodyStyle:'padding:10px;',
    layout:'card',
    items: [
        {
            title:DIGT.msg.groups,
            items:Ext.create('DIGT.Groups_Grid')
        }, 
        {
            title:DIGT.msg.extensions, 
            items:Ext.create('DIGT.Extensions_Grid')
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