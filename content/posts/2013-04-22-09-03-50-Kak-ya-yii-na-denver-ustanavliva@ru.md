---
$title@: Kak-ya-yii-na-denver-ustanavlival
author@: Viktor Zharina
$order: 71
$dates:
  published: 2013-04-22 09:03:50
---
Для начала о версиях ПО:

Yii - 1.1.3

denwer - Денвер-3 2010-11-07

ОС - Win7 домашняя базовая



Все делал по инструкции с http://yiiframework.ru. И при выполнении инструкции получал проблемы. Хочу поделиться решениями.



<code>% YiiRoot/framework/yiic webapp WebRoot/testdrive</code> - не заработало. Прежде чем выполнять команду добавьте путь в системную переменную. Мой компьютер - свойства - доп. параметры системы - переменные среды - Path = blablabla;Z:\usr\local\php5\.



<code>http://hostname/testdrive/index.php?r=gii</code> - не заработало. Ругался 'You are not allowed to access this page'. Помогло редактирование файла main.php

<code>	

'modules'=>array(

		// uncomment the following to enable the Gii tool

		'gii'=>array(

			'class'=>'system.gii.GiiModule',

			'password'=>'ghtdtl',

			// If removed, Gii defaults to localhost only. Edit carefully to taste.

			'ipFilters'=>array(<strong>$_SERVER['REMOTE_ADDR']</strong>),

		),

	),

</code>



