
var fs = require('fs');

process.stdin.resume();

// создание конструктора 
function Nodeyate( type, name, retval, origin, id, handled,param ){
  this.type = type;
  this.name = name;
  this.retval = retval;
  this.origin = origin;
  this.id = id;
  this.handled = handled;
  this.param = param;
}
 // первый метод - нужно понять что прид\летело
Nodeyate.prototype.parse = function(data){
  var buf = data.toString();
  var arr = buf.split(':');
  // проверка того что это сообщение
  var mes = arr[0].split('%%');
 
  // запсуск функции заполения функция (id date)
  this.yate();

   // анализируем что нам пришло
   switch(mes[mes.length - 1]){
       // сообщение мы на него подписались
    case '>message':
     this.type = 'incoming';
     this.id = arr[1];
     this.name = arr[3];
     this.retval= arr[4];
     this.param_print(data);
     break;

      //сообщение   - ответ на наше сообщение
    case '<message':
     this.type = 'answer';
     this.id = arr[1];
     this.handled = this.Str2bool(arr[2]);
     this.name = arr[3];
     this.retval = arr[4];
     this.param_print(data);
     break;
  
     // сообщение - результат продписи на события
     case '<install':
      this.type = 'installed';
      this.name = arr[2];
      this.handled = this.Str2bool(arr[3]);
      break;

      // сообщение - результат одтписки от события
     case '<uninstall':
      this.type = 'uninstall';
      this.name = arr[2];
      this.handled = this.Str2bool(arr[3]);
      break;

      //сообщение - наблюдатель за сообщениями
      case '<watch':
       this.type = 'watch';
       this.name = arr[1];
       this.handled = this.Str2bool(arr[2]);
       break;
 
     // сообщение отписки от события
      case 'unwatch':
        this.type = 'unwatch';
        this.name = arr[1];
        this.handled = this.Str2bool(arr[2]);
        break;

      // сообщение - изменени некторых параметров
      case '<setlocal':
        this.type = 'setlocal';
        this.name = arr[1];
        this.handled = this.Str2bool(arr[3]);
        this.id = arr[2];
        break;

     // по умолчанию отправляем ошибку в логи
     default:
        fs.appendFile(data);
        var err = 'Что - то пошло не так' + data.toString();
        this.Output(err);
  }
};

// функция инициализации первоначальных данных
Nodeyate.prototype.yate = function(name){
    this.type = 'outging';

    this.id = this.yate_id();

    this.name = name;
    this.retval = '';
    this.origin = Math.ceil(Date.now()/1000);
    this.handled = 'false';
    this.param = {};
};
// функция форимрование Уникального id 
Nodeyate.prototype.yate_id = function(imput){
  var nums = '1234567890';
  var chars = '0123456789qwertyuiopasdfghjklzxcvbnm';
   // паервоначально последовательность из буксв из цифр
  var str = '';
   for(var i = 0; i < 17; i++){
    // случайное число
     if ((i >= 0) && (i <= 4)){
      var num = Math.floor(Math.random() * nums.length);
      str += nums.substring(num,num + 1);
     }
     else {
      var num = Math.floor(Math.random() * chars.length);
      str += chars.substring(num,num + 1);
     }
   }

   str += '.';
    for(var i = 0; i < 9; i++){
     var num = Math.floor(Math.random() * nums.length);
     str += nums.substring(num, num + 1);
   }
   imput = null;
   imput = str;
   return str;
};

// функция от handled
Nodeyate.prototype.Str2bool = function(str){
  if(str == 'true'){
  return true;}
  else {return false;}
};
Nodeyate.prototype.Bool2str = function(bool){
  if (bool){
  return 'true';  }
  else {return 'false';}
};

