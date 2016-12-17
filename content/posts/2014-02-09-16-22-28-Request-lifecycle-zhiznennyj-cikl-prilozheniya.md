---
$title@: Request-lifecycle-zhiznennyj-cikl-prilozheniya
author@: Viktor Zharina
$order: 140
$dates:
  published: 2014-02-09 16:22:28
---
<h1>Жизненный цикл приложения</h1>

<h2>Введение</h2>

При использовании различных вещей чувствуешь твердую почву под ногами, когда понимаешь как эти вещи устроены. И разработка программного обеспечения не исключение. Главной целью данного документа является дать хороший, высокоуровневый взгляд на то, как устроен Laravel изнутри. В процессе более глубого изучения фреймворка большая часть будет все меньше напоминать магию и вы будете становится более уверенными при создании вашего приложения. В дополнении к высокоуровневому взгляду жизеннного цикла приложения документ предоставит информацию о "старт" файлах и событиях приложения. 

Если вы не понимаете все эти термины не теряйтесь. Просто попробуйте разобраться в том, что происходит и ваши знания вырастут при изучении других разделов документации.

<!--more-->

<h2>Жизненный цикл приложения</h2>



All requests into your application are directed through the public/index.php script. When using Apache, the .htaccess file that ships with Laravel handles the passing of all requests to index.php. From here, Laravel begins the process of handling the requests and returning a response to the client. Getting a general idea for the Laravel bootstrap process will be useful, so we'll cover that now!



By far, the most important concept to grasp when learning about Laravel's bootstrap process is Service Providers. You can find a list of service providers by opening your app/config/app.php configuration file and finding the providers array. These providers serve as the primary bootstrapping mechanism for Laravel. But, before we dig into service providers, let's go back to index.php. After a request enters your index.php file, the bootstrap/start.php file will be loaded. This file creates the new Laravel Application object, which also serves as an IoC container.



After creating the Application object, a few project paths will be set and environment detection will be performed. Then, an internal Laravel bootstrap script will be called. This file lives deep within the Laravel source, and sets a few more settings based on your configuration files, such as timezone, error reporting, etc. But, in addition to setting these rather trivial configuration options, it also does something very important: registers all of the service providers configured for your application.



Simple service providers only have one method: register. This register method is called when the service provider is registered with the application object via the application's own register method. Within this method, service providers register things with the IoC container. Essentially, each service provider binds one or more closures into the container, which allows you to access those bound services within your application. So, for example, the QueueServiceProvider registers closures that resolve the various Queue related classes. Of course, service providers may be used for any bootstrapping task, not just registering things with the IoC container. A service provider may register event listeners, view composers, Artisan commands, and more.



After all of the service providers have been registered, your app/start files will be loaded. Lastly, your app/routes.php file will be loaded. Once your routes.php file has been loaded, the Request object is sent to the application so that it may be dispatched to a route.



So, let's summarize:



    Request enters public/index.php file.

    bootstrap/start.php file creates Application and detects environment.

    Internal framework/start.php file configures settings and loads service providers.

    Application app/start files are loaded.

    Application app/routes.php file is loaded.

    Request object sent to Application, which returns Response object.

    Response object sent back to client.



Now that you have a good idea of how a request to a Laravel application is handled, let's take a closer look at "start" files!



<a href="http://laravel.com/docs/lifecycle">Источник</a> 