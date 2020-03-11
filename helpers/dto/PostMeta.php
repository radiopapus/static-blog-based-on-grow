<?php

namespace Mashinka\dto;

class PostMeta
{
    public $title;
    public $publishDate;
    public $author;
    public $order;
    public $image;
    public $lang;
    public $slug;
    public $keywords;
    public $description;

    /**
     * @param array $array
     *
     * @return \Mashinka\dto\PostMeta
     */
    public static function fromArray(array $array): self
    {
        $meta = new self();

        foreach ($array as $item) {
            list($key, $value) = explode(':', $item);
            $meta->$key = $value;
        }

        return $meta;
    }
}
