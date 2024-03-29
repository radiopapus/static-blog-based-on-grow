---
title: Разбираемся с Coroutine в Kotlin - часть первая
lang: ru 
description: Цикл статей, чтобы разобраться с идеей и внутренним устройством coroutine в Kotlin 
keywords: kotlin, coroutine, under the hood
---

<h3>Введение</h3>

Впервые я столкнулся с корутинами в одном из проектов. В нем было принято решение использовать реактивный подход. До
этого я не работал с реактивщиной и решил разобраться с тем, что за зверь такой эти "корутины". Я начну от идеи и
истоков и надеюсь дойти до понимания реализации в Kotlin. Мне удалось найти
статью [Design of a Separable Transition-diagram Compiler](https://www.melconway.com/Home/pdf/compiler.pdf)
от 1963 года. Люди пишут, что это одно из самых ранних упоминаний идеи корутин. В статье есть часть с названием
"Coroutines and Separable Programs", про которую я узнал из поста
[Why using Kotlin Coroutines?](https://kt.academy/article/cc-why). С этой части, а точнее с ее перевода я и начну.

<h3>Корутины и разделяемые программы</h3>

Программа является разделяемой, если она может быть "разбита" (break up) на отдельные модули, которые обмениваются
данными с учетом следующих ограничений:

1) обмен данными происходит в форме передачи дискретных значений информации;

2) поток данных однонаправленный и фиксированного размера;

3) программа представлена как набор модулей, у которых слева расположены входы (прим. прием данных), а справа выходы и
   информационный поток движется между модулями слева направо. <i>(Прим. pipeline)</i>

<div class="quote__right">
 <p class="quote">Таким образом корутины это подпрограммы, расположенные на
одном уровне исполнения</p>
</div>

При соблюдении этих условий каждый модуль может быть превращен в сопрограмму (далее корутину), то есть может быть
закодирован как отдельная программа, которая обменивается данными со смежными модулями, принимая входные данные,
обрабатывая и передавая их на выход. Таким образом корутины это подпрограммы, расположенные на
одном уровне исполнения, так как если бы они исполнялись main программой, хотя на самом деле никакой main программы нет.
Нет никаких ограничений на количество входов и выходов, которое может иметь корутина. Концепция корутин может заметно
упростить концепцию программы, когда модули не обмениваются данными в синхронном режиме. В качестве примера рассмотрим
программу чтения данных с перфокарт и записи символов, которые будут считаны с 1-й до 80-й колонки первой карты, потом
второй и т.д. с учетом того, что каждый раз, когда встречаешь два символа * нужно заменить их на символ "&uarr;".
Блок-схема такой программы, представленная как набор процедур, выполняемых в main программе изображена на рисунке 1.

<div class="text-centered">
<img src="https://viktor.zharina.info/static/images/kotlin-coroutines/subroutine-flowchart.png" alt="Рисунок 1. Процедурный подход" />
<p>Рисунок 1. Процедурный подход.</p>
</div>

Отметим, что необходимость схлопывания символа '*' требует добавления переключателя (switch), который разделяет поток
выполнения и выбирает ветку, по которой программа будет выполняться в зависимости от результата последнего вызова.
Причина для использования switch в том, что каждый вызов должен выводить ровно один символ.

Подход, к той же задаче, основанный на корутинах выполняет переключение неявно, используя последовательность вызовов
подпрограмм. Когда корутины А и B соединены так, что А отправляет данные к B, B работает до тех пор, пока не получит
команду на чтение. Потом управление будет передано к А пока она не "решит" записать данные, после чего управление
вернется к B в точку, из которой она передала управление ранее. На рисунке 2 представлена схема программы для сжатия
звездочек, когда обе программы являются корутинами.

<div class="text-centered">
<img src="https://viktor.zharina.info/static/images/kotlin-coroutines/coroutine-flowchart.png" alt="Рисунок 2. Подход, основанный на корутинах" />
<p>Рисунок 2. Подход, основанный на корутинах.</p>
</div>

Рисунок 3 иллюстрирует суть разделяемости (separability). Вместо того, чтобы модули А и В передавали управление
туда-сюда каждый раз когда один из символов прочитан/записан можно без изменения в А или В позволить А записать все
данные на ленту, перемотать ленту и потом позволить В считать символы с ленты. В этом смысле программы А и В могут
работать как однопроходной или двухпроходной обработчик с простейшими изменениями.

<div class="text-centered">
<img src="https://viktor.zharina.info/static/images/kotlin-coroutines/pic3-separable-programm.png" alt="Рисунок 3." />
<p>Рисунок 3.</p>
</div>

В качестве предыстории можно привести здесь описание работы корутин
на [Burroughs 220](https://en.wikipedia.org/wiki/Datatron). 220 это последовательная, одноадресная машина с
последовательным счетчиком, называемым P-регистр. Во время выполнения инструкций он содержит адрес следующей инструкции.
Пока не будет ветвления регистр будет указывать на адрес следующей инструкции. Инструкция BUN A помещает адрес А в
P-регистр. Инструкция STP B помещает содержимое P по адресу B и переходит к следующей инструкции. Стандартный вызов
процедуры это

```
STP EXIT 
BUN ENTRANCE
```

Где EXIT содержит BUN инструкцию, чей адрес будет изменен STP инструкцией во время вызова. Пара процедур становится
парой корутин после добавления для каждой отдельной BUN инструкции, которую можно назвать маршрутизатором (router), и
изменения адресов в STP инструкции, когда корутина А вызывает B, вызов происходит следующим образом:

```
STP AROUTER 
BUN BROUTER
```

Таким образом маршрутизатор является обобщением оператора switch. Запустить систему корутин можно правильно
инициализировав маршрутизаторы. Рисунок 3 показывает, что корутины могут исполняться параллельно и последовательно.
Когда доступно два физических процессора, то факт того, что корутины разделяемых программ могут исполняться
одновременно (simultaneously) становится еще более значимым.

<h3>Выводы</h3>

1. Программу, написанную как набор вложенных процедур можно представить в виде отдельных модулей, которые передают друг
   другу данные по определенным правилам. В этом случае вложенность исчезает и каждую программу можно представить в виде
   отдельной сопрограммы. Это и есть корутины.

2. Проверку выполнения условий перехода можно заменить на переключение управления от одной сопрограммы к другой и таким
   образом программы, представленные на рисунке 1 и рисунке 2 эквивалентны.

3. Возвращать управление нужно по тому адресу, с которого произошла передача управления.

В следующем посте будем разбираться с асинхронным кодом и начнем перевод статьи 
[Why using Kotlin Coroutines?](https://kt.academy/article/cc-why). 

Список литературы:

1. [wiki - coroutine](https://en.wikipedia.org/wiki/Coroutine)

2. [wiki - melwin_conway](https://en.wikipedia.org/wiki/Melvin_Conway)

3. [Design of a Separable Transition-diagram Compiler](https://www.melconway.com/Home/pdf/compiler.pdf)

4. [Why using Kotlin Coroutines?](https://kt.academy/article/cc-why)