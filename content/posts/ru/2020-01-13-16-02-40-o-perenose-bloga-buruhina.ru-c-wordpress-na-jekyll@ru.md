---
author@: Viktor Zharina
description: Перенос блога buruhina.ru с wordpress на jekyll 4.0
keywords: wordpress, jekyll, migration, cloudflare
$order: 319
$dates:
  published: 13.01.2020 16:02:40
$title@: o-perenose-bloga-buruhina.ru-c-wordpress-na-jekyll
image: https://blog.webjeda.com/assets/thumbs/wordpress-to-jekyll-migration-tutorial.png
slugRu: o-perenose-bloga-buruhina.ru-c-wordpress-na-jekyll
---


Начать следует с описания проблемы. Проблем не было. Было желание отказаться от wordpress из-за монструозности и перейти на что-то легковесное и сэкономить денег.
Уже более 6 лет арендовал у digitalocean droplet, на котором крутится wordpress. В самом начале платил за дроплет 50-180. Потом доллар подорожал в 2 раза и цена выросла до 360 рублей в месяц. А потом добавили VAT равный 20% и это составило еще один доллар. То есть 400 - 440 рублей. Я уже знал про static site generators перенести блог с wordpress.

### О static site generators.

Пользователь создает контент на локальной машине, как правило .md файл определенной структурой (заголовок + контент), далее запускает команду (обычно build) и в результате получает набор html, которые выгружает на сервер и далее раздает как статику. Не знаю почему, но тогда для персонального блога я не выбрал jekyll, а выбрал [grow](https://grow.io/). Блог buruhina.ru я переносил на [jekyll](https://jekyllrb.com/).

Поскольку сайт все есть статика, то на сайте нет комментариев в привичном смысле. Это решается по-другому. Скажем через сторонний сервис вроде [disqus](https://disqus.com/) или [commento](https://commento.io/).

### О новом месте
У Amazon есть S3, у Selectel это облачное хранилище. Платишь только за место и трафик. Облачное хранилище хранит статику и предоставляет доступ из интернета. Поддерживает сторонние домены, позволяет задать ограничения для контейнеров, дает API для работы с хранилищем. Я выбрал Selectel. Более подробно об облачном хранилище [здесь](https://kb.selectel.ru/23136007.html)

### Миграция - План действий
1. Локально установить jekyll на Linux/Windows.
2. Выбрать тему
3. Мигрировать контент из Wordpress в Jekyll
4. Понять проблемы после переноса
5. Решить проблемы из п.4.
6. Выгрузить новый контент в облачное храннилище вручную
7. Написать скрипт для автоматической выгрузки в облачное хранилище
8. Перенастроить DNS на Selectel.


### Миграция - Начало
Локально установить jekyll не составило труда. Я работал с версией 4.0 и делал все как написано в [официальной документации](https://jekyllrb.com/docs/installation/).

Первой темой выбрал [Hyde](https://hyde.getpoole.com/) с небольшими изменениями для мобильной версии.

Чтобы перенести контент с wordpress на jekyll нужно трансформировать данные wordpress (posts, pages, category, tags) в сущности jekyll. Это хорошо делает плагин [Jekyll Export](https://wordpress.org/plugins/jekyll-exporter/) by Ben Balter. На выходе получается архив со всем необходимым. 

Проблемы после переноса возникли из-за того, что имена файлов изображений были на русском языке. Решением является транслитерация в латиницу.

Также пришлось реализовать "Список категорий" в sidebar, установить плагины: jekyll-feed, jekyll-admin, jekyll-gist и jekyll-paginate, сделать сворачивающееся меню для мобильной версии.

### Миграция - Выгрузка в облако
Все  просто. Выгрузить вручную данные не составило труда. У Selectel есть личный кабинет, там можно загрузить архив tar.gz и указать системе распаковать архив после выгрузки. 

Автоматизировать процесс тоже не сложно. Ниже скрипт. Переменные SEL_* и  BLOG_BUILD_PATH  задаем через перменные окружения.

```
#!/bin/bash
echo 'Fetching updates'
git pull

echo 'Step 1'
git add . && git commit -am "deploy arg from command line" && git push

JEKYLL_ENV=production jekyll build -q

echo 'Step 2'
cd _site && tar -czf b.tar.gz * --exclude=./*.gz

echo 'Step 3'
shopt -s extglob
API_AUTH_URL=https://api.selcdn.ru/auth/v1.0
while IFS=':' read -r key value; do
    value=${value##+([[:space:]])}; value=${value%%+([[:space:]])}

    case "$key" in
        x-auth-token*) SEL_TOKEN="$value"
          ;;
     esac
done < <(curl -i -s $API_AUTH_URL -H "X-Auth-User:${SEL_USER}" -H "X-Auth-Key:${SEL_PASS}")

echo "Step 4 = $SEL_TOKEN"
API_URL="https://api.selcdn.ru/v1/SEL_${SEL_ACCOUNT}/${SEL_CONTAINER}/?extract-archive=tar.gz"
curl -i -XPUT -s API_URL -H "X-Auth-Token: ${SEL_TOKEN}" -T $BLOG_BUILD_PATH/b.tar.gz

 echo 'Step 5'
 rm -rf $BLOG_BUILD_PATH

echo "Finish. Press any key."
read
```
Скрипт работает так: получает последние изменения из репозитория, фиксирует текущие изменения, создает архив tar.gz, запрашивает token из api selectel, загружает архив с указанием "Распаковать после завершения", удаляет старый build. 

Скрипт не идеален, но суть ясна.

### После переезда
Выяснилось, что сайт был на http и нужно настроить редирект на https. Я уже знал как это сделать, так как тренировался на персональном блоге и уже "съел собаку". Использовал cloudflare и убил сразу двух зайцев: https и редиректы. Делать редиректы - это не обязанность хранилища, но я об этом сразу не подумал. Следующей проблемой были старые ссылки в google. Формат старых ссылок был такой: http://buruhina.ru/{category}/{title}, а в облачном хранилище лежали https://buruhina.ru/{category}/{title}.html. Можно было передeлать структуру и хранить index.html в каталоге /root/category/title/index.html, но я добавил редирект в cloudflare.

### Заключение
В целом решение для "нищебродов" вполне годное. Работает шустро. Я доволен. Можете заценить https://buruhina.ru/. Потребление в рублях за месяц составило примерно 2 рубля. После 420 рублей в месяц. 


Надо разобраться с картинками и переименовать в латиницу и далее поискать сервис для загрузки презентаций, так как в этом есть нужда. 

Еще рассматриваю вариант визуального редактора, вместо чистого md, для оформления контента. Пока вариант с чистым md тоже работает. jekyll admin кажется рабочим вариантом, позже посмотрю на него. 

Если вы собираетесь выбирать статический генератор сайтов, то [ресурс](https://www.staticgen.com/) упростит вам жизнь. В конце концов не jekyllом единым.


### Чтиво
1. https://artslab.info/jekyll/wordpress-to-jekyll/
2. https://digitaldrummerj.me/blogging-on-github-part-12-editing-locally/
3. https://www.kobzarev.com/wordpress/wordpress-to-jekyll/
4. https://reyhan.org/2018/02/928
5. https://ben.balter.com/wordpress-to-jekyll-exporter/
6. https://medium.com/@bennettscience/moving-from-wordpress-to-jekyll-36a3413dd7ec