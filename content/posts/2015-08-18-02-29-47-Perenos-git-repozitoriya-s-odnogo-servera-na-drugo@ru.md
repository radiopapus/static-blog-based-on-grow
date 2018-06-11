---
$title@: Perenos-git-repozitoriya-s-odnogo-servera-na-drugoj
author@: Viktor Zharina
$order: 230
$dates:
  published: 2015-08-18 02:29:47
---
1. Создаем пользователя git

sudo adduser git



2. Копируем старые ssh ключи со старого на новый через mc в каталог /home/git/.ssh



1. Клонируем репозиторий с первого севреа на второй

git clone --bare ssh://git@172.17.57.3:2812/home/git/db.nts.su/.git



2. Переходим в первый репозиторий и выполняем mirror-push для копирования всех веток и тегов в новый репозиторий

git push --mirror ssh://git@172.17.53.204:2812/home/git/db.nts.su/.git



3. Добавить ссылки на старый репозиторий

git config remote.origin.url ssh://git@172.17.53.204:2812/home/git/.git