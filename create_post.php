<?php
require 'util/util.php';

if (!isset($argv[1])) {
    die('Title must be set');
}

function getPostTitle($src)
{
    $prepared = ucfirst(transliterate($src));
    $postTitle = str_replace([', ', ',', ' ', '!', '?', "'", '#'], '-', $prepared);
    return trim(str_replace(['. '], '.', $postTitle), "-!?#'");
}

function filePrepend($filename, $content)
{
    $fileContent = file_get_contents($filename);
    $content = PHP_EOL.$content;
    file_put_contents($filename, $content . PHP_EOL . $fileContent);
}

$title = getPostTitle($argv[1]);

$now = strtotime('now');
$date = date('Y-m-d-H-i-s', $now);
$pathRu = sprintf('content/posts/ru/%s-%s%s', $date, $title, '@ru.md');
$pathEn = sprintf('content/posts/en/%s-%s%s', $date, $title, '@en.md');

$postHeader = sprintf('---
author@: Viktor Zharina
$order: %s
$dates:
  published: %s
$title@: %s
---
', getOrder('content/posts/ru/'), date('Y-m-d H:i:s', $now), $title);

$post = $postHeader . file_get_contents('drafts/post.md');

// writes post
file_put_contents($pathRu, $post);
file_put_contents($pathEn, $post);

// write translations
$msgId = sprintf('msgid "%s"%s', $title, PHP_EOL);
$msgStr = sprintf('msgstr "%s"%s', $title, PHP_EOL);
$ruMsgStr = sprintf('msgstr "%s"%s', $argv[1], PHP_EOL);
filePrepend('translations/messages.pot', $msgId.$msgStr);//en
filePrepend('translations/ru/LC_MESSAGES/messages.po', $msgId.$ruMsgStr);//ru
