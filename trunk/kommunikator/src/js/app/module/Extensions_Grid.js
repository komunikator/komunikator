Ext.define('app.module.Extensions_Grid', {
    extend : 'app.Grid',
    store_cfg: {
        //groupField: 'group_name',
        autorefresh : false,
        loadmask: true,
        fields : ['id','status', 'extension', 'password', 'firstname','lastname','address','group_name','forward','forward_busy','forward_noanswer','noanswer_timeout'],
        storeId :'extensions',
        sorters : [{
            direction: 'DESC',
            property: 'group_name'	
        }]
    },
    advanced :['forward'],//'forward_busy','forward_noanswer','noanswer_timeout'],	
    not_create_column: true,
    columns : [
    {
        hidden: true
    },
    {
        width: 70
    },
    { 
        width: 130,
        editor :  {
            xtype: 'textfield',
            regex: /^\d{3}$/
            ,
            allowBlank: false 
        }
    },

    {
        sortable: false,
        groupable: false, 
        renderer : function(){
            return '***';
        },
        editor :  {
            //		msgTarget:'side',
            xtype: 'textfield',
            inputType: 'password',
            regex: /^\d{3,10}$/
            ,
            allowBlank: false 
        }
    },

    {
        editor :  {
            xtype: 'textfield'
        }
    },

    { 
        editor :  {
            xtype: 'textfield'
        }
    },
    { 
        width: 130,
        editor :  {
            vtype: 'email',
            xtype: 'textfield'
        }
    },

    { 
        text: app.msg['group'],
        editor :  {
            xtype: 'combobox',
            store: Ext.StoreMgr.lookup('groups')?
            Ext.StoreMgr.lookup('groups'):
            Ext.create('app.Store',{
                fields : ['id', 'group', 'description', 'extension'],   
                storeId : 'groups'
            }),
            //queryCaching: false,
            //triggerAction: 'query',
            displayField: 'group',
            valueField: 'group'//,
        //queryMode: 'remove'
        }

    },
    {
        header: app.msg.forward,
        dataIndex: 'forward',
        headers:[
        {
            header: app.msg.number,
            dataIndex: 'forward',
            width: 120
        },
        {
            header: app.msg.forward_busy,
            dataIndex: 'forward_busy',
            width: 120
        },
        {
            header: app.msg.forward_noanswer,
            dataIndex: 'forward_noanswer',
            width: 120
        },
        {
            header: app.msg.noanswer_timeout,
            dataIndex: 'noanswer_timeout',
            width: 120,
            editor :  {
                xtype: 'textfield',
                regex: /^\d{1,3}$/
            }
        }
        ]
    }
    ],
    requires: 'Ext.ux.grid.FiltersFeature',
    features : [{
        ftype:'grouping'/*,            
		groupByText    : '???????????? ?? ????? ????',
            	showGroupsText : '?????????? ?? ???????'*/
    },{ 
        ftype : 'filters' ,
        //autoReload: true,//false,//true,  //don't reload automatically
        local: false, //only filter locally
        encode : true,
        filters:
        [{
            encode: 'encode',
            local: true,
            type: 'list',
            local: true,
            options: [['online',app.msg['registered']],['offline',app.msg['unregistered']]],
            dataIndex: 'status'
        }, {
            type: 'string',
            dataIndex: 'extension'
        },{
            type: 'string',
            dataIndex: 'firstname'
        },{
            type: 'string',
            dataIndex: 'lastname'
        },{
            type: 'string',
            dataIndex: 'address'
        },{
            type: 'string',
            dataIndex: 'group_name'/*,
            encode: 'encode',
            //local: true,
            type: 'list',
            local: true,
            store: Ext.StoreMgr.lookup('groups'),
	    labelField: 'group',
	    valueField: 'group' -- not work, need 'id'
	+*/
        }]
    /*
	    type: 'boolean',
            //type: 'string',
	 	yesText: app.msg.online,     // default
        	noText:  app.msg.online,        // default
            dataIndex: 'status'

*/
    } ], 
    columns_renderer : app.online_offline_renderer, 
    initComponent : function () {
        app.Loader.load(['js/ux/grid/css/GridFilters.css','js/ux/grid/css/RangeMenu.css']);
        this.listeners.beforerender = function(){
            if (app['lang'] == 'ru')
                app.Loader.load(['js/app/locale/filter.ru.js']);        
        };
        this.forward_editor = {
            xtype	: 'combobox',
            mode	: 'local',
            editable	: true,
            triggerAction:'all',
            regex: new RegExp('(^\\d{1,11}$)|(^'+app.msg.voicemail+'$)'),
            store :[
            ['vm',app.msg.voicemail],
            ]
        };  
        this.forward_renderer  = function(value){
            if (value == 'vm') return app.msg.voicemail;
            return value;
        };
        //console.log(this.columns);	
        this.columns[8] = {
            text: app.msg.forward,
            groupable: false,
            sortable: false,
            menuDisabled: true ,
            defaults:{
                editor:this.forward_editor,
                renderer:this.forward_renderer,
                menuDisabled: true ,
                groupable: false,
                hidden: true
            },
            columns:[
            {
                header: app.msg.always,
                dataIndex: 'forward',
                width: 120
            },
            {
                header: app.msg.forward_busy,
                dataIndex: 'forward_busy',
                width: 120
            },
            {
                header: app.msg.forward_noanswer,
                dataIndex: 'forward_noanswer',
                width: 120
            },
            {
                header: app.msg.noanswer_timeout,
                dataIndex: 'noanswer_timeout',
                width: 120,
                editor :  {
                    xtype: 'textfield',
                    regex: /^\d{1,3}$/
                }
            }
            ]
        };
      
        this.callParent(arguments);
    }
});
