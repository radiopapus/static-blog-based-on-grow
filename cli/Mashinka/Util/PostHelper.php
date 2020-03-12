<?php

namespace Mashinka\Util;

use Exception;
use Mashinka\DTO\Post;
use Mashinka\DTO\PostMeta;
use Mashinka\DTO\Translation;

class PostHelper
{
    public static function buildSlug($text)
    {
        $prepared  = ucfirst(self::transliterate($text));
        $postTitle = str_replace([', ', ',', ' ', '!', '?', "'", '#'], '-', $prepared);
        $postTitle = str_replace(['.-'], '-', $postTitle);

        return strtolower(trim(str_replace(['.@'], '@', $postTitle)));
    }

    public static function buildPostFileName(string $path, Post $post, string $ext = 'md'): string
    {
        $lang = $post->meta->lang;
        $date = date('Y-m-d-H-i-s', $post->meta->publishTime);
        $slug = $post->meta->slug;

        return sprintf('%s/%s/%s-%s@%s.%s', $path, $lang, $date, $slug, $lang, $ext);
    }

    /**
     * Transliterate letters from russian to latin
     *
     * @param string - string for transliteration
     *
     * @return string transliterated string
     */
    public static function transliterate($content): string
    {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ь' => '', 'ы' => 'i', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ь' => '', 'Ы' => 'i', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];

        return trim(strtr($content, $converter));

    }

    /**
     * @param \Mashinka\DTO\Post $post
     *
     * @return string
     */
    public static function buildContent(Post $post): string
    {
        $data = [
            'title'       => $post->meta->title,
            'author'      => $post->meta->author,
            'description' => $post->meta->description,
            'keywords'    => $post->meta->keywords,
            'order'       => $post->meta->order,
            'image'       => $post->meta->image,
            'slug'        => $post->meta->slug,
            'lang'        => ucfirst($post->meta->lang),
            'publishDate' => date('d.m.Y H:i:s', $post->timestamp),
            'content'     => $post->content,
        ];

        if ($post->meta->lang == 'en') {
            $data['publishDate'] = date('m.d.Y H:i:s', $post->timestamp);
        }

        return self::processTemplate(getenv('POST_TEMPLATE_PATH'), $data);
    }

    private static function processTemplate(string $templatePath, array $data): string
    {
        $template = file_get_contents($templatePath);
        foreach ($data as $key => $value) {
            $template = str_replace(sprintf('{%s}', $key), $value, $template);
        }

        return $template;
    }


    public static function buildTranslation(Post $post): string
    {
        $data = [
            'id'    => $post->meta->slug,
            'value' => $post->meta->title,
        ];

        $translationFile = str_replace('{lang}', $post->meta->lang, getenv('TRANSLATIONS_PATH'));

        return self::processTemplate($translationFile, $data);
    }

    public static function writeContent(string $file, string $content)
    {
        $result = file_put_contents($file, $content);

        if ($result === false) {
            throw new Exception("Unable to write to file $file");
        }
    }

    public function extractMeta(string $meta): PostMeta
    {
        if (empty($meta)) {
            throw new Exception(
                sprintf('Meta must not be empty. Check --- delimiter. rawData = %s', print_r($draft))
            );
        }

        $explodedByLine = explode(PHP_EOL, $meta);
        array_pop($explodedByLine);

        $meta = new PostMeta();

        foreach ($explodedByLine as $item) {
            [$key, $value] = explode(':', $item);
            $meta->$key = $value;
        }

        return $meta;
    }

    /**
     * @param \Mashinka\DTO\Post $post
     * @param bool               $dry
     *
     * @throws \Exception
     */
    public static function loadContent(Post $post, bool $dry = false): void
    {
        $postFile = self::buildPostFileName(getenv('POSTS_PATH'), $post);
        $content  = self::buildContent($post);

        $translationContent = self::buildTranslation($post);

        if ($dry) {
            var_dump(sprintf('file: %s', $postFile));
            var_dump($content);

            var_dump(sprintf('translation: %s', substr($translationContent, 0, 500)));

            return;
        }

        self::writeContent($postFile, $content);

        self::writeContent(getenv('TRANSLATIONS_PATH'), $translationContent);
    }
}
