---
$title@: sortirovka-gruppirovka-tablicy
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: sortirovka-gruppirovka-tablicy
$dates:
  published: 2014-02-20 03:47:14
---
Нужно мне было значит сделать следующее. Есть таблица:

1 ид_записи1 01.01.2014

2 ид_записи1 02.02.2014

3 ид_записи1 03.03.2014



надо выбрать самую актуальную на текущий момент информацию, то есть за 03.03.2014

Делается это через вложенный запрос.

[sql]

SELECT * FROM (

    SELECT *

    FROM `table1`

    INNER JOIN ...

    INNER JOIN ...

    ORDER BY `table1`.id DESC

) AS orderedTable

GROUP BY orderedTable.ид_записи1;&quot;

[/sql]

То есть вначале выбираемся все необходимые данные и сортируем их в нужном порядке, а далее группируем по ид_записи. Такое надо делать не думая, но я как-то подзабыл.