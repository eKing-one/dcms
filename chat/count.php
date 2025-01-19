<?php

if (isset($user))dbquery("DELETE FROM `chat_who` WHERE `id_user` = '$user[id]'");
dbquery("DELETE FROM `chat_who` WHERE `time` < '".($time-120)."'");
echo '('.dbresult(dbquery("SELECT COUNT(*) FROM `chat_who`"),0).' 人)';