<?php
// 数据库服务器上的授权
$db = @mysqli_connect($set['mysql_host'], $set['mysql_user'], $set['mysql_pass'],$set['mysql_db_name']);
if (mysqli_connect_errno($db)) 
{ 
  exit("连接 MySQL 失败: " . mysqli_connect_error()); 
}
$query_number = 0;
$tpassed = 0;
function dbresult($result, $row, $field = 0)
{
  $numrows = mysqli_num_rows($result);
  if ($numrows && $row <= ($numrows - 1) && $row >= 0) {
    mysqli_data_seek($result, $row);
    $resrow = (is_numeric($field)) ? mysqli_fetch_row($result) : mysqli_fetch_assoc($result);
    if (isset($resrow[$field])) {
      return $resrow[$field];
    }
  }
}
function dbquery($query)
{
  global $db;
  return mysqli_query($db, $query);
}
function dbrows($result)
{
  global $db;
  return mysqli_num_rows($result);
}
function dbarray($result)
{
  global $db;
  return mysqli_fetch_array($result);
}
function dbassoc($result)
{
  global $db;
  return mysqli_fetch_assoc($result);
}
function dbinsertid(){
  global $db;
  return mysqli_insert_id($db);
}
dbquery('set charset utf8mb4',$db);
dbquery('SET names utf8mb4',$db);
dbquery('set character_set_client="utf8mb4"',$db);
dbquery('set character_set_connection="utf8mb4"',$db);

function db_optimize()
{
	$tab = dbquery('SHOW TABLES');
	while ($tables = dbarray($tab))
	{
		dbquery("OPTIMIZE TABLE `$tables[0]`");
	}
}
