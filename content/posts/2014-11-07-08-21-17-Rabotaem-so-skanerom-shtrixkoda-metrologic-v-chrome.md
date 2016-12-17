---
$title@: Rabotaem-so-skanerom-shtrixkoda-metrologic-v-chrome
author@: Viktor Zharina
$order: 197
$dates:
  published: 2014-11-07 08:21:17
---
Задача: сканировать штрихкод и ввести данные в поле html страницы.



Мне нужно было сканировать штрихкод и ввести данные в поле, после этого отправить запрос на сервер и обработать данные и вернуть ответ. Исходными данными являются Elementary (fork ubuntu), Chrome, Barcode Scanner Metrologic MS9520.



Если коротко, то решение примерно такое: нужно скачать утилиту crikey, которая умеет эмулировать нажатия клавиатуры.



делай раз:

[code]

sudo apt-get install libx11-dev x11proto-xext-dev libxt-dev libxtst-dev

cd /usr/src

sudo wget http://www.shallowsky.com/software/crikey/crikey-0.8.3.tar.gz

sudo tar zxvf crikey-0.8.3.tar.gz

cd crikey-0.8.3

sudo make

sudo make install

[/code]

Далее поставить picocom - minimal dumb-terminal emulation program. 

Делай два

[code]

sudo apt-get install picocom

[/code]

<!--more-->





Далее сделать так, чтобы данные с терминала перенеправлялись в браузер. 

Делай три:

[code]

google-chrome |crikey -i

sudo picocom /dev/ttyUSB0|crikey -i -t

[/code]



При этом /dev/ttyUSB0 - это ваш сканер - для упрощения задачи можно поставить права 666 на  /dev/ttyUSB0.



Таким образом все данные полученные из сканера будут перенаправлены в браузер. Остается лишьт настроить автофокус текстового поля и обработку данных.



