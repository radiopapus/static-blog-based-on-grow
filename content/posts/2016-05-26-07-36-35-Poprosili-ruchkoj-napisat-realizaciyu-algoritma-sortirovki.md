---
$title@: Poprosili-ruchkoj-napisat-realizaciyu-algoritma-sortirovki
author@: Viktor Zharina
$order: 259
$dates:
  published: 2016-05-26 07:36:35
---
Вначале я затупил (или заволновался) и не смог, но потом стало стыдно, пришел домой и написал вот это минут за 10.



[php]

&lt;?php

$array = array(1,5,3,4,2);

              

$max = 0;    

$maxIdx = 0;

$len = count($array);



for($i=0;$i&lt;$len;$i++) {

    $max = $array[$i];

    for($k=$i;$k &lt; $len;$k++) {

        if ($array[$k] &gt;= $max) {

            $maxIdx = $k;

            $max = $array[$k];

        }

    }

    $tmp = $array[$i];

    $array[$i] = $max;

    $array[$maxIdx] = $tmp;

}



print_r($array);

[/php]