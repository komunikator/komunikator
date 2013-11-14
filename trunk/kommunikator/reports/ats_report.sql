use PBX;
select "Дата", 
	    DATE_FORMAT(DATE_ADD(CURDATE(),INTERVAL -7 DAY)-weekday(DATE_ADD(CURDATE(),INTERVAL -7 DAY)),'%d.%m.%Y') "Начало",
          DATE_FORMAT(curdate()-weekday(curdate()),'%d.%m.%Y') "Окончание"
union
select "Линия", "Количество","Время"
union
select 
case `Линия`
    when 'accepted' then 'принятый'
    when 'answered' then 'отвеченный'
    when 'cancelled' then 'отменен'
    when 'ringing' then 'вызов'
    when 'rejected' then 'отклоненный'
    when 'outgoing' then 'исходящий'
    when 'address_incomplete' then 'неполный номер'
    when 'incoming' then 'входящий'
    when 'internal' then 'внутренний'
    when 'request_terminated' then 'отменен'
    when 'busy_here' then 'занят'
    when 'busy_here_[call_processing_released]' then 'занят при дозвоне'
    when 'transfer' then 'переведённый'
    when 'temporarily_unavailable' then 'недоступен'
    when 'temporarily_unavailable_[call_processing_released]' then 'временно недоступен'
    when 'normal,_unspecified' then 'отвеченный'
    when 'pickup' then 'перехваченный'
    when 'temporarily_not_available' then 'недоступен'
	when 'forbidden_[call_processing_released]' then 'недоступен'
	when 'not_found_[call_processing_released]' then 'не найден'
	when 'service_unavailable'  then 'сервис недоступен'
    when 'dropped'	then 'упущенный'
    when 'forbidden'	then 'звонок запрещён'
    when 'divert_busy'	then 'переадресация'
    when 'not_acceptable'	 then 'неправильно набран номер'
    when 'divert_noanswer'	then 'не отвечает'
    when 'not_found'	then 'не найден'
    when 'server_internal_error'	then 'внутренняя ошибка сервера'
    when 'request_timeout'	then 	'время ожидания истекло'
    when 'unallocated_(unassigned)_number'	then 'недопустимый номер'
    when 'normal_call_clearing' then 	'сбой вызова'
else `Линия`
end,
`Количество`,`Время` 
 from (
select gateway "Линия", 
	     cast(count(*) as char(5)) "Количество",
		-- TIME_FORMAT(
		SEC_TO_TIME(sum(duration)) "Время"
		-- ,'%Hч %iм %Sсек') 
	    from (
select	
    b.time,
/*
        case 
          when x.extension is not null and x2.extension is not null then 'internal'
          when x.extension is not null then 'outgoing'
      else 'incoming'
        end type,

    case when x.firstname is null then a.caller else concat(x.firstname,' ',x.lastname,' (',a.caller,')') end caller,
    case when x2.firstname is null then b.called else concat(x2.firstname,' ',x2.lastname,' (',b.called,')') end called,
*/
    round(b.billtime) duration,
        case 
     when g.description is not null and g.description !='' then g.description 
     when g.gateway     is not null                        then g.gateway	
     when g.authname    is not null                        then g.authname
    else null 
        end gateway -- ,
      --  case when b.reason="" then b.status else replace(lower(b.reason),' ','_') end status
from call_logs a  
join call_logs b on b.billid=a.billid and b.ended=1 and b.direction='outgoing' and b.status!='unknown'
left join extensions x on x.extension=a.caller 
left join extensions x2 on x2.extension=b.called
left join gateways g  on g.authname=a.called or g.authname=b.caller
   where a.ended=1 and a.direction='incoming' and a.status!='unknown') a
where
gateway is not null and
time between 
  UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -7 DAY)-weekday(DATE_ADD(CURDATE(),INTERVAL -7 DAY)))
	and 
  UNIX_TIMESTAMP(curdate()-weekday(curdate()))
group by gateway
union
select "Статусы", "Количество","Время"
union
select 
    status, 
	     cast(count(*) as char(5)),
		-- TIME_FORMAT(
		SEC_TO_TIME(sum(duration))
		-- ,'%Hч %iм %Sсек') 
	    from (
select	
    b.time,
/*
        case 
          when x.extension is not null and x2.extension is not null then 'internal'
          when x.extension is not null then 'outgoing'
      else 'incoming'
        end type,

    case when x.firstname is null then a.caller else concat(x.firstname,' ',x.lastname,' (',a.caller,')') end caller,
    case when x2.firstname is null then b.called else concat(x2.firstname,' ',x2.lastname,' (',b.called,')') end called,
*/
    round(b.billtime) duration,
        case 
     when g.description is not null and g.description !='' then g.description 
     when g.gateway     is not null                        then g.gateway	
     when g.authname    is not null                        then g.authname
    else null 
        end gateway,
        case when b.reason="" then b.status else replace(lower(b.reason),' ','_') end status
from call_logs a  
join call_logs b on b.billid=a.billid and b.ended=1 and b.direction='outgoing' and b.status!='unknown'
left join extensions x on x.extension=a.caller 
left join extensions x2 on x2.extension=b.called
left join gateways g  on g.authname=a.called or g.authname=b.caller
   where a.ended=1 and a.direction='incoming' and a.status!='unknown') a
where
-- duration > 0 and 
gateway is not null and
time between 
  UNIX_TIMESTAMP(DATE_ADD(CURDATE(),INTERVAL -7 DAY)-weekday(DATE_ADD(CURDATE(),INTERVAL -7 DAY)))
	and 
  UNIX_TIMESTAMP(curdate()-weekday(curdate()))
group by status having count(*) > 0
) a
INTO OUTFILE '/tmp/ats_report.tmp'
FIELDS TERMINATED BY ';'
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
