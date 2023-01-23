---
$title@: mashinka-na-rust-pervie-vpechatleniya
author@: Виктор Жарина
description: Это тестовая запись, опубликованная с помощью Mashinka (Rust)
keywords: запись,mashinka,rust
image: /static/images/default.png
slugRu: mashinka-na-rust-pervie-vpechatleniya
$dates:
  published: 2023-01-23 12:36:51
---
<p>
<img src="https://upload.wikimedia.org/wikipedia/commons/d/d5/Rust_programming_language_black_logo.svg" style="float:left;"/> Проект, в котором я участвовал поставлен на паузу, нового пока не предвидится. Я устал и хочу отдохнуть. У меня накопилось более 30 рабочих дней отпуска, но я думаю мне этого не хватит. Как мне кажется прекрасное время заняться образованием и всякой ерундой и хорошенько побездельничать.

Прошло более месяца как я изучаю Rust. Чтобы закрепить знания, после прочтения документации, я решил переписать старые скрипты (php) на Rust с некоторыми улучшениями. Эти скрипты я написал довольно давно и они нужны мне для автоматизации публикации записей в блоге. Я не использую wordpress и мой блог это статичный сайт, то есть, грубо говоря, набор html страниц. Мой процесс создания записи выглядит так, что я пишу черновик на локальной машине, далее публикую, потом добавляю данные в индекс для полнотекстового поиска и после этого выгружаю в облачное хранилище. Таким образом мои записи хранятся в репозитории в виде файлов. Никакой б.д., никакого php и никакого backend (формально). Как раз эту запись я публикую с помощью новой CLI утилиты на Rust, которую я назвал Mashinka.
</p>

В этой записи я расскажу про:

- Почему Rust;

- Mashinka

- Процесс переписывания;

- Впечатления;

- Планы и результаты;

- Ссылки;

## Почему Rust

