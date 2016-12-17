---
$title@: Ciklicheskij-vyzov-nesushhestvuyushhego-metoda
author@: Viktor Zharina
$order: 113
$dates:
  published: 2013-10-23 08:48:34
---
[php]

&lt;?php



class TestClass extends TestClassBase {

	private $name = 'name';



	public function __call($method, $arguments)

	{

		echo 'method: '.$method.PHP_EOL;

		return $this-&gt;$method($this-&gt;name);

	}

}



class TestClassBase {

	protected function getName($name) {

		echo $name.PHP_EOL;

	}



	/*

	protected function getUndefined($name) {

		echo $name.PHP_EOL;

	}

	*/

}





$tc = new TestClass();

//echo $tc-&gt;getName('testname');

echo $tc-&gt;getUndefined();

[/php]



Выше происходит следующее: вызываем метод, которого нет ни в классе TestClass, ни в его родителе. Поскольку __call перехватывает такие вызовы, в нем снова $this->$method, то так продолжается до тех пор, пока не будет превышена PHP-константа ограничения вызовов метода.