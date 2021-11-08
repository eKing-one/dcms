<?php

if (file_exists(H.'sys/dat/settings_6.2.dat'))
{
echo '必须删除文件才能继续安装 <b>sys/dat/settings_6.2.dat</b>';
exit;
}


if (!($set=@parse_ini_file(H.'sys/dat/default.ini',false)))
{
echo '找不到配置文件';
exit;
}

$tmp_set=$set;
?>