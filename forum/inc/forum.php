<?
err();
aut();
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id_forum` = '$forum[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
echo "<table class='post'>";
$q = dbquery("SELECT * FROM `forum_r` WHERE `id_forum` = '$forum[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
if (dbrows($q) == 0) {
	echo "  <div class='mess'>";
	echo "没有任何部分";
	echo "  </div>";
}
while ($razdel = dbassoc($q)) {
	/*-----------代码-----------*/
	if ($num == 0) {
		echo "  <div class='nav1'>";
		$num = 1;
	} elseif ($num == 1) {
		echo "  <div class='nav2'>";
		$num = 0;
	}
	/*---------------------------*/
	echo "<a href='/forum/$forum[id]/$razdel[id]/'>" . text($razdel['name']) . "</a> [" . dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'"), 0) . '/' . dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'"), 0) . "]";
	if (!empty($razdel['opis'])) {
		echo '<br/><span style="color:#666;">' . output_text($razdel['opis']) . '</span>';
	}
	echo "   </div>";
}
echo "</table>";
if ($k_page > 1) str("/forum/$forum[id]/?", $k_page, $page); // 输出页数
