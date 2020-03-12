<?php

namespace Mashinka\Commands;

use Exception;
use Mashinka\DTO\PostMeta;
use Mashinka\DTO\Post;
use Mashinka\Util\PostHelper;

class Publish implements CommandInterface
{
    private array $params = [];
    private bool  $dry    = false;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->dry    = in_array('--dry-run', $params, true);
    }

    /**
     * @param string $draftFile
     *
     * @return Post
     * @throws Exception
     */
    private function extractPostFromDraft(string $draftFile): Post
    {
        if (!file_exists($draftFile)) {
            throw new Exception("$draftFile does not exist. Create it before publish");
        }

        $draft = file_get_contents($draftFile);
        [$meta, $content] = explode('---', $draft);

        if (empty($content)) {
            throw new Exception('Content is empty. Add empty line after meta and content --- delimiter');
        }

        $meta = PostHelper::extractMeta($meta);

        $post          = new Post();
        $post->meta    = $meta;
        $post->content = $content;

        return $post;
    }

    private function transformPost(Post $draftPost): Post
    {
        $transformedPost            = new Post();
        $transformedPost->timestamp = strtotime('now');
        $transformedPost->content   = $draftPost->content;

        $lang  = !empty($lang) ? $lang : 'ru';
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
     * @return int
     * @throws \Exception
     */
    private function load(Post $post): void
    {
        PostHelper::loadContent($post, $this->dry);
    }

    /**
     * Move content from draft to published post.
     *
     * @throws \Exception
     */
    public function run()
    {
        $extractedPost   = $this->extractPostFromDraft(getenv('POST_DRAFT_FILE'));
        $transformedPost = $this->transformPost($extractedPost);
        $this->load($transformedPost);
    }
}
