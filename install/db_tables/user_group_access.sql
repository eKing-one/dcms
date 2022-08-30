CREATE TABLE IF NOT EXISTS `user_group_access` (
  `id_group` int(10) unsigned NOT NULL,
  `id_access` varchar(32) NOT NULL,
  KEY `id_group` (`id_group`,`id_access`)
) ENGINE=MyISAM DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
INSERT INTO `user_group_access` (`id_group`, `id_access`) VALUES
(2, 'adm_info'),
(2, 'adm_panel_show'),
(2, 'adm_ref'),
(2, 'adm_set_chat'),
(2, 'adm_show_adm'),
(2, 'adm_statistic'),
(2, 'chat_clear'),
(2, 'chat_room'),
(2, 'user_ban_set'),
(2, 'user_ban_set_h'),
(2, 'user_ban_unset'),
(2, 'user_prof_edit'),
(2, 'user_show_add_info'),
(2, 'user_show_ua'),
(3, 'adm_banlist'),
(3, 'adm_panel_show'),
(3, 'adm_set_forum'),
(3, 'adm_show_adm'),
(3, 'adm_statistic'),
(3, 'forum_for_edit'),
(3, 'forum_post_ed'),
(3, 'forum_razd_create'),
(3, 'forum_razd_edit'),
(3, 'forum_them_del'),
(3, 'forum_them_edit'),
(3, 'user_ban_set_h'),
(3, 'user_prof_edit'),
(3, 'user_show_add_info'),
(3, 'user_show_ip'),
(3, 'user_show_ua'),
(4, 'adm_info'),
(4, 'adm_panel_show'),
(4, 'adm_set_loads'),
(4, 'adm_show_adm'),
(4, 'adm_statistic'),
(4, 'loads_dir_create'),
(4, 'loads_dir_delete'),
(4, 'loads_dir_mesto'),
(4, 'loads_dir_rename'),
(4, 'loads_file_delete'),
(4, 'loads_file_edit'),
(4, 'loads_file_upload'),
(4, 'loads_unzip'),
(5, 'adm_info'),
(5, 'adm_lib_repair'),
(5, 'adm_panel_show'),
(5, 'adm_ref'),
(5, 'adm_set_foto'),
(5, 'adm_statistic'),
(5, 'lib_dir_create'),
(5, 'lib_dir_delete'),
(5, 'lib_dir_edit'),
(5, 'lib_dir_mesto'),
(5, 'lib_stat_create'),
(5, 'lib_stat_delete'),
(5, 'lib_stat_txt'),
(5, 'lib_stat_zip'),
(5, 'user_ban_set_h'),
(5, 'user_prof_edit'),
(6, 'adm_banlist'),
(6, 'adm_info'),
(6, 'adm_panel_show'),
(6, 'adm_set_foto'),
(6, 'adm_show_adm'),
(6, 'adm_statistic'),
(6, 'foto_alb_del'),
(6, 'foto_foto_edit'),
(6, 'foto_komm_del'),
(6, 'user_ban_set_h'),
(6, 'user_show_ua'),
(7, 'adm_banlist'),
(7, 'adm_lib_repair'),
(7, 'adm_panel_show'),
(7, 'adm_set_chat'),
(7, 'adm_set_forum'),
(7, 'adm_set_foto'),
(7, 'adm_statistic'),
(7, 'chat_clear'),
(7, 'chat_room'),
(7, 'forum_post_close'),
(7, 'forum_post_ed'),
(7, 'forum_razd_create'),
(7, 'forum_razd_edit'),
(7, 'forum_them_del'),
(7, 'forum_them_edit'),
(7, 'foto_foto_edit'),
(7, 'foto_komm_del'),
(7, 'guest_clear'),
(7, 'guest_delete'),
(7, 'guest_show_ip'),
(7, 'lib_stat_create'),
(7, 'lib_stat_txt'),
(7, 'loads_file_delete'),
(7, 'loads_file_edit'),
(7, 'loads_file_upload'),
(7, 'notes_delete'),
(7, 'notes_edit'),
(7, 'down_dir_create'),
(7, 'down_dir_delete'),
(7, 'down_dir_edit'),
(7, 'down_file_delete'),
(7, 'down_file_edit'),
(7, 'down_komm_del'),
(7, 'user_ban_set'),
(7, 'user_ban_set_h'),
(7, 'user_ban_unset'),
(7, 'user_collisions'),
(7, 'user_prof_edit'),
(7, 'user_show_add_info'),
(7, 'user_show_ua'),
(8, 'adm_banlist'),
(8, 'adm_ban_ip'),
(8, 'adm_forum_sinc'),
(8, 'adm_info'),
(8, 'adm_lib_repair'),
(8, 'adm_news'),
(8, 'adm_panel_show'),
(8, 'adm_ref'),
(8, 'adm_set_chat'),
(8, 'adm_set_forum'),
(8, 'adm_set_foto'),
(8, 'adm_set_loads'),
(8, 'adm_show_adm'),
(8, 'adm_statistic'),
(8, 'chat_clear'),
(8, 'chat_room'),
(8, 'forum_for_create'),
(8, 'forum_for_delete'),
(8, 'forum_for_edit'),
(8, 'forum_post_ed'),
(8, 'forum_razd_create'),
(8, 'forum_razd_edit'),
(8, 'forum_them_del'),
(8, 'forum_them_edit'),
(8, 'foto_alb_del'),
(8, 'foto_foto_edit'),
(8, 'foto_komm_del'),
(8, 'guest_clear'),
(8, 'guest_delete'),
(8, 'guest_show_ip'),
(8, 'lib_dir_create'),
(8, 'lib_dir_delete'),
(8, 'lib_dir_edit'),
(8, 'lib_dir_mesto'),
(8, 'lib_stat_create'),
(8, 'lib_stat_delete'),
(8, 'lib_stat_txt'),
(8, 'lib_stat_zip'),
(8, 'loads_dir_create'),
(8, 'loads_dir_delete'),
(8, 'loads_dir_mesto'),
(8, 'loads_dir_rename'),
(8, 'loads_file_delete'),
(8, 'loads_file_edit'),
(8, 'loads_file_upload'),
(8, 'loads_unzip'),
(8, 'notes_delete'),
(8, 'notes_edit'),
(8, 'down_dir_create'),
(8, 'down_dir_delete'),
(8, 'down_dir_edit'),
(8, 'down_file_delete'),
(8, 'down_file_edit'),
(8, 'down_komm_del'),
(8, 'user_ban_set'),
(8, 'user_ban_set_h'),
(8, 'user_ban_unset'),
(8, 'user_change_group'),
(8, 'user_change_nick'),
(8, 'user_collisions'),
(8, 'user_delete'),
(8, 'user_prof_edit'),
(8, 'user_show_add_info'),
(8, 'user_show_ip'),
(8, 'user_show_ua'),
(8, 'votes_create'),
(8, 'votes_settings'),
(9, 'adm_banlist'),
(9, 'adm_ban_ip'),
(9, 'adm_forum_sinc'),
(9, 'adm_info'),
(9, 'adm_ip_edit'),
(9, 'adm_lib_repair'),
(9, 'adm_log_read'),
(9, 'adm_menu'),
(9, 'adm_news'),
(9, 'adm_panel_show'),
(9, 'adm_ref'),
(9, 'adm_rekl'),
(9, 'adm_set_chat'),
(9, 'adm_set_forum'),
(9, 'adm_set_foto'),
(9, 'adm_set_loads'),
(9, 'adm_set_sys'),
(9, 'adm_set_user'),
(9, 'adm_show_adm'),
(9, 'adm_statistic'),
(9, 'adm_themes'),
(9, 'chat_clear'),
(9, 'chat_room'),
(9, 'forum_for_create'),
(9, 'forum_for_delete'),
(9, 'forum_for_edit'),
(9, 'forum_post_close'),
(9, 'forum_post_ed'),
(9, 'forum_razd_create'),
(9, 'forum_razd_edit'),
(9, 'forum_them_del'),
(9, 'forum_them_edit'),
(9, 'foto_alb_del'),
(9, 'foto_foto_edit'),
(9, 'foto_komm_del'),
(9, 'guest_clear'),
(9, 'guest_delete'),
(9, 'guest_show_ip'),
(9, 'lib_dir_create'),
(9, 'lib_dir_delete'),
(9, 'lib_dir_edit'),
(9, 'lib_dir_mesto'),
(9, 'lib_stat_create'),
(9, 'lib_stat_delete'),
(9, 'lib_stat_txt'),
(9, 'lib_stat_zip'),
(9, 'loads_dir_create'),
(9, 'loads_dir_delete'),
(9, 'loads_dir_mesto'),
(9, 'loads_dir_rename'),
(9, 'loads_file_delete'),
(9, 'loads_file_edit'),
(9, 'loads_file_import'),
(9, 'loads_file_upload'),
(9, 'loads_unzip'),
(9, 'notes_delete'),
(9, 'notes_edit'),
(9, 'down_dir_create'),
(9, 'down_dir_delete'),
(9, 'down_dir_edit'),
(9, 'down_file_delete'),
(9, 'down_file_edit'),
(9, 'down_komm_del'),
(9, 'user_ban_set'),
(9, 'user_ban_set_h'),
(9, 'user_ban_unset'),
(9, 'user_change_group'),
(9, 'user_change_nick'),
(9, 'user_collisions'),
(9, 'user_delete'),
(9, 'user_mass_delete'),
(9, 'user_prof_edit'),
(9, 'user_show_add_info'),
(9, 'user_show_ip'),
(9, 'user_show_ua'),
(9, 'votes_create'),
(9, 'votes_settings'),
(11, 'adm_banlist'),
(11, 'adm_info'),
(11, 'adm_panel_show'),
(11, 'adm_show_adm'),
(11, 'adm_statistic'),
(11, 'forum_post_close'),
(11, 'notes_delete'),
(11, 'notes_edit'),
(11, 'user_ban_set'),
(11, 'user_ban_set_h'),
(12, 'adm_banlist'),
(12, 'adm_info'),
(12, 'adm_panel_show'),
(12, 'adm_show_adm'),
(12, 'adm_statistic'),
(12, 'guest_clear'),
(12, 'guest_delete'),
(12, 'user_ban_set'),
(12, 'user_ban_set_h'),
(15, 'adm_accesses'),
(15, 'adm_banlist'),
(15, 'adm_ban_ip'),
(15, 'adm_forum_sinc'),
(15, 'adm_info'),
(15, 'adm_ip_edit'),
(15, 'adm_lib_repair'),
(15, 'adm_log_delete'),
(15, 'adm_log_read'),
(15, 'adm_menu'),
(15, 'adm_mysql'),
(15, 'adm_news'),
(15, 'adm_panel_show'),
(15, 'adm_ref'),
(15, 'adm_rekl'),
(15, 'adm_set_chat'),
(15, 'adm_set_forum'),
(15, 'adm_set_foto'),
(15, 'adm_set_loads'),
(15, 'adm_set_sys'),
(15, 'adm_set_user'),
(15, 'adm_show_adm'),
(15, 'adm_statistic'),
(15, 'adm_themes'),
(15, 'chat_clear'),
(15, 'chat_room'),
(15, 'forum_for_create'),
(15, 'forum_for_delete'),
(15, 'forum_for_edit'),
(15, 'forum_post_close'),
(15, 'forum_post_ed'),
(15, 'forum_razd_create'),
(15, 'forum_razd_edit'),
(15, 'forum_them_del'),
(15, 'forum_them_edit'),
(15, 'foto_alb_del'),
(15, 'foto_foto_edit'),
(15, 'foto_komm_del'),
(15, 'guest_clear'),
(15, 'guest_delete'),
(15, 'guest_show_ip'),
(15, 'lib_dir_create'),
(15, 'lib_dir_delete'),
(15, 'lib_dir_edit'),
(15, 'lib_dir_mesto'),
(15, 'lib_stat_create'),
(15, 'lib_stat_delete'),
(15, 'lib_stat_txt'),
(15, 'lib_stat_zip'),
(15, 'loads_dir_create'),
(15, 'loads_dir_delete'),
(15, 'loads_dir_mesto'),
(15, 'loads_dir_rename'),
(15, 'loads_file_delete'),
(15, 'loads_file_edit'),
(15, 'loads_file_import'),
(15, 'loads_file_upload'),
(15, 'loads_unzip'),
(15, 'notes_delete'),
(15, 'notes_edit'),
(15, 'down_dir_create'),
(15, 'down_dir_delete'),
(15, 'down_dir_edit'),
(15, 'down_file_delete'),
(15, 'down_file_edit'),
(15, 'down_komm_del'),
(15, 'user_ban_set'),
(15, 'user_ban_set_h'),
(15, 'user_ban_unset'),
(15, 'user_change_group'),
(15, 'user_change_nick'),
(15, 'user_collisions'),
(15, 'user_delete'),
(15, 'user_mass_delete'),
(15, 'user_prof_edit'),
(15, 'user_show_add_info'),
(15, 'user_show_ip'),
(15, 'user_show_ua'),
(15, 'votes_create'),
(15, 'votes_settings');
