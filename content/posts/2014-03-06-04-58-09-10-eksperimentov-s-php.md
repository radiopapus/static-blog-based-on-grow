---
$title@: 10-eksperimentov-s-php
author@: Viktor Zharina
$order: 155
$dates:
  published: 2014-03-06 04:58:09
---
<h3>Тест 1. Передача данных по ссылке.</h3>

[php]&lt;?php



function byReference(&amp;$variable = 5) {

	++$variable;

}



$a = 12;

byReference($a);

echo $a;



/* ==================== */

class MyClass{

    public $value;

}



$a = new MyClass;

$a-&gt;value = 1;



$b = $a;

$b-&gt;value = 2;



echo $a-&gt;value;

[/php]

<!--more-->

<h3>Тест 2. Вызов неопределенного метода и использование магической функции __call</h3>



[php]&lt;?php



class TestClass extends TestClassBase {

	public function __call($method, $arguments)

	{

		echo 'method: '.$method.PHP_EOL;

		return $this-&gt;$method($arguments[0]);

	}

}



class TestClassBase {

	protected function getUndefined($name) {

		echo $name.PHP_EOL;

	}

}



$tc = new TestClass();

echo $tc-&gt;getUndefined('undef');

[/php]



<h3>Тест 3. Пример работы со строками</h3>

[php]&lt;?php



$var1 = &quot;10foos&quot;;

$var2 = &quot;20 bars&quot;;



print &quot;implode(&quot;&quot;, array($var1, $var2)) &quot;.implode(&quot;&quot;, array($var1, $var2));

print &quot;var1.var2 &quot;.$var1.$var2;

print &quot;var1 + var2 &quot;. ($var1 + $var2);

[/php]



<h3>Тест 4. Как наследуются статические члены класса</h3>



[php]&lt;?php

class Foo {

    public static $my_static = 'foo';



    public function staticValue() {

        return self::$my_static;

    }

}



class Bar extends Foo {

    public function fooStatic() {

        return self::$my_static;

    }

}



print Bar::$my_static . &quot;\n&quot;;

$bar = new Bar();

print $bar-&gt;fooStatic() . &quot;\n&quot;;

print $bar-&gt;staticValue() . &quot;\n&quot;;

[/php]



<h3>Тест 5. Задача из тестового задания</h3>

[php]&lt;?php

$oranges = 10;

$apples = 5;

$string = &quot;I have %d apples and %d oranges&quot;;



echo PHP_EOL;

printf($string, $apples, $oranges);

echo PHP_EOL;

print sprintf($apples, $oranges);

echo PHP_EOL;

sprintf($string, $oranges, $apples);

[/php]



<h3>Тест 6. Проверял как работает autoload</h3>

[php]&lt;?php



spl_autoload_register(function ($class) {

    include 'classes/' . $class . '.class.php';

});



$obj = new MyTestClass();

echo $obj-&gt;testVar;

[/php]



<h3>Тест 7. str и float</h3>



[php]&lt;?php



$var1 = '0.0123';



if ($var1 &gt; 0) {

	echo PHP_EOL.'More'.PHP_EOL;

} else {

	echo PHP_EOL.'Less'.PHP_EOL;

}

[/php]



<h3>Тест 8. Тестирование исключений. Был очень похожий пример в реальном коде</h3>

[php]&lt;?php 



class MyException extends Exception {

}



class TestException {

	public static function testExc() {

		throw new MyException('MyException '. __CLASS__);

	}

}



class Test2Exception {

	public static function test2Exc() {

		TestException::testExc();

	}

}



class Main {

	public function methodMain() {

		try {

			Test2Exception::test2Exc();

		} catch (Exception $e) {

			echo $e-&gt;getMessage().PHP_EOL;

		}

	}

}



$m = new Main();

$m-&gt;methodMain();

[/php]



<h3>Тест 9. merge и + для массивов </h3>



[php]&lt;?php

	$arr1 = array('comment1', array(1,2,3,4,5,6));

	$arr2 = array('comment2', array(3,3,3,4,5,6));

	echo 'arr1'.PHP_EOL;

	print_r($arr1);

	echo PHP_EOL.'arr2'.PHP_EOL;

	print_r($arr2);

	

	$m = array_merge($arr1, $arr2);

	echo &quot;merge &quot;.PHP_EOL;

	print_r($m);

	$new2 = $arr1 + $arr2;

	echo &quot;plus &quot;.PHP_EOL;

	print_r($new2);

[/php]



<h3>Тест 10. Ленивая проверка. Если первое условие не выполнено, то второе не проверяется</h3>

[php]&lt;?php



$arr = array('t1' =&gt; 0);



if($arr['t1'] AND $arr['t2']) {

	echo 'not';

} else {

	echo 'lazy';

}

[/php]