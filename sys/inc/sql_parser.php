<?php
/**
 * Простой парсер SQL-дампов для извлечения запросов
 *
 * Использование:
 *   SQLParser::getQueriesFromFile('Имя файла')  - Извлечение запросов из файла
 *   SQLParser::getQueries('SQL-дамп')           - Извлечение запросов из строки
 *
 *   Обе функции возвращают массив из запросов. Пустые запросы опускаются.
 *
 * Лицензия:
 *   Это свободный код, используйте его на свой страх и риск.
 *   Автор не несёт никакой ответственности! :)
 *
 * @author Прибора Антон Николаевич (http://anton-pribora.ru)
 * @copyright (c) Прибора Антон Николаевич, 2008-11-07
 */


/**
 * Парсер SQL-запросов
 *
 */
class SQLParser
{
    /**
     * Выборка SQL-запросов из файла
     *
     * @param string $file
     * @return array
     */
    static public function getQueriesFromFile( $file )
    {
        return self::getQueries( file_get_contents($file) );
    }

    /**
     * Разбор SQL-строки на запросы
     *
     * @param string $sql
     * @return array
     */
    static public function getQueries( $sql )
    {
        $queries  = array();
        $strlen   = strlen($sql);
        $position = 0;
        $query    = '';

        for ( ; $position < $strlen; ++$position )
        {
            $char  = $sql[$position];

            switch ( $char )
            {
                case '-':
                    if ( substr($sql, $position, 3) !== '-- ' )
                    {
                        $query .= $char;
                        break;
                    }

                case '#':
                    while ( $char !== "\r" && $char !== "\n" && $position < $strlen - 1 )
                        $char = $sql[++$position ];
                    break;

                case '`':
                case '\'':
                case '"':
                    $quote  = $char;
                    $query .= $quote;

                    while ( $position < $strlen - 1 )
                    {
                        $char = $sql[++$position ];

                        if ( $char === '\\' )
                        {
                            $query .= $char;

                            if ( $position < $strlen - 1 )
                            {
                                $char   = $sql[++$position ];
                                $query .= $char;

                                if ( $position < $strlen - 1 ) $char = $sql[ ++$position ];
                            }
                            else
                            {
                                break;
                            }
                        }

                        if ( $char === $quote ) break;

                        $query .= $char;
                    }

                    $query .= $quote;
                    break;

                case ';':
                    $query = trim($query);
                    if ( $query ) $queries[] = $query;
                    $query     = '';
                    break;

                default:
                    $query .= $char;
                    break;
            }
        }

        $query = trim( $query );
        if ( $query ) $queries[] = $query;

        return $queries;
    }
}

?>