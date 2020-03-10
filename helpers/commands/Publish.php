<?php

namespace Mashinka\Commands;

use Mashinka\dto\Meta;
use Mashinka\dto\Post;

class Publish implements CommandInterface
{
    private $params;

    public function setParams(CommandParamsInterface $params): CommandInterface
    {
        $this->params = $params;

        return $this;
    }

    private function extractMeta(string $meta): Meta
    {
        $explodedByLine = explode(PHP_EOL, $meta);
        array_pop($explodedByLine);

        return Meta::fromArray($explodedByLine);
    }

    /**
     * @param string $draftFile
     * @return Post
     * @throws \Exception
     */
    private function extract(string $draftFile): Post
    {
        $draft = file_get_contents($draftFile);
        list($meta, $content) = explode('---', $draft);
        if (empty($meta)) {
            throw new \Exception(
                sprintf('Meta must not be empty. Check --- delimiter. rawData = %s', print_r($draft))
            );
        }

        if (empty($content)) {
            throw new \Exception('Content is empty. Add empty line after meta and content --- delimiter');
        }

        $post = new Post();
        $meta = $this->extractMeta($meta);
        $post->setMeta($meta);
        $post->setContent($content);

        return $post;
    }

    private function transform(Post $draftPost): Post
    {
        $transformedPost = new Post();
        $transformedPost->setContent($draftPost->getContent());

        $meta = new Meta();
        $meta->author = empty($draftPost->meta->author) ? $draftPost->meta->author : "Viktor Zharina";
        $meta->title = $draftPost->meta->title;
        $meta->order = $draftPost->getOrder();
        $meta->slug = $draftPost->meta->slug;
        $meta->lang = empty($draftPost->meta->lang) ? $draftPost->meta->lang : "ru";
        $meta->keywords = $draftPost->meta->keywords;
        $meta->description = $draftPost->meta->description;
        $meta->image = empty($draftPost->meta->image) ? $draftPost->meta->image : "/static/images/default.png";
        $transformedPost->setMeta($meta);

        return $transformedPost;
    }

    private function load(Post $transformedPost, string $postFile): Post
    {
        $data = [
            
        ];
        
        $template = file_get_contents('/templates/post.tpl');

        foreach ($data as $datum) {
            
        }
        $draft = file_put_contents($postFile, $transformedPost);
        return new Post();
    }

    /**
     * Move content from draft to post state.
     *
     * @param string $draftFile
     *
     * @return bool
     * @throws \Exception
     */
    public function run(string $draftFile = '/srv/www/static-blog/drafts/post.md'): bool
    {
        if (!file_exists($draftFile)) {
            throw new \Exception('post.md does not exist. Create it before publish');
        }

        $extractedPost = $this->extract($draftFile);
        $transformedPost = $this->transform($extractedPost);
        $this->load($transformedPost);

        // check if draft exists
        // show how much draft will be proccesed
        // dry-run or not?
        return true;
    }
}
