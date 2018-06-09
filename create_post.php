<?php
date_default_timezone_set('Asia/Novosibirsk');
require 'util/util.php';

if (!isset($argv[1]) || !isset($argv[2]) || !isset($argv[3]) || !isset($argv[4])) {
    die('format: lang , title, description, keywords');
}
list($script, $lang, $title, $description, $keywords) = $argv;
$titleTranslateId = getPostTitle($title);

$now = strtotime('now');
$date = date('Y-m-d-H-i-s', $now);
$postPath = 'content/posts/';

$order = getOrder($postPath . 'ru/'); // use ru always due to ru contains more posts then en
$params = [
  'titleTranslateId' => $titleTranslateId,
  'title' => $title,
  'titleKey' => '$title@',
  'description' => $description,
  'keywords' => $keywords,
  'order' => $order,
  'postPath' => $postPath . "$lang/$date-$titleTranslateId@$lang.md",
  'translatePath' => "translations/$lang/LC_MESSAGES/messages.po"
];

$lcParams = [
 'en' => [
     'published' => date('m.d.Y H:i:s', $now),
 ],
 'ru' => [
     'published' => date('d.m.Y H:i:s', $now),
 ],
];

$params = array_merge($params, $lcParams[$lang]);
$content =   getPostHeader($params) . file_get_contents('drafts/post.md');
file_put_contents($params['postPath'], $content);
writeTranslations($params);
