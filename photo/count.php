<?php
$k_p = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo`"), 0);
$k_n = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `time` > '" . mktime(0, 0, 0) . "'"), 0);
if ($k_n == 0) {
	$k_n = NULL;
} else {
	$k_n = '+' . $k_n;
}
echo '(' . $k_p . ') <font color="red">' . $k_n . '</font>';
