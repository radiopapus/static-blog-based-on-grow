<?php

namespace Mashinka\Util;

use Mashinka\DTO\Post;

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
        $date = date('Y-m-d-H-i-s', $post->timestamp);
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
}
