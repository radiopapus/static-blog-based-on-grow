---
$title@: razbiraemsya-s-coroutine-v-kotlin--chast-chetvertaya
author@: Виктор Жарина
description: корутины в kotlin и модификатор suspend
keywords: kotlin,coroutine,suspend,modifier,callback,async
image: /static/images/default.png
slugRu: razbiraemsya-s-coroutine-v-kotlin--chast-chetvertaya
$dates:
  published: 2024-07-06 10:58:15
---
## В предыдущих сериях

В первой статье была представлена идея корутин, которую высказал Мэлвин Конвей в 1963 году. Он предложил рассматривать функции как независимые программные модули, которые передают управление друг другу вместо подхода, когда программа содержит набор процедур, вызывающих друг друга. Во второй статье, основанной на тексте Саймона Стетхема описана наивная реализация идеи корутин на языке Си и продемонстрирована возможность "приостановить" и "возобновить" выполнение функции. В третьей части было сравнение корутин с существующими механизмами, вроде потоков, функций обратного вызова и ответ на вопрос: "Зачем использовать корутины?". Идея корутин была озвучена много лет назад, но стала набирать популярность и поддерживаться многим языками программирования последние  лет 15. Корутины менее требовательны к ресурсам и позволяют писать код, который работает асинхронно при этом выглядит как привычный синхронный код. Прежде чем переходить к деталям корутин в Kotlin надо все-таки сделать реверанс в сторону работы асинхронных движков. Во-первых корутины в Kotlin под капотом используют функции обратного вызова и поэтому надо понимать как эти функции выполняются в операционной системе. Во-вторых, в предыдущих текстах я практически не говорил про работу асинхронных движков, а это довольно большой и важный пласт информации, который я не хочу оформлять в виде отдельного текста. Поэтому прежде чем читать дальше предлагаю читателю посмотреть <a href="https://www.youtube.com/watch?v=hxMiDiOmnP4">13. Computer Science Center - Асинхронный ввод/вывод. Корутины</a> и <a href="https://www.youtube.com/watch?v=bSJp3lRjU7k
">C++ User group - Антон Полухин — Анатомия асинхронных движков</a>  для общего понимания дальнейшего повествования. 

## Корутина в Kotlin и модификатор suspend

В Kotlin есть специальный модификатор suspend, с помощью которого можно отметить обычную функцию и указать компилятору, что функция будет корутиной. Suspend не запускает корутину, а является указанием преобразовать функцию так, что она может быть приостановлена и возобновлена при выполнении.

Функция func
```kotlin
suspend fun func() { }
```
после компиляции будет преобразована в функцию с дополнительным параметром Continuation.

```java
@Nullable  
public final Object func(@NotNull Continuation $completion) {  
   return Unit.INSTANCE;  
}
```

Текст из спецификации языка ёмко описывает идею корутинизации обычной функции. 

<blockquote>
Every suspending function is associated with a generated `Continuation` subtype, which handles the suspension implementation; the function itself is adapted to accept an additional continuation parameter to support the Continuation Passing Style.
</blockquote>
Continuation Passing Style это академический термин, который по своей сути является функцией обратного вызова (callback). Continuation это тип, точнее interface, по сути контейнер, который содержит функцию обратного вызова resumeWith и контекст CoroutineContext.

```kotlin
public interface Continuation<in T> {
    public abstract val context: CoroutineContext
    public abstract fun resumeWith(result: Result<T>)
}
```

Про CoroutineContext поговорим в следующий раз, это контейнер, который хранит дополнительную информацию необходимую для работы корутины во время приостановки и возобновления. Не хватает только компонента для реализации конечного автомата, подобный наивной реализации на Си из второй статьи. Рассмотрим следующий пример.

```kotlin
import kotlinx.coroutines.runBlocking  
  
suspend fun suspendFunction(): Int = 1  
fun function(data: Int) = data  
  
fun main(): Unit = runBlocking {  
        val r1 = suspendFunction()  
        function(r1)  
    }
}
```
Пример кода с вызовом корутины и обычный функции

Есть функция suspendFunction, отмеченная как suspend и есть обычная (не suspend) функция function. runBlocking это так называемый coroutine builder, про который мы пока забудем. Он нужен чтобы из обычной функции main вызвать корутину.
Декомпилируем код и посмотрим на результат

```java
import kotlin.Metadata;
import kotlin.ResultKt;
import kotlin.Unit;
import kotlin.coroutines.Continuation;
import kotlin.coroutines.CoroutineContext;
import kotlin.coroutines.intrinsics.IntrinsicsKt;
import kotlin.coroutines.jvm.internal.Boxing;
import kotlin.jvm.functions.Function2;
import kotlin.jvm.internal.Intrinsics;
import kotlinx.coroutines.BuildersKt;
import org.jetbrains.annotations.NotNull;
import org.jetbrains.annotations.Nullable;

public final class MainKt {
   public static final Object suspendFunction(Continuation $completion) {
      return Boxing.boxInt(1);
   }

   public static final int function(int data) {
      return data;
   }

   public static final void main() {
      BuildersKt.runBlocking$default((CoroutineContext)null, (Function2)(new Function2((Continuation)null) {
         int label;

         public final Object invokeSuspend(Object $result) {
            Object var3 = IntrinsicsKt.getCOROUTINE_SUSPENDED();
            Object var10000;
            switch (this.label) {
               case 0:
                  this.label = 1;
                  var10000 = MainKt.suspendFunction(this);
                  if (var10000 == var3) {
                     return var3;
                  }
                  break;
               case 1:
                  var10000 = $result;
                  break;
               default:
                  throw new IllegalStateException("call to 'resume' before 'invoke' with coroutine");
            }

            int r1 = var10000.intValue();
            MainKt.function(r1);
            return Unit.INSTANCE;
         }

         public final Continuation create(Object value, Continuation completion) {
            Function2 var3 = new <anonymous constructor>(completion);
            return var3;
         }

         public final Object invoke(Object var1, Object var2) {
            return (this.create(var1, (Continuation)var2)).invokeSuspend(Unit.INSTANCE);
         }
      }), 1, (Object)null);
   }

   // $FF: synthetic method
   public static void main(String[] var0) {
      main();
   }
}

```

