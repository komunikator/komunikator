#!/usr/bin/env node

var fs = require('fs');
var Nodeyate = require('./Nodeyate.js');
var path = require('path');
var mysql = require('mysql');
var child = require('child_process');

process.stdin.resume();

process.stdin.on('data', function(data) {
  
  //fs.appendFile("/tmp/msg", data);
  // анализируем данные  - ми запускаем обработку - если это сообщение
  var tex = data.toString();
  var ar = tex.split(':');
   // проверка условия того что это сообщение
  var mes = ar[0].split('%%');
   
  //проверка условия считывания данных message
  if((mes[mes.length - 1] == '>message') || (mes[mes.length - 1] == '<message')){

     //создание конструктора класса
     var f = ''; 
     var array = {};
     var Nod = new Nodeyate(f,f,f,f,f,f,f);
     Nod.param = {};

     //метод объекта  - определени вида сообщения
     Nod.parse(data);     

     // тут основной цикл по обработке 
     switch(Nod.type){
       case 'incoming': 
         switch (Nod.name){
           
           case 'chan.disconnected':
               // оветим на это сообщение
             Nod.handled = 'false';
             Nod.Acknowledge();

            if (Nod.param['answered'] == 'true'){
               // имена файлов ног - такие же как и у первого процесса
               var billid = Nod.param['billid'];
               var temp_file = billid.split('-');
               var file_name = '';
               for (var t = 0; t < temp_file.length; t++ ){
                 file_name += temp_file[t];
               }
               var file_1 = file_name + '_1.au';
               var file_2 = file_name + '_2.au';

               // полнй путь к файлам 
               var path_1 = path.join(way_leg, file_1);
               var path_2 = path.join(way_leg, file_2);

               // запустим функцию 
               //temp_arr.call(billid, path_1, path_2);
               setTimeout(function(){ set_timer(billid, path_1, path_2);}, 8000);
            }
            

            break; 
  

        default:
         Nod.handled = false;
         Nod.Acknowledge();
         break;
       } // второй switc
       break;
   
      case 'answer':
        break;
     } // первый switch
   }
});

// фуннкция запроса и запуска скриптов
function set_timer(billid, file_1, file_2)
{
    var query = "SELECT\
  tab.time,\
  tab.caller,\
  tab.called,\
  rt.call_records_id\
FROM (\
  SELECT\
    CASE\
      WHEN (x.extension IS NOT NULL AND x2.extension IS NOT NULL) THEN 3\
      WHEN (x.extension IS NOT NULL) THEN 1\
      ELSE 2\
    END AS type,\
    a.caller AS caller,\
    a.called AS called,\
    date_format(FROM_UNIXTIME (a.time), '%d_%m_%Y_%H_%i_%s') AS time,\
    g.gateway_id AS gateway_id,\
    CASE WHEN gm.group_id IS NULL THEN 0 ELSE gm.group_id END AS caller_group_id,\
    CASE WHEN gm2.group_id IS NULL THEN 0 ELSE gm2.group_id END AS called_group_id\
  FROM call_history a\
  LEFT JOIN extensions x ON x.extension=a.caller\
  LEFT JOIN extensions x2 ON x2.extension=a.called\
  LEFT JOIN gateways g ON g.authname=a.called OR g.authname=a.caller\
  LEFT JOIN group_members gm ON x.extension_id = gm.extension_id\
  LEFT JOIN group_members gm2 ON x2.extension_id = gm2.extension_id\
  WHERE a.billid = '" + billid + "'\
    AND a.status IN ('answered','normal_call_clearing')\
) AS tab\
JOIN call_records rt ON rt.enabled=1\
  -- caller section\
    AND CASE WHEN (rt.caller_number='*' OR RIGHT(tab.caller,10)=RIGHT(rt.caller_number,10)) THEN TRUE ELSE FALSE END\
  AND CASE WHEN (rt.caller_group=tab.caller_group_id OR (rt.caller_group is null and tab.caller_group_id is null)) THEN TRUE ELSE FALSE END\
  -- called section\
    AND CASE WHEN (rt.called_number='*' OR RIGHT(tab.called,10)=RIGHT(rt.called_number,10)) THEN TRUE ELSE FALSE END\
  AND CASE WHEN (rt.called_group=tab.called_group_id OR (rt.called_group is null and tab.called_group_id is null)) THEN TRUE ELSE FALSE END\
  -- other params\
  AND CASE WHEN (rt.gateway='*') THEN TRUE ELSE (rt.gateway=tab.gateway_id) END\
  AND CASE WHEN (rt.type='*') THEN TRUE ELSE (rt.type=tab.type) END\
";
           
   //fs.appendFile('/tmp/select', query + '\n');

   var qu = connection.query(query, function(err,rezult){
      if (err){//fs.appendFile('/tmp/mysql_err', err + '\n');
      } 

      //fs.appendFile('/tmp/mysql_rez', rezult.length +  '\n');


      if ( rezult.length == 0) {

        var command = '/del_file.sh ' +  file_1 + ' ' +  file_2 + '\n';
        //fs.appendFile('/tmp/command', command);
      
        // запуск bash скриптов
        var rt =  child.exec(__dirname + command, function (error){
         if (error){//fs.appendFile('/tmp/exec', error + '\n'); 
            }
        });   
      }

      else{

        for(i = 0 ; i < rezult.length; i++ ){

          var name = way + ''; // параметры звонка
          time = rezult[0].time;
          caler = rezult[0].caller;
          caled = rezult[0].called;
          rule = rezult[0].call_records_id;

          // временное название результирующего файла
          var file_3 = path.join( way , time + '~' + caler + '~' + caled + '.wav');

          // строка содержащая суть команды
          var command = '/wav_file.sh ' +  file_1 + ' ' +  file_2 + ' ' + file_3 + '\n';
          //fs.appendFile('/tmp/command', command);
       
          // запуск bash скриптов
          var rt =  child.exec(__dirname + command, function (error){
            if (error){//fs.appendFile('/tmp/exec', error + '\n'); 
            }
          });      

           // загрузка в логи яти какое правило сраьотало
           Node.Output( 'CALL FROM ' + caler + ' TO ' + ' RECORDED ACCORDING BY A RULE ' + rule + '\n' );
        
          break;
        }
      }   
   });
};



// переменные - праюочие
var Node = new Nodeyate();

// подключение соединения с SQL
var tt = 0;
var connection = mysql.createConnection({
   host:'localhost',
   user: 'root',
   password:'root'
});
connection.connect(function(err){
   if (err){
   }
   else{
    //fs.appendFile( '/tmp/mysql', connection.threadId);
   }
});

var q ='use kommunikator'; //+ connection.escapeId;
connection.query(q, function(err,rezult){
     if (err) { console.log(err); }
     else { 
    // console.log(rezult);
    }
});




var way = '/var/lib/misc/records/';

var way_leg = '/var/lib/misc/records/leg';
// массив содержащий звонки
var arr = new Array();


// переменная определяющая что нет никакой разницы в правилах звонка
var all = 'all';


// запсук команд yate 
Node.Install('chan.disconnected', 80);
Node.SetLocal('restart',true);