<?php


function getFileList($path)
{
    return glob($path . "*");
}

/**
 * Get post title for grow
 */
function getPostTitle($src)
{
    $prepared = ucfirst(transliterate($src));
    $postTitle = str_replace([', ', ',', ' ', '!', '?', "'", '#'], '-', $prepared);
    $postTitle = str_replace(['.-'], '-', $postTitle);
    return strtolower(trim(str_replace(['.@'], '@', $postTitle)));
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
image: %s' .PHP_EOL;

    if (isset($params['slugRu'])) {
        $header .= '%s' . PHP_EOL;
    }

    if (isset($params['slugEn'])) {
        $header .= '%s' . PHP_EOL;
    }

    $header .= '---';

    return sprintf(
            $header,
            $params['description'],
            $params['keywords'],
            $params['$order'],
            $params['published'],
            $params['$title@'],
            $params['image'],
            isset($params['slugRu']) ? 'slugRu: ' . $params['slugRu'] : '',
            isset($params['slugEn']) ? 'slugEn: ' . $params['slugEn'] : ''
        ) . PHP_EOL;
}

function writeTranslations($params)
{
    $msgId = sprintf('msgid "%s"%s', $params['$title@'], PHP_EOL);
    $msgStr = sprintf('msgstr "%s"%s', $params['title'], PHP_EOL);
    filePrepend($params['translatePath'], $msgId . $msgStr);
}

/**
 *
 */
function getMetaData($rawDraft)
{
    list($meta, $content) = explode('---', $rawDraft);
    if (empty($meta)) {
        throw new \Exception(
            sprintf('Meta must not be empty. Check --- delimiter. rawData = %s', print_r($rawDraft))
        );
    }

    if (empty($content)) {
        throw new \Exception('AbstractContent.php is empty. Add empty line after meta and content --- delimiter');
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

/**
 * Get list of images from path and return html for post
 */
function prepareGallery($prefixPath="", $path='/home/viktorz/blog/source/images/')
{
    $list = getFileList($path.$prefixPath.'/orig/');
    $names = array_map(
        function($item) use ($prefixPath) {
            $exploded = explode('/', $item);
            $name = $exploded[count($exploded) - 1];
            $thumbPath = "/static/images/$prefixPath/thumb/$name";
            $origPath = "/static/images/$prefixPath/orig/$name";
            return "
<a href=$origPath data-responsive=\"$thumbPath 400, $origPath 759\">
  <img src=$thumbPath />
</a>";}, $list);
    return sprintf('<div id="lightgallery" class="lightgallery">
  %s
</div>', implode(' ', $names));
}
