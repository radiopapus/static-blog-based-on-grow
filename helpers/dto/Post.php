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
}
