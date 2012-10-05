Ext.define('DIGT.Card_Panel', {
    extend : 'Ext.Panel',
    defaults:{
        border:false,
        style:'padding:15px;'
    },
    bodyStyle:'padding:10px;',
    layout:'card',
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