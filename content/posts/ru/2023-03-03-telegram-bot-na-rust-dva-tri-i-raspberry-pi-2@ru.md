---
$title@: telegram-bot-na-rust-dva-tri-i-raspberry-pi-2
author@: Виктор Жарина
description: Разработка telegram бота на Rust и Raspberry Pi. От идеи до реализации.
keywords: telegram,bot,rust,teloxide,raspberry pi
image: /static/images/default.png
slugRu: telegram-bot-na-rust-dva-tri-i-raspberry-pi-2
$dates:
  published: 2023-03-03 12:11:17
---
<h3>Идея</h3>

Написать telegram бота-помощника на Rust, который будет работать на Raspberry Pi. Идея была давно. До этого изучал Rust и решил применить его здесь.

Существуют разные сервисы, вроде перевода раскладки с одного языка на другой, конвертера unix timestamp в форматированную дату, base64 кодирования, jwt, json validator/prettifier, bin2hex и так далее. Почему бы не переместить эти функции в телеграм бота и пользоваться из одного приложения вместо посещения нескольких ресурсов. Реализовать вроде не сложно. При этом давно валяется без дела Raspberry Pi 2, купленная у коллеги за символическую сумму.

<h3>Подготовка и выбор инструментов</h3>
Вижу три фронта работ: работа с железом, написание программы и интеграция.

Начнём с железяки aka малинка aka <a href="https://amperka.ru/product/raspberry-pi-2-model-b">Raspberry PI 2 Model B</a>. Вначале нужно было установить на неё ОС. Делается это с помощью записи образа на карту памяти. Этот процесс описан <a href="https://www.tomshardware.com/how-to/set-up-raspberry-pi">тут</a>. Выбрал Debian. Подключил малинку к монитору, настроил ssh. После того как ssh настроен, можно убрать плату подальше со стола (поближе к роутеру).

<p class="fig">
  <img width="25%" alt="Рисунок 1 - Raspberry Pi 2" src="/static/images/bezzabot/raspberry.jpg" />
  <p class="figsign">Рисунок 1 - Raspberry Pi 2</p>
</p>

Далее переместился к своему прекрасному, мощному, удивительному и неповторимому компьютеру, где я столько раз проливал чай и кофе, теперь можно подключиться по ssh, и творить дела удалённо. Удалил ненужные пакеты и отключил службы, которые не планирую использовать. Хотел удалить python, но подумал и не стал. Не удаляй python - не совершай ошибку. Python используют другие утилиты и удаление python сделает систему не пригодной для работы. С железякой закончили.

Дальше надо написать простейшее приложение (hello world) на рабочей машине, скомпилировать под платформу arm, выгрузить на Raspberry Pi и запустить. Добавлю подробностей. Рабочий компьютер это x86 архитектура, а Raspberry Pi это arm. Если коротко
<p class="fig">
    <img width="75%" alt="Рисунок 2 - Нельзя просто так скомпилировать программу на одной архитектуре, а исполнить на другой" src="/static/images/bezzabot/nelza.jpg" />
    <p class="figsign">Рисунок 2 - Нельзя просто так скомпилировать программу на одной архитектуре, а исполнить на другой</p>
</p>

Умные люди придумали кросс-компиляторы. Это программы, которые собирают программу под целевую (target) платформу. Помучавшись со сборкой libc и openssl, нашёл проект <a href = "https://github.com/cross-rs/cross">cross - zero setup cross compilation and cross testing</a>, который решил все проблемы.

```bash
cross build 
  --release  
  --target arm-unknown-linux-musleabihf
```

так выглядит команда на сборку под Raspberry Pi. Под капотом у cross работает docker. Указываете target и cross выкачивает нужный образ и собирает проект. Изящно, не правда ли?

