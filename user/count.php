<?php
$koll = dbresult(dbquery("SELECT COUNT(*) FROM `user`"), 0);
$k_new = dbresult(dbquery("SELECT COUNT(*) FROM `user` where `date_reg` > '" . mktime(0, 0, 0) . "'"), 0);
if ($k_new > 0) {
	$k_new = '<font color="red">+' . $k_new . '</font>';
} else {
	$k_new = null;
}
echo '(' . $koll . ') ' . $k_new;
