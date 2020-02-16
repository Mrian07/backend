<?
include_once("common.php");

$dist_fare = isset($_REQUEST['dist_fare'])?$_REQUEST['dist_fare']:'';
$time_fare = isset($_REQUEST['time_fare'])?$_REQUEST['time_fare']:'';
$fromLoc = isset($_REQUEST['fromLoc'])?$_REQUEST['fromLoc']:'';

function fetch_address_geocode($address) {
	global $generalobj;
	$address = str_replace(" ", "+", "$address");
	//$GOOGLE_SEVER_API_KEY_WEB=$generalobj->getConfigurations("configurations","GOOGLE_SEVER_API_KEY_WEB");
	$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
	$result = file_get_contents("$url");
	$result = stripslashes(preg_replace("/[\n\r]/", "", $result));
	$json = json_decode($result);
	
	$city = $state = $country = $country_code = '';
	
	foreach ($json->results as $result) {
		foreach($result->address_components as $addressPart) {
			if(((in_array('locality', $addressPart->types)) && (in_array('political', $addressPart->types))) || ((in_array('sublocality', $addressPart->types)) && (in_array('political', $addressPart->types)) && (in_array('sublocality_level_1', $addressPart->types)))) {
				$city = $addressPart->long_name;
			}else if ((in_array('administrative_area_level_1', $addressPart->types)) && (in_array('political', $addressPart->types))) {
				$state = $addressPart->long_name;
			}else if ((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types))){
				$country = $addressPart->long_name;
				$country_code = $addressPart->short_name;
			}
		}
	}
	
	$returnArr = array('city'=>$city,'state'=> $state,'country'=>$country,'country_code'=>$country_code);
	
	
	return $returnArr;
}


if($dist_fare != '' && $time_fare != "")
{
	$priceRatio = 1;
	$db_country = array();
	
	$sql = "select * from vehicle_type";
	$db_vType = $obj->MySQLSelect($sql);
	
	$address_data = fetch_address_geocode($fromLoc);
	
	$sql2 = "select eUnit from country WHERE LOWER(vCountry) = '".strtolower($address_data['country'])."'";
	$db_country = $obj->MySQLSelect($sql2);
	
	if(!empty($db_country)){
		if($db_country[0]['eUnit'] == 'KMs' || $db_country[0]['eUnit'] == ''){
			$dist_fare_new = $dist_fare;
		}else {
			$dist_fare_new = $dist_fare * 0.621371;
		}
	}else {
		$dist_fare_new = $dist_fare;
	}
	//0.621371 for miles
	$cont = '';
	$cont .= '<ul>';
    for($i=0;$i<count($db_vType);$i++){
		
		$Minute_Fare =round($db_vType[$i]['fPricePerMin']*$time_fare,2) * $priceRatio;
		$Distance_Fare =round($db_vType[$i]['fPricePerKM']*$dist_fare_new,2)* $priceRatio;
		$iBaseFare =round($db_vType[$i]['iBaseFare'],2)* $priceRatio;
		$total_fare=$iBaseFare+$Minute_Fare+$Distance_Fare;
		
		$cont .= '<li><label>'.$db_vType[$i]['vVehicleType'].'<img src="assets/img/question-icon.jpg" alt="" title="'.$langage_lbl['LBL_APPROX_DISTANCE_TXT'].' '.$langage_lbl['LBL_FARE_ESTIMATE_TXT'].'"><b>'.$generalobj->trip_currency($total_fare).'</b></label></li>';		
    }
	$cont .= '<li><p>'.$langage_lbl['LBL_HOME_PAGE_GET_FIRE_ESTIMATE_TXT'].'</p></li>';
	if(!isset($_SESSION['sess_user']) && $_SESSION['sess_user'] == "") {
		$cont .= '<li><strong><a href="sign-up-rider"><em>'.$langage_lbl['LBL_RIDER_SIGNUP1_TXT'].'</em></a></strong></li>';
	}
	$cont .= '</ul>';
    echo $cont; exit;
}
?>
