<?php
date_default_timezone_set('Asia/Novosibirsk');
require 'util/util.php';

$rawDraft = file_get_contents('drafts/post.md');
list($metaArr, $content) = getMetaData($rawDraft);
extract($metaArr);
$now = strtotime('now');
$date = date('Y-m-d-H-i-s', $now);
$postPath = 'content/posts/';

$order = getOrder($postPath . 'ru/'); // use ru always due to ru contains more posts then en
$slug = getPostTitle($title);

$lcParams = [
  'en' => ['published' => date('m.d.Y H:i:s', $now), 'secondLang' => 'ru'],
  'ru' => ['published' => date('d.m.Y H:i:s', $now), 'secondLang' => 'en'],
];

$params = [
  'title' => $title,
  '$title@' => $slug,
  'slug' . ucfirst($lang) => $slug,
  'description' => $description,
  'keywords' => $keywords,
  '$order' => $order,
  'postPath' => $postPath . "$lang/$date-$slug@$lang.md",
  'translatePath' => "translations/$lang/LC_MESSAGES/messages.po"
];

$secondLang = ucfirst($lcParams[$lang]['secondLang']);
if (isset(${'slug' . $secondLang})) {
    $params['slug' . $secondLang] = getPostTitle(${'slug' . $secondLang});
}

$params = array_merge($params, $lcParams[$lang]);
$content = getPostHeader($params) . $content;
var_dump($content);
file_put_contents($params['postPath'], $content);
file_put_contents('drafts/post.md', $rawDraft);
writeTranslations($params);
