<?php
$my_dir = dbassoc(dbquery("SELECT * FROM `downnik_dir` WHERE `my` = '1' LIMIT 1"));
$k_p=dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_dir` != '$my_dir[id]'",$db), 0);
$k_n= dbresult(dbquery("SELECT COUNT(*) FROM `downnik_files` WHERE `id_dir` != '$my_dir[id]' AND `time_go` > '".$ftime."'",$db), 0);
if ($k_n==0)$k_n=NULL;
else $k_n='+'.$k_n;
echo "($k_p) <font color='red'>$k_n</font>";