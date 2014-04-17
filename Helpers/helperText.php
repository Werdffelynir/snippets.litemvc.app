<?php




if (!function_exists('convertStr')) {
/**
 * Функция переобразования строк
 *
 * @param $string
 * @param $format
 *      MB_CASE_UPPER - В верхний регистр
 *      MB_CASE_LOWER - В нижний регистр
 *      MB_CASE_TITLE - Первый символ каждого слова в верхний регистр
 *      UPPER - В верхний регистр
 *      LOWER - В нижний регистр
 *      UPPER_FIRST - В верхний регистр первый символ строки. (по умолчанию)
 *      UPPER_FIRST_E - В верхний регистр первый символ строки? каждого приложения.
 * @return bool|string
 */
function convertStr($string, $format = "UPPER_FIRST")
{

    switch ($format) {
        case "MB_CASE_UPPER":
            $string = mb_convert_case($string, MB_CASE_UPPER, "UTF-8");
            break;
        case "MB_CASE_LOWER":
            $string = mb_convert_case($string, MB_CASE_LOWER, "UTF-8");
            break;
        case "MB_CASE_TITLE":
            $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
            break;
        case "UPPER":
            $string = mb_strtoupper($string, "UTF-8");
            break;
        case "LOWER":
            $string = mb_strtolower($string, "UTF-8");
            break;
        case "UPPER_FIRST":
            preg_match_all("|^(.)(.*)$|u", convertStr($string, "MB_CASE_LOWER"), $strArr);
            return mb_strtoupper($strArr[1][0], "UTF-8") . $strArr[2][0];
            break;
        case "UPPER_FIRST_E":
            $tempStr = explode(".", $string);
            $tempJoin = '';
            foreach ($tempStr as $value) {
                $tempJoin .= convertStr(trim($value), "UPPER_FIRST") . ". ";
            }
            $string = mb_substr($tempJoin, 0, -3);
            break;
        default:
            $string = false;
    }

    return $string;
}
}


if (!function_exists('cutText')) {
/**
 * Обрезать строку по количеству слов.
 *
 * @param string    $string     строка для обработки
 * @param number    $maxLen     количество слов
 * @param string    $more       окончание more '...'
 * @param string    $linkMore   ссылка на more
 * @param bool      $encode     strip_tags()
 * @return string обрезаная строка
 *
 */
function cutText($string, $maxLen, $more = '...', $linkMore = null, $encode = true)
{
    if ($encode == true) {
        $string = strip_tags($string);
        $string = trim(preg_replace('~\s+~s', ' ', $string));
    }

    if (!is_null($linkMore))
        $more = '<a href="' . $linkMore . '">' . $more . '</a>';

    $words = explode(' ', $string);

    if ($maxLen < 1 || sizeof($words) <= $maxLen) {
        return $string;
    }

    $words = array_slice($words, 0, $maxLen);

    $out = implode(' ', $words);

    return $out . ' ' . $more;
}
}


if (!function_exists('wordLimiter')) {
/**
 * @param string    $str
 * @param int       $n
 * @param string    $endChar
 * @return string
 */
function wordLimiter($str, $n = 100, $endChar = '&#8230;')
{
    if (trim($str) == '')
        return $str;

    preg_match('/^\s*+(?:\S++\s*+){1,' . (int)$n . '}/', $str, $matches);

    if (strlen($str) == strlen($matches[0]))
        $endChar = '';

    return rtrim($matches[0]) . $endChar;
}
}


if (!function_exists('characterLimiter')) {
/**
 * @param string    $str
 * @param int       $n
 * @param string    $endChar
 * @return mixed|string
 */
function characterLimiter($str, $n = 500, $endChar = '&#8230;')
{
    if (strlen($str) < $n)
        return $str;

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

    if (strlen($str) <= $n)
        return $str;

    $out = "";
    foreach (explode(' ', trim($str)) as $val) {
        $out .= $val . ' ';
        if (strlen($out) >= $n) {
            $out = trim($out);
            return (strlen($out) == strlen($str)) ? $out : $out . $endChar;
        }
    }
}
}


