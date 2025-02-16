<?
if (!function_exists('copyright')) {
	function copyright($fiera) {
		return preg_replace("#(|\r)*</body>#i", "<div style='font-size: xx-small;text-align:center;'>&copy; <a  target='_blank' style='font-size:xx-small;' title='Dcms引擎的修改' href='http://dcms-social.ru'>DCMS-Social</a> </div></body>", $fiera);
	}
	ob_start ("copyright");
}