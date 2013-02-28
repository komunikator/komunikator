{
    Ext.apply(Ext.ux.grid.FiltersFeature.prototype,{
        menuFilterText:'Фильтры'
    }); 
    Ext.apply(Ext.ux.grid.filter.DateFilter.prototype,{
        afterText:'После',
        beforeText:'До',
        onText:'Равно'
    });
    Ext.apply(Ext.ux.grid.menu.RangeMenu.prototype,{
        menuItemCfgs:{
            'emptyText': 'Введите значение ..'
        }
    });

}
