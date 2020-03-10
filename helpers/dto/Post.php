<?php

namespace Mashinka\dto;

use AbstractContent;

class Post extends AbstractContent
{
    public string     $state           = 'DRAFT';
    private string    $postsPathPrefix = 'content/posts';
    private string    $defaultLang     = 'ru';
    private int       $timestamp;

    public function __construct()
    {
        $this->timestamp = strtotime('now');
    }

    /**
     * Count all posts and increase order value
     *
     * @return string
     */

    public function getOrder(): string
    {
        return count(glob("$this->postsPathPrefix/$this->defaultLang/*")) + 1;
    }

    /**
     * Set meta information.
     *
     * @param \Mashinka\dto\Meta $meta
     *
     * @return $this
     */
    public function setMeta(Meta $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     *
     */
    public function getContent(): self
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPath(): string
    {
        $date = date('Y-m-d-H-i-s', $this->timestamp);

        return sprintf("$this->postsPathPrefix/%s-%s@%s.md", $date, $this->meta->slug, $this->meta->lang);
    }

    public function __toString(): string
    {
        $header = '---' . PHP_EOL . 'author@: %s' . PHP_EOL . 'description: %s' . PHP_EOL . 'keywords: %s'
            . PHP_EOL . '$order: %s'
$dates:
  published: %s
$title@: %s
image: %s' .PHP_EOL;

        if (isset($params['slugRu'])) {
            $header .= '%s' . PHP_EOL;
        }

        if (isset($params['slugEn'])) {
            $header .= '%s' . PHP_EOL;
        }

        $header .= '---';

        return sprintf(
                $header,
                $params['description'],
                $params['keywords'],
                $params['$order'],
                $params['published'],
                $params['$title@'],
                $params['image'],
                isset($params['slugRu']) ? 'slugRu: ' . $params['slugRu'] : '',
                isset($params['slugEn']) ? 'slugEn: ' . $params['slugEn'] : ''
            ) . PHP_EOL;
    }
}
