<?
if (user_access('down_komm_del') && isset($_GET['del_post']) && dbresult(dbquery("SELECT COUNT(*) FROM `downnik_komm` WHERE `id` = '".intval($_GET['del_post'])."' AND `id_file` = '$file_id[id]'"),0))
{
dbquery("DELETE FROM `downnik_komm` WHERE `id` = '".intval($_GET['del_post'])."' LIMIT 1");
msg ('评论成功删除');
}
?>