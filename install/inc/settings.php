<?php
if (file_exists(H.'sys/dat/settings.php')) {
	echo '必须删除<b>sys/dat/settings.php</b>文件才能继续安装';
	exit;
}
if (!($set=@parse_ini_file(H.'sys/dat/default.ini',false))) {
	echo '找不到必须的前置配置文件（sys/dat/default.ini）';
	exit;
}
$tmp_set=$set;