// функции ответа от message
Nodeyate.prototype.Acknowledge = function(data){
   // перенаправление звонка
   /*
   this.retval ='sip/sip%z127@172.17.2.44%z' + this.param['ip_port'] ;
   this.handled = 'true';
   */
  // формирование строки твета
   var buf = '';
   buf = '%%<message'+ ':' + this.id + ':' + this.handled + ':' + this.name + ':' + this.retval  ;
    
   for (var i in this.param){

    if(i != '' ) {
      // проверка на новую строку
      var temp_v = this.str(this.param[i]);
      var temp_k = this.str(i);
      buf += ':' + temp_k  + '=' + temp_v; 
    }
    
   }

   //fs.appendFile('/tmp/msg2', buf + '\n');

   this._yate_print(buf);
   this.type = 'acknowledge';
};
// функция запроса от massge
Nodeyate.prototype.Dispatch = function(){
  
  if (this.type != 'outgoing'){
   this.Output('Все пропало!!!!'+ this.type);
  }

  // формирование строки для посылки  
  var buf = '%%>message' + ':' + this.id + ':' + this.origin +':' + this.name + ':' + this.retval;
   
   for (var i in this.param){
   
    if (i != ''){
      // проверка на новую строк
     var temp_v = this.str(this.param[i]); // this.param[i].split('\n');
     var temp_k = this.str(i); // i.split('\n');
     buf += ':' + temp_k + '=' + temp_v;
    }
  }
   
  var out = buf + '\n';
  //fs.appendFile('/tmp/msg3', out);

  // ДОБАВЛЕН�? НЕКОТОРЫХ ПАРАМЕТРОВ
  this._yate_print(buf);
  this.type = 'dispatch';
};
//функция разделяющая новые строки и пробелы
Nodeyate.prototype.str = function(string){
   var ret = '';

   if ((string == true) || (string == false) || (string == '') || (string == ' ') || (string == null)){
     ret = string;
     if (string == null) {ret = '';}
   }
   // проверка того что этот массив что - то содержит
   else if (string.length >= 0) {
     for (var i = 0; i < string.length; i++){
       if(string[i] == '\n') {}
        else {
        ret += string[i];
       }
     }
   }
   return ret;
};

// функция нарисования параметров сообщения
Nodeyate.prototype.param_print = function(data){
   // создание массива по знгаку
    var buf = data.toString();
    var arra = buf.split(':');

   // пойдем сначала и будеи проверять на длину '='
    for (var i = 0; i < arra.length; i++){
      var key = arra[i].split('=');

      // запись  параметров в массив сообщения
      if (key.length == 2){
       this.param[key[0]] = key[1];      
      }
      
     // если параметры 
     else if (key.length > 2){
         // формирование строки знаения
        var val = '';
          // последний элемент массива
        var last = key[key.length - 1];
        for (var k = 1; k < key.length - 1; k++){
           val += key[k] + '=';          
        }
       val += last;
       this.param[key[0]]= val; 
      }
    } 
}; 
// функцияя возвращающая нудные значения в зависомости от команлы
Nodeyate.prototype.to_file = function(){

};

// команда плучения записи
Nodeyate.prototype.GetValue = function (key){
    var val = null;

    for (var i in this.param){
      if (i === key){
      val = this.param[i];
      break;
      }
    }
    return val;
};
//функция записи параметров
Nodeyate.prototype.SetParam = function(key,value){
    if ((key != '') || (key != null) || (key != ' ')){
      this.param[key] = value;
    }
};

// ФУНКЦ�?�? кстановки различных сообщений
Nodeyate.prototype.Install = function(name,priority,filtname,filtvalue){
   // формирование строки для утсановки
   if((filtname != null) || (filtvalue != null)){
     filtname = filtname + ':' +filtvalue;
     var out = '%%>install' + ':' + priority + ':' + name + ':' + filtname;
   }
   else {
    var out = '%%>install' + ':' + priority + ':' + name + '\n' ;
   }   
   this._yate_print(out);
    //fs.appendFile('/tmp/msg25', out );
}; 
Nodeyate.prototype.Unistall = function(name){
   var out = '%%>uninstall' + ':' + name + '\n';
   this._yate_print(out);
};

// функция выводы команд
Nodeyate.prototype._yate_print = function(str){
   var data = str.toString(); 
   process.stdout.write(data + '\n');

};
//функция обработки ОШ�?БК�?
Nodeyate.prototype.Output = function(str){
  // форимрование строки для вывода 
  var tt = '%%>output:' + str.toString() + '\n';
   this._yate_print(tt);
};

// измеени параметров модулей setlocal
Nodeyate.prototype.SetLocal = function(name, value){
  //формирование строки 
  var tt = '%%>setlocal:' + name + ':' + value;
  this._yate_print(tt);
 // fs.appendFile('/tmp/msg26', tt + '\n');
};
Nodeyate.prototype.GetLocal = function (name){
  // формирование строки
  var tt = '%%>setlocal:' + name + ':' + '';
  this._yate_print(tt) ;
};
Nodeyate.prototype.Watch = function(name){
   var tt = '%%>watch:' + name;
   this._yate_print(tt);o
};

// попытка чтения аргументов 
Nodeyate.prototype.arg = function(){
   return process.argv[2];
};

module.exports = Nodeyate;