Я убрал все, что не относится к корутинам и отвлекает, вроде @Metadata и @NotNull аннотаций.

При вызове функции main будет вызван метод invoke(), который вызовет create, который создаст и вернет Continuation и далее вызовет invokeSuspend. invokeSuspend содержит конечный автомат с двумя метками label. При label = 0 попадаем в case 0 и присваиваем label = 1 и вызываем suspendFunction. Функция либо приостанавливается и тогда возвращаем управление (return var3), либо возвращает результат. Рассмотрим случай приостановки функции. Continuation будет преобразован в некую задачу, которая будет помещена в очередь и после того, как она будет выполнена будет вызван метод resumeWith с результатом выполнения.

```kotlin
    public final override fun resumeWith(result: Result<Any?>) {
        // This loop unrolls recursion in current.resumeWith(param) to make saner and shorter stack traces on resume
        var current = this
        var param = result
        while (true) {
            // Invoke "resume" debug probe on every resumed continuation, so that a debugging library infrastructure
            // can precisely track what part of suspended callstack was already resumed
            probeCoroutineResumed(current)
            with(current) {
                val completion = completion!! // fail fast when trying to resume continuation without completion
                val outcome: Result<Any?> =
                    try {
                        val outcome = invokeSuspend(param)
                        if (outcome === COROUTINE_SUSPENDED) return
                        Result.success(outcome)
                    } catch (exception: Throwable) {
                        Result.failure(exception)
                    }
                releaseIntercepted() // this state machine instance is terminating
                if (completion is BaseContinuationImpl) {
                    // unrolling recursion via loop
                    current = completion
                    param = outcome
                } else {
                    // top-level completion reached -- invoke and return
                    completion.resumeWith(outcome)
                    return
                }
            }
        }
    }
```
Исходный код метода resumeWith

Внутри resumeWith много всего инетересно, но если упрощать, то  будет вызван invokeSuspend при этом label уже будет равен 1, а не 0. Будет выбрана ветка  case 1 и выполнен код.

```kotlin
case 1:
    var10000 = $result;
    break;
```

var10000 получит значение равное результату выполнения suspendFunction и далее выйдет из switch.

```kotlin
int r1 = var10000.intValue();
MainKt.function(r1);
```

Потом r1 получит значение var10000 и передаст в обычную функцию function, которая выполнится как обычная функция и вернет результат выполнения.
### Заключение

Глядя на декомпилированный код впервые вряд ли преисполнишься пониманием  работы корутин под капотом, но обладая знаниями из предыдущих статей можно понять немного больше.  Во-первых никакой магии относительно корутин нет.

```kotlin
fun main() = runBlocking {  
    val r1 = suspendFunction()  //suspension point
    function(r1)  
}
```

Компилятор видит ключевое слово suspend и преобразует отмеченную функцию в функцию с дополнительным параметром continuation, который есть функция обратного вызова с дополнительным контекстом. Эту функцию обратного вызова передают в очередь асинхронных задач на выполнение, а сама функция приостанавливается и отдает управление. Когда задача выполнена результат будет передан в функцию обратного вызова resumeWith, корутина будет возобновлена с того места, где была приостановлена и продолжит свое выполнение. Под капотом у корутин обычные функции, которые в связке с работой асинхронного движка, функций операционной системы и механизмов позволяют продолжать приостановить выполнение не "блокируя" основной поток, а позже при получении результата возобновить выполнение из точки, в которой выполнение было приостановлено. 

### Послесловие

Если добавить в исходный пример несколько вложенных вызовов suspend функций, то можно заметить усложнение при построений конечного автомата: появятся новые метки label, добавится вложенность меток. За построение конечного автомата отвечает компилятор, поэтому остается только поблагодарить авторов за проделанную работу.  И, также отдельно отметить, что по сути, добавив в язык один модификатор suspend удалось реализовать такую мощную идею как корутины и для меня это выглядит как изящное дизайнерское решение.

Список литературы и материалов:

1. <a href="https://viktor.zharina.info/posts/razbiraemsya-s-coroutine-v-kotlin-chast-tretya/">Разбираемся с corotine в Kotlin - часть 3</a>

2. <a href="https://viktor.zharina.info/posts/razbiraemsya-s-coroutine-v-kotlin-chast-vtoraya/">Разбираемся с corotine в Kotlin - часть 2</a>

3. <a href="https://viktor.zharina.info/posts/razbiraemsya-s-coroutine-v-kotlin-chast-pervaya/">Разбираемся с corotine в Kotlin - часть 1</a>

4. <a href="https://www.youtube.com/watch?v=hxMiDiOmnP4">13. Computer Science Center - Асинхронный ввод/вывод. Корутины</a>

5. <a href="https://www.youtube.com/watch?v=bSJp3lRjU7k
">C++ User group - Антон Полухин — Анатомия асинхронных движков</a>

6. <a href="https://kotlinlang.org/spec/asynchronous-programming-with-coroutines.html#asynchronous-programming-with-coroutines">Спецификация языка Kotlin - Корутины</a>

7. <a href="https://journal.stuffwithstuff.com/2015/02/01/what-color-is-your-function/">What Color is Your Function?</a>