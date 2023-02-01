---
$title@: izuchaem-ptk-kontar-izuchaem-osnovy-s-pomoshhyu-stenda
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: izuchaem-ptk-kontar-izuchaem-osnovy-s-pomoshhyu-stenda
$dates:
  published: 2013-02-06 04:36:30
---
В данном посте я постараюсь очень кратко и по делу описать то, как начать работать с ПТК "Контар". Для изучения основ был собран стенд. Схема подключения приведена на рисунке. Заранее замечу, что все утилиты и документацию вы можете скачать с mzta.ru. Описание утилит и программ приведено в другом посте.



Также я не буду сильно детализировать и сильно углубляться в те моменты, которые я считаю понятными и с которыми начинающий может самостоятельно разобраться.



[caption id="attachment_537" align="aligncenter" width="300"]<a href="http://viktor.zharina.info/wp-content/uploads/2013/02/stend.png"><img class="size-medium wp-image-537" alt="Схема стенда" src="http://viktor.zharina.info/wp-content/uploads/2013/02/stend-300x111.png" width="300" height="111" /></a> Схема стенда[/caption]



В моем распоряжении были следующие устройства: контроллер MC8 (MC8.1011212), модуль MR20.3, модуль ME20, модуль MA8.3, реле, термометр сопротивления PT1000, кнопка с возвратом.



Решим следующую простую задачку: при нажатии на кнопку должны замкнуться обмотки реле. При отжатии кнопки обмотки реле должны разомкнуться.

<!--more-->

Для начала создадим в "Конграф" простейший алгоритм, который будет решать поставленную задачу. Добавляем на рабочую область контроллер МС8. Указываем в свойствах контроллера то, что он Master и имеет сетевой номер равный 1.



[caption id="attachment_538" align="aligncenter" width="300"]<a href="http://viktor.zharina.info/wp-content/uploads/2013/02/1.png"><img class="size-medium wp-image-538" alt="Контроллер МС8" src="http://viktor.zharina.info/wp-content/uploads/2013/02/1-300x112.png" width="300" height="112" /></a> Контроллер МС8[/caption]



Далее заходим в контроллер и соединяем вход DI1 с выходом DO7. Таким образом сигнал с входа будет оттранслирован на выход. Далее, нужно настроить параметры компилятора.



[caption id="attachment_539" align="aligncenter" width="300"]<a href="http://viktor.zharina.info/wp-content/uploads/2013/02/compiler_params.png"><img class="size-medium wp-image-539" alt="Параметры компилятора" src="http://viktor.zharina.info/wp-content/uploads/2013/02/compiler_params-300x190.png" width="300" height="190" /></a> Параметры компилятора[/caption]



После того, как настройки сделаны компилируем проект (жмем CTRL+T).  После компиляции в каталоге проекта появятся каталоги соответствующие сетевым номерам устройств. В моем случае для MC8 это каталог "1". В каталоге "1" будет bin файл, который и является результатом компиляции. Этот файл необходимо загрузить в контроллер.



Загрузка bin файла выполняется в программе "Консоль". Надо выбрать контроллер из списка (о том, как сделать так, чтобы контроллер появился в списке я написал в <a href="http://viktor.zharina.info/poleznoe/izuchaem-ptk-kontar-rabotaet-s-kontrollerom-mc8-1-cherez-konsol/" title="Изучаем ПТК Контар. Работает с контроллером MC8.1 через «Консоль»" target="_blank">другом посте</a>) и нажать кнопку "Загрузчик".  Далее нажать "Поиск" и выбрать наш bin-файл из каталога "1".  Далее нажать "Загрузить". Начнется процесс загрузки и после ее окончания контроллер запуститься с новой программой.



[caption id="attachment_544" align="aligncenter" width="596"]<img class="size-full wp-image-544" alt="Загрузка программы через Консоль" src="http://viktor.zharina.info/wp-content/uploads/2013/02/Konsole.png" width="596" height="471" /> Загрузка программы через Консоль[/caption]



Теперь убедимся в том, что программа работает. Для этого нажмем на физическую кнопку и убедимся в том, что реле замкнулось.