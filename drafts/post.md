title: Пишем первый custom block для редактора Gutenberg в Wordpress
lang: ru
description: Как написать свой блок для редактора Gutenberg в Wordpress. Статья написана, чтобы закрепить
полученные знания на практике. На этой основе будут созданы другие, более сложные блоки.
keywords: wordpress, custom block, gutenberg
---

## Прежде, чем начать
Wordpress богат плагинами и как CMS мощная штука. Заказчик использует Wordpress для создания контента. Начиная
с версии 5.0 Wordpress использует редактор Gutenberg по-умолчанию. Редактор отличается тем, что предлагает создавать
контент в виде набора блоков. Блок это некоторая надстройка над HTML. Вы, как редактор, добавляете в статью галерею, ссылку на twitter, вставить цитату и так далее.

### Версии программ
1. npm = 6.13.7
2. nodejs = 8.10.0
3. wordpress = 5.2.3
4. Linux = Ubuntu 18.04.3 LTS 

## Задача
Задачей будет создать собственный block, который выводит текст. Информация взята  из
документации wordpress. В планы входит создать plugin, который подключу к блогу. Plugin добавляет custom block, который выводит текст Hola, mundo! обернутый в div.

# Подготовка

Создадим структуру

```mkdir my-first-custom-block && cd my-first-custom-block```

```mkdir build src```

```touch src/index.js index.php``` 

npm init # создаст package.json где пропишем зависимости и необходимые команды

Добавим в блок scripts команды и в результате package.json будет выглядеть так
```json
{
  "name": "my-first-custom-block",
  "version": "1.0.0",
  "description": "My First Custom Block Example",
  "main": "src/index.js",
  "scripts": {
    "build": "wp-scripts build",
    "start": "wp-scripts start",
    "check-engines": "wp-scripts check-engines",
    "check-licenses": "wp-scripts check-licenses",
    "format:js": "wp-scripts format-js",
    "lint:css": "wp-scripts lint-style",
    "lint:js": "wp-scripts lint-js",
    "lint:md:docs": "wp-scripts lint-md-docs",
    "lint:md:js": "wp-scripts lint-md-js",
    "lint:pkg-json": "wp-scripts lint-pkg-json",
    "packages-update": "wp-scripts packages-update",
    "test:e2e": "wp-scripts test-e2e",
    "test:unit": "wp-scripts test-unit-js"
  },
  "author": "Viktor Zharina <viktor.zharina@quantumsoft.ru>",
  "license": "MIT",
  "devDependencies": {
    "@wordpress/scripts": "^7.1.0"
  }
}
```

выполним npm install и дождемся окончания установки зависимостей.

В index.php добавим

```php
<?php
/**
 * Plugin Name: My Custom Block Example
 * Description: This is a plugin demonstrating how to register new blocks for the Gutenberg editor.
 * Version: 1.0.0
 * Author: Viktor Zharina
 *
 * @package gutenberg-examples
 */

defined( 'ABSPATH' ) || exit;

function register_my_custom_block() {
    // Добавляем артефакты сборки плагина
    $assetFile = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

    // регистрируем скрипт 
    wp_register_script(
        'my-custom-block', // имя
        plugins_url('build/index.js', __FILE__ ), // путь до скрипта
        $assetFile['dependencies'], // зависимости доступные в скрипте
        $assetFile['version'], // версия
    );

    // регистрируем новый тип блока - здесь же можно добавить стили
    register_block_type( 'myguten/test-block', [ // имя совпадает с именем с src/index.js 
        'editor_script' => 'my-custom-block', // имя совпадает с именем выше 
    ]);
}

// Регистрируем новую функцию register_my_custom_block
add_action( 'init', 'register_my_custom_block' );
```
в src/index.js добавим

```js
import {registerBlockType} from '@wordpress/blocks';

registerBlockType('myguten/test-block', {
    title: 'My Custom Block Example', // Заголовок
    icon: 'smiley', // иконка
    category: 'layout', // категория блока
    edit: () => <div>Hola, mundo123!</div>, // текст при редактировании поста  
    save: () => <div>Hola, mundo444!</div>, // текст при сохранении поста
});
```

Далее build и делаем zip файл 
```bash 
npm run build && zip myguten.zip index.php build/*
```
, чтобы в дальнейшем загрузить
myguten.zip в wordpress как плагин.

<img alt="My custom block" src="/static/images/wordpress/custom-block/orig/screen1.jpg">

<img alt="My custom block - added" src="/static/images/wordpress/custom-block/orig/screen2.jpg">

### Ссылки
[Gutenberg tutorial](https://developer.wordpress.org/block-editor/tutorials/)

[Gutenberg examples](https://github.com/WordPress/gutenberg-examples) 
