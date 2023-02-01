---
$title@: test-task
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: test-task
$dates:
  published: 2016-01-19 04:00:09
---
Выполнил тестовое задание для одной Томской компании. Результат: вы неверно поняли задание и соотв неверно его выполнили. А теперь сами задачи.

[php]

&lt;?php

// TEST1

/*

    Write a listbox-style binary search for an ordered array of integers. 

    Listbox-style means that you should return the index of the first item 

    greater than or equal to the item being searched for; if all items are 

    less, you should return the index of the last item.  You are guaranteed 

    that there is at least one item in the array.

*/



[/php]

Переводим:

Написать listbox-style бинарный поиск для упорядоченного массива целых чисел. Listbox-style значит что вы должны вернуть индекс первого элемента, который больше или равен значению, которое ищем. Если все элеменыт меньше, то вернуть индекс последнего элемента. Вам гарантируют что массив заполнен хотя бы одним значением.

<!--more-->





А теперь то, как я это понял: есть массив (1, 2, 5, 7, 9). Есть значение, скажем, 3. Для массива (1, 2, 5, 7, 9) ответ будет индекс значения 5, то есть индекс = 2. Для значения 19 индекс будет 5. Для значения 0 индекс будет 0.



Поискал в сети решения и нашел вот это http://homyuksandbox.blogspot.ru/2011/09/blog-post.html. Там реализован обычный бинарный поиск, ну или дихотомический поиск. Делим массив пополам, смотрим наш товарищ, если не наш, то опять попалам и снова проверяем, двигаемся скажем по левой ветке, если в ней не найдено повторяем по правой ветке.



Надо было просить примеры. Правильный вариант решения задачи - это, наверное, как в примере http://homyuksandbox.blogspot.ru/2011/09/blog-post.html. 



[php]

echo '==TEST1==' .PHP_EOL;



function listBoxBinarySearch($needle, $haystack) {

    $filterCallback = function ($var) use ($needle) {

        return ($var &gt;= $needle);

    };



    $greaterThanOrEqual = array_filter($haystack, $filterCallback);



    if (empty($greaterThanOrEqual)) {

        $keys = array_keys($haystack);

        return array_pop($keys); // last of haystack

    }

    

    $keys = array_keys($greaterThanOrEqual);

    return array_shift($keys); // first of filtered

}



$testLb1 = array(

    1 =&gt; 5, 

    3 =&gt; 6, 

    4 =&gt; 7, 

    7 =&gt; 8

);



$r = listBoxBinarySearch(0, $testLb1);

echo $r.PHP_EOL;



$r = listBoxBinarySearch(6, $testLb1);

echo $r.PHP_EOL;



$r = listBoxBinarySearch(19, $testLb1);

echo $r.PHP_EOL;

[/php]



Второе задание. Нужно найти максимальную сумму положительных образующих непрерывную последовательность.

Примеры

<blockquote>

#test1

input: [1,2,3,4,5]

output: 15



#test2

input: [-1,-2,-3,-4,-5]

output: 0



#test3

input = [1, 2, -1, -2, 3, 4, -5]. 

output: 7

</blockquote>

[php]

echo '==TEST2==' .PHP_EOL;

// TEST2

/*

    Suppose you have an array of integers, both positive and negative, 

    in no particular order. Find the largest possible sum of 

    any continuous subarray.  

    For example, if you have all positive numbers, the largest sum 

    would be the sum of the whole array; if you have all negative numbers, 

    the largest sum is 0 (the null subarray)

*/





$test1 = array(1, 2, 3, 4, 5, 6);

$test2 = array(-1, -2, -3, -4, -5, -6);

$test3 = array(1, 2,-3, 9, 7,-6, 9, 7);



$callback = function ($prev, $current) use (&amp;$arr) {

    if ($current &gt; 0) {

        $arr[$prev + $current] = $prev + $current;

        return $prev + $current;

    } else {

        $arr[$prev + $current] = 0;

        return 0;

    }

};



$arr = [];

array_reduce($test1, $callback, 0);

echo max($arr).PHP_EOL;

$arr = []; 

array_reduce($test2, $callback, 0);

echo max($arr).PHP_EOL;



$arr = [];

array_reduce($test3, $callback, 0);

echo max($arr).PHP_EOL;

[/php]