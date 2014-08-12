USE PBX;

-- http://tigor.com.ua/blog/2008/08/23/date_comparison_by_between_operator_of_mysql/
SELECT "Дата",
       DATE_ADD(LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)),INTERVAL 1 DAY) "Начало",
       DATE_ADD(LAST_DAY(CURDATE() - INTERVAL 1 MONTH), INTERVAL 1 DAY) "Окончание",
       "DIGT АТС",
       "Отчет",
       "по",
       "звонкам"
UNION
SELECT CASE `Линия`
           WHEN 'accepted' THEN 'принятый'
           WHEN 'answered' THEN 'отвеченный'
           WHEN 'cancelled' THEN 'отменен'
           WHEN 'ringing' THEN 'вызов'
           WHEN 'rejected' THEN 'отклоненный'
           WHEN 'outgoing' THEN 'исходящий'
           WHEN 'address_incomplete' THEN 'неполный номер'
           WHEN 'incoming' THEN 'входящий'
           WHEN 'internal' THEN 'внутренний'
           WHEN 'request_terminated' THEN 'отменен'
           WHEN 'busy_here' THEN 'занят'
           WHEN 'busy_here_[call_processing_released]' THEN 'занят при дозвоне'
           WHEN 'transfer' THEN 'переведённый'
           WHEN 'temporarily_unavailable' THEN 'недоступен'
           WHEN 'temporarily_unavailable_[call_processing_released]' THEN 'временно недоступен'
           WHEN 'normal,_unspecified' THEN 'отвеченный'
           WHEN 'pickup' THEN 'перехваченный'
           WHEN 'temporarily_not_available' THEN 'недоступен'
           WHEN 'forbidden_[call_processing_released]' THEN 'недоступен'
           WHEN 'not_found_[call_processing_released]' THEN 'не найден'
           WHEN 'service_unavailable' THEN 'сервис недоступен'
           WHEN 'dropped' THEN 'упущенный'
           WHEN 'forbidden' THEN 'звонок запрещён'
           WHEN 'divert_busy' THEN 'переадресация'
           WHEN 'not_acceptable' THEN 'неправильно набран номер'
           WHEN 'divert_noanswer' THEN 'не отвечает'
           WHEN 'not_found' THEN 'не найден'
           WHEN 'server_internal_error' THEN 'внутренняя ошибка сервера'
           WHEN 'request_timeout' THEN 'время ожидания истекло'
           WHEN 'unallocated_(unassigned)_number' THEN 'недопустимый номер'
           WHEN 'normal_call_clearing' THEN 'сбой вызова'
           ELSE `Линия`
       END "Линия",
       `Количество_входящих`,
       `Время_входящих`,
       `Количество_исходящих`,
       `Время_исходящих`,
       `Количество_внутренних`,
       `Время_внутренних`
