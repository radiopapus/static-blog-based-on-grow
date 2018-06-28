<?php
date_default_timezone_set('Asia/Novosibirsk');
require 'util/util.php';

$rawDraft = file_get_contents('drafts/post.md');
list($metaPart, $contentPart) = explode('---', $rawDraft);

if (empty($contentPart)) {
    die('Content must not be empty');
}

$metaArr = [];
foreach (explode(PHP_EOL, $metaPart) as $item) {
    if (empty($item)) {
        continue;
    }

    list($t, $c) = explode(':', $item);
    $metaArr[trim($t)] = trim($c);
}
extract($metaArr);
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
$content = getPostHeader($params) . $contentPart;
file_put_contents($params['postPath'], $content);
file_put_contents('drafts/post.md', $rawDraft);
writeTranslations($params);
