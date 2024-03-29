---
$title@: o-serverless-tehnologii-dlya-chatbotov
author@: Виктор Жарина
description: О применении serverless для создания чат ботов
keywords: чатбот,serverless,селектел,twitch
image: /static/images/default.png
slugRu: o-serverless-tehnologii-dlya-chatbotov
$dates:
  published: 2022-01-11 17:57:51
---
<h3>Disclaimer</h3>

Я буду использовать в статье неточные термины. Вы уж простите меня за это. Моя цель зафиксировать полученные знания в моменте и уже потом систематизировать.

<h3>Коротко о serverless</h3>


Без больших подробностей опишу суть технологии. Напишу историю и проблематику и дальше грубо о технологии. Раньше, чтобы взаимодействовать с клиентом (браузером) нужен был сервер. Это отдельный компьютер или виртуальная машина плюс прграмма, которая принимала запросы от браузера и возвращала ответы. Некоторое время машина простаивает, характеристики фиксированные, а оплата по месячно. То есть по сути это аренда оборудования. Раньше это была реальная железяка, потом виртуальная машина, а потом появилась идея serverless. Serverless это технология, которая позволяет выполнить вычисления по запросу. Таким запросом может быть внешний http-запрос. При получении запроса некая система запускает контейнер (или берет из пула), передает данные в контейнер, в предопределенную точку входа (функцию) и ожидает завершения выполнения. Таким образом можно организовать оплату за потреблённое время и память, указать лимиты, выбрать среду, в которой будет выполняться код (Python, NodeJs) и т.д. То есть идея в том, чтобы платить за потребленные ресурсы.

<h3>Варианты использования serverless</h3>

С помощью serverless можно отправлять уведомления, письма, сохранять данные во внешнее хранилище, организовать чат, проксировать запрос и т.д. Удобство в том, что в отличие от сервера программисту не нужно арендовать сервер, устанавливать операционную систему, настраивать окружение. Нужно написать код и выгрузить его в serverless окружение.
Моя старшая дочь попросила меня помочь ей настроить чат и рассказать что к чему, ввести в курс дела. Задача была в том, чтобы запустить телеграм-бота. Я решил попробовать новую технологию в деле.

<h3>Такие разные Чат-боты</h3>

Чат-боты это программы, которые взаимодействуют с участниками чата. Участники отправляют команды, а чат-бот их выполняет. Платформы, которые предоставляют доступ имеют свои особенности для создания чат-ботов, и далее я расскажу об особенностях telegram и twitch. Оказалось, что не для всех платформ подходит serverless.

Telegram имеет богатую функциональность и умеет отправлять данные при наступлении определенных событий по пути, который вы указали. Это так называемый webhook. Одним из таких событий может быть отправка текста пользователем при общении с ботом. После отправки текста telegram отправит данные по указанному пути, который в нашем случае будет инициировать запуск serverless функции. Для примера мы пишем !hello, а бот должен ответить hello Viktor! С точки зрения реализации все должно быть просто, так как на входе функции мы имеем данные сообщения, данные пользователя и можем в ответ отправить строку hello {username}, где username имя пользователя.

Но не все платформы работают таким способом. Я имею в виду, что не все платформы отправляют данные по указанному пути при наступлении определённого события. Разберем это на примере Twitch. Twitch не отправляет сообщения на указанный wehbook как в примере с telegram. Twitch имеет собственные сервера, которые обслуживают все чаты twitch. Боты, которых вы создаете должны подключиться к их серверам и обмениваться с ним сообщениями. Это несколько меняет подход и ставит под сомнение использование serverless. Давайте объясню процесс создания чат-бота для twitch.

Создаете отдельный аккаунт на твич для чат-бота (далее chatbot)
Создаете приложение для chatbot, в котором задаете права на чтение и изменение данных чата и адрес, на которые будет перенаправлен пользователь после того, как авторизуется.
Получаете токен для пользователя chatbot
Пишете приложение-чат, в котором для подключения к серверам twitch используете реквизиты полученные ранее
Запускаете приложение и общаетесь.

Ваш собственный chatbot работает до тех пор, пока запущено приложение и пока работают сервера twitch. Общение вашего chatbot и серверов twitch идет по протоколу IRC (да-да тот самый, когда общались в мирке, если кто помнит). Таким образом непонятно как можно использовать serverless технологию для построения chatbot для twitch. Работоспособным является вариант аренды виртуальной машины или собственного компьютера для запуска приложения.
В случае с serverless после того, как функция выполнится, контейнер, в котором она выполнялась будет остановлен, что собственно совсем не то, что нам нужно.

Таким образом telegram позволяет обработать сообщение и вернуть ответ и использование serverless тут отлично подходит. Twitch требует постоянно запущенного приложения которое обменивается данными с серверами twitch. Такие разные chatbotы.