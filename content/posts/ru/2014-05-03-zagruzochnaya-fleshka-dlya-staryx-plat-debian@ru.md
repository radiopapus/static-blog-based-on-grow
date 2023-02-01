---
$title@: zagruzochnaya-fleshka-dlya-staryx-plat-debian
author@: Viktor Zharina
description: 
keywords: 
image: /static/images/default.png
slugRu: zagruzochnaya-fleshka-dlya-staryx-plat-debian
$dates:
  published: 2014-05-03 15:05:32
---
Не знаю как у вас, а у меня получилось сделать так (материнка Gigabyte 7VT600 - http://www.gigabyte.ru/products/page/mb/ga-7vt600/):

== все под рутом ==

1. mkdiskimage -Mz4 /dev/sdX 700 64 32

sdX - ваша флешка, воткните и узнайте через dmesg, 700 мегабайт раздел, остальное для usb-zip

2. blikd

узнаем UUID устройства

3. syslinux --install /dev/sdX4

ставим syslinux

4. mount -t vfat /dev/sdb4 /mnt

5. nano /mnt/syslinux.cfg

Default USB-Stick

display syslinux.msg

F1 syslinux.f1

prompt 40

timeout 30

Label USB-Stick

kernel linux

append initrd=initrd.gz root=UUID=&gt;UUID of the root partition from blkid&lt;



6. cp initrd.gz /mnt/initrd.gz

копируем образ fs <a href="http://viktor.zharina.info/wp-content/uploads/2014/05/initrd.gz">initrd debian</a>

7. cp linux /mnt/linux

копируем ядро <a href="http://viktor.zharina.info/wp-content/uploads/2014/05/linux.gz">linux</a> (после скачивания уберите расширение .gz из названия, д.б. просто linux)

8. копируем iso образ дистрибутива (в моем случае debian-live-7.4-i386-standard.iso)

9. umount /mnt

В биосе ставим usb-zip и ждем пока загрузится ядро, а потом initrd.gz. Далее установщик сам найдет iso.