<?php
set_time_limit(0);
$totalPIP = 0;
$totalTrWP = 0;

function showData($data){
    echo "<br><pre>";
    print_r($data);
    echo "</pre><br>";
}

function getListRecords($userName){
    $result = array();
    $url = "http://54.165.246.86/recordvn/getListRecord?username=$userName";
    echo $url;
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


function getContent($userName,$recordId){
    $url = "http://54.165.246.86/recordvn/getRecordContent?username=$userName&recordId=$recordId";
    $json = file_get_contents($url); // this WILL do an http request for you
    $data = json_decode($json,true);
    // showData($data);
    return $data;
}

function changeSenSectionName($section) {
    $sectionStr = $section;
    switch($section) {
        case 'LYDO':
        $sectionStr = 'Lý Do';
        break;
        case 'HB_BENHLY':
        $sectionStr = 'Bệnh Lý';
        break;
        case 'HB_BANTHAN':
        $sectionStr = 'Tiền Sử Bệnh';
        break;
        case 'HB_GIADINH':
        $sectionStr = 'Tiền Sử Gia Đình';
        break;
        case 'KB_TOANTHAN':
        $sectionStr = 'Khám Toàn Thân';
        break;
        case 'KB_TUANHOAN':
        $sectionStr = 'Khám Tuần Hoàn';
        break;
        case 'KB_HOHAP':
        $sectionStr = 'Khám Hệ Hô Hấp';
        break;
        case 'KB_TIEUHOA':
        $sectionStr = 'Khám Hệ Tiêu Hóa';
        break;
        case 'KB_THAN':
        $sectionStr = 'Khám Thận';
        break;
        case 'KB_THANKINH':
        $sectionStr = 'Khám Thần Kinh';
        break;
        case 'KB_CO':
        $sectionStr = 'Khám Hệ Cơ';
        break;
        case 'KB_NOITIET':
        $sectionStr = 'Khám Nội Tiết';
        break;
        case 'TIENLUONG':
        $sectionStr = 'Tiên Lượng';
        break;
        case 'DIEUTRI':
        $sectionStr = 'Điều Trị';
        break;
        case 'KB_KHAC':
        $sectionStr = 'Khám Bệnh Khác';
        break;
        case 'KB_TOMTAT':
        $sectionStr = 'Tóm Tắt Khám Bệnh';
        break;
        case 'KB_TMH':
        $sectionStr = 'Tai Mũi Họng';
        break;
        case 'KB_RHM':
        $sectionStr = 'Răng Hàm Mặt';
        break;
        case 'KB_MAT':
        $sectionStr = 'Mắt';
        break;
        case 'TK_BENHLY':
        $sectionStr = 'Tổng Kết Bệnh Lý';
        break;
        case 'TK_TOMTAT':
        $sectionStr = 'Tóm Tắt';
        break;
        case 'TK_PHUONGPHAP':
        $sectionStr = 'Tổng Kết Phương Pháp Điều Trị';
        break;
        case 'TK_TINHTRANG':
        $sectionStr = 'Tình Trạng Ra Viện';
        break;
        case 'TK_DIEUTRI':
        $sectionStr = 'Điều Trị Sau Khi Ra Viện';
        break;
        case 'PHANBIET':
        $sectionStr = 'Bệnh Dễ Lây Nhiễm - Nguy Hiểm';
        break;
        case 'KB_NGOAI':
        $sectionStr = 'Khám Bệnh Ngoại Khoa';
        break;
    }
    return $sectionStr;
}
function changeType($shortType){
    switch($shortType){
        case 'PR':
        return 'problem';
        case 'TR':
        return 'treatment';
        case 'TE':
        return 'test';

    }

}

function autoDeterRelation($section, $concepts, $line, $order, $folder){
	$length = sizeof($concepts);
	$textConceptRelation = '';
	
	if ($length <= 1)
		return;
	switch ($section){
		case 'LYDO':
		case 'HB_BANTHAN':
			for ($i=0; $i < $length-1; $i++){
				for ($j=$i+1; $j < $length; $j++){
					if ($concepts[$i]['type'] == 'PR' && $concepts[$j]['type'] == 'PR' && $concepts[$i]['content'] != $concepts[$j]['content']){
						$textConceptRelation .= 'c="'.$concepts[$i]['content'].'" '.$line.':'.$concepts[$i]['fromWord'].
							' '.$line.':'.$concepts[$i]['toWord'].'||r="PIP"||c="'.$concepts[$j]['content'].'" '.$line.':'.
							$concepts[$j]['fromWord'].' '.$line.':'.$concepts[$j]['toWord']."\n";
						$GLOBALS['totalPIP']++;
					}
				}
			}
			break;
		case 'HB_BENHLY':
		case 'TK_BENHLY':
		case 'KB_TOMTAT':
			for ($i=0; $i < $length-1; $i++){
				for ($j=$i+1; $j < $length; $j++){
					if ($concepts[$i]['type'] == 'PR' && $concepts[$j]['type'] == 'PR' && $concepts[$i]['content'] != $concepts[$j]['content']){
						$textConceptRelation .= 'c="'.$concepts[$i]['content'].'" '.$line.':'.$concepts[$i]['fromWord'].
							' '.$line.':'.$concepts[$i]['toWord'].'||r="PIP"||c="'.$concepts[$j]['content'].'" '.$line.':'.
							$concepts[$j]['fromWord'].' '.$line.':'.$concepts[$j]['toWord']."\n";
						$GLOBALS['totalPIP']++;
					}
					else if(($concepts[$i]['type'] == 'PR' && $concepts[$j]['type'] == 'TR' ||
							$concepts[$j]['type'] == 'PR' && $concepts[$i]['type'] == 'TR') && $concepts[$i]['content'] != $concepts[$j]['content']){
						$proConcept = ($concepts[$i]['type'] == 'PR') ? $i : $j;
						$treatConcept = ($i +$j) - $proConcept;
						$textConceptRelation .= 'c="'.$concepts[$treatConcept]['content'].'" '.$line.':'.$concepts[$treatConcept]['fromWord'].
							' '.$line.':'.$concepts[$treatConcept]['toWord'].'||r="TrWP"||c="'.$concepts[$proConcept]['content'].'" '.$line.':'.
							$concepts[$proConcept]['fromWord'].' '.$line.':'.$concepts[$proConcept]['toWord']."\n";
						$GLOBALS['totalTrWP']++;
						/*if ($proConcept < $treatConcept){
							$check = true;
							for ($k = $treatConcept + 1; $k < $length - 1; $k ++)
								if ($concepts[$k]['type'] == 'PR'){
									$check = false; break;
								}
							if ($check)
								echo "<br>" . $folder . "  " . $order;
						}*/
					}
					if ($concepts[$i]['type'] == "TE" || $concepts[$j]['type'] == "TE"){
						echo "<br>" . $order;
					}
				}
			}
			break;
		//case '':
		default:
	}
	return $textConceptRelation;
}

function createTextContent($arr, $folder, $order, &$history, &$fileArr){
    $textRecordContent = '';
    $textConceptContent = '';
	$textConceptRelation = '';
    $htmlStr = '';
    $section = '';
    $countLine = 1;
	

    foreach ($arr as $sentence) {
        if($section != $sentence['section']){
            // $section = changeSenSectionName($sentence['section']);
			$kt = true;
			for ($i = 0; $i < sizeof($history); $i ++){
				if ($history[$i]["name"] == $section){
					$history[$i]["count"] ++;
					$kt = false;
					break;
				}	
			}
			if ($kt){
				$history[sizeof($history)] = array("name" => $section, "count" => 1);
			}
			$fileArr[sizeof($fileArr)] = array("section" => $section, "folder" => $folder, "file" => $order);
            $section = $sentence['section'];
            $changedSection = changeSenSectionName($section);
            $textRecordContent .= $changedSection."\n";
            $htmlStr .= $changedSection."\n";
            $countLine++;
        }
		
		//relation
		$textConceptRelation .= autoDeterRelation($sentence['section'], $sentence['concept'], $countLine, $order, $folder);

        $textRecordContent .= $sentence['content']."\n";
        $senArr = explode(" ",$sentence['content']);
        foreach ($sentence['concept'] as $aConcept) {
            $fromWord = $aConcept['fromWord'];
            $toWord = $aConcept['toWord'];
            $type = changeType($aConcept['type']);

            $textConceptContent .= 'c="'.$aConcept['content'].'" '.$countLine.':'.$fromWord.' '.$countLine.':'
            .$toWord.'||t="'.$type.'"'."\n";
            $senArr[$fromWord-1] = '<span class="selected '.$type.'". start="'.$fromWord.'" end="'.$toWord.'" line="'.$countLine.'">'. $aConcept['content'].'</span>';
            for($i = $fromWord ; $i < $toWord ; $i++ )
            {
                unset($senArr[$i]);
            }


        }
        $htmlStr .= implode(" ",$senArr)."\n";
        $countLine++;
    }

    //echo "Text ".$order." : <hr/> ";
    //showData($textRecordContent);
    //writeToFile("doc/$folder/$order.txt",$textRecordContent);
    //echo "Concept : <hr/> ";
    //showData($textConceptContent);
    //writeToFile("mention/$folder/$order.txt",$textConceptContent);
	//echo "Relation : <hr/> ";
	//showData($textConceptRelation);
	//writeToFile("relation/$folder/$order.txt", $textConceptRelation);

    //echo "html : <hr/> ";
    //showData($htmlStr);
    //writeToFile("html/$folder/$order.txt",$htmlStr);
}

function writeToFile($fileName,$data){
    $file = fopen($fileName, "w+");
    fwrite($file, $data);
    fclose($file);
}

function getListOfFile($section, $Listfile){
	for ($i = 0; $i < sizeof($Listfile); $i ++){
		if ($Listfile[$i]["section"] == $section)
			echo "folder: " . $Listfile[$i]["folder"] . ", file: " . $Listfile[$i]["file"] . "<br>";
	}
}

$arr_input = array(
    array ('folder' => 'diepdt', 'num' => 418),
	array ('folder' => 'huyen', 'num' => 148),
	array ('folder' => 'khoi', 'num' => 8),
	array ('folder' => 'minh', 'num' => 2),
	array ('folder' => 'sinhlk', 'num' => 110),
	array ('folder' => 'tuan', 'num' => 1)
    );
// $content = getContent('diepdt','140506081955047000');

$history = array();
$file = array();
$k = 1;
for ($i = 0 ; $i < sizeof($arr_input); $i++ ){
	for ($j = 1; $j <= $arr_input[$i]['num']; $j++){
		$folder = $arr_input[$i]['folder'];
	//	$content = json_decode(file_get_contents("./json_data/$folder/$j.json"),true);
		rename("./txt/$folder/$j.txt", "./txt/$folder/$k.txt");
		rename("./concepts/$folder/$j.txt", "./concepts/$folder/$k.txt");
		rename("./relation/$folder/$j.txt", "./relation/$folder/$k.txt");
		copy("./txt/$folder/$k.txt", "./txt/txt/$k.txt");
		copy("./concepts/$folder/$k.txt", "./concepts/concept/$k.txt");
		copy("./relation/$folder/$k.txt", "./relation/relation/$k.txt");
		$k ++;
    // showData($content['listSentences']);
	//	createTextContent($content['listSentences'],$folder,$j, $history, $file);
		
	}   
}
echo($k);
/*
echo "Total file had crawler: " . sizeof($file);
echo "<br>" . "Sections in medical records";
for ($i = 0; $i < sizeof($history); $i ++){
	echo "<br>" . $history[$i]["name"] . "  " . $history[$i]["count"];
}
echo "<br>";
echo "PIP: " . $GLOBALS['totalPIP'];
echo "<br>" . "TrWP: " . $GLOBALS['totalTrWP'];
echo "<br>";
echo "HB_BENHLY: " . "<br>";
getListOfFile("HB_BENHLY", $file);
echo "<br> KB_TOMTAT: " . "<br>";
getListOfFile("KB_TOMTAT", $file);
echo "<br> TK_BENHLY: " . "<br>";
getListOfFile("TK_BENHLY", $file);
*/
