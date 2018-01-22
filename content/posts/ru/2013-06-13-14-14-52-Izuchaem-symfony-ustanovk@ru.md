---
$title@: Izuchaem-symfony-ustanovka
author@: Viktor Zharina
$order: 89
$dates:
  published: 2013-06-13 14:14:52
---
Напишу только то, с чем возникли проблемы. А именно после установки с помощью composer я запустил php app/check.php из корня проекта и получил Error о том, что у меня не задана date.timezone. Решается так, что нужно поправить файл /etc/php5/cli/php.ini, в котором задать нужную временную зону (http://php.net/manual/en/timezones.php). Ну и я еще в  /etc/php5/apache2/php.ini такую же временную зону добавил и apache перезапустил.

Собственно это все.