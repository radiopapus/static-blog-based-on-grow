---
$title@: Myisam-vs-innodb
author@: Viktor Zharina
$order: 217
$dates:
  published: 2015-05-24 12:58:25
---
From stackoverflow



MyISAM:



The MyISAM storage engine in MySQL.



Simpler to design and create, thus better for beginners. No worries about the foreign relationships between tables.

Faster than InnoDB on the whole as a result of the simpler structure thus much less costs of server resources.

Full-text indexing.

Especially good for read-intensive (select) tables.

InnoDB:



The InnoDB storage engine in MySQL.



Support for transactions (giving you support for the ACID property).

Row-level locking. Having a more fine grained locking-mechanism gives you higher concurrency compared to, for instance, MyISAM.

Foreign key constraints. Allowing you to let the database ensure the integrity of the state of the database, and the relationships between tables.

InnoDB is more resistant to table corruption than MyISAM.

Support for large buffer pool for both data and indexes. MyISAM key buffer is only for indexes.

MyISAM is stagnant; all future enhancements will be in InnoDB

MyISAM Limitations:



No foreign keys and cascading deletes/updates

No transactional integrity (ACID compliance)

No rollback abilities

Row limit of 4,284,867,296 rows (232)

Maximum of 64 indexes per row

InnoDB Limitations:



No full text indexing (Below-5.6 mysql version)

Cannot be compressed for fast, read-only

