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
Ext.define('DIGT.Attendant_Panel', {
    extend : 'Ext.Panel',
    defaults:{
        style:'padding:15px;'
    },
    bodyStyle:'padding:10px;',
    title: DIGT.msg.attendant,
    layout:'card',
    //layout: 'anchor', 
    items: [/* 
    {
        xtype: 'combobox',
        fieldLabel: 'Label',
        enableKeyEvents: true,
        displayField: 'field1',
        forceSelection: true,
	value: 222,
        queryMode: 'local',
        store: [[11,11],[12,12],[22,22],[222,222],[333,333],[3333,3333],[33331,33331],[555,555]],
        valueField: 'field1',
	listeners:{
	keyup:function(comboField,e){alert (e.tester);
	    delete comboField.lastQuery;
	    comboField.store.loadData([[1,2],[3,4]]);
	    console.log(comboField.getRawValue());
            console.log(comboField.store.getCount());
     }} 
    }, */{items: 
    Ext.create('DIGT.Prompts_Panel'/*,{title:'Prompts'}*/),
    html:DIGT.msg.first_step?DIGT.msg.first_step:'',
    css:'',
    style:'padding:45px;',
    bodyStyle:'padding:10px;'
	},
    Ext.create('DIGT.Keys_Grid',{
        title:DIGT.msg.key?DIGT.msg.key:'Key'
    })/*,
    Ext.create('DIGT.Time_Frames_Grid',{
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
            text: DIGT.msg.previous?DIGT.msg.previous:'Previous'
        },{
            handler: function() {
                this.ownerCt.ownerCt.tb_nav(this,1)
                },
            text: DIGT.msg.next?DIGT.msg.next:'Next'
        }];

        this.callParent(arguments);
    }    
});