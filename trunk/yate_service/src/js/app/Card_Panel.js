Ext.define('app.Card_Panel', {
    extend : 'Ext.Panel',
    //autoScroll: true,
    //autoHeight: true,
    //scroll: 'vertical',
    defaults:{
        border:false,
        style:'padding:15px;'//,
	//autoHeight: true
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