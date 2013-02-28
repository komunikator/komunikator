Ext.define('app.module.Time_Frames_Grid', {
    extend : 'app.Grid',
    no_adddelbuttons : true,	
    store_cfg : {
        fields : ['id','day', 'start_hour', 'end_hour'],
        storeId : 'time_frames',
        sorters : [{
            direction: '',
            property: ''	
        }]
    },
    //enableHdMenu: true,
    enableColumnHide:false,
    columns : [
    {
        hidden: true
    },
    { 
        sortable: false
    },
    {
        sortable: false,
        editor :  {
            xtype	: 'combobox',
            mode	: 'local',
            editable	: false,
            triggerAction:'all'
        }
    },
    { 
        sortable: false,
        editor :  {
            xtype	: 'combobox',
            mode	: 'local',
            editable	: false,
            triggerAction:'all'
        }
    }
    ],
    columns_renderer :
    function(value, metaData, record, rowIndex, colIndex, store) {
        if (colIndex==1)
        {
            return app.msg[value];
        }
        if ((colIndex==2 || colIndex==3) && value==null)
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
        this.columns[2].editor.store = clock;
        this.columns[3].editor.store = clock;
        this.callParent(arguments); 
    }
})
