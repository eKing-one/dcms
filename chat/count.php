<?php
// 用于菜单栏的在线用户计数
if (isset($user) && empty($in_chat_room)) dbquery("DELETE FROM `chat_who` WHERE `id_user` = '$user[id]'");
dbquery("DELETE FROM `chat_who` WHERE `time` < '" . ($time - 120) . "'");
echo '(' . dbresult(dbquery("SELECT COUNT(*) FROM `chat_who`"), 0) . ' 人)';