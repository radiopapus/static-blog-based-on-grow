---
$title@: Bem-mac-na-chasti
author@: Viktor Zharina
$order: 227
$dates:
  published: 2015-07-14 03:41:18
---
Входные данные: строка

Ожидаемый результат 4символа.4символа.4символа и т.д.



Решение

<code>implode('.', str_split($mac, 4));</code>