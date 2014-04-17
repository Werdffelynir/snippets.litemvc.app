<?php

// Подключение и класса для работы с БД idiorm
//include( './Components/idiorm.php' );
// Подключение базы данных sqlite
//ORM::configure('sqlite:./DataBase/database.db');

define('URL', App::$url);
define('URL_THEME', App::$urlTheme);



function limitChars($text, $limit=200, $more=' ... '){
    $text=  mb_substr($text,0,$limit);
    if(mb_substr($text, mb_strlen($text)-1,1) && mb_strlen($text) == $limit) {
        $textReturn = mb_substr($text,0,mb_strlen($text)-mb_strlen(strrchr($text,' ')));
        if(!empty($textReturn)) {
            return $textReturn.$more;
        }
    }
    return $text;
}

function limitWords($input_text, $limit = 50, $end_str = '...') {
    $input_text = strip_tags($input_text);
    $words = explode(' ', $input_text);
    if ($limit < 1 || sizeof($words) <= $limit) {
        return $input_text;
    }
    $words = array_slice($words, 0, $limit);
    $out = implode(' ', $words);
    return $out.$end_str;
}

