<?php

namespace Mashinka\Commands;

use Exception;
use Mashinka\DTO\PostMeta;
use Mashinka\DTO\Post;
use Mashinka\Util\PostHelper;
use Mashinka\Util\Template;

class Publish implements CommandInterface
{
    private Template $template;
    private array    $params = [];
    private bool     $dry    = false;

    public function __construct(Template $template, array $params)
    {
        $this->template = $template;
        $this->params   = $params;
        $this->dry      = in_array('--dry-run', $params, true);
    }

    /**
     * Move content from draft to published post.
     *
     * @throws \Exception
     */
    public function run()
    {
        $draft           = getenv('POST_DRAFT_FILE');
        $draftPost       = $this->extract($draft);
        $transformedPost = $this->transform($draftPost);
        $this->load($transformedPost);
    }

    /**
     * @param string $draftFile
     *
     * @return Post
     * @throws Exception
     */
    private function extract(string $draftFile): Post
    {
        if (!file_exists($draftFile)) {
            throw new Exception("$draftFile does not exist. Create it before publish");
        }

        $draft = file_get_contents($draftFile);
        [$meta, $content] = explode('---', $draft);

        if (empty($content)) {
            throw new Exception('Content is empty. Add empty line after meta and content --- delimiter');
        }

        $meta = $this->extractMeta($meta);

        $post          = new Post();
        $post->meta    = $meta;
        $post->content = $content;

        return $post;
    }

    private function transform(Post $draftPost): Post
    {
        $transformedPost            = new Post();
        $transformedPost->timestamp = strtotime('now');
        $transformedPost->content   = $draftPost->content;

        $lang  = !isset($draftPost->meta->lang) ? $draftPost->meta->lang : 'ru';
        $title = $draftPost->meta->title;

        $meta = new PostMeta();
        if ($lang == 'ru') {
            $meta->author = isset($draftPost->meta->author) ? $draftPost->meta->author : 'Виктор Жарина';
        } else {
            $meta->author = isset($draftPost->meta->author) ? $draftPost->meta->author : 'Viktor Zharina';
        }

        $meta->title = $draftPost->meta->title;
        $meta->order = count(glob(getenv('POSTS_PATH') . "/" . getenv('DEFAULT_LANG') . "/*")) + 1;
        $meta->lang  = $lang;

        $meta->slug = PostHelper::buildSlug($title);

        $meta->keywords    = $draftPost->meta->keywords;
        $meta->description = $draftPost->meta->description;
        $meta->image       = isset($draftPost->meta->image) ? $draftPost->meta->image : '/static/images/default.png';

        $transformedPost->meta = $meta;

        return $transformedPost;
    }

    /**
     * @param Post $post
     *
     * @return void
     * @throws \Exception
     */
    private function load(Post $post): void
    {
        $postFile = PostHelper::buildPostFileName(getenv('POSTS_PATH'), $post);
        $content  = $this->buildContent($post);

        $translationFile    = str_replace('{lang}', $post->meta->lang, getenv('TRANSLATIONS_PATH'));
        $translationContent = $this->buildTranslation($post);

        if ($this->dry) {
            var_dump(sprintf('file: %s', $postFile));
            var_dump($content);

            var_dump(sprintf('translation file: %s', $translationFile));
            var_dump(sprintf('translation: %s', substr($translationContent, 0, 500)));

            return;
        }

        $this->writeContent($postFile, $content);
        $this->writeContent($translationFile, $translationContent);
    }

    private function extractMeta(string $meta): PostMeta
    {
        if (empty($meta)) {
            throw new Exception(
                sprintf('Meta must not be empty. Check --- delimiter. rawData = %s', $meta)
            );
        }

        $explodedByLine = explode(PHP_EOL, $meta);
        array_pop($explodedByLine);

        $meta = new PostMeta();

        foreach ($explodedByLine as $item) {
            [$key, $value] = explode(':', $item);
            $meta->$key = trim($value);
        }

        return $meta;
    }

    /**
     * @param \Mashinka\DTO\Post $post
     *
     * @return string
     */
    public function buildContent(Post $post): string
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

        return $this->template->process(getenv('POST_TEMPLATE_PATH'), $data);
    }


    private function buildTranslation(Post $post): string
    {
        $data = [
            'id'    => $post->meta->slug,
            'value' => $post->meta->title,
        ];

        return $this->template->process(getenv('TRANSLATION_TEMPLATE_PATH'), $data);
    }

    private function writeContent(string $file, string $content)
    {
        $result = file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

        if ($result === false) {
            throw new Exception("Unable to write to file $file");
        }
    }
}
