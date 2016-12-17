---
$title@: Lenivaya-proverka-uslovij
author@: Viktor Zharina
$order: 99
$dates:
  published: 2013-08-12 06:01:46
---
[php]

$arr = array('t1'=&gt;1);



if($arr['t1'] AND $arr['t2'])

{

    echo 'not';

}

else

{

    echo 'lazy';

}

[/php]