---
$title@: Nginx-php-fpm-arch
author@: Viktor Zharina
$order: 119
$dates:
  published: 2013-11-10 09:56:28
---
Про установку nginx и fpm писать не буду, так как она не сложнее чем в ubuntu. 

Приведу минимальные конфиги для nginx, php-fpm. В конфиге для nginx помимо основных настроек написан location для обработки php-файлов. Смысл здесь вот в чем: когда данные прихдят на 80 порт, nginx понимает php-файл это или нет, если php, то передает данные на 9000 порт. 9000 порт слушает php-fpm, который обрабатывает php и передает результат (html) обратно nginx, который передает в браузер. 

Для проверки необходимо положить php-файл с правами на чтение в каталог /var/www/. В файле написать всем известный <?php echo phpinfo(); ?> и в адресной строке браузера ввести ip/test.php. Если все ок, то увидим информацию о настройках php, 

иначе лезем в лог.



А вообще вот хорошая статья 

http://www.homecomputerlab.com/nginx-php-mariadb-wordpress-on-archlinux-on-a-raspberry-pi.

https://library.linode.com/lemp-guides/arch-linux



<strong>nginx.conf</strong>

[code lang="plain"]

user http http;

worker_processes  1;

error_log  /var/log/nginx/error.log;



events {

    worker_connections  1024;

}



http {

    server {

        listen       80;

        # pass the PHP scripts to FastCGI server listening on 127.0.0.1:9000

        location ~ \.php$ {

            root           /var/www;

            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;

            include        fastcgi_params;

            fastcgi_pass   127.0.0.1:9000;

        }

    }

}

[/code]



<strong>php-fpm.conf</strong>

[code lang="plain"]

pid = /run/php-fpm/php-fpm.pid

error_log = log/php-fpm.log

log_level = error

[www]

user = http

group = http

listen = 127.0.0.1:9000

pm = dynamic

pm.max_children = 5

pm.start_servers = 2

pm.min_spare_servers = 1

pm.max_spare_servers = 3

chdir = /var/www

catch_workers_output = yes

[/code]