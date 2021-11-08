<?php
/*
mysql2i.func.php rev 3
member of mysql2i.class.php ver 1.5
*/

//predifined fetch constants
//define('MYSQL_BOTH', MYSQLI_BOTH);
//define('MYSQL_NUM', MYSQLI_NUM);
//define('MYSQL_ASSOC', MYSQLI_ASSOC);

function mysql_affected_rows($link = NULL)
{

  return mysql2i::mysql_affected_rows($link);

}

function mysql_client_encoding($link = NULL)
{

  return mysql2i::mysql_client_encoding($link);

}

function mysql_close($link = NULL)
{

  return mysql2i::mysql_close($link);

}

function mysql_connect($host = '', $username = '', $passwd = '', $new_link = FALSE, $client_flags = 0)
{

  return mysql2i::mysql_connect($host, $username, $passwd);

}

function mysql_create_db($database_name, $link = NULL)
{

  return mysql2i::mysql_create_db($database_name, $link);

}

function mysql_data_seek($result, $offset)
{

  return mysql2i::mysql_data_seek($result, $offset);

}

function mysql_db_name($result, $row, $field = NULL)
{

  return mysql2i::mysql_db_name($result, $row, $field);

}

function mysql_db_query($database, $query, $link = NULL)
{

  return mysql2i::mysql_db_query($database, $query, $link);

}

function mysql_drop_db($database, $link = NULL)
{

  return mysql2i::mysql_drop_db($database, $link);

}

function mysql_errno($link = NULL)
{

  return mysql2i::mysql_errno($link);

}

function mysql_error($link = NULL)
{

  return mysql2i::mysql_error($link);

}

function mysql_escape_string($escapestr)
{

  return mysql2i::mysql_escape_string($escapestr);

}

function mysql_fetch_array($result, $resulttype = MYSQLI_BOTH)
{

  return mysql2i::mysql_fetch_array($result, $resulttype);

}

function mysql_fetch_assoc($result)
{

  return mysql2i::mysql_fetch_assoc($result);

}

function mysql_fetch_field($result, $field_offset = NULL)
{

  return mysql2i::mysql_fetch_field($result, $field_offset);

}

function mysql_fetch_lengths($result)
{

  return mysql2i::mysql_fetch_lengths($result);

}

function mysql_fetch_object($result, $class_name = NULL, $params = NULL)
{

  return mysql2i::mysql_fetch_object($result, $class_name, $params);

}

function mysql_fetch_row($result)
{

  return mysql2i::mysql_fetch_row($result);

}

function mysql_field_flags($result, $field_offset)
{

  return mysql2i::mysql_field_flags($result, $field_offset);

}

function mysql_field_len($result, $field_offset)
{

  return mysql2i::mysql_field_len($result, $field_offset);

}

function mysql_field_name($result, $field_offset)
{

  return mysql2i::mysql_field_name($result, $field_offset);

}

function mysql_field_seek($result, $fieldnr)
{

  return mysql2i::mysql_field_seek($result, $fieldnr);

}

function mysql_field_table($result, $field_offset)
{

  return mysql2i::mysql_field_table($result, $field_offset);

}

function mysql_field_type($result, $field_offset)
{

  return mysql2i::mysql_field_type($result, $field_offset);

}

function mysql_free_result($result)
{

  return mysql2i::mysql_free_result($result);

}

function mysql_get_client_info()
{

  return mysql2i::mysql_get_client_info();
}

function mysql_get_host_info($link = NULL)
{

  return mysql2i::mysql_get_host_info($link);

}

function mysql_get_proto_info($link = NULL)
{

  return mysql2i::mysql_get_proto_info($link);

}

function mysql_get_server_info($link = NULL)
{

  return mysql2i::mysql_get_server_info($link);

}

function mysql_info($link = NULL)
{

  return mysql2i::mysql_info($link);

}

function mysql_insert_id($link = NULL)
{

  return mysql2i::mysql_insert_id($link);

}

function mysql_list_dbs($link = NULL)
{

  return mysql2i::mysql_list_dbs();

}

function mysql_list_fields($database_name, $table_name, $link = NULL)
{

  return mysql2i::mysql_list_fields($database_name, $table_name, $link);

}

function mysql_list_processes($link = NULL)
{

  return mysql2i::mysql_list_processes($link);

}

function mysql_list_tables($database, $link)
{

  return mysql2i::mysql_list_tables($database, $link);

}

function mysql_num_fields($result)
{

  return mysql2i::mysql_num_fields($result);

}

function mysql_num_rows($result)
{

  return mysql2i::mysql_num_rows($result);

}

function mysql_pconnect($host = '', $username = '', $passwd = '', $new_link = FALSE, $client_flags = 0)
{

  return mysql2i::mysql_pconnect($host, $username, $passwd, $new_link, $client_flags);

}

function mysql_ping($link = NULL)
{

  return mysql2i::mysql_ping($link);

}

function mysql_query($query, $link = NULL)
{

  return mysql2i::mysql_query($query, $link);

}

function mysql_real_escape_string($escapestr, $link = NULL)
{

  return mysql2i::mysql_real_escape_string($escapestr, $link);

}

function mysql_result($result, $row, $field = 0)
{

  return mysql2i::mysql_result($result, $row, $field);

}

function mysql_select_db($dbname, $link = NULL)
{

  return mysql2i::mysql_select_db($dbname, $link);

}

function mysql_set_charset($charset, $link = NULL)
{

  return mysql2i::mysql_set_charset($charset, $link);

}

function mysql_stat($link = NULL)
{

  return mysql2i::mysql_stat($link);

}

function mysql_tablename($result, $row, $field = NULL)
{

  return mysql2i::mysql_tablename($result, $row, $field);

}

function mysql_thread_id($link = NULL)
{

  return mysql2i::mysql_thread_id($link);

}

function mysql_unbuffered_query($query, $link = NULL)
{

  return mysql2i::mysql_unbuffered_query($query, $link);

}

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
