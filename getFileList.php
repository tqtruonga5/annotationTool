<?php
	if (isset($_GET['folder'])){
		$folder = $_GET['folder'];
		$directory = "doc\\".$folder."\*.txt";
		$files = glob($directory,GLOB_NOSORT);

		for($i=0; $i<count($files); $i++){
			$files[$i] = str_replace("doc\\".$folder."\\", "", $files[$i]);
		}
		sort($files, SORT_NUMERIC);

		echo json_encode($files);
	}
	
?>