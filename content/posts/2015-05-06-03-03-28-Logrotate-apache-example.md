---
$title@: Logrotate-apache-example
author@: Viktor Zharina
$order: 215
$dates:
  published: 2015-05-06 03:03:28
---
<code>

/var/log/apache2/*.log {

	weekly

	missingok

	rotate 52

	compress

	delaycompress

	notifempty

	create 640 root group

	su root group

	sharedscripts

	postrotate

		/etc/init.d/apache2 reload > /dev/null

	endscript

	prerotate

		if [ -d /etc/logrotate.d/httpd-prerotate ]; then \

			run-parts /etc/logrotate.d/httpd-prerotate; \

		fi; \

	endscript

}

</code>



logrotate -d /etc/logrotate.d/apache2 - test

logrotate -v -f /etc/logrotate.d/apache2 - запуск