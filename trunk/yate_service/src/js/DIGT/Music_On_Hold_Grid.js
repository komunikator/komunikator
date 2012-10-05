Ext.define('DIGT.Music_On_Hold_Grid', {
    extend : 'DIGT.Grid',
    initComponent : function () {
        this.store.autorefresh = undefined;  
        this.store.fields = ['id', 'music_on_hold', 'description', 'file'];
        this.store.storeId ='music_on_hold';
        this.viewConfig.loadMask = false;
        this.columns_renderer = function(value, metaData, record, rowIndex, colIndex, store) {
            if (colIndex == 3)
                value = value?'<audio type="audio/wav" src="moh/'+value+'?dc_='+new Date().getTime()+'" controls autobuffer>Your browser does not support the audio element.</audio>':''
            return value; 
        }
        this.columns = 
        [
        {
            hidden: true
        },
	{width:320},
	{
	hidden: true
	},
	{width:320}
        ];
        this.callParent(arguments);
    }
})
