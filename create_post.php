<?php
require 'util/util.php';

if (!isset($argv[1])) {
    die('Title must be set');
}

$titleSrc = $argv[1];

$postTitle = str_replace([', ', ',', ' ', '!', '?', "'", '#'], '-', ucfirst(transliterate($titleSrc)));
$now = strtotime('now');
$postTitleDate = date('Y-m-d-H-i-s', $now);
$postpath = 'content/posts/' . $postTitleDate . '-' . $postTitle . '.md';

$order = getOrder('content/posts/');
$postContent = file_get_contents('drafts/post.md');

$postDate = date('Y-m-d H:i:s', $now);

$postHeader = '---
author@: Viktor Zharina
$order: ' . $order . '
$dates:
  published: ' . $postDate . '
$title@: ' . $postTitle . '
---
';
$post = $postHeader . $postContent;

// writes post
file_put_contents($postpath, $post);

// write translations
$msgId = PHP_EOL.'msgid "' . $postTitle.'"'.PHP_EOL;
$msgStr = "msgstr \"$postTitle\"".PHP_EOL.PHP_EOL;
$ruMsgStr = "msgstr \"$titleSrc\"".PHP_EOL.PHP_EOL;
file_put_contents('translations/messages.pot', $msgId.$msgStr, FILE_APPEND); //en
file_put_contents('translations/ru/LC_MESSAGES/messages.po', $msgId.$ruMsgStr, FILE_APPEND); //ru