Это все маркетинг и реклама. Просто увидел где-то, прочел, что он быстрый как С++, memory safety и без gc и стало интересно. Это как пошел в магазин со списком товаров, но увидел какую-то вкусняшку, решил что она вкусная и купил попробовать. Вопрос [почему](https://www.youtube.com/watch?v=vC3jnJy_Ids&t=59s) очень интересный, но с какого-то момента очень сложно дать на него ответ.

## Немного о Mashinka

Это CLI утилита, которая из-за своей специфики нужна только мне. Также я решил, что буду добавлять минимум сторонних зависимостей. Итак, есть набор входных параметров, которые после парсинга превращаются в структуры, которые реализуют трейт Command (метод run). Внутри метода run расположена вся логика команды. Метод run возвращает CommandResult. CommandResult содержит имя команды и детали выполнения команды. Команд на данный момент две: Publish и Index. Все команды лежат в модуле command.

## Процесс переписывания

Я решил, что буду стримить весь процесс переписывания. Возьму старые скрипты на PHP и перепишу. Но перед этим установил Rust и примерно неделю читал
документацию, практиковался и отмечал недочеты, которые обнаружил в русской версии. Я прочитал почти всю документацию, за исключением последней главы и после этого начал вспоминать что я там понаписал в своих скриптах и главное зачем.
Походу работы появлялись ребята, которые заходили на стримы и писали комментарии, иногда бывало так, что комментарии уводили в сторону, иногда мы просто общались. Все это увеличивало время переписывания, но это не было тратой времени. Я узнал много всего интересного и познакомился с новыми людьми.

## Впечатления

Я люблю строгие и понятные правила. Первое время я ощущал себя человеком, которого бьют по рукам идущего по граблям. То я владение передам, а потом использую переменную, то возвращаю ссылку на переменную, которая вышла за scope. Первое время складывалось впечатление, что я прочел книгу, а на практике собираю все грабли, про которые там писали. Одна штука меня приятно удивила это вывод ошибок компилятора. Читаешь и понимаешь что компилятор пытается общаться с человеком указывая на то, где возникла ошибка и предлагая способ ее исправить. Я бы назвал вывод ошибок компилятора самой крутой, что я видел.
Вообще я считаю, что если концепция имеет высокий порог входа, но при этом дает весомый плюс, то на нее стоит обратить внимание. Memory safety это весомый плюс.
Однако я бы не выбрал Rust для решения задач на LeetCode или в качестве олимпиадного языка, как раз по причине его строгости. При решении задачи хочется выразить идею, а концептуальный слой, как мне кажется, может это усложнить.
Меня не удивил cargo, потому что я считаю, что любой современный язык должен быть подаваться с пакетным менеджером и если его нет, то это проблема.

У меня до сих пор вызывают затруднения модули. Я забываю пометить их public и про то, что нужно создать файл с именем модуля и потом создать директорию с таким же именем. Еще помню разбирался с интеграционными тестами и никак не мог понять, как сделать import из основного приложения. Оказалось что надо создать файл lib.rs и там перечислить все модули, которые должны быть доступны в tests.

Мне очень понравилось то, что в Rust нет исключений. С точки зрения CLI я объявил enum Error и использовал thiserror. Там, где возникают проблемы я возвращаю Error и обрабатываю его на самом верхнем уровне. Все ошибки описаны в одном месте. Использование ? делает обработку ошибок довольно изящной.

Да, кстати, enum это не просто перечисление. Приведу пример для наглядности

```rust
/// Список ошибок
#[derive(Error, Debug)]
pub enum Error {
    // config
    #[error("Check parameter format, please. Should be --param-name or --param-name=value")]
    Parse(),
    #[error("Check date time format `{0}`")]
    DateTimeError(ParseError),
    #[error("Value for {0} should be filled (not empty)")]
    EmptyValue(String),
}
```

Таким образом, enum может состоять из элементов, где каждый может хранить значение определенного типа и дальше, в коде, вы можете это использовать. Помимо этого можно написать имплементацию для enum точно так же как и для структуры.

Ах, да, в rust нет классического привычного наследования. И я поначалу был в ступоре от того, как так жить вообще. Но жить можно и даже посещают некоторые крамольные мысли.

А еще в Rust нет null. Есть Option, который может быть None, но это другое. Вы всегда знаете, где будет -этот None, а елси используете match, то компилятор заставит вам обработать этот случай.

А еще я удивился системе макросов. В PHP, Kotlin есть так называемая Reflection. Которая позволяет получать и изменять данные объекта по время исполнения. К примеру получить список имен и значений полей объекта и изменить их, сгенерить объект и т.д. Это используют при написании всяких фреймворков. В Rust нет Reflection, но есть макросы. С помощью них вы можете творить "магию". Можно написать свой собтсвенный макрос, который создаст вместо вас кусок кода, можно пометить поля и изменить их в макросе, можно взять кусок кода и изменить его в макросе и т.д.

Rust - современный, мультипарадигменный язык, в основе которого лежит концепция направленая на обеспечение memory safety, fearless concurrency и современных фишек (итераторы, трейты, жирная стандартная библиотека, функциональщина) без ущерба для производительности. Мне бы хотелось и дальше использовать этот язык и было бы любопытно наблюдать за его развитием.

## Планы и результаты

Эту запись я публикую с помощью Mashinka, написанной на Rust. В планах написать команду Deploy и Help. В подвале я нашел Raspberry Pi и планирую использовать ее в качестве сервера. Хочу попробовать Rust в web. Также, после чтения документации я обнаружил несколько недочетов, которые отправил в виде PR.

## Ссылки

<a href="https://explainshell.com/">Explainshell.com - match command-line arguments to their help text</a>

<a href="https://doc.rust-lang.org/book">The Rust Programming Language</a>

<a href="https://cheats.rs/">Rust Language Cheat Sheet</a>

<a href="https://rust-lang.github.io/api-guidelines/naming.html">Naming - Rust API Guidelines</a>

<a href="https://godbolt.org/">Conventions for Command Line Options</a>

<a href="https://rust-cli.github.io/book/tutorial/testing.html">Testing - Command Line Applications in Rust</a>

<a href="https://paste.rs/web">Paste.rs</a>

<a href="https://github.com/rust-cli/human-panic">Panic messages for humans.</a>

<a href="https://profpatsch.de/notes/rust-string-conversions">String-conversions</a>

<a href="https://www.sobyte.net/post/2022-07/rust-string/">Some summaries on Rust string literals</a>

<a href="https://google.github.io/comprehensive-rust/welcome-day-1.html">Comprehensive Rust</a>

<a href="https://learn.microsoft.com/en-gb/training/modules/rust-introduction/3-rust-features">Unique features of Rust - Training | Microsoft Learn</a>

<a href="https://github.com/rust-lang/rustlings">Small exercises to get you used to reading and writing Rust code!</a>

<a href="https://www.lurklurk.org/effective-rust/iterators.html">Effective Rust</a>

<a href="https://doc.rust-lang.org/reference/macros-by-example.html">Macros By Example</a>

<a href="https://veykril.github.io/tlborm/">The Little Book of Rust Macros</a>

<a href="https://adventures.michaelfbryan.com/posts/non-trivial-macros/">Writing Non-Trivial Macros in Rust · Michael-F-Bryan</a>

<a href="http://prog.tversu.ru/library/tapl.pdf">Типы в языках программирования</a>

<a href="https://rustc-dev-guide.rust-lang.org/overview.html">Overview of the Compiler - Guide to Rustc Development</a>

<a href="https://rust-unofficial.github.io/patterns/idioms/coercion-arguments.html">Use borrowed types for arguments - Rust Design Patterns</a>

<a href="https://os.phil-opp.com/minimal-rust-kernel/">A Minimal Rust Kernel | Writing an OS in Rust</a>

<a href="https://github.com/zesterer/chumsky">zesterer/chumsky: A parser library for humans with powerful error recovery.</a>

<a href="https://github.com/rust-bakery/nom">rust-bakery/nom: Rust parser combinator framework</a>