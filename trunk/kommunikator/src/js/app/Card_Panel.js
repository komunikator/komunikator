Ext.define('app.Card_Panel', {
    extend : 'Ext.Panel',
    frame:true,
    //autoScroll: true,
    //autoHeight: true,
    //scroll: 'vertical',
    defaults:{
        border:false,
        style:'padding:0px;'//,
    //autoHeight: true
    },
    bodyStyle:'padding:5px 2px;',
    layout:'card',
    initComponent : function () {
        this.tbar = [];
        for (var item in this.items) {
            this.tbar.push({
                //cls:'x-btn-default-small',
                handler: this.items[item].handler?this.items[item].handler:eval ('(function(){return function(c){c.ownerCt.ownerCt.getLayout().setActiveItem('+item+')} })()'),
                text: this.items[item].text?this.items[item].text:this.items[item].title
            });
        }; /*
	this.bbar = [
		{ width: '100%', 
		  border:false,
		  bodyStyle:'padding:10px;', 
		  style : {textAlign : 'center', padding:'0px'}, 
		  xtype : 'panel', 
		  html:'test<br>test<br>test<br>test<br>test<br>test<br>test<br>test<br>test<br>',
		}]; 	
	*/
        //this.items = [];
        //this.items.push({html:'test'}); 

        this.callParent(arguments);
    }    
});