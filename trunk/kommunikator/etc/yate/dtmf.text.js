//TODO
//add to c2c-api.js 
//GEO INFO
// var about = {}; 
// function geoData(data){about = {geo:data};}; 
// document.write(unescape("%3Cscript src='http://www.telize.com/geoip?callback=geoData' + (new Date).getTime(); type='application/javascript'%3E%3C/script%3E"));
// ...
//        tsk_utils_log_info('[C2C] session event = ' + e.type);
//        switch (e.type) {
//+++	    case 'connected': c2c.callSession.dtmf(escape(JSON.stringify(about)));//dtmf hack
//            case 'connecting': case 'connected':
// ...
//GEO INFO


function onText(msg)
{
    //Engine.print_r(msg);
       var m = new Message("database");
       m.account = "kommunikator";//from mysqldb.conf
       m.query = "update call_logs set gateway= '"+msg.text.substr(7)+"' where billid = '"+msg.billid+"'";//without 'Signal='
       m.dispatch();
}

Message.install(onText, "chan.text",10);
