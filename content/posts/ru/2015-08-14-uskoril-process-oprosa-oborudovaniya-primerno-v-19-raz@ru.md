---
$title@: uskoril-process-oprosa-oborudovaniya-primerno-v-19-raz
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: uskoril-process-oprosa-oborudovaniya-primerno-v-19-raz
$dates:
  published: 2015-08-14 08:05:32
---
Стояла задача автоматизировать процесс определения проброшен ли влан на заданном оборудовании или нет. Оборудования много и оно подключено по схеме звезда. У меня было 19 устройств, которые являются головными, то есть стоят на вершине иерархической цепочки. Мне предстояло поличуть все нижестоящие устройства и проверить прописан ли у них влан. 

Задачу в первом приближении я решил последовательно. Берем первое устройство, получаем список нижестоящих, бежим по списку, проверяем вланы. По такой схеме у меня на опрос всех устройств уходило часов 19-20. Основной затык был из-за больших задержек при работе с оборудованием (от 2 - 4 сеукунд).

Я задумался о том, как можно ускорить это процесс. У каждого предрута своя цепочка оборудования, то есть оборудование на одном предуруте независит от оборудования на другом. То есть я могу совершенно легко распараллелить обработку. Нужно всего навсего запустить столько процессов, сколько у меня предрутов. Предурутов 19. Теперь осталось решить как создать несколько процессов.

От браузера запрос приходит на apache, apache передает запрос php, php отдает ответ apache, apache отдает ответ в браузер.

Грубо говоря сколько запросов столько и процессов. Со скрипта я убрал ограничение на время выполнения (set_time_limit = 0) в браузере открыл 19 вкладок и внес в них ссылки на обработчики. В каждой вкладке своя ссылка, в которой содержатся данные предрута. Последовательно нажал Enter в каждой вкладке и вуаля я запустил параллельно 19 обработчиков. Примерно через час я получил результаты.