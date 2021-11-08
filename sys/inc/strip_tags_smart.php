<?php

/**
 * Более продвинутый аналог strip_tags() для корректного вырезания тагов из html кода.
 * Функция strip_tags(), в зависимости от контекста, может работать не корректно.
 * Воможности:
 *   - корректно обрабатываются вхождения типа "a < b > c"
 *   - корректно обрабатывается "грязный" html, когда в значениях атрибутов тагов могут встречаться символы < >
 *   - корректно обрабатывается разбитый html
 *   - вырезаются комментарии, скрипты, стили, PHP, Perl, ASP код, MS Word таги
 *   - автоматически форматируется текст, если он содержит html код
 *   - защита от подделок типа: "<<fake>script>alert('hi')</</fake>script>"
 *
 * @param   string  $s
 * @param   array   $allowable_tags     Массив тагов, которые не будут вырезаны
 * @param   bool    $is_format_spaces   Форматировать пробелы и переносы строк?
 *                                      Текст форматируется автоматически, если он содержит html код:
 *                                      вид текста на выходе (plain) максимально приближеется виду текста в браузере на входе
 *                                      Другими словами, грамотно преобразует text/html в text/plain
 *                                      Если текст содержит html таги, $is_format_spaces = TRUE
 * @param   array   $pair_tags   массив имён парных тагов, которые будут удалены вместе с содержимым
 *                               см. значения по умолчанию
 * @param   array   $para_tags   массив имён парных тагов, которые будут восприниматься как параграфы (если $is_format_spaces = true)
 *                               см. значения по умолчанию
 * @return  string
 *
 * @author   Nasibullin Rinat <n a s i b u l l i n  at starlink ru>
 * @charset  ANSI
 * @version  4.0.4-dev2  :TODO: <pre>text\r\ntext</pre>
 */
function strip_tags_smart(
    /*string*/ $s,
    array $allowable_tags = null,
    /*boolean*/ $is_format_spaces = false,
    array $pair_tags = array('script', 'style', 'map', 'iframe', 'frameset', 'object', 'applet', 'comment', 'button'),
    array $para_tags = array('p', 'td', 'th', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'form', 'title')
)
{
    static $_callback_type  = false;
    static $_allowable_tags = array();
    static $_para_tags      = array();
    #регулярное выражение для атрибутов тагов
    #корректно обрабатывает грязный и битый HTML в однобайтовой или UTF-8 кодировке!
    static $re_attrs_fast_safe =  '(?> (?>[\x20\r\n\t]+|\xc2\xa0)+  #пробельные символы (д.б. обязательно)
                                       (?>
                                         #правильные атрибуты
                                                                        [^>"\']+
                                         | (?<=[\=\x20\r\n\t]|\xc2\xa0) "[^"]*"
                                         | (?<=[\=\x20\r\n\t]|\xc2\xa0) \'[^\']*\'
                                         #разбитые атрибуты
                                         |                              [^>]+
                                       )*
                                   )?';

    if (is_array($s) && $_callback_type === 'strip_tags')
    {
        $tag = strtolower($s[1]);
        if ($_allowable_tags &&
            (array_key_exists($tag, $_allowable_tags) || array_key_exists('<' . trim(strtolower($s[0]), '< />') . '>', $_allowable_tags))
            ) return $s[0];
        if ($tag == 'br') return "\r\n";
        if ($_para_tags && array_key_exists($tag, $_para_tags)) return "\r\n\r\n";
        return '';
    }

    if (($pos = strpos($s, '<') === false) || strpos($s, '>', $pos) === false)  #оптимизация скорости
    {
        #таги не найдены
        return $s;
    }

    #непарные таги (открывающие, закрывающие, !DOCTYPE, MS Word namespace)
    $re_tags = '/<[\/\!]? ([a-zA-Z][a-zA-Z\d]* (?>\:[a-zA-Z][a-zA-Z\d]*)?)' . $re_attrs_fast_safe . '\/?>/sx';

    $patterns = array(
        '/<([\?\%]) .*? \\1>/sx',     #встроенный PHP, Perl, ASP код
        '/<\!\[CDATA\[ .*? \]\]>/sx', #блоки CDATA
        #'/<\!\[  [\x20\r\n\t]* [a-zA-Z] .*?  \]>/sx',  #:DEPRECATED: MS Word таги типа <![if! vml]>...<![endif]>

        '/<\!--.*?-->/s', #комментарии

        #MS Word таги типа "<![if! vml]>...<![endif]>",
        #условное выполнение кода для IE типа "<!--[if expression]> HTML <![endif]-->"
        #условное выполнение кода для IE типа "<![if expression]> HTML <![endif]>"
        #см. http://www.tigir.com/comments.htm
        '/<\! (?>--)?
              \[
              (?> [^\]"\']+ | "[^"]*" | \'[^\']*\' )*
              \]
              (?>--)?
         >/sx',
    );
    if ($pair_tags)
    {
        #парные таги вместе с содержимым:
        foreach ($pair_tags as $k => $v) $pair_tags[$k] = preg_quote($v, '/');
        $patterns[] = '/<((?i:' . implode('|', $pair_tags) . '))' . $re_attrs_fast_safe . '> .*? <\/(?i:\\1)' . $re_attrs_fast_safe . '>/sx';
    }
    #d($patterns);

    $i = 0; #защита от зацикливания
    $max = 99;
    while ($i < $max)
    {
        $s2 = preg_replace($patterns, '', $s);
        if ($i == 0)
        {
            $is_html = ($s2 != $s || preg_match($re_tags, $s2));
            if ($is_html)
            {
                #В библиотеке PCRE для PHP \s - это любой пробельный символ, а именно класс символов [\x09\x0a\x0c\x0d\x20\xa0] или, по другому, [\t\n\f\r \xa0]
                #Если \s используется с модификатором /u, то \s трактуется как [\x09\x0a\x0c\x0d\x20]
                #Браузер не делает различия между пробельными символами,
                #друг за другом подряд идущие символы воспринимаются как один
                #$s = str_replace(array("\r", "\n", "\t"), ' ', $s);
                $s2 = strtr($s2, "\x09\x0a\x0c\x0d", '    ');

                #массив тагов, которые не будут вырезаны
                if ($allowable_tags) $_allowable_tags = array_flip($allowable_tags);

                #парные таги, которые будут восприниматься как параграфы
                if ($para_tags) $_para_tags = array_flip($para_tags);
            }
        }#if

        #обработка тагов
        if ($is_html)
        {
            $_callback_type = 'strip_tags';
            $s2 = preg_replace_callback($re_tags, __FUNCTION__, $s2);
            $_callback_type = false;
        }

        if ($s === $s2) break;
        $s = $s2; $i++;
    }#while
    if ($i >= $max) $s = strip_tags($s); #too many cycles for replace...

    if ($is_format_spaces || $is_html)
    {
        #вырезаем дублирующие пробелы
        $s = preg_replace('/\x20\x20+/s', ' ', trim($s));
        #вырезаем пробелы в начале и в конце строк
        $s = str_replace(array("\r\n\x20", "\x20\r\n"), "\r\n", $s);
        #заменяем 2 и более переносов строк на 2 переноса строк
        $s = preg_replace('/\r\n[\r\n]+/s', "\r\n\r\n", $s);
    }
    return $s;
}

?>