if (!function_exists('wordCensor')) {
/** */
function wordCensor($str, $censored, $replacement = '')
{
    if (!is_array($censored))
        return $str;

    $str = ' ' . $str . ' ';
    $delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';
    foreach ($censored as $badWord) {
        if ($replacement != '')
            $str = preg_replace("/({$delim})(" . str_replace('\*', '\w*?', preg_quote($badWord, '/')) . ")({$delim})/i", "\\1{$replacement}\\3", $str);
        else
            $str = preg_replace("/({$delim})(" . str_replace('\*', '\w*?', preg_quote($badWord, '/')) . ")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'", $str);
    }
    return trim($str);
}
}



if (!function_exists('limitChars')) {
/**
 * filter return cropped string by chars 
 */
function limitChars($text, $limit = 200, $more = ' ... ')
{
    $text = mb_substr($text, 0, $limit);
    if (mb_substr($text, mb_strlen($text) - 1, 1) && mb_strlen($text) == $limit) {
        $textReturn = mb_substr($text, 0, mb_strlen($text) - mb_strlen(strrchr($text, ' ')));
        if (!empty($textReturn)) {
            return $textReturn . $more;
        }
    }
    return $text;
}
}


if (!function_exists('limitWords')) {
/* filter return cropped string by words */
function limitWords($input_text, $limit = 50, $end_str = '...')
{
    $input_text = strip_tags($input_text);
    $words = explode(' ', $input_text);
    if ($limit < 1 || sizeof($words) <= $limit) {
        return $input_text;
    }
    $words = array_slice($words, 0, $limit);
    $out = implode(' ', $words);
    return $out . $end_str;
}
}


if (!function_exists('ruToLatin')) {
function ruToLatin($string)
{
    $string = mb_strtolower($string);
    $string = str_replace(' ', '', $string);
    $table = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ґ' => 'g',
        'д' => 'd', 'е' => 'e', 'є' => 'e', 'ё' => 'yo', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'і' => 'i', 'й' => 'j', 'ї' => 'i',
        'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh',
        'щ' => 'csh', 'ь' => '', 'ы' => 'y', 'ъ' => '', 'э' => 'e',
        'ю' => 'yu', 'я' => 'ya');
    $output = str_replace(array_keys($table), array_values($table), $string);
    return $output;
}
}


if (!function_exists('translit')) {

    function translit($string){
        $string = trim(strip_tags($string));
        $string = toLower($string);
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => "", 'ы' => 'y', 'ъ' => "",
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            "'" => '', '"' => '', '_' => '-',
            ";" => '', '^' => '', "/" => '',
            ":" => '', '*' => '', "\\" => '',
        );
        return str_replace(" ", "_", strtr($string, $converter));
    }
}

if (!function_exists('toLower')) {
/**
 * Перевод строки в нижний регистр
 *
 * @since 0.3.0
 *
 * @param   string  $text
 * @return  string
 */
function toLower($text) {
    if (function_exists('mb_strtolower')) {
        $text = mb_strtolower($text, 'UTF-8');
    } else {
        $text = strtolower($text);
    }
    return $text;
}
}


if (!function_exists('toUpper')) {
/**
 * Перевод строки в верхний регистр
 *
 * @since 0.3.0
 *
 * @param   string  $text
 * @return  string
 */
function toUpper($text) {
    if (function_exists('mb_strtoupper')) {
        $text = mb_strtoupper($text, 'UTF-8');
    } else {
        $text = strtoupper($text);
    }
    return $text;
}
}




/* SECURITY FUNCTIONS
 **********************************************************************  */


