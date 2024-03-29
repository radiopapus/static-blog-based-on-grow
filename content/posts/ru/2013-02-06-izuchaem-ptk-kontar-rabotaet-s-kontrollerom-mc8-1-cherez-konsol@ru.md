---
$title@: izuchaem-ptk-kontar-rabotaet-s-kontrollerom-mc8-1-cherez-konsol
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: izuchaem-ptk-kontar-rabotaet-s-kontrollerom-mc8-1-cherez-konsol
$dates:
  published: 2013-02-06 04:30:13
---
Нашей задачей на сегодня будет подключение к контроллеру МС8.1 с помощью программы "Консоль". Консоль это сервисная программа, которая выполняет следующие функции:

<ul>

	<li>настройка оборудования комплекса "Контар";</li>

	<li>сетевые настройки;</li>

	<li>чтение/ запись переменных программы контроллера;</li>

	<li>загрузка программы (bin-файлов) в контроллер;</li>

	<li>настройка планировщика;</li>

	<li>просмотр внутренних архивов контроллеров;</li>

	<li>задание даты и времени контроллера;</li>

	<li>и другие функции, о которых вы можете прочесть из официальной документации.</li>

</ul>

Вообще подключение к контроллеру возможно выполнить несколькими способами:

<ol>

	<li>через RS232 (разъем RJ12), расположенный на субмодуле;</li>

	<li>через Ethernet, расположенный на субмодуле;</li>

</ol>

В данном посте я опишу второй способ. Для подключения понадобится кабель типа "витая пара" обжатый как cross (подключение компьютер/компьютер). Один конец кабеля надо воткнуть к контроллер, второй в компьютер или ноутбук.

Теперь необходимо подключиться к контроллеру из "Консоли". Для этого нужно знать ip адрес контроллера. Заводской ip адрес = 192.168.30.239. Однако есть более универсальный способ узнать ip адрес контроллера. Для этого нужно воспользоваться сканером сети. Я использовал lanscope. Очень простая утилита, которая позволяет сканировать сеть в заданном диапазоне. То есть задаете диапазон от 192.168.0.0 до 192.168.255.255 и начинаете сканировать. Среди просканированных устройств окажется контроллер с указанием ip адреса. К этому адресу и нужно подключаться.

После того, как ip адрес контроллера стал известен необходимо задать его в Консоли. Для этого жмем "Показать свойства".

Далее в поле "IP объекта" ввести ip адрес контроллера, а в поле "Часовой пояс" задать часовой пояс. Далее ввести закрыть. После этого нужно выполнить обновление состава сети.

Далее начнется процесс обновления состава сети и после его завершения контроллер появится в списке. Теперь можно работать с контроллером: попробовать вручную управлять выходами, загрузить программу из Кограф и т.д.