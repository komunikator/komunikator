/*
app.cardNav = function(me,incr){
    var l = me.ownerCt.getLayout();
    var i = l.activeItem.id.split('card-')[1];
    var next = parseInt(i, 10) + incr;
    l.setActiveItem(next);
    me.setDisabled(next===0);
    me.setDisabled(next===2);
};
   */
Ext.define('app.module.Attendant_Panel', {
    extend : 'Ext.Panel',
    defaults:{
        style:'padding:15px;'
    },
    bodyStyle:'padding:10px;',
    title: app.msg.attendant,
    layout:'card',
    //layout: 'anchor', 
    items: [
    {
        items: 
        Ext.create('app.Prompts_Panel'/*,{title:'Prompts'}*/),
        html:app.msg.first_step?app.msg.first_step:'',
        //css:'',
        style:'padding:45px;',
        bodyStyle:'padding:10px;'
    },
    Ext.create('app.Keys_Grid',{
        title:app.msg.key?app.msg.key:'Key'
    })/*,
    Ext.create('app.Time_Frames_Grid',{
        title:'Time_Frames'
    })  */
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
        this.tbar = [/*'->',*/ {
            handler: function() {
                this.ownerCt.ownerCt.tb_nav(this,-1)
            },
            text: app.msg.previous?app.msg.previous:'Previous'
        },{
            handler: function() {
                this.ownerCt.ownerCt.tb_nav(this,1)
            },
            text: app.msg.next?app.msg.next:'Next'
        }];

        this.callParent(arguments);
    }    
});