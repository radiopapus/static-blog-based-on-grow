<?php

namespace Mashinka\Commands;

use Exception;
use Mashinka\DTO\PostMeta;
use Mashinka\DTO\Post;
use Mashinka\Util\PostHelper;
use Mashinka\Util\Template;

class Index implements CommandInterface
{
    private array    $params         = [];
    private bool     $dry            = false;
    private array    $availableLangs = ["en", "ru"];

    public function __construct(array $params)
    {
        $this->params   = $params;
        $this->dry      = in_array('--dry-run', $params, true);
    }

    /**
     * Build index for all posts.
     *
     * @throws \Exception
     */
    public function run()
    {
        $postsPaths = [];
        $posts = [];
        $translations = [];
        foreach ($this->availableLangs as $lang) {
            $translationPath = str_replace("{lang}", $lang, getenv("TRANSLATIONS_PATH"));
            $titlesTranslation = $this->getTitlesFromTranslation($translationPath);

            $translations = array_merge($translations, $titlesTranslation);
            $path = sprintf("%s/%s/", getenv('POSTS_PATH'), $lang);
            $files = scandir($path);
            
            foreach ($files as $file) {
                $filePath = sprintf("%s/%s", $path, $file);    
                if (is_file($filePath)) {
                    $posts[] = $this->extract($filePath, $lang);
                }
            }
        }

        $json = $this->transform($posts, $translations);

        $this->load($json);
    }

    /**
     * @param string $draftFile
     *
     * @return Post
     * @throws Exception
     */
    private function extract(string $file, $lang): Post
    {
        if (!file_exists($file)) {
            throw new Exception("$file does not exist. Create it before publish");
        }

        $file = file_get_contents($file);
        $meta = explode('---', $file)[1];
        $content = explode('---', $file)[2];

        if (empty($content)) {
            throw new Exception('Content is empty. Add empty line after meta and content --- delimiter');
        }

        $meta = $this->extractMeta($meta);
        $meta->lang = $lang;

        $post              = new Post();
        $post->meta        = $meta;
        $post->content     = $content;

        return $post;
    }

    private function transform(array $posts, array $translation): string
    {
        $transformed = [];
        foreach ($posts as $post) {
            $id = sprintf("/%s/posts/%s", $post->meta->lang,PostHelper::buildSlug($post->meta->title));
            $transformed[] = [
                "id" => $id,
                "title" => $translation[$post->meta->title],
                "content" => str_replace(
                    PHP_EOL, "", strip_tags($post->content)
                )
            ];
        }
        return json_encode($transformed);
    }

    /**
     * @param String $json
     *
     * @return void
     * @throws \Exception
     */
    private function load(string $json): void
    {
        if ($this->dry) {
            print_r($json);

            return;
        }

        if (empty(getenv("INDEX_DATA_FILE"))) {
            throw new Exception("INDEX_DATA_FILE env variable not set");
        }
            
        $this->writeContent(getenv("INDEX_DATA_FILE"), $json);
    }

    private function extractMeta(string $meta): PostMeta
    {
        if (empty($meta)) {
            throw new Exception(
                sprintf('Meta must not be empty. Check --- delimiter. rawData = %s', $meta)
            );
        }

        $explodedByLine = explode(PHP_EOL, $meta);

        $meta = new PostMeta();

        foreach ($explodedByLine as $item) {
            if (!empty($item)) {
                [$key, $value] = explode(':', $item, 2);
                $key = preg_replace('/[^A-Za-z0-9\-]/', '', $key);

                $meta->$key = trim($value);
            }
        }

        return $meta;
    }

    private function writeContent(string $file, string $content)
    {
        $result = file_put_contents($file, $content);

        if ($result === false) {
            throw new Exception("Unable to write to file $file");
        }
    }

    private function getTitlesFromTranslation(string $file): array
    {
        
        if (empty(getenv("TRANSLATIONS_PATH"))) {
            throw new Exception("TRANSLATIONS_PATH env variable not set");
        }

        $content = file_get_contents($file);

        $content = str_replace(PHP_EOL, "", $content);
        $content = str_replace("\r\n", "", $content);

        $re = '/msgid "(.*)msgstr "(.*)"/mU';
        
        preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);
        $r = array_map(
            fn($m) => ["id" => trim($m[1], '"'), "title" => trim($m[2], '"')] , $matches
        );

        $kv = [];
        foreach ($r as $v) {
            $kv[$v["id"]] = $v["title"];
        }

        return $kv;
    }
}
