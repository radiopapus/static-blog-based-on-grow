---
$title@: telegram-bot-na-rust-i-raspberry-pi-2
author@: Виктор Жарина
description: Разработка telegram бота на rust и Raspberry Pi. От идеи до реализации.
keywords: telegram,bot,rust,teloxide,raspberry pi
image: /static/images/default.png
slugRu: telegram-bot-na-rust-i-raspberry-pi-2
$dates:
  published: 2023-03-02 16:12:23
---
<h3>Идея</h3>

Написать telegram бота-помощника на Rust, серверная часть которого будет работать на raspberry pi.

Идея создания telegram бота была давно. Изучал Rust, решил  попробовать Rust в web. Не классическое crud - приложение, но для разогрева вполне сгодится.

Существуют разные сервисы, вроде перевода раскладки с одного языка на другой, конвертера unix timestamp в форматированную дату, base64 кодирования, jwt, json validator/prettifier, bin2hex и так далее. Почему бы не переместить эти функции в телеграм бота и пользоваться из одного приложения вместо посещения нескольких ресурсов. Да и реализовать вроде бы . Набор команд, преобразование, результат. И да, давно валяется без дела Raspberry Pi 2, купленная у бывшего коллеги за символическую сумму.

<h3>Подготовка и выбор инструментов</h3>
Вижу три фронта работ : работа с железом, написание программы и интеграция.

Начнём с железяки aka малинка aka <a href="https://amperka.ru/product/raspberry-pi-2-model-b">Raspberry PI 2 Model B</a>. Вначале нужно установить на неё ОС. Делается это с помощью записи образа на карту памяти. Этот процесс прост и описан <a href="https://www.tomshardware.com/how-to/set-up-raspberry-pi">тут</a>. Выбрал Debian. Подключаете малинку к монитору, настраиваете ssh, SSH ключ наше всё. После того как ssh настроен, можно убрать плату подальше (поближе к роутеру).

<p class="fig">
  <img width="25%" alt="Рисунок 1 - Raspberry Pi 2" src="/static/images/bezzabot/raspberry.jpg" />
  <p class="figsign">Рисунок 1 - Raspberry Pi 2</p>
</p>

Далее переместимся к своему прекрасному, мощному, удивительному и неповторимому компьютеру, где столько раз проливал чай и кофе, подключаться по ssh, и творить дела удалённо. Удалил ненужные пакеты и отключил службы, которые не планирую использовать. Только не удаляй python - не совершай ошибку. Python используют другие утилиты и удаление python сделает систему не пригодной для работы. С железякой закончили.

Дальше надо написать простейшее приложение (привет hello world) на рабочей машине, скомпилировать под платформу arm, выгрузить на Raspberry Pi и запустить. Добавлю подробностей. Рабочий компьютер это x86 архитектура, а Raspberry Pi это arm. Если коротко
<p class="fig">
    <img width="75%" alt="Рисунок 2 - Нельзя просто так скомпилировать программу на одной архитектуре, а исполнить на другой" src="/static/images/bezzabot/nelza.jpg" />
    <p class="figsign">Рисунок 2 - Нельзя просто так скомпилировать программу для другой платформы</p>
</p>

Умные люди придумали кросс-компиляторы. Это программы, которые собирают программу под целевую (target) платформу. Помучавшись со сборкой libc и openssl нашёл проект <a href = "https://github.com/cross-rs/cross">cross - zero setup cross compilation and cross testing of Rust crates</a>, который решил все проблемы.

```bash
cross build --release --target arm-unknown-linux-musleabihf
```

так выглядит команда на сборку под Raspberry Pi. Под капотом у cross работает docker. Указываете target и cross выкачивает нужный образ и собирает проект. Изящно, не правда ли?

Есть ряд настроек, которые позволяют уменьшить размер бинарника. 
```bash
[profile.release]
strip = true
opt-level = "z"
lto = true
codegen-units = 1
```
теперь можно быстрее выгружать бинарник по ssh. Деплоить rust приложение - одно удовольстивие. Бинарник достаточно перенести на платформу и запустить. Также добавил .env файл, чтобы задать переменные окружения. В systemd файле конфигурации /etc/systemd/system/bezzabot.service указал .env файл. Из него будут загружены пeременные окружения, необходимые для бота.

```bash
[Service]
ExecStart=/srv/bezzabot/bezzabot
ExecRestart=bash -c "/srv/bezzabot/bezzabot"
User=radio
EnvironmentFile=/srv/bezzabot/.env
Restart=always
RestartSec=2
```

