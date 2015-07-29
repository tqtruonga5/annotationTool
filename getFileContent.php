<?php
	if(!isset($_GET['file']) || empty($_GET['file'])){
		echo "Error";
		return;
	}
	$file = $_GET["file"];

	$doc = "doc/";
	$mention = "mention/";
	$html = "html/";
	$relation = "relation/";

	if(file_exists($html.$file))
	{
		$lines = file($html.$file, FILE_IGNORE_NEW_LINES);

		$mentions = array();
		if(file_exists($mention.$file))
		{
			$mentions = file($mention.$file, FILE_IGNORE_NEW_LINES);
		}

		$relations = array();
		if(file_exists($relation.$file))
		{
			$relations = file($relation.$file, FILE_IGNORE_NEW_LINES);
		}

		$res["content"] = $lines;
		$res["mention"] = $mentions;
		$res["relation"] = $relations;

		echo json_encode($res);
	}
	else if(file_exists($doc.$file))
	{
		$lines = file($doc.$file, FILE_IGNORE_NEW_LINES);

		$mentions = array();
		if(file_exists($mention.$file)){
			$mentions = file($mention.$file, FILE_IGNORE_NEW_LINES);
		}

		$relations = array();
		if(file_exists($relation.$file)){
			$relations = file($relation.$file, FILE_IGNORE_NEW_LINES);
		}
		$res["content"] = $lines;
		$res["mention"] = $mentions;
		$res["relation"] = $relations;

		echo json_encode($res);
	} else {
		echo "error";
	}
?>