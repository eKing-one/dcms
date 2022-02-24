<?php
/*
  mysql2i.class.php ver 1.5

  #fixed statement structure to support legacy versions of PHP older than 5.5
  #updated mysql_result
  #fixed mysql_fetch_object so third param defaults to an array

  ver 1.0

  Initial release

  This class is released into the public domain without copyright

*/

/*

  Доработка biket
  DCMS-Social.ru

*/


class mysql2i
{

  public static $queries = [];

  public static $sql_count = 0;

  public static $currObj;


  public static function sql_count()
  {


    return self::$sql_count;

  }

  public static function queries()
  {


    return self::$queries;

  }


  public static function mysql_affected_rows($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_affected_rows($link);

  }

  public static function mysql_client_encoding($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_character_set_name($link);

  }

  public static function mysql_close($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_close($link);

  }

  public static function mysql_connect($host = '', $username = '', $passwd = '', $new_link = FALSE, $client_flags = 0)
  {
    mysqli_report(MYSQLI_REPORT_ERROR);
    $link = mysqli_connect($host, $username, $passwd);
    self::$currObj = $link;

    return $link;

  }

  public static function mysql_create_db($database_name, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    $query = "CREATE DATABASE `" . $database_name . "`";
    mysqli_query($link, $query);

    $e = mysqli_errno($link);
    if (empty($e)) {
      return TRUE;
    } else {
      return FALSE;
    }

  }

  public static function mysql_data_seek($result, $offset)
  {

    return mysqli_data_seek($result, $offset);

  }

  public static function mysql_db_name($result, $row, $field = NULL)
  {

    mysqli_data_seek($result, $row);

    $f = mysqli_fetch_row($result);

    return $f[0];

  }

  public static function mysql_db_query($database, $query, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }
    mysqli_select_db($link, $database);
    $r = mysqli_query($link, $query);

