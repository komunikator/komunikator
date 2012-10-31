Ext.define('app.Music_On_Hold_Grid', {
    extend : 'app.Grid',
    store_cfg : {
        fields : ['id', 'music_on_hold', 'description', 'file'],
        storeId : 'music_on_hold'
    },
    columns :
    [
    {
        hidden: true
    },
    {
        width:320
    },

    {
        hidden: true
    },
    {
        width:320
    }
    ],        
    initComponent : function () {
        this.columns_renderer = function(value, metaData, record, rowIndex, colIndex, store) {
            if (colIndex == 3)
                value = value?'<audio type="audio/wav" src="moh/'+value+'?dc_='+new Date().getTime()+'" controls autobuffer>Your browser does not support the audio element.</audio>':''
            return value; 
        };        
        this.callParent(arguments);
    }
})
