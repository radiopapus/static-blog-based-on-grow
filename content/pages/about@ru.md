---
$title@: About
image: /static/images/viktorzharina.jpg
description: Резюме. Меня зовут Виктор Жарина. Я профессиональный веб-разработчик. Последние несколько лет пишу на Kotlin, до этого неколько лет писал на PHP.
$titles:
  nav@: About
$order: 1

---
<img src="/static/images/viktorzharina.jpg" alt="Фотография Виктора Жарины" class="about-photo" />
Меня зовут Виктор Жарина (ударение на и). Сейчас живу в Калининграде и удаленно работаю на Quantumsoft. Люблю Linux и GIT. Говорю, пишу и читаю на английском. Без вредных привычек. Последние несколько лет участвую в стартапе, который делает софт для людей, у которых диагностировали рак головного мозга. Пишу на Kotlin в связке со Spring Boot. Используем Graphql и пробуем Event Sourcing.

Образование: Томский Политехнический университет, Физико-технический факультет, Электроника и автоматика физических установок.

Контакты: viktor@zharina.info

Сертификаты: [Coursera Algorithmic Toolbox](/static/images/coursera/cert.png)

<div class="timeline">
    <div class="row">
        <div class="points">
            <div class="point">
                <div class="year"><script type="text/javascript">document.write(Math.abs(new Date().getUTCFullYear()));</script>
                </div>
                <div class="month">Т.М.</div>
            </div>
            <div class="line__solid" style="height:30vh"></div>
            <div class="point__interval">
                <div class="year">><script type="text/javascript">document.write(Math.abs(new Date().getUTCFullYear() - 2018));</script> лет</div>
            </div>
            <div class="line__solid" style="height:30vh"></div>
            <div class="point">
                <div class="year">2018</div>
                <div class="month">ОКТ</div>
            </div>
            <div class="line__grey"></div>
        </div>
        <div class="details">
            <div class="details__title--main"><a href="//www.quantumsoft.pro">Quantumsoft (Удаленно)</a></div>
            <div class="details__title--sub">Software Developer</div>
            <p class="details__text"><b>Заказчик</b></p>
            <p class="details__text"><a href="//navio.com">navio.com</a></p>
            <p class="details__text">Разработка программного обеспечения для людей, у которых диагностировали рак.</p> 
            <p class="details__text"><b>Чем занимался</b></p>
            <p class="details__text">Был одни из первых инженеров, кто начал разработку после собеседования с тех. директором</p>
            <p class="details__text">Переключился с PHP стэка на Kotlin</p>
            <p class="details__text">Общение с закачиками: еженедельные стречи, 1-1, планирование работ</p>
            <p class="details__text">Разработал сервис для хранения данных пациентов</p>
            <p class="details__text">Активно участвовал в разработке новых функций для приложений пациента и доктора.</p>
            <p class="details__text">Участвовал в применении подхода Event Sourcing и Kafka в качестве брокера сообщений.</p>
            <p class="details__text">Участвовал в разработке event-source фреймворка, созданного для проекта и написанного на Kotlin. Разработал специальный сервис (event-migrator), который отправлял сообщения в Kafka и далее они считывались на стороне приложения.</p>
            <p class="details__text">Предложил способ backup/restore на основе утилиты kafkacat для kafka.</p>
            <p class="details__text">Улучшил логгирование данных тем, что добавил correaltion id в события Kafka и считывание его и добавление в логи системы. Это повзолило быстрее идентифицировать проблему.</p>
            <p class="details__text">Разработал функцию поиска данных в elasticsearch по данным пациента.</p>
            <p class="details__text">Написал несколько библиотек, которые использовали внутри проекта, такие как шаблонизатор сообщений для sms и email и клиента для cognito для выполнения простых CRUD операций для user pool.</p>
            <p class="details__text">Периодически обновлял проекты для аудита системы и по соображениям безопасности</p>
            <p class="details__text"><b>Технологии</b></p>
            <p class="details__skill">GraphQL, REST, Kafka, Postgres, Redis, Elasticsearch, Spring Boot, Hibernate, Junit, Kotlin, Twilio, CopperCRM, Datadog, Sentry, Slack, Google Meet, Jira.</p>
        </div>
    </div>
    <div class="row">
        <div class="points">
            <div class="point">
                <div class="year">2018</div>
                <div class="month">ОКТ</div>
            </div>
            <div class="line__solid" style="height:15vh"></div>
            <div class="point__interval">
                <div class="year">>2 лет</div>
            </div>
            <div class="line__solid" style="height:15vh"></div>
            <div class="point">
                <div class="year">2016</div>
                <div class="month">ИЮН</div>
            </div>
            <div class="line__grey"></div>
        </div>
        <div class="details">
            <div class="details__title--main">ООО "Офти"</div>
            <div class="details__title--sub">Senior Web Developer</div>
            <p class="details__text"><b>Заказчик</b></p>
            <p class="details__text"><a href="//usedcarsni.com">usedcarsni.com</a></p> 
            <p class="details__text">Сервис в Северной Ирландии для покупки и продажи авто/мото транспорта</p> 
            <p class="details__text"><b>Чем занимался</b></p> 
            <p class="details__text">Разрабатывал функции и интеграции с сервисами: финансовый провайдер кредитных предложений Ivendi, фильтр и поиск автомобилей доступных в кредит, Trustpilot, Worldpay, Visitor Chat, 360 degree Image.</p>
            <p class="details__text">Участвовал в переходе с версии 5.3 до 7 для PHP.</p>
            <p class="details__text">Общение с заказчиками: планирование, задачи в Redmine.</p>
            <p class="details__text">Участвовал в разделении монолитного legacy приложения на отдельные сервисы.</p>
            <p class="details__text">Участвовал во внутреннем проекте как teamlead и организовал разработку просто чата с учатием Junior разработчиков.</p>
            <p class="details__text"><b>Технологии</b></p>
            <p class="details__skill">PHP, Mysql, Redis, Sphinx, Redmine, GIT, Ivendi, Trustpilot, Worldpay.</p>
        </div>
    </div>
    <div class="row">
        <div class="points">
            <div class="point">
                <div class="year">2016</div>
                <div class="month">ИЮН</div>
            </div>
            <div class="line__solid" style="height:15vh"></div>
            <div class="point__interval">
                <div class="year">>2 лет</div>
            </div>
            <div class="line__solid" style="height:15vh"></div>
            <div class="point">
                <div class="year">2014</div>
                <div class="month">АПР</div>
            </div>
            <div class="line__grey"></div>
        </div>
        <div class="details">
            <div class="details__title--main">Интернет-провайдер "Новые Телесистемы"</div>
            <div class="details__title--sub">Программист</div>
            <p class="details__text"><b>Заказчик</b></p>
            <p class="details__text">Интернет-провайдер с более чем 20 000 клиентами</p> 
            <p class="details__text"><b>Чем занимался</b></p> 
            <p class="details__text">Участвовал в разработке и поддержке системы учета.</p>
            <p class="details__text">Работал и писал скрипты для работы с роутерами Cisco, Eltex, Dlink.</p>
            <p class="details__text">Разработал утилиту для перемещения групп пользователей с одной подсети в другую.</p>
            <p class="details__text">Разработал модуль для создания запросов на включение/отключение пользователей.</p>
            <p class="details__text">Внедрил систему GIT вместо SVN.</p>
            <p class="details__text"><b>Технологии</b></p>
            <p class="details__skill">PHP, Mysql, Codeigniter, Ext4js, GIT, MSSQL(2012) network devices, bash, telnet</p>
        </div>
    </div>
    <div class="row">
        <div class="points">
            <div class="point">
                <div class="year">2014</div>
                <div class="month">ИЮН</div>
            </div>
            <div class="line__solid"></div>
            <div class="point__interval">
                <div class="year">1 год</div>
            </div>
            <div class="line__solid"></div>
            <div class="point">
                <div class="year">2013</div>
                <div class="month">ИЮН</div>
            </div>
            <div class="line__grey"></div>
        </div>
        <div class="details">
            <div class="details__title--main">ФорексИнн</div>
            <div class="details__title--sub">Junior web-developer</div>
            <p class="details__text"><b>Заказчик</b></p>
            <p class="details__text">Форекс брокер</p> 
            <p class="details__text"><b>Чем занимался</b></p> 
            <p class="details__text">Занимался поддрежкой сайта форекс-брокера.</p>
            <p class="details__text">Изучил основы финансовой торговли.</p>
            <p class="details__text">Познакомился и начал сипользовать фреймворк symfony за короткий срок.</p>
            <p class="details__text">Разработал модуль для проведения соревнований среди инвесторов.</p>
            <p class="details__text"><b>Технологии</b></p>
            <p class="details__skill">Symfony, PHP,jQuery, Mercurial, MetaTrader4, Bootstrap, HTML, JS</p>
        </div>
    </div>
    <div class="row">
        <div class="points">
            <div class="point">
                <div class="year">2012</div>
                <div class="month">ДЕК</div>
            </div>
            <div class="line__solid"></div>
            <div class="point__interval">
                <div class="year">>3 лет</div>
            </div>
            <div class="line__solid"></div>
            <div class="point">
                <div class="year">2009</div>
                <div class="month">АПР</div>
            </div>
            <div class="line__grey"></div>
        </div>
        <div class="details">
            <div class="details__title--main"><a href="http://uetm.ru">ООО «Эльмаш (УЭТМ)». Екатеринбург</a></div>
            <div class="details__title--sub">Специалист группы систем управления</div>
            <p class="details__text">Программное обеспечение ПЛК Beckhoff, командировки, создание документации, наставничество.</p>
            <p class="details__skill">PLC, Beckhoff, EtherCAT, TwinCAT, CodeSYS</p>
        </div>
    </div>
    <div class="row">
        <div class="points">
            <div class="point">
                <div class="year">2007</div>
                <div class="month">ОКТ</div>
            </div>
            <div class="line__solid"></div>
            <div class="point__interval">
                <div class="year">>2 лет</div>
            </div>
            <div class="line__solid"></div>
            <div class="point">
                <div class="year">2005</div>
                <div class="month">МАЙ</div>
            </div>
        </div>
        <div class="details">
            <div class="details__title--main"><a href="//datakrat.com">ЗАО НПФ «ДатаКрат-С» г. Томск</a></div>
            <div class="details__title--sub">Инженер ЦТО</div>
            <p class="details__text">Автоматизация общепита и кинотеатров. Обучал персонал, решал аппаратные и программные проблемы.</p>
            <p class="details__skill">Rkeeper, Delphi7, StoreHouse, UCS Cinema</p>
        </div>
    </div>
</div>
