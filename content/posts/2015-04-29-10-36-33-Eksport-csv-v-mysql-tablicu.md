---
$title@: Eksport-csv-v-mysql-tablicu
author@: Viktor Zharina
$order: 214
$dates:
  published: 2015-04-29 10:36:33
---
<code>

LOAD DATA LOCAL INFILE '/tmp/file.csv' 

INTO TABLE db_project.table_N

FIELDS TERMINATED BY ';' 

ENCLOSED BY '"'

LINES TERMINATED BY '\n'

IGNORE 1 ROWS;

</code>