---
$title@: Nastrojki-dlya-innodb-my-cnf
author@: Viktor Zharina
$order: 211
$dates:
  published: 2015-04-28 04:12:27
---
innodb_buffer_pool_size = 1024M

innodb_flush_method = O_DIRECT

#innodb_log_file_size = 256M

innodb_log_buffer_size = 4M

innodb_flush_log_at_trx_commit = 2

innodb_thread_concurrency = 8