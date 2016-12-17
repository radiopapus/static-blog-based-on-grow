---
$title@: Igraem-v-igru-izuchaem-python-codingames-temperatures
author@: Viktor Zharina
$order: 254
$dates:
  published: 2016-02-05 11:14:18
---
<img src="http://viktor.zharina.info/wp-content/uploads/2016/02/temperature-300x196.png" alt="temperature" width="300" height="196" class="alignleft size-medium wp-image-2078" />Write a program that prints the temperature closest to 0 among input data. If two numbers are equally close to zero, positive integer has to be considered closest to zero (for instance, if the temperatures are -5 and 5, then display 5).



[python]

import sys

import math



n = input()

tempsInput = input().split()

temps = [int(x) for x in tempsInput]

temps_abs = [abs(int(x)) for x in tempsInput]

if not tempsInput:

    print(len(tempsInput))

else: 

    minT = min(temps_abs)

    maxT = max(temps)

    result = minT if (minT and maxT &gt; 0) else maxT

    print(result)

[/python]