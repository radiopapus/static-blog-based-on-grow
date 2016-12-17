---
$title@: Uznat-v-kakix-tablicax-zadejstvovano-pole
author@: Viktor Zharina
$order: 232
$dates:
  published: 2015-09-16 07:13:14
---
[sql]

SELECT TABLE_NAME

FROM information_schema.`COLUMNS`

WHERE COLUMN_NAME LIKE 'COLUMN'

group by `TABLE_NAME`

[/sql]