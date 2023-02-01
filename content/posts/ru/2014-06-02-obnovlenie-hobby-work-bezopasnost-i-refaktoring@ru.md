---
$title@: obnovlenie-hobby-work-bezopasnost-i-refaktoring
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: obnovlenie-hobby-work-bezopasnost-i-refaktoring
$dates:
  published: 2014-06-02 12:48:32
---
Я начинаю работы по обновлению hobby-work.ru. В основном обновление посвящено безопасности севриса. Я сделал так, чтобы каждый пользователь имел доступ к своему аккаунту. Для этого пришлось написать простенький сокет сервер, который принимает и выполняет команды. Также я отрефакторил сам код сервиса, удалил на мой взгляд лишнее и унифицировал существующее. 

В планах выгрузить все это дело на production и запустить для тестов. Планирую сделать это до выходных.