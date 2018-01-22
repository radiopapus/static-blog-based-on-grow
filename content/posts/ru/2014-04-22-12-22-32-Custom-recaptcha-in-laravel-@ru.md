---
$title@: Custom-recaptcha-in-laravel-4
author@: Viktor Zharina
$order: 174
$dates:
  published: 2014-04-22 12:22:32
---
Recaptcha - сервис, который позволяет отличить пользователя от компьютерной программы с помощью определенного теста. Я использую recaptcha в сервисе hobby-work.ru, чтобы предотвратить случайные нажатия на кнопку и, таким образом, уберечь сервер от лишней

нагрузки при автоматической установке CMS.



Итак, нам понадобятся:

<ol>

	<li>плагин recaptcha</li>

	<li>public и private ключи от recaptcha</li>

</ol>

<!--more-->

Так как для laravel уже есть <a href="https://github.com/greggilbert/recaptcha" target="_blank">готовый плагин</a>, то мы используем его и далее настроим его под себя. Ключи возьмем <a href="https://www.google.com/recaptcha/admin" target="_blank">тут</a>

Поехали: 

<ul>

	<li>Добавляем следующие строки в composer.json:

[php]

{

    &quot;require&quot;: {

        &quot;greggilbert/recaptcha&quot;: &quot;dev-master&quot;

    }

}

[/php]</li>

	<li>Выполняем [php]php composer.phar update[/php]</li>

	<li>Выполняем [php]php artisan config:publish greggilbert/recaptcha[/php]</li>

	<li>В app/config/packages/greggilbert/recaptcha/config.php вводим reCAPTCHA public и private ключи</li>

	<li>Добавляем текст в app/lang/[lang]/validation.php: [php]&quot;recaptcha&quot; =&gt; ':attribute поле заполнено неверно.',[/php]</li>

</ul>

С первоначальной установкой и настройкой закончили.



Использовать плагин просто. Для этого в код формы надо добавить {{ Form::captcha() }}, а в коде, где надо выполнить проверку добавить

[php]

$rules = array(

  ...

  'recaptcha_response_field' =&gt; 'required|recaptcha',

};

[/php]



В /vendor/greggilbert/recaptcha/src/views находим captcha.blade.php и добавляем код после @endif

Мой код для примера:

[html]

&lt;div id=&quot;recaptcha_widget&quot; style=&quot;display:none&quot;&gt;

    &lt;span class=&quot;recaptcha_only_if_audio&quot;&gt;Enter the numbers you hear:&lt;/span&gt;--&gt;

    &lt;div class=&quot;input-group&quot;&gt;

      &lt;input class=&quot;form-control&quot; type=&quot;text&quot; id=&quot;recaptcha_response_field&quot; name=&quot;recaptcha_response_field&quot; placeholder=&quot;Введите символы как на картинке&quot; /&gt;

      &lt;span class=&quot;input-group-btn&quot;&gt;

        &lt;button class=&quot;btn btn-default&quot; type=&quot;button&quot; onclick=&quot;javascript:Recaptcha.reload()&quot;&gt;Обновить&lt;/button&gt;

    &lt;/div&gt;

    &lt;div id=&quot;recaptcha_image&quot;&gt;&lt;/div&gt;

&lt;/div&gt;

[/html]

Дальше настраивайте под себя.

