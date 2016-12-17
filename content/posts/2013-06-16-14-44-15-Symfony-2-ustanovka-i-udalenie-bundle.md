---
$title@: Symfony-2-ustanovka-i-udalenie-bundle
author@: Viktor Zharina
$order: 90
$dates:
  published: 2013-06-16 14:44:15
---
Bundle - это структура файлов и каталогов для решения задачи и выполнения определенных функций. Чтобы создать Bundle нужно выполнить команду:

<blockquote> php app/console generate:bundle --namespace=BundleClass/HelloBundle --format=yml</blockquote>



Чтобы удалить Bundle нужно:

<ul>

<li>удалить каталог BundleClass в каталоге src;</li>

<li>удалить route в /app/config/routing.yml;</li>

<li>в /app/AppKernel.php удалить строку содержащую BundleClass.</li>

</ul>



и почистить кеш с помощью команды: 

<blockquote>./app/console cache:clear</blockquote>









