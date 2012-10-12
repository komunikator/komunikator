Ext.define('app.Time_Frames_Grid', {
    extend : 'app.Grid',
    no_adddelbuttons : true,	
    store_cfg : {
        fields : ['id','prompt_id','day', 'start_hour', 'end_hour','numeric_day'],
        storeId : 'time_frames'
    },
    columns : [
    {
        hidden: true
    },
    {
        hidden: true
    },
    { 
    },
    {
        editor :  {
	    xtype	: 'combobox',
	    mode	: 'local',
	    editable	: false,
	    triggerAction:'all'
        }
    },
    { 
        editor :  {
	    xtype	: 'combobox',
	    mode	: 'local',
	    editable	: false,
	    triggerAction:'all'
        }
    },
    {
	hidden: true
    }
    ],
    columns_renderer :
        function(value, metaData, record, rowIndex, colIndex, store) {
            if (colIndex==2)
	    {
		return app.msg[value];
	    }
	    if ((colIndex==3 || colIndex==4) && value==null)
	    {
		return app.msg['notselected'];
	    }
            return value;
    },
    initComponent : function () {
	var clock = [
		     [null,app.msg['notselected']],
		     ['1','1'],
		     ['2','2'],
		     ['3','3'],
		     ['4','4'],
		     ['5','5'],
		     ['6','6'],
		     ['7','7'],
		     ['8','8'],
		     ['9','9'],
		     ['10','10'],
		     ['11','11'],
		     ['12','12'],
		     ['13','13'],
		     ['14','14'],
		     ['15','15'],
		     ['16','16'],
		     ['17','17'],
		     ['18','18'],
		     ['19','19'],
		     ['20','20'],
		     ['21','21'],
		     ['22','22'],
		     ['23','23'],
		     ['24','24']
	];                      
	this.columns[3].editor.store = clock;
	this.columns[4].editor.store = clock;
        this.callParent(arguments); 
    }
})
