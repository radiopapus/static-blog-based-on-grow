---
$title@: Sravnenie-testovyx-zadach-vypolnennyx-na-php-i-python
author@: Viktor Zharina
$order: 258
$dates:
  published: 2016-05-17 13:35:43
---
Когда-то я проходил собеседование в xiag. ОДним из этапов собеседования были задания, которые нужно было выполнять online. Параллельно с тех. спецом. Ниже приведены задания, но теперь я решил реализовать их на python.

1. Посчитать слова в тексте

2. Посчитать число вхождений каждой буквы в тексте и отсортировать по убыванию



<a href="http://viktor.zharina.info/razvitie/sobesedovanie-s-xiag/" target="_blank">PHP-вариант</a>

Python-версия ниже

Задание

[python]

import re



text = &quot;&quot;&quot;

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

&quot;&quot;&quot;

[/python]



1.

[python]

splited_text = re.split('[\n ]', text)

print(len(list(filter(lambda x: x != '', splited_text))))

[/python]



2.

[python]

only_letters = re.sub('([,?.;!\n \'])', '', text.lower())

uniq = list(set(only_letters))

repeats = {only_letters.count(x): x for x in uniq}

print(sorted(repeats.items(), reverse=True))

[/python]