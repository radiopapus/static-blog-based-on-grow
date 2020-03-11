<?php

namespace Mashinka\Commands;

use Exception;
use Mashinka\dto\PostMeta;
use Mashinka\dto\Post;
use Mashinka\Helper\StringHelper;

class Publish implements CommandInterface
{
    private array $params = [];
    private bool  $dry    = false;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->dry    = in_array('--dry-run', $params, true);
    }

    private function extractMeta(string $meta): PostMeta
    {
        $explodedByLine = explode(PHP_EOL, $meta);
        array_pop($explodedByLine);

        return PostMeta::fromArray($explodedByLine);
    }

    /**
     * @param string $draftFile
     *
     * @return Post
     * @throws Exception
     */
    private function extractPost(string $draftFile): Post
    {
        if (!file_exists($draftFile)) {
            throw new Exception('post.md does not exist. Create it before publish');
        }

        $draft = file_get_contents($draftFile);
        list($meta, $content) = explode('---', $draft);
        if (empty($meta)) {
            throw new Exception(
                sprintf('Meta must not be empty. Check --- delimiter. rawData = %s', print_r($draft))
            );
        }

        if (empty($content)) {
            throw new Exception('Content is empty. Add empty line after meta and content --- delimiter');
        }

        $meta = $this->extractMeta($meta);

        $post          = new Post();
        $post->meta    = $meta;
        $post->content = $content;

        return $post;
    }

    private function transformPost(Post $draftPost): Post
    {
        $transformedPost = new Post();

        $timestamp = strtotime('now');
        $lang      = !empty($lang) ? $lang : 'ru';
        $title     = $draftPost->meta->title;

        $meta         = new PostMeta();
        $meta->author = !empty($author) ? $author : 'Viktor Zharina';
        $meta->title  = $draftPost->meta->title;
        $meta->order  = count(glob(getenv('POST_CONTENT_PATH') . "/" . getenv('DEFAULT_LANG') . "/*")) + 1;
        $meta->lang   = $lang;
        $meta->slug   = ($lang === 'ru') ? StringHelper::transliterate($title) : $title;

        $meta->publishDate = ($meta->lang === 'ru') ? date('d.m.Y H:i:s', $timestamp) : date('m.d.Y H:i:s', $timestamp);
        $meta->keywords    = $draftPost->meta->keywords;
        $meta->description = $draftPost->meta->description;
        $meta->image       = empty($draftPost->meta->image) ? $draftPost->meta->image : '/static/images/default.png';

        $transformedPost->content = $draftPost->content;
        $transformedPost->meta    = $meta;

        return $transformedPost;
    }

    /**
     * @param Post $transformedPost
     *
     * @param bool $dry
     *
     * @return int
     * @throws \Exception
     */
    private function load(Post $transformedPost): int
    {
        $data = [
            'title'       => $transformedPost->meta->title,
            'author'      => $transformedPost->meta->author,
            'description' => $transformedPost->meta->description,
            'keywords'    => $transformedPost->meta->keywords,
            'order'       => $transformedPost->meta->order,
            'image'       => $transformedPost->meta->image,
            'slug'        => $transformedPost->meta->slug,
            'lang'        => $transformedPost->meta->lang,
            'publishDate' => $transformedPost->meta->publishDate,
            'content'     => $transformedPost->content,
        ];

        $template = file_get_contents(getenv('POST_TEMPLATE_PATH'));
        foreach ($data as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }

        $lang        = $transformedPost->meta->lang;
        $publishDate = $transformedPost->meta->publishDate;
        $slug        = $transformedPost->meta->slug;
        $postFile    = getenv('POST_CONTENT_PATH') . "/$lang/$publishDate-$slug@$lang.md";

        if ($this->dry) {
            var_dump($postFile);
            var_dump($template);

            return 0;
        }

        $result = file_put_contents($postFile, $template);

        if ($result === false) {
            throw new Exception("Unable to write to file $postFile");
        }

        return $result;
    }

    /**
     * Move content from draft to published post.
     *
     * @param array $params
     *
     * @throws \Exception
     */
    public function run()
    {
        $extractedPost   = $this->extractPost(getenv('POST_DRAFT_FILE'));
        $transformedPost = $this->transformPost($extractedPost);
        $this->load($transformedPost);
    }
}