Есть [ряд настроек, которые позволяют уменьшить размер бинарника](https://github.com/johnthagen/min-sized-rust). 
```bash
[profile.release]
strip = true
opt-level = "z"
lto = true
codegen-units = 1
```
теперь можно быстрее выгружать бинарник по ssh. Деплоить Rust приложение - одно удовольствие. Бинарник достаточно перенести на платформу и запустить. Также добавил .env файл, чтобы задать переменные окружения. В systemd файле конфигурации /etc/systemd/system/bezzabot.service указал .env файл. Из него будут загружены пeременные окружения, необходимые для работы приложения.

```bash
[Service]
ExecStart=/srv/bezzabot/bezzabot
ExecReload=bash -c "/srv/bezzabot/bezzabot"
User=radio
EnvironmentFile=/srv/bezzabot/.env
Restart=always
RestartSec=2
```

Считаем, что вопрос со сборкой и deploy приложения временно решен. Перейдём к регистрации бота, домена, сертификатам. Это была самая ненавистная часть, но, как оказалось, зря я её ненавидел. Выбрал webhooks вместо long-polling как способ коммуникации бота. В таком случае telegram требует https со всеми вытекающими. Я являюсь счастливым обладателем роутера keenetic zyxel и в dashboard обнаружил пункт меню Domain name. Оказалось Keenetic предоставляет доменное имя 4-го уровня. Вместе с сертификатом. Кайф. Зарегистрировал доменное имя и пробросил порты до raspberry. Ещё одна проблема решена малой кровью. 

Как регистрировать бота в телеграм рассказывать не буду, а оставлю <a href="https://core.telegram.org/bots#how-do-i-create-a-bot">ссылку</a>.

Бот зарегистрирован, webhook настроен, https работает. Переходим к созданию бота. Сэкономить время можно на реализации клиента для API. Несмотря на то, что для текущей задачи не нужна большая функциональность я бы не хотел ограничивать себя в будущем. Первой ссылкой по теме telegram bot rust выпадает <a hre="https://github.com/teloxide/teloxide">teloxide</a>.  Пример с <a href="https://github.com/teloxide/teloxide/blob/master/crates/teloxide/examples/command.rs">командами</a> это почти все, что мне нужно.

Определяете набор команд. Определяете реакцию на команды. Теперь к деталям. По-умолчанию teloxide работает в long-polling режиме, и поэтому надо (мне надо) перенастроить на webhook-mode. Смотрите <a href="https://github.com/teloxide/teloxide/blob/master/crates/teloxide/examples/ngrok_ping_pong.rs">пример здесь</a>. А дальше пишем логику. Единственное, что хочу отметить это парсинг аргументов. В моем случае команды содержат обязательные и необязательные параметры. Пользователь может задать лишние параметры, перепутать, ввести ерунду. Я использую поле parse_with макроса BotCommand, в которой указываю собственную функцию для разбора аргументов.

```rust
#[derive(BotCommands, Debug, Clone)]
#[command(
  rename_rule = "lowercase", 
  description = "Доступные команды:"
)]
pub enum BotCommand {
  #[command(
    description = "Отображает этот текст")
  ]
  Help,

  #[command(
    parse_with = skb_parser,
    description = "йцукен -> qwerty"
  )]
  Skb(
    String, 
    Layout, 
    FromLanguage, 
    ToLanguage
  ),
  ...
}
```

Доступны 3 команды, не считая help:

- /skb — Превращает йцукен в qwerty. Пример: /skb йцукен

- /utime — Превращает unix timestamp в дату в формате %Y-%m-%d %H:%M:%S

- /winner — Выбирает случайный id из списка. Пример: /winner 1 2 3 4 5

В планах добавить ещё. Кстати, если есть идеи команд - буду рад рассмотреть и реализовать.
В завершении отмечу, что создать dev-окружение для бота не составило труда. Я создал второго бота, ещё один домен и пробросил порт до рабочей машины.

<div class="videoWrapper">
    <iframe style="margin: 1rem auto; display: block; float: none" width="560" height="315" src="https://www.youtube.com/embed/sDludSLpM0k" title="YouTube video player" frameborder="0" allow="fullscreen; autoplay; picture-in-picture; web-share"></iframe>
</div>

В качестве заключения хочется сказать, что создание простого telegram бота на Raspberry Pi это совсем не сложно и даже весело.

<h3>Ссылки</h3>

[Bezzabot - Helper for developers - github](https://github.com/radiopapus/bezzabot)

[Bezzabot - Telegram](https://t.me/Ym90X2JlX3ph_bot)

[Teloxide - library to build Telegram bots on Rust](https://github.com/teloxide/teloxide)

[How Do I Create a Bot?](https://core.telegram.org/bots#how-do-i-create-a-bot)

[Botfather](https://telegram.me/BotFather)

[Asynchronous Programming in Rust](https://rust-lang.github.io/async-book)

[How to Set Up a Raspberry Pi for the First Time](https://www.tomshardware.com/how-to/set-up-raspberry-pi)

[Minimizing Rust Binary Size](https://github.com/johnthagen/min-sized-rust)

[Telegram Bot API](https://core.telegram.org/bots/api)

[Cross - zero setup cross compilation](https://github.com/cross-rs/cross)

[Rust](https://www.rust-lang.org)

[Характеристики Raspberry Pi](https://amperka.ru/product/raspberry-pi-2-model-b)