if (!function_exists('clean')) {
/**
 * Очистка строки
 *
 * @since 0.3.0
 *
 * @param string    $text
 * @param bool      $max
 * @return string
 */
function clean($text,$max=true){
    if($max){
        $text = trim(stripslashes(strip_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'))));
        $text = str_replace(chr(3), ' ', $text); // ETX
        $text = str_replace(chr(12), '', $text); // FF
        $text = str_replace(chr(13), '', $text); // CR
    }else{
        $text = trim(stripslashes(strip_tags($text, ENT_QUOTES, 'UTF-8')));
    }
    return $text;
}
}


if (!function_exists('cleanUrl')) {
/**
 * Очистка URL строки
 *
 * @since 0.3.0
 *
 * @param   string  $text
 * @return  string
 */
function cleanUrl($text)  {
    $text = strip_tags(toLower($text));
    $codeEntitiesMatch = array(' ?',' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=','.');
    $codeEntitiesReplace = array('','-','-','','','','','','','','','','','','','','','','','','','','','','','','');
    $text = str_replace($codeEntitiesMatch, $codeEntitiesReplace, $text);
    $text = urlencode($text);
    $text = str_replace('--','-',$text);
    $text = rtrim($text, "-");
    return $text;
}
}


if (!function_exists('clearQuotes')) {
/**
 * Очистка от тегов и кавычек по умолчанию,
 * или если указан второй аргумент кодирует кавычки
 *
 * @since 0.3.0
 *
 * @param string $text
 * @param bool $encode
 * @return string
 */
function clearQuotes($text, $encode=false)  {
    $text = strip_tags($text);
    if(!$encode){
        $codeEntitiesMatch = array('"','\'','&quot;');
        $text = str_replace($codeEntitiesMatch, '', $text);
    }else{
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
    }
    return trim($text);
}
}


if (!function_exists('clearXSS')) {
/**
 * Очищает строку от небезопасных сиволов, обезопасит от XSS атак
 *
 * @since 0.3.0
 * @author Martijn van der Ven
 *
 * @param   string  $str    Строка для очистки
 * @return  string
 */
function clearXSS($str){
    // attributes blacklist:
    $attr = array('style','on[a-z]+');
    // elements blacklist:
    $elem = array('script','iframe','embed','object');
    // extermination:
    $str = preg_replace('#<!--.*?-->?#', '', $str);
    $str = preg_replace('#<!--#', '', $str);
    $str = preg_replace('#(<[a-z]+(\s+[a-z][a-z\-]+\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*)\s+href\s*=\s*(\'javascript:[^\']*\'|"javascript:[^"]*"|javascript:[^\s>]*)((\s+[a-z][a-z\-]*\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*\s*>)#is', '$1$5', $str);
    foreach($attr as $a) {
        $regex = '(<[a-z]+(\s+[a-z][a-z\-]+\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*)\s+'.$a.'\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*)((\s+[a-z][a-z\-]*\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*\s*>)';
        $str = preg_replace('#'.$regex.'#is', '$1$5', $str);
    }
    foreach($elem as $e) {
        $regex = '<'.$e.'(\s+[a-z][a-z\-]*\s*=\s*(\'[^\']*\'|"[^"]*"|[^\'">][^\s>]*))*\s*>.*?<\/'.$e.'\s*>';
        $str = preg_replace('#'.$regex.'#is', '', $str);
    }
    return $str;
}
}


if (!function_exists('htmlEncode')) {
/**
 * Html в спец-символы
 *
 * @since 0.3.0
 *
 * @param   string  $text
 * @return  string
 */
function htmlEncode($text) {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $text = str_replace(chr(3), ' ', $text); // ETX
    $text = str_replace(chr(12), '', $text); // FF
    $text = str_replace(chr(13), '', $text); // CR
    return $text;
}
}


if (!function_exists('htmlDecode')) {
/**
 * Спец-символы HTML Decode
 *
 * @since 0.3.0
 *
 * @param   string  $text
 * @return  string
 */
function htmlDecode($text) {
    $text = stripslashes(htmlspecialchars_decode($text, ENT_QUOTES));
    return $text;
}
}


if (!function_exists('specFilter')) {
/**
 * Осуществляет фильтрацию переменной, возвращается к htmlentities
 * использует стандартную функ filter_var.
 *
 * @since 0.3.0
 *
 * @param  string $var    Строка фильтрации
 * @param  string $filter Тип фильтра (string|int|float|url|email|special или decode)
 * @return string
 */
function specFilter($var,$filter = "special"){

    if($filter=="decode")
        return stripslashes(htmlspecialchars_decode($var, ENT_QUOTES));

    if(function_exists( "filter_var") ){
        $aryFilter = array(
            "string"  => FILTER_SANITIZE_STRING,
            "int"     => FILTER_SANITIZE_NUMBER_INT,
            "float"   => FILTER_SANITIZE_NUMBER_FLOAT,
            "url"     => FILTER_SANITIZE_URL,
            "email"   => FILTER_SANITIZE_EMAIL,
            "special" => FILTER_SANITIZE_SPECIAL_CHARS,
        );
        if(isset($aryFilter[$filter])) return filter_var( $var, $aryFilter[$filter]);
        return filter_var( $var, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    else {
        return htmlentities($var);
    }
}
}



















