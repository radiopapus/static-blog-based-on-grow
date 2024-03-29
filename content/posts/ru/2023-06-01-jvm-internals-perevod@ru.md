---
$title@: jvm-internals-perevod
author@: Виктор Жарина
description: Перевод статьи блога jamesdbloom об устройстве jvm.
keywords: jvm,java 7,internals,перевод
image: /static/images/default.png
slugRu: jvm-internals-perevod
$dates:
  published: 2023-06-01 19:19:13
---
<i>От переводчика: <a href="https://blog.jamesdbloom.com/JVMInternals.html">Оригинальная статья</a> написана 24 ноября 2013 года. В нескольких местах я оставил термины без перевода, вроде runtime constant pool и native стек, так как не придумал корректного и благозвучного перевода на русский. Также я дополнил статью примечаниями и дополнительными ссылками и немного изменил структуру заголовков.</i>

Статья объясняет внутреннюю архитектуру виртуальной машины Java (JVM). Диаграмма отображает ключевые внутренние компоненты типичной JVM, которая соответствует спецификации <a href="https://docs.oracle.com/javase/specs/jvms/se7/html/index.html">The Java Virtual Machine Specification Java SE 7 Edition</a>

<img id="non-heap-image" src="https://viktor.zharina.info/static/images/jvm-internals/1_jvm_internal_architecture.png" />

