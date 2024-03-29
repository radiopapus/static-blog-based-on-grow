---
$title@: opit-uchastiya-v-teamlab-v-roli-nastavnika
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: opit-uchastiya-v-teamlab-v-roli-nastavnika
$dates:
  published: 2017-12-17 15:21:30
---
<i>Данная статья будет обзорной, без особых деталей разработки ПО. Моей целью является познакомить читателя с проектом Тимлаб и опытом, который я приобрел в роли наставника, пока участвовал в этом проекте</i>

## Введение
В Томске сложно найти разработчиков, даже среди казалось бы такого популярного стека как php + mysql. Собственно, идея участия возникла из-за проблем с нехваткой кадров, и мы  (компания) решили пойти на эксперимент и попробовать найти сотрудников среди людей с небольшим опытом. Как-то на кухне был поднят вопрос о том, кто и что знает про проект Teamlab. Нашлись те, кто в нем участвовал, поделились, обсудили. Я сообщил о том, что готов принять участие. Я хотел получить:

1. Ответ на вопрос: "Можно ли сделать рабочий продукт с людьми, у которых мало/нет опыта?";

2. Премию;

3. Прокачать soft-skills;

4. Познакомить стажеров с процессом разработки ПО;

## О Teamlab
![Teamlab](https://static.tildacdn.com/tild3132-3833-4734-b536-656363333232/F7E90886A45108B1AC22884403DC5968EA97DE45605AE73252pimgpsh_fullsize_distr.png)
Teamlab это проект, который дает стажерам возможность в течение 2 месяцев, работая не менее 20 часов в неделю, выполнить проект под руководством наставника с целью дальнейшего трудоустройства. Стажеры получают опыт, знакомятся с процессом разработки. Для компании это выгодно тем, что стажеры приходят не нулевые, а, так скажем, "подогретые" минимальными знаниями. По итогам есть выбор из уже подготовленных ребят.

## Краткое описание проекта
Проектом для стажировки был выбран "Онлайн-консультант". Вы все видели их на сайтах, я не буду подробно на этом останавливаться. Виджет в углу, жмешь на иконку, открывается окно чата, чатишься с оператором/ботом. Их тысячи, но я подчеркну, что смысл не в том, чтобы создать что-то новое, а в том, чтобы подготовить стажеров к реальной работе.

## Презентация
Для того, чтобы привлечь людей на стажировку я подготовил и выступил с презентацией в трёх крупных университетах Томска. Знали бы вы как тяжело выступать перед аудиторией. Это примерно также, как знакомиться с людьми, только людей сразу несколько десятков. Ничего кроме улыбки та презентация и выступление у меня не вызывают. Сейчас я бы кое-что изменил.

## Результаты и отбор
После выступления начали поступать заявки на участие. Из примерно 150 - 180 человек, посетивших мое выступление, на участие в проекте подали заявки шесть участников. Из них мы выбрали пятерых. У ребят был разный уровень подготовки. Один специализировался на фронтенде и хотел развиваться в этом направлении. Второй имел опыт разработки на PHP + Mysql. Остальные ребята пришли почти нулевыми, не считая лабораторных работ в университете.

## Требования Teamlab
Прежде чем мы приступили к проекту меня и ребят ждали 2 выходных по 8 часов интенсива: знакомство, лекции, практика по системе контроля версий, аналитике, тестированию и т.д.
Teamlab требовал ежедевные отчеты о выполеннной работе. Участие всей команды в пятничных встречах, где остальные участники рассказывали о прогрессе и обсуждали проекты друг друга.
Teamlab давал право выгонять участников из проекта. И также мы должны были выступить с презентацией на demo-day. Это публичное мероприятие, где стажеры демонстрируют результаты своей работы.

## Teamlab будни- Начало
К началу проекта у нас уже был развернута инфраструктура: gitlab, redmine, завели почты, аккаунты и т.д. За исключением некоторых технических недочетов, все было отлично. Мы очно встречались по средам и, спустя неделю после начала, пришли к тому, что нужны ежедневные онлайн-встречи. Пришли к тому, что это будет 21:30. На них мы обсуждали успехи и проблемы за день. Также на неделю назначали менеджера, который отсылал отчёты и выступал по пятницам с докладом перед остальными участниками.

Первые две недели мы раскачивались и я объяснял как работает система управления задачами, как писать отчеты, как брать задачу, сдавать задачу, как работать с git, что я жду и хочу увидеть в отчёте по выполненной задаче и т.д. В общем чисто организация.

Да, кстати, у нас было ТЗ. На основе него мы набросали компоненты системы, обсудили архитектуру и технологии.
Это один из первых вариантов
![До](/static/images/schema_before.jpeg)

А этот вариант уже был на demo-day.
![После](/static/images/schema_after.jpeg)

## Teamlab будни - ход проекта
![Ход проекта](/static/images/laptop.png)
Создали задачи в redmine по названиям компонентов. Это были крупные задачи. Скажем "Виджет", "Приложение оператора", "Сервер обмена сообщениями" и т.д. Потом создали подзадачи, более мелкие, скажем "Прототип виджета", "Вёрстка виджета", "установка nodejs", "алгоритм обмена сообщениями" и т.д. Потом ещё более мелкие, вроде "Вёрстка layout для админки", "Вёрстка фильтра истории диалогов" и т.д. В итоге получилось около 60 задач (к концу проекта их число увеличилось до 94 из-за мелких задач по исправлению недочетов).

Спустя три недели у нас был прогресс по всем модулям, кроме сервера обмена сообщениями. Не буду останавливаться на этом, так как это уведет в сторону мое повествование, но скажу, что я исключил из проекта одного из участников.

Примерно за полторы недели до окончания у нас был сырой прототип. Кривая верстка, баги, не реализованные функции. Эти полторы недели мы допиливали проект.

## Demo day
На видео Дима - один из участников проекта.
<p>
  <div class="videoWrapper">
    <iframe width="560" height="315" src="https://www.youtube.com/embed/QBsaarwe3LU?ecver=1" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
  </div>
</p>
Вы можете зайти  и посмотреть то, что у нас получилось.

[Сайт](http://teamlab-srv.oft-e.com/)

[Админка](http://backend-php.teamlab-srv.oft-e.com/login)
admin/admin, operator1/operator, owner1/owner.

Приложение-оператора - зайдите в админку под оператором.

## Выводы

1. Сделать проект со студентами можно и даже нужно.

2. Код, скорее всего, будет низкого качества, так как времени мало. Рабочий прототип важнее красоты кода в данном конкретном проекте.

3. Потратил около 80 часов чистого времени за 2 месяца. Следует учитывать эту цифру в будущем;

4. Пришлось объяснять базовые вещи весь рабочий процесс.

5. Важно, чтобы все члены команды понимали то, что нужно сделать.

6. Не уверен, что мы справились бы, если бы все были без опыта. Дело в том, что опытные ребята делают задачи быстрее и сдают их, они попадают в основную ветку и остальные могут смотреть на примеры их работы и делать похожие задачи. В это время опытные ребята переключаются на новые задачи и делают их.

7. Проблемы интеграции всегда есть и будут. Интеграцию лучше делать очно.

8. Нужно быть готовым к исключению людей из проекта. Исключать людей из проекта психологически сложно.