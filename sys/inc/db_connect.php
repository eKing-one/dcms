<?php
// 数据库服务器上的授权
if(!($db = @mysql_connect($set['mysql_host'], $set['mysql_user'], $set['mysql_pass'])))
{
	//echo $set['mysql_host'], $set['mysql_user'],$set['mysql_pass'];
	echo "没有连接到数据库服务器<br />*检查连接设置";
	exit;
}
// 基座连接
if (!@mysql_select_db($set['mysql_db_name'],$db))
{
	echo '找不到数据库<br />*检查此数据库是否存在';
	exit;
}
$query_number = 0;
$tpassed = 0;
function dbresult($result, $row, $field = 0)
{
 return mysql_result($result, $row, $field);
}
function dbquery($query)
{
  return query($query);
}
function dbrows($result)
{
  return mysql_num_rows($result);
}
function dbarray($result)
{
  return mysql_fetch_array($result);
}
function dbassoc($result)
{
  return mysql_fetch_assoc($result);
}

// Псевдоним dbquery
function query($query) 
{
    global $query_number;
    global $tpassed;
    $query_number++;
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tstart = $mtime;
    $query = mysql_query($query);
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = $mtime[1] + $mtime[0];
    $tend = $mtime;
    $tpassed += ($tend - $tstart);
    return $query;
}
query('set charset utf8mb4',$db);
query('SET names utf8mb4',$db);
query('set character_set_client="utf8mb4"',$db);
query('set character_set_connection="utf8mb4"',$db);
//query('set character_set_result="utf8mb4"',$db);
// оптимизация всех таблиц
function db_optimize()
{
	time_limit(20);// Ставим ограничение на 20 секунд
	$tab = query('SHOW TABLES');
	while ($tables = dbarray($tab))
	{
		query("OPTIMIZE TABLE `$tables[0]`");
	}
}
