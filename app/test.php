<?php
	//Starting camera interfacing
	$cmd = escapeshellcmd('C:/xampp/htdocs/park/system/system.py');
	$ok = shell_exec("python $cmd");
	var_dump($ok);
?>