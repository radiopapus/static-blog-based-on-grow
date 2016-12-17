<?php
/**
* Transliterate letters from russian to latin
* @param string - string for transliteration
* @return string transliterated string
*/
function transliterate($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v', 'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z', 'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n', 'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u', 'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sh', 'ь' => '',  'ы' => 'i',   'ъ' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya', 

        'А' => 'A',   'Б' => 'B',   'В' => 'V', 'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z', 'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N', 'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U', 'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sh', 'Ь' => '',  'Ы' => 'i',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya'
    );
    return strtr($string, $converter);
}

/**
* Count all posts and increase order value
* @param $directory path which files must be counted
* @param int count - num files in $directory
*/
function getOrder($directory) {
    return count(glob($directory . "*")) + 1;
}