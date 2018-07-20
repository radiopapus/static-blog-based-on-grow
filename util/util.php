<?php
/**
* Transliterate letters from russian to latin
* @param string - string for transliteration
* @return string transliterated string
*/
function transliterate($string)
{
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v', 'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z', 'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n', 'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u', 'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sh', 'ь' => '',  'ы' => 'i',   'ъ' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V', 'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z', 'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N', 'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U', 'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sh', 'Ь' => '',  'Ы' => 'i',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya'
    );
    return trim(strtr($string, $converter));
}

/**
* Count all posts and increase order value
* @param $directory path which files must be counted
* @param int count - num files in $directory
*/
function getOrder($directory)
{
    return count(glob($directory . "*")) + 1;
}

/**
* Get post title for grow
*/
function getPostTitle($src)
{
    $prepared = ucfirst(transliterate($src));
    $postTitle = str_replace([', ', ',', ' ', '!', '?', "'", '#'], '-', $prepared);
    $postTitle = str_replace(['.-'], '-', $postTitle);
    return mb_strtolower(trim(str_replace(['.@'], '@', $postTitle)));
}

/**
* Prepend file instead of append
* @param $filename is a absolute path to the file
* @param $content data for prepending
*/
function filePrepend($filename, $content)
{
    $fileContent = file_get_contents($filename);
    $content = PHP_EOL.$content;
    file_put_contents($filename, $content . PHP_EOL . $fileContent);
}

/**
* Make post header based on $params
* $params array
* return string;
*/
function getPostHeader($params)
{
    $header = '---
author@: Viktor Zharina
description: %s
keywords: %s
$order: %s
$dates:
  published: %s
$title@: %s
slugRu: %s
slugEn: %s
---';
    return sprintf(
        $header,
        $params['description'],
        $params['keywords'],
        $params['$order'],
        $params['published'],
        $params['$title@'],
        $params['slugRu'],
        $params['slugEn']
    ) . PHP_EOL;
}

function writeTranslations($params)
{
    $msgId = sprintf('msgid "%s"%s', $params['$title@'], PHP_EOL);
    $msgStr = sprintf('msgstr "%s"%s', $params['title'], PHP_EOL);
    filePrepend($params['translatePath'], $msgId . $msgStr);
}

function getMetaData($rawDraft)
{
    list($meta, $content) = explode('---', $rawDraft);
    if (empty($meta)) {
        die('Meta must not be empty');
    }

    if (empty($content)) {
        die('Content must not be empty');
    }
    $metaArr = [];
    $metaExplodedByLine = explode(PHP_EOL, $meta);
    array_pop($metaExplodedByLine);
    foreach ($metaExplodedByLine as $item) {
        list($key, $value) = explode(':', $item);
        $metaArr[trim($key)] = trim($value);
    }

    return [$metaArr, $content];
}
