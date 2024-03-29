---
$title@: beckhoff-kl3362-v-dele
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: beckhoff-kl3362-v-dele
$dates:
  published: 2012-06-14 07:29:37
---
<h1>Введение</h1>

Я принес и положил на стол контроллер с набором модулей. Большинство из них были стандартны и мною давно опробованы в деле. Однако были среди них и парочку модулей, с которыми я еще ни разу не работал. Это были KL3362 или по простому модули - осциллографы. Их мне и предстояло настроить. 

Возможности модулей широки, однако в данном посте я опишу процедуру подключения и настройку модулей для решения одной определенной задачи. В посте будет описана настройка модулей KL3362 для решения задачи циклического измерения сигналов с частотой от 50 до 200 Гц. 

Пост предназначен для людей уже имеющих опыт работы с аппаратурой Beckhoff и программным обеспечением TwinCAT. 

<h1>Более подробное описание задачи</h1>

Необходимо циклически синхронно измерить один период трех аналоговых сигналов с частотой от 50 до 200 Гц. Количество измеренных значений равно 50, время между измерениями 100 мкс.

<!--more-->

<h1>Подключение и настройка</h1>

<h2>Подключение</h2>

Подключение модуля выполним согласно рисунку.

<img title="connect_kl3362" src="http://viktor.zharina.info/wp-content/uploads/2012/06/connect_kl3362.jpg" alt="" width="386" height="378" />

Рисунок – соединение модулей (красная линия - провода)

и далее я опишу почему именно так, а не по другому. Первый модуль мы далее настроим так, чтобы он устанавливал дискретный выход и начинал запись измеренных значений во внутреннюю память по обоим каналам при переходе через ноль (из минуса в плюс) аналогового сигнала первого канала. Второй модуль настроим на запись измеренных значений по переднему фронту дискретного входа. Дискретный выход первого модуля соединим с дискретным входом второго модуля. Таким образом первый модуль срабатывает и начинает запись и тут же сообщает об этом второму модулю, который также начинает запись. В итоге получаем почтисинхронное измерение трех аналоговых сигналов.



<h2>Настройка</h2>

Настройка всех модулей Beckhoff выполняется одинаково. Настроив хотя бы один модуль один раз вы будете способны настроить практически любой модуль. Проблема правда была в том, что до этого я модули никогда не настраивал. Но все когда-то бывает в первый раз.

Итак, настройка модуля заключается в записи определенных значений в регистры модуля. Процедура записи описана в документации на модуль, ноя все же остановлюсь на некоторых моментах для прояснения картины.

