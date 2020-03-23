---
$title@: testovaya-zapis-posle-transfera-domena-s-reg.ru-na-amazon
author@: Виктор Жарина
description: Домены в зоне .info оказался дешевле на amazon.com
keywords: domain, transfer, amamzon, reg.ru
$order: 321
image: /static/images/default.png
slugRu: testovaya-zapis-posle-transfera-domena-s-reg.ru-na-amazon
$dates:
  published: 23.03.2020 20:43:12
---


Это тестовая запись, что проверить выдачу. Опишу немного схему и архитектуру. Мой блог это чистая статика. Все файлы размещены в облачном хранилище селектела. Раньше домен был в reg.ru. Я решил перенести
его на амазон так это дешевле. Даже несмотря на соотношение рубль-доллар. Перед облачным хранилищем работает cloudflare, который много чего умеет, но я его использую как прокси-кеш. Он умеет редиректы и я это использую.
После переноса домена по неведомым для меня причинам по адресу viktor.zharina.info не отображается index.html, отображается предыдущая кешированная страница. При этом если спросить viktor.zharina.info/index.html, то все
ок. Я добавил редирект и хочу проверить, что все работает.

