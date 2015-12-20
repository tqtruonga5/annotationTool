<?php
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		if( !array_key_exists("folder", $request)
			|| !array_key_exists("file", $request)
			|| !array_key_exists("mentions", $request)
			|| !array_key_exists("html", $request)
			|| !array_key_exists("relations", $request)){
			echo "Error";
			return;
		}
		$foldername = $request->folder;
		$filename = $request->file;
		$mentions = $request->mentions;
		$html = $request->html;
		$relations = $request->relations;

		$file = fopen("mention/".$foldername."/".$filename, "w+");
		for($i=0; $i<count($mentions); $i++){
			fwrite($file, $mentions[$i]."\n");
		}
		fclose($file);

		$file = fopen("html/".$foldername."/".$filename, "w+");
		for($i=0; $i<count($html); $i++){
			fwrite($file, $html[$i]."\n");
		}
		fclose($file);

		$file = fopen("relation/".$foldername."/".$filename, "w+");
		for($i=0; $i<count($relations); $i++){
			fwrite($file, $relations[$i]."\n");
		}
		fclose($file);

		Echo "Success";
	}
?>