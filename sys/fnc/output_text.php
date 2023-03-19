<?
// 该函数在输出到浏览器之前处理文本字符串
// 强烈不建议在这里修改任何东西
function output_text($str, $br = 1, $html = 1, $smiles = 1, $links = 1, $bbcode = 1)
{
	global $theme_ini;
	//if ($br && isset($theme_ini['text_width']))$str=wordwrap($str, $theme_ini['text_width'], ' ',1);
	if ($html) $str = htmlentities($str, ENT_QUOTES, 'UTF-8'); // преобразуем все к нормальному перевариванию браузером
	if ($links) $str = links($str); // обработка ссылок
	if ($smiles)
		$str = smiles($str); // вставка смайлов
	if ($bbcode) {
		$tmp_str = $str;
		$str = bbcode($str); // обработка bbcode
	}
	if ($br) {
		$str = br($str); // переносы строк
	}
	return stripslashes($str); // возвращаем обработанную строку
}
// для форм
function input_value_text($str)
{
	return output_text($str, 0, 1, 0, 0, 0);
}
function rez_text($text, $maxwords = 15, $maxchar = 100)
{
	$sep = ' ';
	$sep2 = ' &raquo;';
	$words = explode($sep, $text);
	$char = iconv_strlen($text, 'utf-8');
	if (count($words) > $maxwords) {
		$text = join($sep, array_slice($words, 0, $maxwords));
	}
	if ($char > $maxchar) {
		$text = iconv_substr($text, 0, $maxchar, 'utf-8');
	}
	return output_text($text) . $sep2;
}
function rez_text2($text, $maxwords = 70, $maxchar = 700)
{
	$sep = ' ';
	$sep2 = '';
	$words = explode($sep, $text);
	$char = iconv_strlen($text, 'utf-8');
	if (count($words) > $maxwords) {
		$text = join($sep, array_slice($words, 0, $maxwords));
	}
	if ($char > $maxchar) {
		$text = iconv_substr($text, 0, $maxchar, 'utf-8');
	}
	return output_text($text) . $sep2;
}
function rez_text3($text, $maxwords = 150, $maxchar = 1500)
{
	$sep = ' ';
	$sep2 = '';
	$words = explode($sep, $text);
	$char = iconv_strlen($text, 'utf-8');
	if (count($words) > $maxwords) {
		$text = join($sep, array_slice($words, 0, $maxwords));
	}
	if ($char > $maxchar) {
		$text = iconv_substr($text, 0, $maxchar, 'utf-8');
	}
	return output_text($text) . $sep2;
}