FROM
  (SELECT "        Линия",
          "Количество_входящих",
          "Количество_исходящих",
          "Количество_внутренних",
          "Время_входящих",
          "Время_исходящих",
          "Время_внутренних"
   UNION SELECT gateway "Линия",
                -- cast(count(*) as char(5)) "Количество",

                cast(count(CASE WHEN TYPE='incoming' THEN 1 ELSE NULL END) AS char(5)) "Количество_входящих",
                cast(count(CASE WHEN TYPE='outgoing' THEN 1 ELSE NULL END) AS char(5)) "Количество_исходящих",
                cast(count(CASE WHEN TYPE='internal' THEN 1 ELSE NULL END) AS char(5)) "Количество_внутренних",
                -- TIME_FORMAT(

                SEC_TO_TIME(IFNULL(sum(CASE WHEN TYPE='incoming' THEN duration ELSE NULL END),0)) "Время_входящих",
                SEC_TO_TIME(IFNULL(sum(CASE WHEN TYPE='outgoing' THEN duration ELSE NULL END),0)) "Время_исходящих",
                SEC_TO_TIME(IFNULL(sum(CASE WHEN TYPE='internal' THEN duration ELSE NULL END),0)) "Время_внутренних" -- ,'%Hч %iм %Sсек')
FROM
     (SELECT b.time,
             CASE WHEN x.extension IS NOT NULL
      AND x2.extension IS NOT NULL THEN 'internal' WHEN x.extension IS NOT NULL THEN 'outgoing' ELSE 'incoming' END TYPE,
                                                                                                                    round(b.billtime) duration,
                                                                                                                    CASE WHEN g.description IS NOT NULL
      AND g.description !='' THEN g.description WHEN g.gateway IS NOT NULL THEN g.gateway WHEN g.authname IS NOT NULL THEN g.authname ELSE 'Внутренние звонки' -- null
 END gateway
      FROM call_logs a
      JOIN call_logs b ON b.billid=a.billid AND b.duration <> 9999.999
      AND b.ended=1
      AND b.direction='outgoing'
      AND b.status!='unknown'
      LEFT JOIN extensions x ON x.extension=a.caller
      LEFT JOIN extensions x2 ON x2.extension=b.called
      LEFT JOIN gateways g ON g.authname=a.called
      OR g.authname=b.caller
      WHERE a.ended=1
        AND a.direction='incoming'
        AND a.status!='unknown') a
   WHERE -- gateway is not null and
time BETWEEN UNIX_TIMESTAMP(DATE_ADD(LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)),INTERVAL 1 DAY)) AND UNIX_TIMESTAMP(DATE_ADD(LAST_DAY(CURDATE() - INTERVAL 1 MONTH), INTERVAL 1 DAY))
   GROUP BY gateway
   UNION SELECT "        Статусы",
                "",
                "",
                "",
                "",
                "",
                ""
   UNION SELECT status,
                count(CASE WHEN TYPE='incoming' THEN 1 ELSE NULL END) "Количество_входящих",
                count(CASE WHEN TYPE='outgoing' THEN 1 ELSE NULL END) "Количество_исходящих",
                count(CASE WHEN TYPE='internal' THEN 1 ELSE NULL END) "Количество_внутренних",
                -- TIME_FORMAT(

                SEC_TO_TIME(IFNULL(sum(CASE WHEN TYPE='incoming' THEN duration ELSE NULL END),0)) "Время_входящих",
                SEC_TO_TIME(IFNULL(sum(CASE WHEN TYPE='outgoing' THEN duration ELSE NULL END),0)) "Время_исходящих",
                SEC_TO_TIME(IFNULL(sum(CASE WHEN TYPE='internal' THEN duration ELSE NULL END),0)) "Время_внутренних" -- ,'%Hч %iм %Sсек')

   FROM
     (SELECT b.time,
             CASE WHEN x.extension IS NOT NULL
      AND x2.extension IS NOT NULL THEN 'internal' WHEN x.extension IS NOT NULL THEN 'outgoing' ELSE 'incoming' END TYPE,
                                                                                                                    round(b.billtime) duration,
                                                                                                                    CASE WHEN g.description IS NOT NULL
      AND g.description !='' THEN g.description WHEN g.gateway IS NOT NULL THEN g.gateway WHEN g.authname IS NOT NULL THEN g.authname ELSE NULL END gateway,
                                                                                                                                                    CASE WHEN b.reason="" THEN b.status ELSE replace(lower(b.reason),' ','_') END status
      FROM call_logs a
      JOIN call_logs b ON b.billid=a.billid AND b.duration<>9999.999
      AND b.ended=1
      AND b.direction='outgoing'
      AND b.status!='unknown'
      LEFT JOIN extensions x ON x.extension=a.caller
      LEFT JOIN extensions x2 ON x2.extension=b.called
      LEFT JOIN gateways g ON g.authname=a.called
      OR g.authname=b.caller
      WHERE a.ended=1
        AND a.direction='incoming'
        AND a.status!='unknown') a
   WHERE -- duration > 0 and
-- gateway is not null and
time BETWEEN UNIX_TIMESTAMP(DATE_ADD(LAST_DAY(DATE_SUB(CURDATE(), INTERVAL 2 MONTH)),INTERVAL 1 DAY)) AND UNIX_TIMESTAMP(DATE_ADD(LAST_DAY(CURDATE() - INTERVAL 1 MONTH), INTERVAL 1 DAY))
   GROUP BY status HAVING count(*) > 0) a 
INTO OUTFILE '/tmp/ats_report.tmp'
FIELDS TERMINATED BY ';'
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