Считаем, что вопрос со сборкой и deploy приложения временно решен. Перейдём к регистрации бота, домена, сертификатам. Это была самая ненавистная часть, но, как оказалось, зря я её ненавидел. Выбрал webhooks вместо long-polling как способ коммуникации бота. В таком случае telegram требует https со всеми вытекающими. Я являюсь счастливым обладателем роутера keenetic zyxel и в dashboard обнаружил пункт меню Domain name. Оказалось Keenetic предоставляет доменное имя 5-го уровня для клиентов (это я). Вместе с сертификатом. Кайф. Зарегистрировал доменное имя и пробросил порты до raspberry и, забегая вперед, скажу, что это работает. Ещё одна проблема решена малой кровью. Как регистрировать бота в телеграм рассказывать не буду, а оставлю <a href="https://core.telegram.org/bots#how-do-i-create-a-bot">ссылку</a>.

Бот зарегистрирован, webhook настроен, https работает. Переходим к созданию бота. Сэкономить время можно на API. Несмотря на то, что для текущей задачи не нужна большая функциональность я бы не хотел ограничивать себя в будущем. Первой ссылкой по теме telegram bot rust выпадает <a hre="https://github.com/teloxide/teloxide">teloxide</a>. Это заслуженно. Пример с <a href="https://github.com/teloxide/teloxide/blob/master/crates/teloxide/examples/command.rs">командами</a> это почти все что мне нужно.

Определяете набор команд в enum Command. Определяете реакцию на команды в функции answer. Теперь к деталям. По-умолчанию teloxide работает в long-polling режиме, и поэтому надо перенастроить на webhook-mode. Смотрите <a href="https://github.com/teloxide/teloxide/blob/master/crates/teloxide/examples/ngrok_ping_pong.rs">пример здесь</a>. Кстати, ngrok отлично подходит для старта разработки как, собственно и локальный telegram сервер, выбор за вами. А дальше пишем логику. Единственное, что хочу отметить это парсинг аргументов. Команды содержат обязательные и необязательные параметры. Пользователь может задать лишние параметры, перепутать, ввести ерунду. Я использую поле parse_with макроса BotCommand, в которой укажем собственную функцию для разбора аргументов.

```rust
#[derive(BotCommands, Debug, Clone)]
#[command(rename_rule = "lowercase", description = "Доступные команды:")]
pub enum BotCommand {
  #[command(description = "Отображает этот текст")]
  Help,

  #[command(
   parse_with = skb_parser,
   description = "Превращает йцукен -> qwerty. Пример: /skb йцукен"
  )]
  Skb(String, Layout, FromLanguage, ToLanguage),
  ...
}
```

Доступны 3 команды, не считая help.
```bash
/skb — Превращает йцукен в qwerty. Пример: /skb йцукен
/utime — Превращает unix timestamp в дату в формате %Y-%m-%d %H:%M:%S.
/winner — Выбирает случайный id из списка. Пример: /winner 1 2 3 4 5
```
В планах добавить ещё. Кстати, если есть идеи команд - буду рад рассмотреть и реализовать.
В завершении отмечу, что создать dev-окружение для бота не составило никакого труда. Я создал второго бота, ещё один домен и пробросил порт до рабочей машины.

<iframe style="margin: 1rem auto; display: block; float: none" width="560" height="315" src="https://www.youtube.com/embed/sDludSLpM0k" title="YouTube video player" frameborder="0" allow="fullscreen; autoplay; picture-in-picture; web-share"></iframe>

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

[Bezzabot - Helper for developers - github](github.com/radiopapus/bezzabot)

[Bezzabot - Telegram](t.me/Ym90X2JlX3ph_bot)

[Teloxide - library to build Telegram bots on Rust](github.com/teloxide/teloxide)

[How Do I Create a Bot?](core.telegram.org/bots#how-do-i-create-a-bot)

[Botfather](telegram.me/BotFather)

[Asynchronous Programming in Rust](rust-lang.github.io/async-book)

[How to Set Up a Raspberry Pi for the First Time](www.tomshardware.com/how-to/set-up-raspberry-pi)

[Telegram Bot API](core.telegram.org/bots/api)

[Cross - zero setup cross compilation](github.com/cross-rs/cross)

[Rust](www.rust-lang.org)

[Характеристики Raspberry Pi](amperka.ru/product/raspberry-pi-2-model-b)