---
$title@: process-razrabotki-servisa-arendy-plaginov-i-shablonov-hobby-work-ru-2
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: process-razrabotki-servisa-arendy-plaginov-i-shablonov-hobby-work-ru-2
$dates:
  published: 2014-04-06 16:37:07
---
Три категории машин: development, preProd, Production.

На development я разрабатываю, обычная такая грязная разработка. У меня даже вебсервер не настроен, так как использую php-ный. После того, как убедился в том, что все ок (а именно на локальной машине я больше всего тестирую), то грузим на preProd. preProd настроен максимально схоже с prod. На нем оттачиваем процедуру обновления и еще раз тестим. Далее заливаем на prod. Все махинации с файлами делаем через git (как бы я без него жил).

<img src="http://viktor.zharina.info/wp-content/uploads/2014/04/schema.jpg" alt="schema" width="553" height="216" class="aligncenter size-full wp-image-1517" />