В каждом модуле по два канала и каждому каналу соответствует свой регистр. Описание регистров приведено в документации (http://viktor.zharina.info/inzhener/obzor-modulya-kl3362/). По умолчанию все регистры защищены от записи. Разрешить запись можно, если записать значение 0x1235 в регистр R31: Code word register. Пока в регистре R31 записано значение 0x1235 возможна запись в остальные регистры доступные для записи. Если в регистре записано любое другое значение, то запись также будет произведена, но все ваши настройки будут сброшены после перезагрузки контроллера. Однако я не стал это проверять и каждый раз когда настраивал модули записывал значение 0x1235 в регистр R31, а после окончания настройки записывал в него 0.



<h3>Пример чтения и записи регистров</h3>

Модуль поддерживает 2 режима работы: режим обработки данных (Process Data Mode) и режим коммуникации (Communication Mode). Чтение и запись регистров доступна в Communication Mode. Для примера рассмотрим запись и чтение значения 0х1235 в R31: Code word register для первого канала.

Назначим физические входы и выходы модуля программным переменным так как показано на рисунке.

<img title="system_manager" src="http://viktor.zharina.info/wp-content/uploads/2012/06/system_manager.jpg" alt="" width="313" height="586" />

Рисунок 2, где State (Channel 1) слинкована к переменной state_ch1

Data In[0], Data In[1] к элементам массива data_in_ch1[0], data_in_ch1[1]

Ctrl (Channel 1) к переменной ctrl_ch1

Data Out[0], Data Out[1] к элементам массива data_out_ch1[0], data_ out _ch1[1]



В ctrl_ch1 запишем значение 0xDF (1101 1111<sub>bin</sub>), В data_out_ch1[1] запишем значение 0х1235. В таблице приведена расшифровка битов значения 0xDF, которое мы записываем в ctrl_ch1. Из данных таблицы должно быть понятно что мы включаем режим Communication и записываем в регистр 31 значение. Это значение модуль берез data_out_ch1[1].



Таблица – Расшифровка битов ctrl_ch1

<table width="100%" border="1" cellspacing="0" cellpadding="0">

<tbody>

<tr>

<td valign="top" width="14%"><strong>Бит</strong><strong></strong></td>

<td valign="top" width="40%"><strong>7</strong></td>

<td valign="top" width="19%"><strong>6</strong></td>

<td valign="top" width="4%"><strong>5</strong></td>

<td valign="top" width="4%"><strong>4</strong></td>

<td valign="top" width="4%"><strong>3</strong></td>

<td valign="top" width="4%"><strong>2</strong></td>

<td valign="top" width="4%"><strong>1</strong></td>

<td valign="top" width="4%"><strong>0</strong></td>

</tr>

<tr>

<td valign="top" width="14%"><strong>Значение</strong></td>

<td valign="top" width="40%">1</td>

<td valign="top" width="19%">1</td>

<td valign="top" width="4%">0</td>

<td valign="top" width="4%">1</td>

<td valign="top" width="4%">1</td>

<td valign="top" width="4%">1</td>

<td valign="top" width="4%">1</td>

<td valign="top" width="4%">1</td>

</tr>

<tr>

<td valign="top" width="14%"><strong>Описание</strong></td>

<td valign="top" width="40%">Активирует Communication Mode</td>

<td valign="top" width="19%"> 1 - Запись/0 - чтение</td>

<td colspan="6" valign="top" width="26%">Номер регистра = 31</td>

</tr>

</tbody>

</table>

&nbsp;



Теперь выполним чтение регистра R31 для проверки записанного значения.

В ctrl_ch1 запишем значение 0x9F (1001 1111<sub>bin</sub>) и обратим внимание на переменные state_ch1,  data_in_ch1[0]. state_ch1 будет равна 0x9F, data_in_ch1[0] = 0х1235. Что и требовалось. Аналогичным образом выполняется чтение и запись в остальные регистры.

<h2>Чтение и запись регистров для решения задачи</h2>

Ниже я представлю последовательность того, чего нужно и куда нужно записать для решения задачи. М1 – первый модуль, К1 – первый канал, Рхх – регистр номер хх.

1. Значение 0х1235 - разрешает запись в другие регистры.

М1_К1_Р31 = 0х1235

М1_К2_Р31 = 0х1235

М2_К1_Р31 = 0х1235



2. 100 - время между измерениями в мкс

М1_К1_Р35 =100

М1_К2_Р35 = 100

М2_К1_Р35 = 100



3. 50 - количество измеренных значений записываемых в память

М1_К1_Р36 = 50

М1_К2_Р36 = 50

М2_К1_Р36 = 50



4. Настройки триггера

М1_К1_Р40 = 0х531

М1_К2_Р40 = 0хD31

М2_К1_Р40 = 0хD31



5. 300 - порог срабатывания триггера в отсчетах

М1_К1_Р41 = 300



6. 0 - индекс, с которого модуль будет выполнять чтение измеренных данных

М1_К1_Р62 = 0

М1_К2_Р62 = 0

М2_К1_Р62 = 0



7. 1 - последовательное чтение всех данных (если бы записал 2, то читал бы каждое второе значение, 3 каждое третье и т.д.)

М1_К1_Р63 = 1

М1_К2_Р63 = 1

М2_К1_Р63 = 1



8. 0 – запрещаем запись в регистры

М1_К1_Р31 = 0

М1_К2_Р31 = 0

М2_К1_Р31 = 0



Ураааа! Настройка модуля осциллографа завершена!!! 

На шагах 4, 6, 7 остановлюсь подробнее. В шаге 4 мы настраиваем триггер каждого канала на срабатывание. Первый канал настраиваем на срабатывание по переходу через 0 и при срабатывании устанавливаем дискретный выход. Остальные каналы настраиваем на срабатывание по переднему фронту дискретного входа. Расшифровка битов регистра 40 приведена в конце поста.

В шаге 6 задаем индекс, с которого нужно выполнить чтение данных. При старте записи во внутреннюю память модуль сбрасывает индекс в 0, но я все-таки записываю туда 0 после старта программы для надежности.

В шаге 7 задаем, как мы будем читать данные из внутренней памяти. 1 означает то, что будем читать все данные последовательно.

Теперь остается запускать триггеры и ожидать когда они сработают, и данные будут записаны в память. После этого необходимо считать данные из внутренней памяти в массивы, с которыми можно делать все что угодно (хочешь передай куда то наверх, а хочешь тут же обработай).



<h1>Циклические измерения</h1>

Теперь когда модуль настроен программа может выполнить однократное измерение и записать данные во внутреннюю память. Для проведения циклических измерений необходимо циклически запускать триггеры и ожидать их срабатывания и далее выполнять чтение измеренных данных из соответствующих регистров. Запускать триггеры на срабатывание нужно в режиме Process Data Mode. Чтение измеренных данных из регистров нужно выполнять в режиме Communication Mode. Согласно документации возможно чтение только одного измеренного значения за один такт контроллера. Есть еще один момент, на который я бы хотел обратить внимание. Модуль работает таким образом что он инкрементирует индекс в регистре 62 на шаг записанный в регистре 63 после изменения адреса. Например, у нас есть 10 точек по внутренней памяти и мы хотим их считать. Читаем регистр 60 и получаем первое измеренное значение, далее читаем регистр 61 и получаем второе измеренное значение, далее снова читаем регистр 60 и получаем третье измеренное значение и так до 10.

<a href="http://viktor.zharina.info/wp-content/uploads/2012/06/osc.jpg"><img src="http://viktor.zharina.info/wp-content/uploads/2012/06/osc-837x1024.jpg" alt="" title="А" width="600" height="768" class="aligncenter size-large wp-image-326" /></a>

Рисунок - Алгоритм циклического запуска триггеров и чтения измеренных значений из внутренней памяти модуля



<h1>Приложение</h1>

Таблица расшифровки регистра 40 – логика срабатывания триггера

<table width="100%" border="1" cellspacing="0" cellpadding="0">

<tbody>

<tr>

<td width="7%">

<p align="center"><strong>Бит </strong><strong></strong></p>

</td>

<td width="15%"></td>

<td width="11%">

<p align="center"><strong>Значения</strong><strong></strong></p>

</td>

<td colspan="2" width="52%">

<p align="center"><strong>Описание</strong><strong></strong></p>

</td>

<td width="12%">

<p align="center"><strong>По умолчанию</strong><strong></strong></p>

</td>

</tr>

<tr>

<td width="7%">R40.15</td>

<td width="15%"></td>

<td width="11%">-</td>

<td colspan="2" width="52%">Резерв</td>

<td width="12%">0<sub>bin</sub></td>

</tr>

<tr>

<td rowspan="7" width="7%">R40.14,

R40.13,

R40.12</td>

<td rowspan="7" width="15%">enableSource</td>

<td width="11%">000<sub>bin</sub></td>

<td colspan="2" width="52%">Trigger always enabled



Триггер всегда включен</td>

<td rowspan="7" width="12%">000<sub>bin</sub></td>

</tr>

<tr>

<td width="11%">001<sub>bin</sub></td>

<td colspan="2" width="52%">Trigger enabled if signal at first analog input above threshold level 2



Триггер включен, если аналоговый сигнал первого канала превышает пороговый уровень 2.</td>

</tr>

<tr>

<td width="11%">010<sub>bin</sub></td>

<td colspan="2" width="52%">Trigger enabled if signal at first analog input below threshold level 2



Триггер включен, если аналоговый сигнал первого канала ниже порогового уровня 2.</td>

</tr>

<tr>

<td width="11%">011<sub>bin</sub></td>

<td colspan="2" width="52%">Trigger enabled if signal at second analog input above threshold level 2



Триггер включен, если аналоговый сигнал второго канала превышает пороговый уровень 2.</td>

</tr>

<tr>

<td width="11%">100<sub>bin</sub></td>

<td colspan="2" width="52%">Trigger enabled if signal at second analog input below threshold level 2



Триггер включен, если аналоговый сигнал второго канала ниже порогового уровня 2.</td>

</tr>

<tr>

<td width="11%">101<sub>bin</sub></td>

<td colspan="2" width="52%">Trigger enabled if trigger input (24V trigger) on high potential.



Триггер включен, если дискретный вход имеет высокий уровень (24 В).</td>

</tr>

<tr>

<td width="11%">110<sub>bin</sub></td>

<td colspan="2" width="52%">Trigger enabled if trigger input (24V trigger) on low potential.



Триггер включен, если дискретный вход имеет низкий уровень.</td>

</tr>

<tr>

<td rowspan="4" width="7%">R40.11,

R40.10</td>

<td rowspan="4" width="15%">TriggerSource</td>

<td width="11%">00<sub>bin</sub></td>

<td colspan="2" width="52%">Timer with threshold level 1



Таймер с пороговым значением 1</td>

<td rowspan="4" width="12%">11<sub>bin</sub></td>

</tr>

<tr>

<td width="11%">01<sub>bin</sub></td>

<td colspan="2" width="52%">First analog input (IN1), with threshold level 1



Первый канал с порогом 1</td>

</tr>

<tr>

<td width="11%">10<sub>bin</sub></td>

<td colspan="2" width="52%">Second analog input (IN2), with threshold level 1



Второй канал с порогом 1</td>

</tr>

<tr>

<td width="11%">11<sub>bin</sub></td>

<td colspan="2" width="52%">Digital input



Дискретный вход</td>

</tr>

<tr>

<td rowspan="3" width="7%">R40.9,

R40.8</td>

<td rowspan="3" width="15%">TriggerMode</td>

<td width="11%">00<sub>bin</sub></td>

<td colspan="2" width="52%">Shot: The trigger is triggered with an edge of the <em>bEnableTrigger</em> bit of the control byte (CB1.0), if it is enabled via enableSource.



&nbsp;



Shot: триггер срабатывает по переднему фронту



<em>bEnableTrigger </em><em>CB1.0 </em></td>

<td rowspan="3" width="12%">01<sub>bin</sub></td>

</tr>

<tr>

<td width="11%">01<sub>bin</sub></td>

<td colspan="2" width="52%">Edge: The trigger is triggered via the edge selected via TriggerSource and bLogic, if it is enabled via enableSource.



&nbsp;



Edge: триггер срабатывает по фронту согласно TriggerSource и bLogic.</td>

</tr>

<tr>

<td width="11%">10<sub>bin</sub></td>

<td colspan="2" width="52%">Glitch: The trigger is triggered via the pulse selected via TriggerSource, bLogic and bLarger, if it is enabled via enableSource.



Glitch: триггер срабатывает по импульсу согласно TriggerSource и bLogic и bLarger.</td>

</tr>

<tr>

<td width="7%">R40.7</td>

<td width="15%">-</td>

<td width="11%">-</td>

<td colspan="2" width="52%">Резерв</td>

<td width="12%">0<sub>bin</sub></td>

</tr>

<tr>

<td width="7%">R40.6</td>

<td width="15%">bTriggerWinEn</td>

<td width="11%">1<sub>bin</sub></td>

<td colspan="2" width="52%">The trigger condition for the following trigger unit must arrive within the valid trigger time for trigger unit 1. Otherwise everything is reset



Флаг позволяет запускать триггеры каскадом. При условии что состояние первого триггера будет выдержано в течении valid trigger time for trigger unit 1. Иначе все будет сброшено.</td>

<td width="12%">0<sub>bin</sub></td>

</tr>

<tr>

<td rowspan="2" width="7%">R40.5</td>

<td rowspan="2" width="15%">bStartScopeRec</td>

<td width="11%">0<sub>bin</sub></td>

<td colspan="2" width="52%">The trigger event causes the downstream trigger unit to be enabled



Срабатывание триггера активирует срабатывание  второго триггера согласно его настройкам.</td>

<td rowspan="2" width="12%">0<sub>bin</sub></td>

</tr>

<tr>

<td width="11%">1<sub>bin</sub></td>

<td colspan="2" width="52%">The trigger event causes the recording to be started



Срабатывание триггера вызывает запись данных во внутреннюю память.</td>

</tr>

<tr>

<td width="7%">R40.4</td>

<td width="15%">bTriggerOutEn</td>

<td width="11%">1<sub>bin</sub></td>

<td colspan="2" width="52%">The trigger event causes the digital output to be set, if this is enabled by bit 2 of the Control Byte 1 (CB1.2).



Срабатывание триггера вызывает установку дискретного выхода, если установлен бит2 Control Byte 1.</td>

<td width="12%">0<sub>bin</sub></td>

</tr>

<tr>

<td width="7%">R40.3</td>

<td width="15%">bLatchtimer</td>

<td width="11%">1<sub>bin</sub></td>

<td colspan="2" width="52%">The trigger event causes the current value of the running timer to be stored.



Срабатывание триггера вызывает сохранение текущего значения таймера.</td>

<td width="12%">0<sub>bin</sub></td>

</tr>

<tr>

<td width="7%">R40.2</td>

<td width="15%">bResetTimer</td>

<td width="11%">1<sub>bin</sub></td>

<td colspan="2" width="52%">The trigger event causes the timer to be reset to zero. The timer will start running again automatically immediately.



Срабатывание триггера вызывает сброс таймера, таймер начнет отсчет автоматически.</td>

<td width="12%">0<sub>bin</sub></td>

</tr>

<tr>

<td rowspan="2" width="7%">R40.1</td>

<td rowspan="2" width="15%">bLarger</td>

<td width="11%">0<sub>bin</sub></td>

<td colspan="2" width="52%">in glitch mode: pulse width less than the pulse width specified for trigger unit 1



в glitch mode: длительность импульса менее чем длительность импульса задачная для trigger unit 1</td>

<td rowspan="2" width="12%">0<sub>bin</sub></td>

</tr>

<tr>

<td width="11%">1<sub>bin</sub></td>

<td colspan="2" width="52%">in glitch mode: pulse width greater than the pulse width specified for trigger unit 1



в glitch mode: длительность импульса более чем длительность импульса задачная для trigger unit 1</td>

</tr>

<tr>

<td rowspan="2" width="7%">R40.0</td>

<td rowspan="2" width="15%">bLogic</td>

<td width="11%">0<sub>bin</sub></td>

<td width="28%">in edge mode (edge triggering): falling edge



в edge mode: по заднему  фронту</td>

<td width="23%">in glitch mode: negative pulse



в glitch mode: отрицательный импульс</td>

<td rowspan="2" width="12%">1<sub>bin</sub></td>

</tr>

<tr>

<td width="11%">1<sub>bin</sub></td>

<td width="28%">in edge mode (edge triggering): rising edge



в edge mode: по переднему  фронту</td>

<td width="23%">in glitch mode: positive pulse



в glitch mode: положительный импульс</td>

</tr>

</tbody>

</table>

&nbsp;