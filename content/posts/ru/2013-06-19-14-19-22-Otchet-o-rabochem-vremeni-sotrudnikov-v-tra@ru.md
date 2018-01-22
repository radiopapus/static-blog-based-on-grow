---
$title@: Otchet-o-rabochem-vremeni-sotrudnikov-v-trac
author@: Viktor Zharina
$order: 91
$dates:
  published: 2013-06-19 14:19:22
---
На работе были поставлены следующие задачи:

<ul>

<li>Учет рабочего времени по тикетам;</li>

<li>Учет рабочего времени по дням (Получить по каждому сотруднику отработанные часы начиная с заданного за каждый рабочий день);</li>

</ul>



# Отчет по дням

[sql]

SELECT DATE_FORMAT( FROM_UNIXTIME( `starttime` ) , &quot;%d %M %Y&quot; ) AS `Дата`

    SEC_TO_TIME(SUM((TIMESTAMPDIFF(SECOND , FROM_UNIXTIME(`starttime`) , FROM_UNIXTIME( `endtime`))))) AS 'Отработано',

    ticket

FROM `work_log`

WHERE FROM_UNIXTIME( `starttime` ) &gt;= 'year-mont-day' AND ( `endtime` ) &lt;&gt; 0

GROUP BY ticket, `Дата`

ORDER BY DATE_FORMAT( FROM_UNIXTIME(`starttime`) , &quot;%d %M %Y&quot; ) DESC

[/sql]



# Отчет по тикетам

#Создаем временную таблицу, в которую заносим затраченное время на тикет и номер тикета

[sql]CREATE TEMPORARY TABLE tmp (spent TIME, ticket INT(11));[/sql]



#Добавляем во временную таблицу значения потраченно времени и номер тикета

[sql]

INSERT INTO tmp

SELECT

    SEC_TO_TIME(SUM((TIMESTAMPDIFF(SECOND , FROM_UNIXTIME(`starttime`) , FROM_UNIXTIME( `endtime`))))) as spent,

    ticket

FROM `work_log`

WHERE FROM_UNIXTIME( `starttime` ) &gt;= '2013-06-17' AND ( `endtime` ) &lt;&gt; 0

GROUP BY ticket

ORDER BY DATE_FORMAT( FROM_UNIXTIME(`starttime`) , &quot;%d %M %Y&quot; ) DESC;

[/sql]

#Обновляем таблицу ticket_custom с учетом данных временной таблицы

[sql]

UPDATE `ticket_custom` AS tc, `tmp` AS t SET tc.value = t.spent WHERE tc.ticket = t.ticket AND tc.name = 'tt_spent';[/sql]



# Выводим данные для проверки

[sql]SELECT * FROM ticket_custom[/sql]





