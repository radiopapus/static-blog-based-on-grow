---
$title@: telegram-bot-na-rust-i-raspberry-pi-2
author@: Виктор Жарина
description: Разработка telegram бота на rust и Raspberry Pi. От идеи до реализации.
keywords: telegram,bot,rust,teloxide,raspberry pi
image: /static/images/default.png
slugRu: telegram-bot-na-rust-i-raspberry-pi-2
$dates:
  published: 2023-03-01 14:45:14
---
<h3>Идея</h3>

Написать telegram бота-помощника на Rust, серверная часть которого будет работать на raspberry pi.

Идея создания telegram бота была давно. Изучал Rust, решил оценить что может Rust в web. Не классическое crud - приложение конечно, но для разогрева вполне сгодится.

Существуют разные сервисы, вроде перевода раскладки с одного языка на другой, конвертера unix timestamp в форматированную дату, base64 кодирования, jwt, json validator/prettifier, bin2hex и так далее. Почему бы не переместить эти функции в телеграм бота и пользоваться ими из одного приложения. Да и выглядит довольно просто в реализации. Набор команд, преобразование, результат. И да, давно валяется без дела Raspberry Pi 2, купленная у бывшего коллеги за символические деньги.

<iframe style="margin: 1rem auto; display: block; float: none" width="560" height="315" src="https://www.youtube.com/embed/sDludSLpM0k" title="YouTube video player" frameborder="0" allow="autoplay; picture-in-picture; web-share"></iframe>

<h3>Подготовка и выбор инструментов</h3>
Для реализации идеи я вижу 3 вида работ: работа с железом, написание программы, интеграция.

Начнём с железяки aka малинка aka <a href="https://amperka.ru/product/raspberry-pi-2-model-b">Raspberry PI 2 Model B.</a> Вначале нужно установить на неё ОС. Делается это с помощью записи образа на карту памяти. Этот процесс не представляет ничего интересного и описан <a href="https://www.tomshardware.com/how-to/set-up-raspberry-pi">тут</a>. Я выбрал Debian. Подключаете малинку к монитору, настраиваете ssh, упаси вас господь настраивать вход по паролю, в 2023 году живём, SSH ключ наше все. Для своих нужд я разрешил доступ только из локальной сети. После того как ssh настроен, можно убрать плату подальше (поближе к роутеру).

<p class="fig">
    <img width="25%" alt="Рисунок 1 - соотношение сторон 1 к x" src="https://322111.selcdn.ru/blog/static/images/bezzabot/raspberry.jpg" />
    <p class="figsign">Рисунок 1 - Raspberry Pi 2</p>
</p>

Далее переместимся к своему прекрасному, мощному, удивительному и неповторимому компьютеру, где я столько раз проливал чай и кофе и подключаться по ssh и творить дела удалённо. Первое что я сделал это поудалял ненужные пакеты и отключил службы, которые не планирую использовать. Только не удаляй python - не совершай ошибку. Его используют другие утилиты и удаление python скорее всего сделает систему не пригодной для дальнейшей работы. С железякой закончили.

Дальше надо написать простейшее приложение на рабочей машине, скомпилировать его под платформу arm, выгрузить на Raspberry Pi и запустить. Добавлю немного подробностей. Рабочий компьютер это x86 архитектура, а Raspberry Pi это arm. Если коротко, то нельзя просто взять и скомпилировать программу на одной архитектуре, а исполнять на другой. Умные люди придумали кросс-компиляторы. Это программы, которые собирают программу под целевую (target) платформу. Помучавшись со сборкой libc и openssl я нашёл проект <a href = "https://github.com/cross-rs/cross">cross - zero setup cross compilation and cross testing of Rust crates</a>. Для меня это стало панацеей. 
```bash 
cross build --release --target arm-unknown-linux-musleabihf
```
так выглядит команда на сборку под Raspberry Pi. Под капотом у cross работает docker. Указываете target и cross выкачивает нужный образ и собирает проект. Изящно, не правда ли?

А да, есть ряд настроек, которые позволяют уменьшить размер бинарника. 

```bash
[profile.release]
strip = true
opt-level = "z"
lto = true
codegen-units = 1
```
Сделал я это для того, чтобы быстрее выгружать бинарник по ssh. Деплоить rust приложение - одно удовольстивие. Rust бинарник содержит все необходимое и достаточно перенести его на платформу, где он будет исполнятся и запустить. В моем случае я добавил .env файл, чтобы задать переменные окружения. В systemd файле конфигураций /etc/systemd/system/bezzabot.service я указал .env файл. Из него будут загружены пемренные окружения, необходимые для бота. 

