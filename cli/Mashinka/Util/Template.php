<?php

namespace Mashinka\Util;

class Template
{
    public function process(string $templatePath, array $data): string
    {
        $template = file_get_contents($templatePath);
        foreach ($data as $key => $value) {
            $template = str_replace(sprintf('{%s}', $key), $value, $template);
        }

        return $template;
    }
}
