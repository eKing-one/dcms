<?php
function down_path($path) {	
	$path = preg_replace("#(/){1,}#","/",$path);	
	$path = preg_replace("#(^(/){1,})|((/){1,}$)#","",$path);	
	$path_arr = explode('/',$path);	
	$rdir = '';	
	$rudir = '';			

	for ($i = 0; $i < count($path_arr); $i++) {			
		$of = '/';			
		for ($z = 0; $z <= $i; $z++) $of .= $path_arr[$z] . '/';
		$rdir .= $path_arr[$i] . '/';			
		$dir_id = dbassoc(dbquery("SELECT * FROM `downnik_dir` WHERE `dir` = '/$rdir' OR `dir` = '$rdir/' OR `dir` = '$rdir' LIMIT 1"));
		$dirname = $dir_id['name'];			
		$rudir .= "<a href=\"/down/".url(preg_replace("#(^(/){1,})|((/){1,}$)#","",$rdir))."/?page=$_SESSION[page]\">".$dirname.'</a> &gt; ';		
	}	
	return preg_replace("# &gt; $#", "", $rudir);
}