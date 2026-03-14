<?php
$k_n = $db->queryColumn('SELECT COUNT(*) AS message_count FROM adm_chat WHERE time >= UNIX_TIMESTAMP(NOW() - INTERVAL 1 DAY);');
if ($k_n > 0) {
	echo " <font color='red'>+$k_n</font> ";
}
