---
$title@: Sobesedovanie-s-xiag
author@: Viktor Zharina
$order: 250
$dates:
  published: 2015-12-30 04:04:41
---
<img src="http://viktor.zharina.info/wp-content/uploads/2015/12/xiag_ag_logo_wi.png" alt="xiag_ag_logo_wi" width="142" height="41" class="alignleft size-full wp-image-2026" />Xiag - интересная компания с головным офисом в Швейцарии и разработкой в Новосибирске. Мне какое-то время назад написали из кадрового агентства Сухорукова и предложили пройти у них собеседование. 



Собеседование было в 4 этапа:

1) Собеседование с кадровым агентством;

2) Тестовое задание от xiag (https://github.com/ViktorZharina/php-url-shortener-without-framework);

3) Собеседование с xiag (техническое + орг);

4) Парное программирование с техническим специалистом xiag.

<!--more-->

Первые три пункта формальны и я не вижу смысла писать о них. А вот пункт 4 был для меня новинкой. Суть такова: вы подключаетесь к codeshare.io (кстати интересный ресурс для целей собеседования) и один пишет задание, а второй его выполняет. Все просто. Но, как оказалось, получилось сложнее, чем я ожидал. На будущее я буду просить разнести эти виды собеседования по времени по дням и теперь уже буду готов.



Было два задания. Дан текст

[php]

&lt;?php

//---

  $text = &lt;&lt;&lt;TEXT

Little Fly,

Thy summer's play

My thoughtless hand

Has brush'd away.



Am not I

A fly like thee?

Or art not thou

A man like me?



For I dance,

And drink, and sing,

Till some blind hand

Shall brush my wing.



If thought is life

And strength and breath,

And the want

Of thought is death;



Then am I

A happy fly,

If I live

Or if I die.

  

TEXT;

[/php]



1) Посчитать слова в тексте



Я выполнил примерно так:

[php]

$ar = split('[\s,.]+', $s);

echo sizeof($ar);

[/php]

И это, конечно работает, но есть функция str_word_count, которая сделает все за вас и скорее всего быстрее. Так что не совершайте моей ошибки, учите и знайте язык.



2) Посчитать число вхождений каждой буквы в тексте и отсортировать по убыванию. Тех. специалист продемонстрировал функциональных подход.

[php]

// код тех. спеца - НАЧАЛО

function normalize($str)

{

    return preg_replace('/[^a-z]/', '', strtolower($str));

}



$start = microtime(true);

$map = array_reduce(

    str_split(normalize($text)),

    function ($result, $char) {

        if (!array_key_exists($char, $result)) {

            $result[$char] = 1;

        } else {

            $result[$char]++;

        }

        return $result;

    },

    []

);



arsort($map);

$time_elapsed_secs = microtime(true) - $start;

echo $time_elapsed_secs.PHP_EOL;

// код тех. спеца - ОКОНЧАНИЕ

[/php]

Я, за время собеседования, написать код так и не смог. После того как собеседование закончилось. Я налил чай и решил сделать задание уже в спокойной обстановке. Ниже мой вариант. Я решил не использовать регулярки и задать массив букв заранее.



[php]

$start = microtime(true);

$textLower = strtolower($text);

$r = [];

$letters = 'abcdefghijklmnopqrstuwxyz';

$strLen = strlen($textLower);



for($c = 0; $c &lt; $strLen; $c++) {

    if (stripos($letters, $textLower[$c]) !== false) {

        if (array_key_exists($textLower[$c], $r)) {

            $r[$textLower[$c]] += 1;    

        } else {

            $r[$textLower[$c]] = 1;

        }

    }

}



arsort($r);

$time_elapsed_secs = microtime(true) - $start;

echo $time_elapsed_secs.PHP_EOL;

[/php]



А вот третий вариант с использованием готовой php-ной функции.

[php]

$r2 = [];

function normalize($str)

{

    return preg_replace('/[^a-z]/', '', strtolower($str));

}



foreach (count_chars($textLower, 1) as $i =&gt; $val) {

   $r2[chr($i)] = $val;

}

arsort($r2);

[/php]



Итог: учите язык, знайте язык, не повторяйте ошибок.