Каждый компонент, изображённый на диаграмме будет описан в двух секциях. [Первая секция](#1-потоки) описывает компоненты, создаваемые для каждого потока, а [вторая секция](#2-разделяемые-потоками-данные) описывает компоненты, существующие независимо от потоков.

[TOC]

# 1 Потоки

Поток - это поток выполнения программы. JVM позволяет приложению иметь несколько потоков выполнения одновременно (concurrently). В Hotspot JVM существует прямое соответствие между Java потоком и native потоком операционной системы. После подготовки всех компонентов Java потока таких как thread-local хранилище, allocations буферы, объекты синхронизации, стеки и программные счётчики будет создан native поток. Native поток (операционной системы) освобождается как только Java поток завершается [1](#-native-vs-java-thread). Операционная система ответственна за планировку и распределение потоков на любом доступном процессоре. Как только native поток инициализирован он вызывает метод run() в Java потоке. Когда run() метод возвращает значение, обрабатываются неперехваченные исключения, потом native поток подтверждает нужно ли завершить работу JVM (needs to be terminated) после завершения потока (напр. последний non-daemon thread - main). Когда поток завершается все ресурсы native потока и Java потока освобождаются. 

## 1.1 Cистемные потоки JVM

Если использовать jconsole или любой отладчик, то можно увидеть несколько потоков запущенных в фоновом режиме. Эти фоновые потоки запускаются в дополнении к основному потоку, который создаётся как часть вызова 
```public static void main(String[])``` и любого потока созданного основным потоком.

<div style="overflow-x:auto;">
	<table>
		<tr>
			<td>Вид потока</td>
			<td>Описание</td>
		</tr>
		<tr>
			<td>Потоки виртуальной машины</td>
			<td>Ожидают появления операций, которые нужны JVM для достижения безопасной точки (safe-point). Причина, по которой эти операции должны выполняться в отдельном потоке, заключается в том, что все они требуют, чтобы JVM находилась в безопасной точке, где модификации кучи не могут произойти. Тип операций, выполняемых этим потоком сборка мусора "stop-the-world", дамп стека потока, приостановка потока (thread suspension) и biased locking revocation.</td>
		</tr>
		<tr>
			<td>Поток периодической задачи</td>
			<td>Отвечает за события таймера (прерывания), которые используются для планирования выполнения периодических задач.</td>
		</tr>
		<tr>
			<td>GC</td>
			<td>Сборки мусора различного типа, которые появляются в JVM</td>
		</tr>
		<tr>
			<td>Потоки компилятора</td>
			<td>Компилируют байт-код в машинный код при выполнении программы (runtime)</td>
		</tr>
		<tr>
			<td>Поток диспетчера сигналов (signal dispatcher thread)</td>
			<td>Получает сигналы отправленные JVM процессу и обрабатывает их внутри JVM, вызывая соответствующие JVM методы.</td>
		</tr>	
	</table>
</div>

Таблица - Основные фоновые системные потоки в Hotspot JVM

## 1.2 Поток

Каждый поток выполнения состоит из следующих компонентов: программный счётчик (ПС), стек, native стек.

### 1.2.1 Программный счётчик (ПС)

Если текущий метод является native методом, тогда ПС не определён, иначе содержит адрес текущей инструкции (или опкод). Все процессоры имеют программный счётчик, обычно ПС инкрементируется после каждой инструкции и, таким образом, хранит адрес следующей инструкции, которая должна быть выполнена. JVM использует ПС для отслеживания того, где она выполняет инструкции, ПС на самом деле будет указывать на адрес памяти в области методов.

### 1.2.2 Стек

Каждый поток имеет свой собственный стек, в котором хранится фрейм для каждого метода, выполняющегося в этом потоке. Стек это LIFO (Last In First Out - последний пришел, первый вышел) структура данных, поэтому текущий выполняющийся метод находится на вершине стека. При каждом вызове метода создаётся новый фрейм.

Напрямую стек не модифицируют, кроме как добавления (push) и удаления (pop) объектов фрейма и поэтому объекты фрейма могут быть аллоцированы в куче (Heap) и нет необходимости в том, чтобы память под эти объекты была непрерывной (contiguous).

### 1.2.3 Native стек

Не все JVM поддерживают native методы, однако те, которые поддерживают обычно создают native стек на каждый Java поток. Если JVM была реализована используя C-linkage модель для Java Native Invocation (JNI) тогда native стек будет как в Си. В этом случае порядок аргументов и возвращаемое значение будет точно такие же как и в обычной программе на языке Си.
Native метод обычно может (зависит от реализации JVM) вызывать Java метод в JVM. Такой вызов будет происходить на Java стеке. Поток покинет native стек и создаст новый фрейм в Java стеке.

### 1.2.4 Ограничения стека

Стек может быть фиксированного или переменного размера. В случае когда поток запрашивает стек большего размера, чем разрешено, то произойдёт ошибка StackOverflowError. Если поток запрашивает новый фрейм, а памяти для его выделения не хватает, тогда произойдёт ошибка OutOfMemoryError.

### 1.2.5 Фрейм

Новый фрейм создаётся и добавляется (pushed) на вершину стека при каждом вызове метода. Фрейм удаляется (popped), когда метод штатно завершается, или, если во время выполнения метода возникло необработанное исключение. Более подробная информация по обработке исключений будет дана в [Таблице Исключений](#2.12-таблица-исключений).

Каждый фрейм содержит:

1. Массив локальных переменных

2. Возвращаемое значение

3. Стек операндов 

4. Ссылку на runtime constant pool для класса текущего метода

### 1.2.6 Массив локальных переменных

Массив локальных переменных содержит все переменные, которые нужны при выполнении метода, включая ссылку на `this`, все параметры метода и другие локальные переменные. Для методов класса (статических методов) параметры метода отсчитываются от нуля, однако для метода экземпляра класса нулевой слот зарезервирован для `this`.

Локальные переменные могут иметь следующий тип: boolean, byte, char, long, short, int, float, double, ссылка (reference), возвращаемый адрес (returnAddress).

Все типы занимают один слот в массиве локальных переменных, исключая long и double, которые занимают два последовательных слота так как эти типы имеют размер 64 бита, вместо 32 бит.

### 1.2.7 Стек операндов

Cтек операндов используется во время выполнения инструкций байт-кода и работает подобно регистрам общего назначения в процессоре. Большая часть JVM байт-кода содержит операции со стеком операндов, добавляя (push), удаляя(pop), дублируя, меняя местами (swap) или выполняя операции, которые производят или потребляют значения. Таким образом, инструкции, которые перемещают значения между массивом локальных переменных и стеком операндов очень часто встречаются в байт-коде. Например, простая инициализация переменной будет представлена в виде двух инструкций байт-кода, которые взаимодействуют со стеком операндов. 

```int i;```  компилируется в следующий байт-код:

```java
 0: iconst_0  // добавить 0 на вершину стека операндов
 1: istore_1  // считать значение из стека операндов и сохранить как локальную переменную с номером 1.
```

За более детальным объяснением взаимодействия между массивом локальных переменных, стеком операндов и runtime constant pool обращайтесь к секции [Структура Файла Класса](#2.6-структура-файла-класса) ниже.

### 1.2.8 Динамическое связывание

Каждый фрейм содержит ссылку на runtime constant pool. Ссылка указывает на constant pool для выполняемого метода класса этого фрейма. Эта ссылка реализует динамическое связывание.

Cи/Cи++ код обычно компилируется в объектный файл, потом несколько объектных файлов компонуются вместе в один артефакт, вроде исполняемого файла или библиотеки. Во время фазы компоновки символические ссылки на каждый объект заменяются реальными адресами памяти. В Java эта фаза компоновки выполняется динамически во время исполнения (at runtime).

Когда Java класс скомпилирован, все ссылки на переменные и методы хранятся в class constant pool как символические ссылки. Символические ссылки это логические ссылки, и они не указывают на адреса физической памяти. JVM реализация может выбрать когда произвести замену ссылок на адреса (resolve), это может произойти когда класс прошёл верификацию, после загрузки, так называемое статическое связывание, или же это может произойти при первом вызове (позднее) связывание.

Однако JVM придётся вести себя так, как будто связывание произошло при первом использовании ссылки, и выбрасывать ошибки связывания, которые произойдут в этот момент. [2](#tip-java-resolution)

Binding это процесс когда поле, метод или класс и соответствующие им символические ссылки будут заменены на прямые ссылки, это случается только один раз потому что символические ссылки будут полностью заменены на адреса физической памяти. Если символическая ссылка ссылается на класс, который ещё не был загружен, то этот класс загружается. Каждая прямая ссылка хранится как смещение в памяти ассоциированная с расположением метода или переменной во время исполнения.

# 2 Разделяемые потоками данные

## 2.1 Куча (Heap)

Кучу используют для аллоцирования объектов и массивов во время исполнения программы. Массивы и объекты не могут хранится на стеке потому что фрейм имеет фиксированный размер. Фрейм хранит ссылки, которые указывают на объекты или массивы на куче. В отличие от переменных, которые имеют примитивный тип и ссылок в локальном массиве переменных (в каждом фрейм) объекты всегда хранятся на куче и поэтому они не удаляются, когда метод завершается. Вместо этого объекты могут быть удалены из кучи только с помощью сборщика мусора.
Для облегчения работы сборщика мусора куча поделена на три секции: Young generation - его часто делят на Eden и Survivor части, Old generation также называемое Tenured generation и Permanent generation.

## 2.2 Управление памятью

Объекты и массивы никогда явно не деаллоцируются из памяти вместо этого сборщик мусора автоматически подчищает их.

Обычно это работает так:

1. Новые объекты или массивы создаются в Young generation.

2. Легкая сборка мусора (minor garbage collection) работает в Young generation. Объекты, которые все ещё являются используемыми будут перемещены из eden поколения в survivor. 

3. Основная сборка мусора (major), которая обычно ставит потоки на паузу, перемещает объекты между поколениями, объекты, которые все ещё являются используемыми будут перемещены из young в old (tenured) generation. 

4. Permanent generation заполняется вместе с old generation. Любое из них считается заполненным, если заполнено хотя бы одно из них.

## 2.3 Non-Heap память

Это объекты, которые логически рассматриваются как часть JVM, но они не создаются на куче (Heap).

Non-heap память состоит из Permanent Generation([области методов](#2.5-область-методов) и [interned строки](#2.13-interned-строки)) и Code Cache, используемый хранения методов, которые были скомпилированы в native код JIT-компилятором.

## 2.4 J I T компиляция

Java байт код является интерпретируемым и это не настолько быстро как выполнение кода напрямую на целевой платформе. Для увеличения производительности виртуальная машина Oracle Hotspot оценивает наиболее часто используемые части байт кода и компилирует их в код целевой платформы. В дальнейшем этот код хранится в Code Cache в non-heap памяти. Виртуальная машина Hotspot выбирает наиболее подходящий по времени способ между взятием кода из Code Cache и выполнением интерпретируемого кода.

## 2.5 Область методов

Область методов хранит информацию по каждому классу:

- Ссылка на загрузик классов 

- Run Time Constant Pool

    - Числовые константы

    - Ссылки на поля

    - Ссылки на методы

    - Аттрибуты

- Данные полей (на каждое поле)

    - Имя

    - Тип

    - Модификаторы

    - Аттрибуты

- Данные метода (на каждый метод)

	- Имя

    - Возвращаемый тип

    - Типы параметров (согласно порядку в сигнатуре)

    - Модификаторы

    - Аттрибуты

- Код метода (на каждый метод)

    - Байт код

    - Размер стека операндов

    - Размер локальной переменной

    - Таблица локальных переменных

    - Таблица исключений (на каждый обработчик)

    	- Точка выхода

    	- Точка выхода

    	- Смещение ПС для кода обработчика

    	- Индекс в constant pool для обрабатываемого (caught) класса исключения

Все потоки разделяют одну и туже область методов, поэтому доступ к данным из этой области и процесс динамического связывания должен быть потоко-безопасным. Если два потока пытаются получить доступ к полю или методу класса, который ещё не был загружен, то он должен быть загружен только один раз и оба метода не должны выполняться до тех пор, пока он не будет загружен.

## 2.6 Структура файла класса

Скомпилированный класс имеет следующую структуру:

```
ClassFile {
    u4          magic;
    u2          minor_version;
    u2          major_version;
    u2          constant_pool_count;
    cp_info     contant_pool[constant_pool_count - 1];
    u2          access_flags;
    u2          this_class;
    u2          super_class;
    u2          interfaces_count;
    u2          interfaces[interfaces_count];
    u2          fields_count;
    field_info      fields[fields_count];
    u2          methods_count;
    method_info     methods[methods_count];
    u2          attributes_count;
    attribute_info  attributes[attributes_count];
}
```

<div style="overflow-x:auto;">
	<table>
		<tr>
			<td>Имя</td>
			<td>Описание</td>
		</tr>
		<tr>
			<td>magic, minor_version, major_version</td>
			<td>Содержит информацию о версии класса и версии JDK, для которой он был скомпилирован.</td>
		</tr>
		<tr>
			<td>constant_pool</td>
			<td>Похоже на таблицу символов, но содержит больше информации. Ниже будет более детальная информация.</td>
		</tr>
		<tr>
			<td>access_flags</td>
			<td>Список модификаторов класса.</td>
		</tr>
		<tr>
			<td>this_class</td>
			<td>Указатель на constant pool предоставляющий полное имя класса (напр. org/author/go/Bar).</td>
		</tr>
		<tr>
			<td>super_class</td>
			<td>Указатель на constant pool предоставляющий символическую ссылку  на родительский класс( напр. java/lang/Object).</td>
		</tr>
		<tr>
			<td>interfaces</td>
			<td>Массив указателей на constant pool предоставляющий символические ссылки на все интерфейсы которые были реализованы.</td>
		</tr>
		<tr>
			<td>fields</td>
			<td>Массив указателей на constant pool дающий полное описание каждого поля.</td>
		</tr>
		<tr>
			<td>methods</td> 
			<td>Массив указателей на constant pool дающий полное описание сигнатуры каждого метода, если метод не абстрактный или для целевой платформы (native) байт код также будет представлен.</td>
		</tr>
		<tr>
			<td>attributes</td>
			<td>Массив различных значений, предоставляющий дополнительную информацию о классе, включая любые аннотации вроде RetentionPolicy.CLASS или RetentionPolicy.RUNTIME.</td>
		</tr>
	</table>
</div>
Таблица - Описание структуры класса.

Рассмотрим байт-код в скомпилированном Java классе с помощью команды javap.

Если скомпилировать следующий класс

```java 

package org.jvminternals;

public class SimpleClass {

    public void sayHello() {
        System.out.println("Hello");
    }

}
```
Тогда после запуска команды
```javap -v -p -s -sysinfo -constants classes/org/jvminternals/SimpleClass.class```

на экран будет выведен следующий текст 

<pre>
public class org.jvminternals.SimpleClass
  SourceFile: "SimpleClass.java"
  minor version: 0
  major version: 51
  flags: ACC_PUBLIC, ACC_SUPER
Constant pool:
   #1 = Methodref          #6.#17         //  java/lang/Object."<init>":()V
   #2 = Fieldref           #18.#19        //  java/lang/System.out:Ljava/io/PrintStream;
   #3 = String             #20            //  "Hello"
   #4 = Methodref          #21.#22        //  java/io/PrintStream.println:(Ljava/lang/String;)V
   #5 = Class              #23            //  org/jvminternals/SimpleClass
   #6 = Class              #24            //  java/lang/Object
   #7 = Utf8               <init>
   #8 = Utf8               ()V
   #9 = Utf8               Code
  #10 = Utf8               LineNumberTable
  #11 = Utf8               LocalVariableTable
  #12 = Utf8               this
  #13 = Utf8               Lorg/jvminternals/SimpleClass;
  #14 = Utf8               sayHello
  #15 = Utf8               SourceFile
  #16 = Utf8               SimpleClass.java
  #17 = NameAndType        #7:#8          //  "<init>":()V
  #18 = Class              #25            //  java/lang/System
  #19 = NameAndType        #26:#27        //  out:Ljava/io/PrintStream;
  #20 = Utf8               Hello
  #21 = Class              #28            //  java/io/PrintStream
  #22 = NameAndType        #29:#30        //  println:(Ljava/lang/String;)V
  #23 = Utf8               org/jvminternals/SimpleClass
  #24 = Utf8               java/lang/Object
  #25 = Utf8               java/lang/System
  #26 = Utf8               out
  #27 = Utf8               Ljava/io/PrintStream;
  #28 = Utf8               java/io/PrintStream
  #29 = Utf8               println
  #30 = Utf8               (Ljava/lang/String;)V
{
  public org.jvminternals.SimpleClass();
    Signature: ()V
    flags: ACC_PUBLIC
    Code:
      stack=1, locals=1, args_size=1
        0: aload_0
        1: invokespecial #1    // Method java/lang/Object."<init>":()V
        4: return
      LineNumberTable:
        line 3: 0
      LocalVariableTable:
        Start  Length  Slot  Name   Signature
          0      5      0    this   Lorg/jvminternals/SimpleClass;

  public void sayHello();
    Signature: ()V
    flags: ACC_PUBLIC
    Code:
      stack=2, locals=1, args_size=1
        0: getstatic      #2    // Field java/lang/System.out:Ljava/io/PrintStream;
        3: ldc            #3    // String "Hello"
        5: invokevirtual  #4    // Method java/io/PrintStream.println:(Ljava/lang/String;)V
        8: return
      LineNumberTable:
        line 6: 0
        line 7: 8
      LocalVariableTable:
        Start  Length  Slot  Name   Signature
          0      9      0    this   Lorg/jvminternals/SimpleClass;
}
</pre>

Вывод содержит три основных секции: constant pool, конструктор и sayHello метод.

1. Constant Pool содержит ту же информацию, что и [таблица символов](#2.12-таблица-символов)

2. Методы, где каждый содержит четыре области:
	- сигнатура и флаги доступа
	- LineNumberTable предоставляет информацию отладчику для отображения соответствия строки и инструкции байт-кода, например строка 6 в Java коде соответствует байт-коду 0 в sayHello методе и строка 7 соответствует байт-коду 0.
	- LocalVariableTable список всех локальных переменных внутри фрейма, в обоих примерах представлена только одна локальная переменная this.

Следующие байт-код операнды используются в SimpleClass

<div style="overflow-x:auto;">
	<table>
		<tr>
			<td>Операнд</td>
			<td>Описание</td>
		</tr>
		<tr>
			<td>aload_0</td>
			<td>Опкод из группы опкодов формата aload_<n>. Они все загружают ссылку на объект в стек операндов. <n> ссылается на расположение в массиве локальных переменных к которой обращаются, но может быть только 0, 1, 2 или 3. Есть и другие похожие опкоды для загрузки значений, которые не являются ссылками на объект iload_<n>, lload_<n>, float_<n> и dload_<n> где i для типа int, l для long, f для float и d для double. Локальные переменные с индексом выше трех могут быть загружены с помощью iload, lload, float, dload и aload. Все эти опкоды принимают один операнд, который указывает на локальную переменную для загрузки.</td>
		</tr>
		<tr>
			<td>ldc</td>
			<td>Опкод используется для добавления (push) константы из runtime constant pool в стек операндов</td>
		</tr>
		<tr>
			<td>getstatic</td>
			<td>Опкод используется для добавления (push) статического значения из статического поля представленного в runtime constant pool в стек операндов</td>
		</tr>
		<tr>
			<td>invokespecial, invokevirtual</td>
			<td>Опкоды из группы опкодов, которые вызывают методы вроде invokedynamic, invokeinterface, invokespecial, invokestatic, invokevirtual. В этом файле представлены invokespecial и invokevirutal, разница между ними в том, что invokevirutal вызывает метод (<i>прим. переводчика виртуальный</i>) на объекте. invokespecial инструкция используется для вызова метода инициализации экземпляра класса точно также как приватные методы и родительские методы (superclass) текущего класса.</td>
		</tr>
		<tr>
			<td>return</td>
			<td>Опкод из группы опкодов вроде ireturn, lreturn, freturn, dreturn, areturn и return. Каждый из этих опкодов возвращает разные типы где i это для типа int, l для long, f для float, d для double и a для ссылки на объект. Опкод без буквы вначале возвращает void.</td>
		</tr>
	</table>
</div>
Таблица - Операнды скомпилированного класса SimpleClass

Как и в любом типичном байт-коде большинство операндов взаимодействуют с локальными переменными, стеком операндов и runtime constant pool.

Конструктор имеет две инструкции. Первая кладёт this на вершину стека операндов, потом конструктор вызывается для родительского класса, который читает значение this и таким образом вытаскивает его из стека операндов.  

<img src="https://viktor.zharina.info/static/images/jvm-internals/2_bytecode_explanation_simpleclass_image.png" />

sayHello() более сложный так как ему нужно заменить символические ссылки на настоящие ссылки, используя runtime constant pool, как объяснялось выше. Первый операнд getstatic используется, чтобы положить ссылку на статическое поле `out` класса System на стек операндов. Следующий операнд ldc кладёт строку "Hello" на стек операндов. Последний операнд invokevirtual вызывает println метод из System.out, который читает (pop) "hello" со стека операндов и использует его как аргумент и создаёт новый фрейм для текущего потока.

<img src="https://viktor.zharina.info/static/images/jvm-internals/3_bytecode_explanation_sayhello_smaller.png" />

## 2.7 Загрузчик классов

JVM запускается путём загрузки начального класса с помощью bootstrap загрузчика классов (bootstrap classloader). Потом класс линкуется и инициализируется до вызова ```public static void main(String[])``` Выполнение этого метода приводит к загрузке, компоновке и инициализации других классов и интерфейсов по мере необходимости.

### 2.7.1 Загрузка

Загрузка (Loading) это процесс поиска файла класса представленного классом или интерфейсом с конкретным именем и считывание его в массив байтов. Далее байты анализируются для подтверждения того, что они представляют объект Class и имеют корректную major и minor версии. Любой класс или интерфейс объявленный как прямой родительский класс (super class) также загружается. Как только это завершено 
из двоичного представления создаётся объект класса или интерфейса.

### 2.7.2 Линковка

Линковка (Linking) - это процесс подготовки и проверки типа объекта класса или интерфейса и его прямых родителей (superclass, superinterfaces). Линковка состоит из трёх шагов: верификации, подготовки и опционально resolving.

Верификация (Verifying) это процесс подтверждения того, что класс или интерфейс структурно корректны и подчиняются семантическим требованиям языка программирования Java и JVM, например  выполняются следующие проверки:
	1. последовательная и корректно отформатированная таблица символов
	2. final методы / классы не переопределяются
	3. соблюдается корректность ключевых слов управления доступом (прим. private, protected, public)
	4. методы имеют корректное число и типы параметров
	5. байт-код корректно работает со стеком
	6. переменные инициализированы перед чтением
	7. переменные имеют значения соответствующего типа

Проведение этих проверок в течении фазы верификации означает, что нет необходимости проводить их во время выполнения (runtime). Верификация во время линковки замедляет загрузку класса, однако позволяет избежать множественных проверок при выполнении байт-кода.

Подготовка (Preparing) включает выделение памяти (allocation) для статического хранилища и других структур данных, используемых JVM, таких как таблица методов. Статические поля создаются и инициализируются значениями по-умолчанию, однако на этом этапе не выполняется код и инициализации, так это это происходит в рамках самой инициализации.

Resolving это необязательный этап, который состоит из проверки символических ссылок путём загрузки классов или интерфейсов, на которые ссылаются и проверки корректности этих ссылок. Этот этап может быть отложен до момента их использования в байт-коде.

### 2.7.3 Инициализация

Инициализация класса или интерфейса состоит из выполнения метода инициализации ```<clinit>```
<img src="https://viktor.zharina.info/static/images/jvm-internals/4_class_loading_linking_initializing.png" />

## 2.7.4 Виды загрузчиков

В JVM есть несколько загрузчиков классов с разными ролями. Каждый загрузчик делегирует полномочия своему родительскому загрузчику (который его загрузил), за исключением bootstrap загрузчика, который является верхним (прим. смотри иерархию ниже) загрузчиком классов.

<div style="overflow-x:auto;">
	<table>
		<tr>
			<td>Вид загручика</td>
			<td>Описание</td>
		</tr>
		<tr>
			<td>Bootstrap</td>
			<td>Обычно написан на языке целевой платформы, потому что загружается раньше JVM. Отвечает за загрузку основных Java API, вроде rt.jar. Он только загружает классы, найденные в classpath, которые имеют высокий приоритет доверия, загрузчик пропускает большую часть проверок, которые выполняются для обычных классов.</td>
		</tr>
		<tr>
			<td>Extension</td>
			<td>Загружает классы из стандартного расширения Java API, например расширенные функции безопасности (security extension).</td>
		</tr>
		<tr>
			<td>System</td>
			<td>Загрузчик по-умолчанию, который загружает классы приложения из classpath.</td>
		</tr>
		<tr>
			<td>User Defined</td>
			<td>Может быть использован для загрузки классов приложения. Пользовательский загрузчик используют в определённых случаях, вроде перезагрузки классов во время выполнения программы или для разделения между группами загруженных классов необходимого web-серверам, вроде Tomcat.</td>
		</tr>	
	</table>
</div>

Все загруженные классы содержат ссылки на загрузчик, который их загрузил. Загрузчик также содержит ссылки на все классы, которые он загрузил.

<img src="https://viktor.zharina.info/static/images/jvm-internals/5_class_loader_hierarchy.png" />

## 2.7.5 Ускоренная загрузка классов

Функция Class Data Sharing (CDS) была представлена начиная с 5-й версии HotSpot JVM. Во время процесса установки JVM установщик загружает множество ключевых JVM классов, вроде rt.jar, в память. CDS уменьшает время загрузки классов увеличивая скорость запуска JVM и позволяет делиться этими данными, уменьшая объем занимаемой ими памятью. [3](#tip-cds)

## 2.8 Где расположена область методов

[Спецификация JVM чётко определяет](https://docs.oracle.com/javase/specs/jvms/se7/html/)
Хотя область методов (method area) и является логической частью кучи (heap), простые реализации могут не собирать мусор (gc) и выполнять сжатие этой области. Несмотря на это jconsole для Oracle JVM отображают область методов (Method Area) и Code Cache как часть Non-Heap. OpenJDK код отображает CodeCache как отдельную область (прим. памяти) виртуальной машины по отношению к ObjectHeap.

## 2.9 Run Time Constant Pool

JVM поддерживает Constant Pool для каждого типа, runtime структура данных похожа на таблицу символов, но содержит больше данных. Байт-коды в Java требуют данных, которые часто слишком велики, чтобы хранится прямо в байт-коде, вместо этого эти данные хранятся в constant pool, а байт-код содержит ссылки на constant pool. Run time constant pool используется в [динамическом связывании](#1.2.8-динамическое-связывание).

Следующие типы данных хранятся в constant pool:
- числовые литералы
- строковые литералы
- ссылки на класс
- ссылки на поля
- ссылки на методы

Для примера следующий код ```Object foo = new Object();```

В байт-коде будет выглядет так:
<pre>
 0: 	new #2 		    // Class java/lang/Object
 1:	dup
 2:	invokespecial #3    // Method java/ lang/Object "<init>"( ) V
</pre>

За ```new``` opcode (код операнда) следует ```#2``` операнд. Этот операнд является указателем и ссылается на вторую запись в constant pool. Вторая запись это ссылка на класс, эта запись ссылается на другую запись в constant pool, содержащую имя класса в виде UTF8 строки со значением ```// Class java/lang/Object```. Эта символическая ссылка может быть использована для поиска класса  ```java.lang.Object```. ```new``` опкод создаёт объект класса и инициализирует его переменные. Ссылка на новый объект класса добавляется в стек операндов. Затем dup опкод создаёт дополнительную копию ссылки с вершины стека операндов и добавляет её на вершину стека операндов. Наконец, на строке 2 вызывается метод инициализации экземпляра класса посредством `invokespecial`. Этот операнд тоже содержит ссылку на constant pool. Инициализирующий метод считывает (pop) ссылку с вершины стека операндов в качестве аргумента метода. В самом конце остаётся только одна ссылка на новый объект, который был создан и инициализирован.

Если вы скомпилируете следующий класс.
```java
package org.jvminternals;

public class SimpleClass {

    public void sayHello() {
        System.out.println("Hello");
    }

}
```

Constant pool в сгенерированном файле будет выглядеть примерно так:

```
Constant pool:

   #1 = Methodref          #6.#17         //  java/lang/Object."<init>":()V
   #2 = Fieldref           #18.#19        //  java/lang/System.out:Ljava/io/PrintStream;
   #3 = String             #20            //  "Hello"
   #4 = Methodref          #21.#22        //  java/io/PrintStream.println:(Ljava/lang/String;)V
   #5 = Class              #23            //  org/jvminternals/SimpleClass
   #6 = Class              #24            //  java/lang/Object
   #7 = Utf8               <init>
   #8 = Utf8               ()V
   #9 = Utf8               Code
  #10 = Utf8               LineNumberTable
  #11 = Utf8               LocalVariableTable
  #12 = Utf8               this
  #13 = Utf8               Lorg/jvminternals/SimpleClass;
  #14 = Utf8               sayHello
  #15 = Utf8               SourceFile
  #16 = Utf8               SimpleClass.java
  #17 = NameAndType        #7:#8          //  "<init>":()V
  #18 = Class              #25            //  java/lang/System
  #19 = NameAndType        #26:#27        //  out:Ljava/io/PrintStream;
  #20 = Utf8               Hello
  #21 = Class              #28            //  java/io/PrintStream
  #22 = NameAndType        #29:#30        //  println:(Ljava/lang/String;)V
  #23 = Utf8               org/jvminternals/SimpleClass
  #24 = Utf8               java/lang/Object
  #25 = Utf8               java/lang/System
  #26 = Utf8               out
  #27 = Utf8               Ljava/io/PrintStream;
  #28 = Utf8               java/io/PrintStream
  #29 = Utf8               println
  #30 = Utf8               (Ljava/lang/String;)V
```

<div style="overflow-x:auto;">
	<table>
		<tr>
			<td>Имя типа</td>
			<td>Описание</td>
		</tr>
		<tr>
			<td>Integer</td>
			<td>Константа для 4-х байтных целочисленных чисел.</td>
		</tr>
		<tr>
			<td>Long</td>
			<td>Константа для 8-ми байтных целочисленных чисел.</td>
		</tr>
		<tr>
			<td>Float</td>
			<td>Константа для 4-х байтных чисел с плавающей запятой.</td>
		</tr>
		<tr>
			<td>Double</td>
			<td>Константа для 8-ми байтных чисел с плавающей запятой.</td>
		</tr>
		<tr>
			<td>String</td>
			<td>Cтроковая константа указывающая на другую Utf8 запись в constant pool, который содержит настоящие байты данных.</td>
		</tr>
		<tr>
			<td>Utf8</td>
			<td>Набор байтов представляющих Utf8 закодированную последовательность символов.</td>
		</tr>
		<tr>
			<td>Class</td>
			<td>Константа класса, которая указывает на другую Utf8 запись в constant pool которая содержит полное имя класса (fully qualified class name) во внутреннем формате JVM (используется в динамическом связывании).</td>
		</tr>
		<tr>
			<td>NameAndType</td>
			<td>Разделённая : пара значений, где каждое значение указывает на другие записи в constant pool. Первое значение (слева от :) указывает на строковую Utf8 запись, которая является именем метода или именем поля. Второе значение указывает на Utf8 запись которая представляет тип. В случае поля это полное имя класса (fully qualified class name), в случае метода это список полных имён классов, где каждый элемент списка соответствует параметру метода.</td>
		</tr>
		<tr>
			<td>Fieldref, Methodref, InterfaceMethodref</td>
			<td>Разделённая . пара значений, где каждое значение указывает на другие записи в constant pool. Первое значение (слева от .) указывает на запись класса, второе значение указывает на NameAndType запись.</td>
		</tr>
	</table>
</div>
Таблица - Типы Сonstant pool

## 2.10 Таблица исключений

Таблица исключений хранит информацию на каждый обработчик исключения:
- Начальная точка
- Конечная точка
- Смещение ПС (PC offset) для кода обработчика
- Constant pool индекс для перехватываемого класса исключений.

Если в методе определён обработчик ```try-catch``` или ```try-finally```, то будет создана таблица исключений. Она содержит информацию для каждого обработчика исключений или блока finally, включая область, в которой применяется обработчик, тип обрабатываемого исключения и код обработчика.

При возникновении исключения JVM ищет подходящий обработчик в текущем методе, если он не найден, то метод завершается выгружая текущий фрейм из стека и исключение возникает в вызывающем методе (новый текущий фрейм). Если обработчик исключения не найден до того как все фреймы будут вычитаны (pop) из стека, тогда поток завершается. Это также может привести к завершению работы самой JVM, если исключение возникло в последнем главном потоке (non-daemon main thread).

Обработчики исключений ```finally``` соответствуют всем типам исключений и поэтому всегда выполняются при возникновении исключений. В случае когда исключение не было выброшено, блок ```finally``` все равно выполняется в конце метода, это достигается путём перехода к коду обработчика finally непосредственно перед выполнением оператора return.

## 2.11 Таблица символов

В дополнении к runtime constant pools для каждого типа Hotspot JVM имеет таблицу символов, хранящуюся в [permanent generation](#non-heap-image). Таблица символов это хеш-таблица соответствия указателя на символ и символа (напр. Hashtable<Symbol*, Symbol>), а также содержит указатели на символы, которые хранятся в runtime constant pool в каждом классе.

Подсчёт ссылок используется для контроля при удалении символа из таблицы символов. Например, когда класс выгружается количество ссылок на все символы, хранящиеся в run time constant pool уменьшается. Когда счётчик ссылок на символ в таблице символов становится равным нулю, тогда таблица символов "понимает" что на символ больше никто не ссылается и символ выгружается из таблицы символов. Как для таблицы символов, так и для interned строк (см. ниже) все записи хранятся в определённом формате для повышения эффективности и обеспечения того, что каждая запись появляется только один раз.

## 2.12 Interned строки

Спецификация языка Java требует, чтобы одинаковые строковые литералы, содержащие одинаковую последовательность Unicode символов ссылались один и тот же экземпляр String. Кроме того, при вызове функции String.intern() для экземпляра String должна быть возвращена ссылка, которая была бы идентична возвращённой ссылке, если бы строка была литералом. Поэтому справедливо следующее: ```("j" + "v" + "m").intern() == "jvm"```

В Hotspot JVM interned строки хранятся в таблице строк, которая является Hashtable отображающая указатели объектов на символы (напр. Hashtable<oop, Symbol>) и хранится в permanent generation. Все записи также хранятся в определённом формате для повышения эффективности и обеспечения того, что каждая запись появляется только один раз.

Строковые литералы автоматически интернируются (interned) компилятором и добавляются в таблицу символов при загрузке класса. Кроме того, экземпляры класса String могут быть явно interned вызовом String.intern(). При вызыве String.intern(), если таблица символов уже содержит строку, возвращается ссылка на нее, иначе строка будет добавлена в таблицу строк и будет возвращена ссылка на нее.

<p id="tip-native-vs-java-thread">(1) Следует отдельно отметить что Java поток и native поток это не одно и тоже. Первый является логическим объектом, второй более низкоуровневым объектом, которым управляет операционная система.</p>

<p id="tip-java-resolution">(2) JVM нужно произвести замену символической ссылки на ссылку на класс или метод. Этот процесс происходит при первом использовании, а не при компиляции или загрузке в память. Если при выполненнии JVM выявляет ошибку (например класс на который ссылались не существует), то будет брошено исключение. Таким образом возможна ситуация, когда JVM запущена, но при выполнении кода возникли ошибки.</p>

<p id="tip-cds">(3) A memory-mapped файл содержащий байт-код для классов Java в сжатом виде.</p>