    return $r;

  }

  public static function mysql_drop_db($database, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }
    $query = "DROP DATABASE `" . $database . "`";
    mysqli_query($link, $query);

    $e = mysqli_errno($link);
    if (empty($e)) {
      return TRUE;
    } else {
      return FALSE;
    }

  }

  public static function mysql_errno($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_errno($link);
  }

  public static function mysql_error($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_error($link);
  }

  public static function mysql_escape_string($escapestr)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_real_escape_string($link, $escapestr);
  }

  public static function mysql_fetch_array($result, $resulttype = MYSQLI_BOTH)
  {

    return mysqli_fetch_array($result, $resulttype);

  }

  public static function mysql_fetch_assoc($result)
  {
if ($result==null) return null;
else return mysqli_fetch_assoc($result);

  }

  public static function mysql_fetch_field($result, $field_offset = 0)
  {

    if (!empty($field_offset)) {
      for ($x = 0; $x < $field_offset; $x++) {
        mysqli_fetch_field($result);
      }
    }

    return mysqli_fetch_field($result);

  }

  public static function mysql_fetch_lengths($result)
  {

    return mysqli_fetch_lengths($result);

  }

  public static function mysql_fetch_object($result, $class_name = NULL, $params = array())
  {

    return mysqli_fetch_object($result, $class_name, $params);

  }

  public static function mysql_fetch_row($result)
  {

    return mysqli_fetch_row($result);

  }

  /*
  credit to andre at koethur dot de from php.net and NinjaKC from stackoverflow.com
  */
  public static function mysql_field_flags($result, $field_offset)
  {
    static $flags;

    $flags_num = mysqli_fetch_field_direct($result, $field_offset)->flags;

    if (!isset($flags)) {
      $flags = array();
      $constants = get_defined_constants(TRUE);
      foreach ($constants['mysqli'] as $c => $n) if (preg_match('/MYSQLI_(.*)_FLAG$/', $c, $m)) if (!array_key_exists($n, $flags)) $flags[$n] = $m[1];
    }

    $result = array();
    foreach ($flags as $n => $t) if ($flags_num & $n) $result[] = $t;

    $return = implode(' ', $result);
    $return = str_replace('PRI_KEY', 'PRIMARY_KEY', $return);
    $return = strtolower($return);

    return $return;
  }

  public static function mysql_field_len($result, $field_offset)
  {

    $fieldInfo = mysqli_fetch_field_direct($result, $field_offset);

    return $fieldInfo->length;

  }

  public static function mysql_field_name($result, $field_offset)
  {

    $fieldInfo = mysqli_fetch_field_direct($result, $field_offset);

    return $fieldInfo->name;

  }

  public static function mysql_field_seek($result, $fieldnr)
  {

    return mysqli_field_seek($result, $fieldnr);

  }

  public static function mysql_field_table($result, $field_offset)
  {

    $fieldInfo = mysqli_fetch_field_direct($result, $field_offset);

    return $fieldInfo->table;

  }

  /*
  credit to andre at koethur dot de from php.net and NinjaKC from stackoverflow.com
  */
  public static function mysql_field_type($result, $field_offset)
  {
    static $types;

    $type_id = mysqli_fetch_field_direct($result, $field_offset)->type;

    if (!isset($types)) {
      $types = array();
      $constants = get_defined_constants(TRUE);
      foreach ($constants['mysqli'] as $c => $n) if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) $types[$n] = $m[1];
    }

    return array_key_exists($type_id, $types) ? $types[$type_id] : NULL;
  }

  public static function mysql_free_result($result)
  {

    return mysqli_free_result($result);

  }

  public static function mysql_get_client_info()
  {

    $link = self::$currObj;

    return mysqli_get_client_info($link);
  }

  public static function mysql_get_host_info($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_get_host_info($link);
  }

  public static function mysql_get_proto_info($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_get_proto_info($link);
  }

  public static function mysql_get_server_info($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_get_server_info($link);
  }

  public static function mysql_info($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_info($link);
  }

  public static function mysql_insert_id($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_insert_id($link);
  }

  public static function mysql_list_dbs($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }
    $query = "SHOW DATABASES";
    $r = mysqli_query($link, $query);

    $e = mysqli_errno($link);
    if (empty($e)) {
      return $r;
    } else {
      return FALSE;
    }

  }

  public static function mysql_list_fields($database_name, $table_name, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }
    $query = "SHOW COLUMNS FROM `" . $table_name . "`";
    $r = mysqli_query($link, $query);

    $e = mysqli_errno($link);
    if (empty($e)) {
      return $r;
    } else {
      return FALSE;
    }

  }

  public static function mysql_list_processes($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_thread_id($link);
  }

  public static function mysql_list_tables($database, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }
    $query = "SHOW TABLES FROM `" . $database . "`";
    $r = mysqli_query($link, $query);

    $e = mysqli_errno($link);
    if (empty($e)) {
      return $r;
    } else {
      return FALSE;
    }

  }

  public static function mysql_num_fields($result)
  {

    $link = self::$currObj;

    return mysqli_field_count($link);
  }

  public static function mysql_num_rows($result)
  {

    return mysqli_num_rows($result);
  }

  public static function mysql_pconnect($host = '', $username = '', $passwd = '', $new_link = FALSE, $client_flags = 0)
  {


    $link = mysqli_connect('p:' . $host, $username, $passwd);
    if (!$link) {
      echo mysqli_error($link) . '<br>';
    }
    self::$currObj = $link;



    return $link;

  }

  public static function mysql_ping($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_ping($link);

  }

  public static function mysql_query($query, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tstart = $mtime;

    $r = mysqli_query($link, $query);
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tend = $mtime;
    $tpassed = ($tend - $tstart);
    //	 $tpassed = number_format( $tpassed, '6') ;

    // self::$queries[] = $query;
    self::$sql_count++;
    if ($tpassed > 0.001) {
      $tmp = ["query" => $query, "time" => "$tpassed"];

      self::$queries[] = $tmp;
    }
    return $r;


  }

  public static function mysql_real_escape_string($escapestr, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_real_escape_string($link, $escapestr);

  }


  public static function mysql_result($result, $row, $field = 0)
  {

    //   if (!isset($field)) $field = 0; // WTF: БЕЗ ЭТОГО НЕ РАБОТАЕТ

    //ver 1.5 code credited to Mario Lurig at https://mariolurig.com/coding/mysqli_result-function-to-match-mysql_result/
    $numrows = mysqli_num_rows($result);
    if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
      mysqli_data_seek($result, $row);
      $resrow = (is_numeric($field)) ? mysqli_fetch_row($result) : mysqli_fetch_assoc($result);
      if (isset($resrow[$field])) {
        return $resrow[$field];
      }
    }
    return FALSE;

    /*
              //ver 1.0 code has problems with pointer and does not support numeric offset
              mysqli_data_seek($result,$row);
              if( !empty($field) ){
                  while($finfo = mysqli_fetch_field($result)) {
                      if( $field == $finfo->name ){
                          $f = mysqli_fetch_assoc($result);
                          return $f[$field];
                      }
                  }
              }

              $f = mysqli_fetch_array($result);

              return $f[0];
    */

  }

  public static function mysql_select_db($dbname, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    } elseif (is_object($link)) {
      self::$currObj = $link;
    }
    mysqli_select_db($link, $dbname);

    $e = mysqli_errno($link);
    if (empty($e)) {
      return TRUE;
    } else {
      return FALSE;
    }

  }

  public static function mysql_set_charset($charset, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_set_charset($link, $charset);

  }

  public static function mysql_stat($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_stat($link);

  }

  public static function mysql_tablename($result, $row, $field = NULL)
  {

    mysqli_data_seek($result, $row);

    $f = mysqli_fetch_array($result);

    return $f[0];

  }

  public static function mysql_thread_id($link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    return mysqli_thread_id($link);

  }

  public static function mysql_unbuffered_query($query, $link = NULL)
  {

    if (empty($link)) {
      $link = self::$currObj;
    }

    $r = mysqli_query($link, $query, MYSQLI_USE_RESULT);

    return $r;

  }

}

if (!extension_loaded('mysql')) {
  require_once('mysql2i.func.php');

}


// Дополнительные функции

if (!function_exists('ereg')) {
  function ereg($pattern, $subject, &$matches = [])
  {
    return preg_match('/' . $pattern . '/', $subject, $matches);
  }
}
if (!function_exists('eregi')) {
  function eregi($pattern, $subject, &$matches = [])
  {
    return preg_match('/' . $pattern . '/i', $subject, $matches);
  }
}
if (!function_exists('ereg_replace')) {
  function ereg_replace($pattern, $replacement, $string)
  {
    return preg_replace('/' . $pattern . '/', $replacement, $string);
  }
}
if (!function_exists('eregi_replace')) {
  function eregi_replace($pattern, $replacement, $string)
  {
    return preg_replace('/' . $pattern . '/i', $replacement, $string);
  }
}
if (!function_exists('split')) {
  function split($pattern, $subject, $limit = -1)
  {
    return preg_split('/' . $pattern . '/', $subject, $limit);
  }
}
if (!function_exists('spliti')) {
  function spliti($pattern, $subject, $limit = -1)
  {
    return preg_split('/' . $pattern . '/i', $subject, $limit);
  }
}


?>
