---
$title@: podhodit-li-data-class-dlya-jpa-entity
author@: Виктор Жарина
description: Мои размышления на тему выбора data class и обычного kotlin class для jpa entity
keywords: kotlin,data class,class,jpa entity,spring
image: /static/images/default.png
slugRu: podhodit-li-data-class-dlya-jpa-entity
$dates:
  published: 2023-11-19 10:25:31
---
<h3 style="clear: none">TLDR;</h3>
<div class="quote__right">
 <p class="quote">У тебя класс без data!</p>
 <p class="quote">Да, неплохой такой класс</p>
</div>
Использовать data class для JPA сущности оправдано, если id записи генерится на стороне приложения и избыточно, если id генерится на стороне базы данных, так как придется переопределять методы equals и hashcode.


### Подробности

Есть класс, отмеченный аннотацией @Entity и @Table. Нужно ли добавлять data перед class?

[sourcecode:kotlin]
@Table
@Entity
class Entity(
   @Id
   val id: SomeIdentityType,
   @Column
   val name: String
)
[/sourcecode]

Ответ на вопрос зависит от того, где генерится id, но вначале разберемся с data class. Data class были созданы для тех, кому лень переопределять методы. Ниже написан data class и представлен bytecode.

[sourcecode:kotlin]
data class D(val name: String)
[/sourcecode]

Из bytecode я удалил "лишнее" и оставил только необходимое для дальнейших рассуждений.

[sourcecode:java]
public static final class D {
   @NotNull
   private final String name;

   @NotNull
   public final String getName() {
      return this.name;
   }

   @NotNull
   public String toString() {
      return "D(name=" + this.name + ")";
   }

   public int hashCode() {
      String var10000 = this.name;
      return var10000 != null ? var10000.hashCode() : 0;
   }

   public boolean equals(@Nullable Object var1) {
      if (this != var1) {
         if (var1 instanceof Scratch_7.D) {
            Scratch_7.D var2 = (Scratch_7.D)var1;
            if (Intrinsics.areEqual(this.name, var2.name)) {
               return true;
            }
         }

         return false;
      } else {
         return true;
      }
   }
}
[/sourcecode]

Таким образом, data перед class это указание компилятору переопределить методы toString(), equals(), hashCode() на основании данных полей конструктора.

JPA Entity это объекты, которые отображают поля объекта на строки в таблице базе данных. На первый взгляд data класс это то, что нужно, но тут есть несколько моментов (не нюансов). По сути все крутится вокруг поля, которое отображается на "первичный ключ" (id). Id может быть сгенерирован как со стороны приложения, так и со стороны базы данных. В первом случае нужен тип, который однозначно идентифицирует запись и примером такого типа может быть UUID.

[sourcecode:kotlin]
@Table
@Entity
data class EntityApp(
   @Id
   val id: UUID,
   @Column
   val name: String
)

@Test
fun `test app generated id`() {
   val name = "имя"
   val id = UUID.randomUUID()
   val (entity1, entity2) = EntityApp(id, name) to EntityApp(id, name)

   val m = mutableMapOf<EntityApp, String>()
   m[entity1] = "Найден"

   println("До сохранения в б.д.")
   println("${entity1 == entity2}")   // true

   val result = entityAppRepository.save(entity1)

   println("После сохранения в б.д.")
   println("${entity1 == result}")    // true
   println("${entity1 == entity2}")   // true

   println(m[entity1] ?: "Не найден") // Найден
}
[/sourcecode]

Получается, что написать data перед class для JPA Entity оправдано, если id генерится на стороне приложения.

Теперь рассмотрим случай, когда id генерится на стороне базы данных.

[sourcecode:kotlin]
@Table
@Entity
data class EntityDb(
   @Id
   @GeneratedValue(strategy = GenerationType.IDENTITY)
   val id: Long? = null,
   @Column
   val name: String
)

@Test
fun `test db generated id`() {
   val name = "имя"
   val (entityDb1, entityDb2) = EntityDb(name = name) to EntityDb(name = name)

   val m = mutableMapOf<EntityDb, String>()
   m[entityDb1] = "Найден"

   println("До сохранения в б.д.")
   println("${entityDb1 == entityDb2}")  // true

   val result = entityDbRepository.save(entityDb1)

   println("После сохранения в б.д.")
   println("${entityDb1 == result}")     // true
   println("${entityDb1 == entityDb2}")  // false

   println(m[entityDb1] ?: "Не найден")  // Не найден
}
[/sourcecode]

Объект оказался изменён после сохранения и поэтому entityDb1 != entityDb2. Автоматически переопределенный метод equals содержал проверку на id == other.id. До сохранения id был null, а после принял значение равное id из базы. entityDb1 добавлен в hashmap, но после сохранения в б.д. hashcode изменился (так как изменился id) и теперь entityDb1 не найти в hashmap после сохранения. Оба метода требуют переопределения и указание data class для JPA Entity избыточно. Кстати, автоматически переопределённый метод toString наглядно демонстрирует side эффект, когда id для объекта не задан до сохранения в базу данных и id получает значение после сохранения в базу данных.

Использовать data class для JPA сущности оправдано, если id записи генерится на стороне приложения и избыточно, если id генерится на стороне базы данных, так как придётся переопределять методы equals и hashcode.