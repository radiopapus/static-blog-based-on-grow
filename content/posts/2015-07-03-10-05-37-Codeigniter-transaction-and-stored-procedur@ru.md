---
$title@: Codeigniter-transaction-and-stored-procedure
author@: Viktor Zharina
$order: 225
$dates:
  published: 2015-07-03 10:05:37
---
Есть несколько запросов, среди, которых insert в таблицу и далее вызов хранимой процедуры, внутри которой также есть транзакция. Так вот в этом случае transact_status возвращает 1 при этои запись в таблицу не происходит. 



Предварительный вывод: Транзакции нельзя делать вложенными в codeigniter.



Conclusion: You cannot use nested transaction in codeigniter.