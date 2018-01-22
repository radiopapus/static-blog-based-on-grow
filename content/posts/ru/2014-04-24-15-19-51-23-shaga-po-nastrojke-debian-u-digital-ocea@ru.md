---
$title@: 23-shaga-po-nastrojke-debian-u-digital-ocean
author@: Viktor Zharina
$order: 176
$dates:
  published: 2014-04-24 15:19:51
---
Только для меня, на всякий случай.



1. apt-get update

2. apt-get upgrade

3. sudo dpkg-reconfigure locales (выбрать ru_RU)

4. aptitude install console-cyrillic

5. /etc/init.d/console-cyrillic start

6. aptitude install php5-cli php5-common php5-mysql php5-gd php5-fpm php5-cgi php5-fpm php-pear php5-mcrypt

7. apt-get install python-software-properties

8. apt-key adv --recv-keys --keyserver keyserver.ubuntu.com 0xcbcb082a1bb943db

9. add-apt-repository 'deb http://mirror.mephi.ru/mariadb/repo/10.0/debian wheezy main'

10. apt-get update

11. apt-get install mariadb-server redis-server mc nginx php5-fpm screen python-tornado

14. apt-get purge apache2 apache2-utils apache2.2-bin apache2-common

18. mkdir -p /var/log/web

18. mkdir /var/www

19. chown www-data:www-data /var/www/

20. chmod 755 /var/www/

21. php -r "readfile('https://getcomposer.org/installer');" | php