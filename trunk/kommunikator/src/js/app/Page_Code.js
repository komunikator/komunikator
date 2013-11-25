Ext.define('app.Page_Code', {
    extend: 'Ext.window.Window',
    id: 'Page_Code',
    width: 400,
    height: 300,
    layout: 'fit',
    border: false,
    modal: true, //блокирует всё, что на заднем фоне
    closable: true, //убирает крестик, закрывающий окно
    resizable: false, // нельзя изменить размеры окна
    draggable: false, //перемещение объекта по экрану
    title: 'button code', //получаем название титула окна

    initComponent: function() {
       
        this.callParent(arguments);
    }
}
);