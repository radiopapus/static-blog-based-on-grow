---
$title@: Nastrojka-nginx-dlya-livestreet-kotoraya-ustanovlena-v-otdelnyj-katalog
author@: Viktor Zharina
$order: 162
$dates:
  published: 2014-03-22 13:25:18
---
Задача: настроить nginx так, чтобы при определенном запросе работал сайт на базе livestreet, установленный в подкаталог относительно корневого, а при другом запросе работал ресурс на базе laravel.



Есть сервис laravel.ru, файлы которого расположены в каталоге /var/www/laravel. Есть livestreet, который установлен в /var/www/laravel/domains/число/. Нужно чтобы при запросе laravel.ru работал сервис, а при запроса laravel.ru/domains/число работал работал livestreet.



<h2>Начальные условия</h2>

<ol>	

	<li>nginx 1.4.x</li>

	<li>php-fpm</li>

	<li>Laravel 4</li>

	<li>LivestreetCMS 1.0.3</li>

</ol>



<h2>Конфигурация nginx</h2>

Ниже я предоставляю конфигурацию и дам пояснения.

[plain]

server {

    server_name laravel.ru; 

    root /var/www/hobby-work.ru/public; 

    index index.php;



    #livestreet	

    location ~ ^/domains/([0-9]+) {  # рег. выражение. для работы с livestreet 

# в скобках  выделение, которое дальше используем как $1

        root /var/www/hobby-work.ru;

        try_files $uri $uri/ /domains/$1/index.php$is_args$args; # проверка наличия файлов, каталогов

# $is_args = ? если есть аргументы и &quot;&quot; если нет, $args - аргументы



        # обработка php файлов при работе с livestreet

        location ~ \.php$ {

           internal; # обрабатывать только внутренние редиректы

           include        /etc/nginx/fastcgi_params;

           fastcgi_index  index.php;

           fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;

           fastcgi_pass   127.0.0.1:9000;

        }

    }



    #laravel

    location / {

        try_files $uri $uri/ /index.php$is_args$args;

        index index.php;

    }



    location ~ \.php$ {

    	internal;

        try_files $uri  /index.php =404;

        include        /etc/nginx/fastcgi_params;       

        fastcgi_index  index.php;

        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;

        fastcgi_pass   127.0.0.1:9000;

    }

}

[/plain]



<h2>Вне темы</h2>

Для корректной работы Livestreet придется в /domains/число/config/config.local.php задать параметр path.offset_request_url равный 2;  