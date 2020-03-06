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
     * @param string $draft
     *
     * @return \Mashinka\dto\Post
     * @throws \Exception
     */
    private function extract(string $draft): Post
    {
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

    private function load(): Post
    {
        return new Post();
    }

    private function transform(Post $draftPost): Post
    {
        $transformedPost = new Post();
        $transformedPost->setContent($draftPost->getContent());


        $transformedPost->setContent($draftPost->getContent());
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

        $draft = file_get_contents($draftFile);

        $post = $this->extract($draft);
        $transformedPost = $this->transform($post);
        $this->load($transformedPost);

        // check if draft exists
        // show how much draft will be proccesed
        // dry-run or not?
        return true;
    }
}