```bash
[Service]
ExecStart=/srv/bezzabot/bezzabot
ExecRestart=bash -c "/srv/bezzabot/bezzabot"
User=radio
EnvironmentFile=/srv/bezzabot/.env
Restart=always
RestartSec=2
```
Считаем, что вопрос со сборкой и deploy приложения временно решен. Перейдём к регистрации бота, домена, сертификатам. Это была самая ненавистная часть, но, как оказалось, зря. Я выбрал webhooks вместо long-polling как способ коммуникации бота. В таком случае telegram требует https со всеми вытекающими. Я являюсь счастливым обладателем роутера keenetic zyxel и в dashboard я обнаружил пункт меню Domain name. Оказалось Keenetic предоставляет доменное имя 5-го уровня для своих клиентов (это я). Вместе с сертификатом. Кайф. Я зарегистрировал доменное имя и пробросил порты до raspberry и, забегая вперед, скажу, что это прекрасно работает. Таким образом ещё одна проблема решена малой кровью. Как регистрировать бота в телеграм я рассказывать не буду, а оставлю <a href="https://core.telegram.org/bots#how-do-i-create-a-bot">ссылку</a>.

Бот зарегистрирован и настроен webhook. Переходим к написанию кода. Сэкономить время можно на API. Несмотря на то, что для текущей задачи не нужна большая функциональность я бы не хотел ограничивать себя в будущем. Первой ссылкой по теме telegram bot rust выпадает <a hre="https://github.com/teloxide/teloxide">teloxide</a>. Это заслуженно. Пример с <a href="https://github.com/teloxide/teloxide/blob/master/crates/teloxide/examples/command.rs">командами</a> это почти все что мне нужно.
Определяете набор команд в enum Command. Определяете реакцию на команды в функции answer. Теперь к деталям. По-умолчанию teloxide работает в long-polling режиме, и поэтому надо перенастроить его на webhook-mode. Смотрите <a href="https://github.com/teloxide/teloxide/blob/master/crates/teloxide/examples/ngrok_ping_pong.rs">пример здесь</a>. Кстати, ngrok отлично подходит для старта разработки как, собственно и локальный telegram сервер, выбор за вами. А дальше пишем логику. Единственное, что хочу отметить это парсинг аргументов. В моем случае команды могут содержать 
обязательные и необязательные параметры. Пользователь может задать лишние параметры, перепутать, ввести ерунду. Я использую поле parse_with макроса BotCommand, который позволяет написать свою функцию парсинга, вместо функции по-умолчанию. parse_with - это функция, которая принимает на вход строку, а на выходе у нее Result<(), ParseError>.

```rust
#[derive(BotCommands, Debug, Clone)]
#[command(rename_rule = "lowercase", description = "Доступные команды:")]
pub enum BotCommand {
    #[command(description = "Отображает этот текст")]
    Help,

    #[command(
        parse_with = skb_parser,
        description = "Превращает йцукен -> qwerty. Пример: /skb йцукен")]
    Skb(String, Layout, FromLanguage, ToLanguage),
    ...
}
```

На текущий момент доступны 3 команды, не считая help. 
```bash
/skb — Превращает йцукен -> qwerty. Пример: /skb йцукен
/utime — Превращает unix timestamp в дату в формате %Y-%m-%d %H:%M:%S.
/winner — Выбирает  случайный id из списка. Пример: /winner 1 2 3 4 5
```
и в планах добавить парочку, троечку. Кстати, если есть идеи команд - скидывайте. 

В завершении отмечу, что создать dev окружение для бота не составило вообще никакого труда. Я просто создал второго бота, ещё один домен и пробросил порт до рабочей машины.

В качестве вывода и отчёта о проделанной работе я оставлю список задач, которые я решил и напишу то, что создание простого telegram бота это совсем не сложно и даже весело.

Список задач:

- Установить ОС на Raspberry Pi 2

- Настроить SSH

- Поудалять лишние пакеты и остановить ненужные службы (alsa, bluetooth, wifi и т.д.)

- Собрать проект, написанный на рабочей машине под Raspberry Pi (x86 -> arm 32 bit)

- Выгрузить и запустить собранный проект на Raspberry Pi 

- Написать systemd сервис, который будет запускать бота при загрузке и перезапускать, если сервис упал (такого у нас точно не произойдет)

- Исследовать тему с IP адресом от провайдера и понять как получить фиксированный ip

- Зарегить домен для бота

- Получить и настроить сертификат (let's encrypt?)

- Зарегить telegram бота (придумать имя меньше чем за 1 день)

- Telegram API (режим работы бота long polling или webhook)

- Поискать готовое решение для работы с API на Rust (teloxide)

- Написать бота на rust

- Создать dev окружение (dev бота) для разработки


<h3>Ссылки</h3>

[Bezzabot - Helper for developers - github](https://github.com/radiopapus/bezzabot)

[Bezzabot - Telegram](https://t.me/Ym90X2JlX3ph_bot)

[Teloxide - library to build Telegram bots on Rust](https://github.com/teloxide/teloxide)

[How Do I Create a Bot?](https://core.telegram.org/bots#how-do-i-create-a-bot)

[Botfather](https://telegram.me/BotFather)

[Asynchronous Programming in Rust](https://rust-lang.github.io/async-book/)

[How to Set Up a Raspberry Pi for the First Time](https://www.tomshardware.com/how-to/set-up-raspberry-pi)

[Telegram Bot API](https://core.telegram.org/bots/api)

[Cross - zero setup cross compilation](https://github.com/cross-rs/cross)

[Rust](https://www.rust-lang.org/)

[Характеристики raspberry pi](https://amperka.ru/product/raspberry-pi-2-model-b)