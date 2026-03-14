<?php
function adm_check() {
	global $time, $set;
	if (!isset($_SESSION['adm_auth']) || $_SESSION['adm_auth'] < $time) {
		header('Location: ' . $set['siteurl'] . '/adm_panel/?go=' . base64_encode($_SERVER['REQUEST_URI']) . '&' . passgen() . '&' . session_id()); exit;
	}
	if (isset($_SESSION['adm_auth']) && $_SESSION['adm_auth'] > $time) {
		$_SESSION['adm_auth'] = $time + setget("timeadmin", 1000);
	}
}
