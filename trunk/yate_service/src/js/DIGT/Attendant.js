/*
DIGT.cardNav = function(me,incr){
    var l = me.ownerCt.getLayout();
    var i = l.activeItem.id.split('card-')[1];
    var next = parseInt(i, 10) + incr;
    l.setActiveItem(next);
    me.setDisabled(next===0);
    me.setDisabled(next===2);
};
   */
Ext.define('DIGT.Attendant', {
    extend : 'Ext.Panel',
    defaults:{style:'padding:15px;'},
    bodyStyle:'padding:10px;',
    title: DIGT.msg.attendant,
    layout:'card',
    //layout: 'anchor', 
    items: [
    Ext.create('DIGT.Prompts_Form'/*,{title:'Prompts'}*/),
    Ext.create('DIGT.Prompts_Grid',{
        title:'Prompts'
    }),
    Ext.create('DIGT.Keys_Grid',{
        title:'Key'
    }),
    Ext.create('DIGT.Time_Frames_Grid',{
        title:'Time_Frames'
    })
    ],
    initComponent : function () {
        this.tb_nav = function(me,inc){ 
            var wiz = me.ownerCt.ownerCt; 
            var activeItem = wiz.getLayout().activeItem;
            var activeIndex = wiz.items.indexOf(activeItem);
            var nextIndex = activeIndex+inc;
            if (nextIndex < 0 || nextIndex > wiz.items.getCount()-1) return;
            wiz.getLayout().setActiveItem(nextIndex);
	    //me.setDisabled(nextIndex===0 && inc==-1);
    	    //me.setDisabled(nextIndex===wiz.items.getCount()-1 && inc==1);
        };	
        //this.activeItem = 0;
        this.bbar = ['->', {
            handler: function() {this.ownerCt.ownerCt.tb_nav(this,-1)},
            text: '&laquo; Previous'
        },{
            handler: function() {this.ownerCt.ownerCt.tb_nav(this,1)},
            text: 'Next &raquo;'
        }];

        this.callParent(arguments);
    }    
});