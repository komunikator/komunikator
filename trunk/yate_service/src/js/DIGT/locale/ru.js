Ext.onReady(function() { 
	if (Ext.grid.RowEditor) {
		Ext.apply(Ext.grid.RowEditor.prototype, {
			saveBtnText : "Сохранить",
			cancelBtnText : "Отмена",
			errorsText : "Ошибка",
			dirtyText : "Вы должны сохранить или отменить изменения"
		});
	}
});

if (window['DIGT']== undefined) DIGT = {}; 
DIGT.msg = {
auth_title : 'DIGT PBX авторизация',
login	   : 'Логин',
password   : 'Пароль',
error	   : 'Ошибка',
fail_load  : 'Сбой загрузки',
OK	   : 'ОК',
logout	   : 'Выход',
home	   : 'Главная',
attendant  : 'Автосекретарь',
call_logs  : 'История звонков',
active_calls : 'Активные звонки',
extensions : 'Внутренние номера',
time 	   : 'Время',
status	   : 'Статус',
duration   : 'Длительность',
groups	   : 'Группы',
user	   : 'Пользователь',
total	   : 'Всего',
gateways   : 'Провайдеры',
changepassword: 'Сменить пароль'
}
