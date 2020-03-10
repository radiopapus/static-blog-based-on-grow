<?php

namespace Mashinka\dto;
//title: Пишем первый custom block для редактора Gutenberg в Wordpress
//lang: ru
//description: Как написать свой блок для редактора Gutenberg в Wordpress. Статья написана, чтобы закрепить
//полученные знания на практике. На этой основе будут созданы другие, более сложные блоки.
//keywords: wordpress, custom block, gutenberg

//    public function getTitle()
//    {
//        return trim(preg_replace('/\s+/', ' ', $this->title));
//    }

class Meta
{
    public $title;
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
     * @return string
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
