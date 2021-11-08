<?php
// авторизация на сервере базы
if(!($db = @mysql_connect($set['mysql_host'], $set['mysql_user'], $set['mysql_pass'])))
{
	//echo $set['mysql_host'], $set['mysql_user'],$set['mysql_pass'];
	echo "Нет соединения с сервером базы<br />*проверьте параметры подключения";
	exit;
}

// подключение к базе
if (!@mysql_select_db($set['mysql_db_name'],$db))
{
	echo 'База даных не найдена<br />*проверьте, существует ли данная база';
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
 

query('set charset utf8',$db);
query('SET names utf8',$db);
query('set character_set_client="utf8"',$db);
query('set character_set_connection="utf8"',$db);
query('set character_set_result="utf8"',$db);


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
