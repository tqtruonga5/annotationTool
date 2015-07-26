<?php
set_time_limit(0);

function showData($data){
    echo "<br><pre>";
    print_r($data);
    echo "</pre><br>";
}

function writeToFile($fileName,$data){
    $file = fopen($fileName, "w+");
    fwrite($file, $data);
    fclose($file);
}

function getJsonContent($userName,$recordId,$count){
    $url = "http://54.165.246.86/recordvn/getRecordContent?username=$userName&recordId=$recordId";

    echo $url;
    $json = file_get_contents($url);
    writeToFile("./json_data/$userName/$count.json",$json);
}

function getContent($userName,$recordId){
    $url = "http://54.165.246.86/recordvn/getRecordContent?username=$userName&recordId=$recordId";
    $json = file_get_contents($url); // this WILL do an http request for you
    // showData($json);
    // writeToFile("./json_data/1.json",$json);
    $data = json_decode($json,true);
    // showData($data);
    return $data;
}

// getContent('diepdt','140506081955047000');

function readJsonFile($path){
    return json_decode(file_get_contents($path),true);
}

//get all records associate with user
function getListRecords($userName){
    $result = array();
    $url = "http://54.165.246.86/recordvn/getListRecord?username=$userName";
    // echo $url."<hr/>";
    $json = file_get_contents($url);
    $tmp = json_decode($json,true);
    foreach ($tmp as $key => $value) {
        $item = array();
        $user = $userName;
        $formattedId = number_format($value['id'],0,'','');
        // $recordId = substr_replace($formattedId,'0',strlen($formattedId)-1,strlen($formattedId));

        $item['user'] = $user;
        $item['recordId'] = $formattedId;
        array_push($result,$item);
    }
    return $result;
}

$users = array("diepdt");

showData($users);
foreach ($users as $user) {
    $count = 1;
    $records = getListRecords($user);
    foreach ($records as $record) {
        getJsonContent($record['user'],$record['recordId'],$count++);
    }
}
// showData(readJsonFile("./json_data/1.json"));
?>