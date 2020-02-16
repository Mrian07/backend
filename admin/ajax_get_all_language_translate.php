<?php
include_once("../common.php");

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

  $englishText = isset($_POST['englishText'])?$_POST['englishText']:'';
	//echo '<prE>'; echo $englishText; echo '</pre>';

	
	
	// fetch all lang from language_master table
	$sql = "SELECT vCode,vLangCode FROM `language_master` where vCode!='EN' ORDER BY `iDispOrder`";
	$db_master = $obj->MySQLSelect($sql);
	$count_all = count($db_master);
  
  /*echo "<pre>";
  print_r($db_master);*/
  
  if($count_all > 0) {
	   for($i=0;$i<$count_all;$i++) {
          
            $vCode = $db_master[$i]['vCode'];
      
            $vGmapCode = $db_master[$i]['vLangCode'];
            
            
            
            $vValue = 'vValue_'.$vCode;
            
            $url = 'http://api.mymemory.translated.net/get?q='.urlencode($englishText).'&de=harshilmehta1982@gmail.com&langpair=en|'.$vGmapCode;
            
            $result = file_get_contents($url);
  
            $finalResult = json_decode($result);
            
            $getText = $finalResult->responseData;
            
            
            $data['result'][] = array(
                                  $vValue => $getText->translatedText
                                );
            
            
     }
  }
  
  $output = array();
  foreach($data['result'] as $Result){
             $output[key($Result)] = current($Result);
  }
  
  /*echo "<pre>";
  print_r($data['results']);
  die*/
  
  echo json_encode($output);
  exit;
  

?>