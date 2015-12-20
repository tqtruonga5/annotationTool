<?php
	$directory = "txt";
	$folders = scandir($directory);
	$result = '';
	$i = 0;
	foreach ($folders as $folder){
		if ($folder === '.' || $folder === '..')
			continue;
		$result[$i++] = $folder;
	}
	echo json_encode($result);
?>