---
$title@: plagin-redactor-dlya-livestreet
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: plagin-redactor-dlya-livestreet
$dates:
  published: 2012-05-27 12:55:44
---
<h1>Требования</h1>

Версия LS: 0.5.1

Версия плагина: 0.0.1

Версия редактора: 7.6.0

Работа плагина проверена в следующих браузерах:

<ul>

	<li>Chrome latest;</li>

	<li>Firefox 13;</li>

	<li>IE7 и выше.</li>

</ul>

Работа плагина проверена в следующих шаблонах:

<ul>

	<li>Street Spirit;</li>

	<li>New Jquery.</li>

</ul>

<h1>Установка плагина</h1>

Стандартная. Скопируйте каталог imperavi в каталог plugins LiveStreet и активируйте плагин по следующему пути http://ваш_сайт/admin/plugins

<!--more-->

<h1>Описание плагина</h1>

Плагин заменяет стандартный редактор LiveStreet на редактор Redactor версии 7.6.0. Распространяется платно.



Плагин имеет возможность настройки следующих параметров:

<ul>

	<li>пути для загружаемых файлов;</li>

	<li>разрешенные типы файлов;</li>

	<li>максимально допустимые размеры разрешенных типов файлов;</li>

	<li>виды топиков, в которых производить замену стандартного редактора.</li>

</ul>

<h1>Документация</h1>

Плагин состоит из:

<ul>

<li>дистрибутива редактора (/imperavi/templates/skin/default/js);</li>

<li>файла-скрипта, выполняющего замену стандартного редактора LS (/imperavi/templates/skin/default/js/replace.js);</li>

<li>файлов обеспечивающих взаимодействие с LS.</li>

</ul>



<h1>Настройка редактора</h1>

Согласно документации http://redactorjs.com/ru/docs/



<h1>Файлы, обеспечивающие взаимодействие с LS</h1>

/imperavi/config/config.php

Конфигурационный файл для настройки плагина.



/imperavi/classes/actions/ActionImperavi.class.php

Обеспечивает выполнение загрузки файлов в соответствии с файлом конфигурации config.php.



/classes/hooks/HookImperavi.class.php

Hook обеспечивающий подключение js-библиотек(/imperavi/templates/skin/default/js/redactor, /imperavi/templates/skin/default/js/replace.js)

и css файлов редактора redactor.



/imperavi/templates/language/russian.php

Языковой файл, содержащий описание сообщений об ошибках, возникающих при загрузке файлов.



/imperavi/templates/skin/default/actions/ActionImperavi/upload_files.tpl

Шаблон, обеспечивающий отображение сообщений об ошибках либо ссылки на файл после завершения загрузки.



<h1>Доп. информация</h1>

Редактор redactor начиная с версии 7.6.2 распространяется под новой платной лицензией. Таким образом получение обновлений будет доступно только после его покупки. До момента покупки версия редактора, используемая в плагине будет 7.6.0.



<h1>Проблемы и их решения</h1>

1. Границы ячеек в таблице не отображаются.

Это настройки стиля шаблона. Зайдите в /templates/skin/new-jquery/css/topic.css и добавьте строку .topic .content td { border: 1px solid black;}

Пример приведен для шаблона new-jquery.



2. Редактор не работает в браузере {ИмяБраузера}.

Обновите браузер до самой последней версии.