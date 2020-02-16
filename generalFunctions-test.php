<?php
	/*to clean function */
	function clean($str) {
		$str = trim($str);
		$str = mysql_real_escape_string($str);
		$str = htmlspecialchars($str);
		$str = strip_tags($str);
		return($str);
	}

	/* get vLangCode as per member or if member not found check lcode and then defualt take lang code set at $lang_label */
	function getLanguageCode($memberId = '', $lcode = '') {
		global $lang_label, $lang_code, $obj;
		/* find vLanguageCode using member id */
		if($memberId != '') {

			$sql = "SELECT  `vLanguageCode` FROM  `member` WHERE iMemberId = '".$memberId."' AND `eStatus` = 'Active' ";
			$get_vLanguageCode = $obj->MySQLSelect($sql);

			if(count($get_vLanguageCode) > 0)
			$lcode = (isset($get_vLanguageCode[0]['vLanguageCode']) && $get_vLanguageCode[0]['vLanguageCode'] != '')?$get_vLanguageCode[0]['vLanguageCode']:'';
		}

		/* find default language of website set by admin */
		if($lcode == '') {
			$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
			$default_label = $obj->MySQLSelect($sql);

			$lcode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode'])?$default_label[0]['vCode']:'EN';
		}

		$lang_code = $lcode;
		$sql = "SELECT  `vLabel` ,  `vValue`  FROM  `language_label`  WHERE  `vCode` = '".$lcode."' ";
		$all_label = $obj->MySQLSelect($sql);

		for($i=0; $i<count($all_label); $i++){
			$vLabel = $all_label[$i]['vLabel'];
			$vValue = $all_label[$i]['vValue'];
			$lang_label[$vLabel]=$vValue;
		}
		//echo "<pre>"; print_R($lang_label); echo "</pre>";
	}

	#function to get value from table can be use for any table - create to get value from configuration
	#$check_phone = get_value('configurations', 'vValue', 'vName', 'PHONE_VERIFICATION_REQUIRED');
	function get_value($table, $field_name, $condition_field = '', $condition_value = '', $setParams='',$directValue='') {
		global $obj;
		$returnValue = array();

		$where		= ($condition_field != '')?' WHERE '.clean($condition_field):'';
		$where		.= ($where != '' && $condition_value != '')?' = "'.clean($condition_value).'"':'';

		if($table != '' && $field_name != '' && $where != '') {
			$sql = "SELECT $field_name FROM  $table $where";
			if($setParams != ''){
				$sql .= $setParams;
			}
			$returnValue = $obj->MySQLSelect($sql);
		}else if($table != '' && $field_name != ''){
			$sql = "SELECT $field_name FROM  $table";
			if($setParams != ''){
				$sql .= $setParams;
			}
			$returnValue = $obj->MySQLSelect($sql);
		}
		if($directValue == ''){
			return $returnValue;
		}else{
		$temp = $returnValue[0][$field_name];
			return $temp;
		}

	}

	function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	function createUserLog($userType,$eAutoLogin,$iMemberId,$deviceType){
		global $generalobj,$obj;

		if(SITE_TYPE!="Demo"){
			return "";
		}
		$data['iMemberId']=$iMemberId;
		$data['eMemberType']=$userType;
		$data['eMemberLoginType']="AppLogin";
		$data['eDeviceType']=$deviceType;
		$data['eAutoLogin']=$eAutoLogin;
		$data['vIP']=get_client_ip();

		$id = $obj->MySQLQueryPerform("member_log",$data,'insert');
	}

	function dateDifference($date_1 , $date_2 , $differenceFormat = '%a' ){
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);

		$interval = date_diff($datetime1, $datetime2);

		return $interval->format($differenceFormat);

	}

	function getVehicleTypes($cityName = ""){
		global $obj;
		$sql_vehicle_type="SELECT * FROM vehicle_type";

		$row_result_vehivle_type = $obj->MySQLSelect($sql_vehicle_type);
		return $row_result_vehivle_type;
	}

	function paymentimg($paymentm){
		global $tconfig;
		if($paymentm == "Card"){
			// return "webimages/icons/payment_images/ic_payment_type_card.png";
			return $tconfig["tsite_url"]."webimages/icons/payment_images/ic_payment_type_card.png";
		}
		else
		{
			// return "webimages/icons/payment_images/ic_payment_type_cash.png";
			return $tconfig["tsite_url"]."webimages/icons/payment_images/ic_payment_type_cash.png";
		}
	}

	function ratingmark($ratingval){
		global $tconfig;
		$a = $ratingval;
		$b = explode('.', $a);
		$c = $b[0];

		$str = "";
		$count=0;
		for($i=0; $i<5; $i++)
		{
			if($c>$i){
				$str .= '<img src="'.$tconfig["tsite_url"].'webimages/icons/ratings_images/Star-Full.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
			}
			elseif($a > $c && $count==0){
				$str .= '<img src="'.$tconfig["tsite_url"].'webimages/icons/ratings_images/Star-Half-Full.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
				$count=1;
			}
			else
			{
				$str .= '<img src="'.$tconfig["tsite_url"].'webimages/icons/ratings_images/Star-blank.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
			}
		}
		return $str;

	}

  function getTripFare($Fare_data,$surgePrice){

  	if($surgePrice >= 1){
  			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'] * $surgePrice;
  			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'] * $surgePrice;
  			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'] * $surgePrice;
  			$Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'] * $surgePrice;
		}

		if($Fare_data[0]['eFareType'] == 'Fixed')
		{
			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'];
			$Fare_data[0]['fPricePerMin'] = 0;
			$Fare_data[0]['fPricePerKM'] = 0;
		}

    $Minute_Fare =round($Fare_data[0]['fPricePerMin']*$Fare_data[0]['TripTimeMinutes'] ,2);
		$Distance_Fare =round($Fare_data[0]['fPricePerKM']*$Fare_data[0]['TripDistance'] ,2);
		$iBaseFare =round($Fare_data[0]['iBaseFare'],2);

		$total_fare=$iBaseFare+$Minute_Fare+$Distance_Fare;

		$Commision_Fare =round((($total_fare*$Fare_data[0]['fCommision'])/100),2);

    $result['FareOfMinutes'] = $Minute_Fare;
		$result['FareOfDistance'] = $Distance_Fare;
		$result['FareOfCommision'] = $Commision_Fare;
		// $result['iBaseFare'] = $iBaseFare;
		$result['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];
		$result['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];
		$result['fCommision'] = $Fare_data[0]['fCommision'];
		$result['FinalFare'] = $total_fare;
    $result['iBaseFare'] = ($Fare_data[0]['eFareType'] == 'Fixed')?0:$iBaseFare;
    $result['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];
    $result['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];
    $result['iMinFare'] = $Fare_data[0]['iMinFare'];

		return $result;

  }

  function calculateFare($totalTimeInMinutes_trip,$tripDistance,$vehicleTypeID,$iUserId,$priceRatio,$startDate="",$endDate="",$couponCode="",$tripId){
		global $generalobj,$obj;
		$Fare_data=getVehicleFareConfig("vehicle_type",$vehicleTypeID);

		// $defaultCurrency = ($obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault='Yes'")[0]['vName']);
		$defaultCurrency = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
    $fPickUpPrice = get_value('trips', 'fPickUpPrice', 'iTripId', $tripId,'','true');
    $fNightPrice = get_value('trips', 'fNightPrice', 'iTripId', $tripId,'','true');
    $surgePrice =  $fPickUpPrice > 1?$fPickUpPrice:($fNightPrice > 1?$fNightPrice:1);

    $tripTimeInMinutes = ($startDate != '' && $endDate != '')? (@round(abs(strtotime($startDate) - strtotime($endDate)) / 60,2)):0;

    $Fare_data[0]['TripTimeMinutes'] = $tripTimeInMinutes;
    $Fare_data[0]['TripDistance'] = $tripDistance;

    $result = getTripFare($Fare_data,"1");
    //$resultArr_Orig = getTripFare($Fare_data,"1");


    $total_fare = $result['FinalFare'];
    $fTripGenerateFare = $result['FinalFare'];
    $iMinFare = $result['iMinFare'];

    if($iMinFare > $fTripGenerateFare){
			$MinFareDiff = $iMinFare - $total_fare;
			$total_fare = $iMinFare;
      $fTripGenerateFare = $iMinFare;
		}else{
			$MinFareDiff = "0";
		}

    $fSurgePriceDiff = round(($fTripGenerateFare*$surgePrice)-$fTripGenerateFare,2);
    $total_fare = $total_fare+$fSurgePriceDiff;
    $fTripGenerateFare = $fTripGenerateFare+$fSurgePriceDiff;
    $result['fCommision'] =round((($fTripGenerateFare*$Fare_data[0]['fCommision'])/100),2);  
    /*Check Coupon Code For Count Total Fare Start */
    $discountValue = 0;
    $discountValueType= "cash";
    if( $couponCode != ''){
    	$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode,'','true');
    	$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode,'','true');
		}
    if($couponCode != '' && $discountValue != 0){
			if($discountValueType == "percentage"){
				$vDiscount= round($discountValue,1) .' '."%";
				$discountValue = round(($total_fare * $discountValue),1)/100;
			}else{
				$curr_sym =get_value('currency', 'vSymbol', 'eDefault', 'Yes','','true');
				if($discountValue > $total_fare){
					$vDiscount= round($total_fare,1).' '.$curr_sym;
				}else{
					$vDiscount= round($discountValue,1).' '.$curr_sym;
				}
			}
			$fare = $total_fare - $discountValue;
			if($fare < 0){
				$fare = 0;
				$discountValue = $total_fare;
			}
      $total_fare = $fare;
			$Fare_data[0]['fDiscount']=$discountValue;
			$Fare_data[0]['vDiscount'] =$vDiscount ;
		}
    /*Check Coupon Code Total Fare  End*/

    /*Check debit wallet For Count Total Fare  Start*/
    $user_available_balance = $generalobj->get_user_available_balance($iUserId,"Rider");
		$user_wallet_debit_amount = 0;
		if($total_fare> $user_available_balance){
			$total_fare = $total_fare - $user_available_balance;
			$user_wallet_debit_amount = $user_available_balance;
		}else{
			$user_wallet_debit_amount = $total_fare;
			$total_fare = 0;
		}

		// Update User Wallet
		if($user_wallet_debit_amount > 0){
			$vRideNo = get_value('trips', 'vRideNo', 'iTripId',$tripId,'','true');
			$data_wallet['iUserId']=$iUserId;
			$data_wallet['eUserType']="Rider";
			$data_wallet['iBalance']=$user_wallet_debit_amount;
			$data_wallet['eType']="Debit";
			$data_wallet['dDate']=date("Y-m-d H:i:s");
			$data_wallet['iTripId']=$tripId;
			$data_wallet['eFor']="Booking";
			$data_wallet['ePaymentStatus']="Unsettelled";
			$data_wallet['tDescription']="Amount ".$user_wallet_debit_amount." debited from your account for trip number #".$vRideNo;

			$generalobj->InsertIntoUserWallet($data_wallet['iUserId'],$data_wallet['eUserType'],$data_wallet['iBalance'],$data_wallet['eType'],$data_wallet['iTripId'],$data_wallet['eFor'],$data_wallet['tDescription'],$data_wallet['ePaymentStatus'],$data_wallet['dDate']);
			//$obj->MySQLQueryPerform("user_wallet",$data_wallet,'insert');
		}
    /*Check debit wallet For Count Total Fare  End*/

    if($Fare_data[0]['eFareType'] == 'Fixed')
		{
			$Fare_data[0]['iBaseFare'] = 0;
		}
		else
		{
			$Fare_data[0]['iBaseFare'] = $result['iBaseFare'];
		}

		$finalFareData['total_fare'] = $total_fare;
    $finalFareData['iBaseFare'] = $result['iBaseFare'];
    $finalFareData['fPricePerMin'] = $result['FareOfMinutes'];
		$finalFareData['fPricePerKM'] = $result['FareOfDistance'];
		//$finalFareData['fCommision'] = $result['FareOfCommision'];
    //$finalFareData['fCommision'] = round((($fTripGenerateFare*$result['fCommision'])/100),2);
    $finalFareData['fCommision'] = $result['fCommision'];
    $finalFareData['fDiscount'] = $Fare_data[0]['fDiscount'];
    $finalFareData['vDiscount'] = $Fare_data[0]['vDiscount'];
    $finalFareData['MinFareDiff'] = $MinFareDiff;
    $finalFareData['fSurgePriceDiff'] = $fSurgePriceDiff;
    $finalFareData['user_wallet_debit_amount'] = $user_wallet_debit_amount;
    $finalFareData['fTripGenerateFare'] = $fTripGenerateFare;
    $finalFareData['SurgePriceFactor'] = $surgePrice;
		return $finalFareData;
	}

	function calculateFareEstimate($totalTimeInMinutes_trip,$tripDistance,$vehicleTypeID,$iUserId,$priceRatio,$startDate="",$endDate="",$surgePrice = 1){
		global $generalobj,$obj;
		$Fare_data=getVehicleFareConfig("vehicle_type",$vehicleTypeID);

		// $defaultCurrency = ($obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault='Yes'")[0]['vName']);
		$defaultCurrency = get_value('currency', 'vName', 'eDefault', 'Yes','','true');

		if($surgePrice > 1){
			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'] * $surgePrice;
			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'] * $surgePrice;
			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'] * $surgePrice;
			$Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'] * $surgePrice;
		}

		if($Fare_data[0]['eFareType'] == 'Fixed')
		{
			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'];
			$Fare_data[0]['fPricePerMin'] = 0;
			$Fare_data[0]['fPricePerKM'] = 0;
		}

		$resultArr = $generalobj->getFinalFare($Fare_data[0]['iBaseFare'],$Fare_data[0]['fPricePerMin'],$totalTimeInMinutes_trip,$Fare_data[0]['fPricePerKM'],$tripDistance,$Fare_data[0]['fCommision'],$priceRatio, $defaultCurrency,$startDate,$endDate);

		$resultArr['FinalFare'] = $resultArr['FinalFare']-$resultArr['FareOfCommision']; // Temporary set: Remove addition of commision from above function

		$Fare_data[0]['total_fare'] = $resultArr['FinalFare'];

		if($Fare_data[0]['iMinFare'] > $Fare_data[0]['total_fare']){
			$Fare_data[0]['MinFareDiff'] = $Fare_data[0]['iMinFare'] - $Fare_data[0]['total_fare'];
			$Fare_data[0]['total_fare'] = $Fare_data[0]['iMinFare'];
		}else{
			$Fare_data[0]['MinFareDiff'] = "0";
		}

		if($Fare_data[0]['eFareType'] == 'Fixed')
		{
			$Fare_data[0]['iBaseFare'] = 0;
		}
		else
		{
			$Fare_data[0]['iBaseFare'] = $resultArr['iBaseFare'];
		}
		$Fare_data[0]['fPricePerMin'] = $resultArr['FareOfMinutes'];
		$Fare_data[0]['fPricePerKM'] = $resultArr['FareOfDistance'];
		$Fare_data[0]['fCommision'] = $resultArr['FareOfCommision'];
		return $Fare_data;
	}



	function getVehicleFareConfig($tabelName,$vehicleTypeID){
		global $obj;
		$sql = "SELECT * FROM `".$tabelName."` WHERE iVehicleTypeId='$vehicleTypeID'";
		$Data_fare = $obj->MySQLSelect($sql);

		return $Data_fare;

	}

	function processTripsLocations($tripId,$latitudes,$longitudes){
	global $obj;
		$sql = "SELECT * FROM `trips_locations` WHERE iTripId = '$tripId'";
			$DataExist = $obj->MySQLSelect($sql);

			if(count($DataExist)>0){

					$latitudeList=$DataExist[0]['tPlatitudes'];
					$longitudeList=$DataExist[0]['tPlongitudes'];

					if($latitudeList != ''){
						$data_latitudes=$latitudeList.','.$latitudes;
					}else{
						$data_latitudes=$latitudes;
					}

					if($longitudeList != ''){
						$data_longitudes=$longitudeList.','.$longitudes;
					}else{
						$data_longitudes=$longitudes;
					}

					$where = " iTripId = '".$tripId."'";
					$Data_tripsLocations['tPlatitudes']=$data_latitudes;
					$Data_tripsLocations['tPlongitudes']=$data_longitudes;
					$id = $obj->MySQLQueryPerform("trips_locations",$Data_tripsLocations,'update',$where);


				}else{

					$Data_trips_locations['iTripId']=$tripId;
					$Data_trips_locations['tPlatitudes']=$latitudes;
					$Data_trips_locations['tPlongitudes']=$longitudes;

					$id = $obj->MySQLQueryPerform("trips_locations",$Data_trips_locations,'insert');

				}
		return $id;
	}

	function calcluateTripDistance($tripId){
			global $obj;
			$sql = "SELECT * FROM `trips_locations` WHERE iTripId = '$tripId'";
			$Data_tripsLocations = $obj->MySQLSelect($sql);

			$TotalDistance=0;
			if(count($Data_tripsLocations)>0){
				$trip_path_latitudes=$Data_tripsLocations[0]['tPlatitudes'];
				$trip_path_longitudes=$Data_tripsLocations[0]['tPlongitudes'];

				$trip_path_latitudes = preg_replace("/[^0-9,.-]/", '', $trip_path_latitudes);
				$trip_path_longitudes = preg_replace("/[^0-9,.-]/", '', $trip_path_longitudes);

				$TripPathLatitudes=explode(",",$trip_path_latitudes);

				$TripPathLongitudes=explode(",",$trip_path_longitudes);

				for($i=0;$i < count($TripPathLatitudes)-1;$i++){
					$tempLat_current=$TripPathLatitudes[$i];
					$tempLon_current=$TripPathLongitudes[$i];
					$tempLat_next=$TripPathLatitudes[$i+1];
					$tempLon_next=$TripPathLongitudes[$i+1];

					if($tempLat_current=='0.0' || $tempLon_current=='0.0' || $tempLat_next=='0.0' || $tempLon_next=='0.0' || $tempLat_current=='-180.0' || $tempLon_current=='-180.0' || $tempLat_next=='-180.0' || $tempLon_next=='-180.0'){
						continue;
					}

					$TempDistance=distanceByLocation($tempLat_current,$tempLon_current,$tempLat_next,$tempLon_next,"K");

					if(is_nan($TempDistance)){
						$TempDistance=0;
					}
					$TotalDistance += $TempDistance;
				}

			}

		return round($TotalDistance,2);
	}

	function checkDistanceWithGoogleDirections($tripDistance,$startLatitude,$startLongitude,$endLatitude,$endLongitude,$isFareEstimate = "0",$vGMapLangCode=""){
        global $generalobj,$obj;

		if($vGMapLangCode == "" || $vGMapLangCode == NULL){
    		   $vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault','Yes');
       		$vGMapLangCode=$vLangCodeData[0]['vGMapLangCode'];
    		}
        
		$GOOGLE_API_KEY=$generalobj->getConfigurations("configurations","GOOGLE_SEVER_GCM_API_KEY");
		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$startLatitude.",".$startLongitude."&destination=".$endLatitude.",".$endLongitude."&sensor=false&key=".$GOOGLE_API_KEY."&language=".$vGMapLangCode;

        try {
            $jsonfile = file_get_contents($url);
			} catch (ErrorException $ex) {
            // return $tripDistance;

            $returnArr['Action'] = "0";
            echo json_encode($returnArr);
            exit;
            // echo 'Site not reachable (' . $ex->getMessage() . ')';
		}

        $jsondata = json_decode($jsonfile);
        $distance_google_directions=($jsondata->routes[0]->legs[0]->distance->value)/1000;

        if($isFareEstimate == "0"){
            $comparedDist=($distance_google_directions *85)/100;

            if($tripDistance>$comparedDist){
                return $tripDistance;
			}else{
                return round($distance_google_directions,2);
			}
		}else{
            $duration_google_directions=($jsondata->routes[0]->legs[0]->duration->value)/60;
            $sAddress=($jsondata->routes[0]->legs[0]->start_address);
            $dAddress=($jsondata->routes[0]->legs[0]->end_address);
            $steps=($jsondata->routes[0]->legs[0]->steps);

            $returnArr['Time'] =$duration_google_directions;
            $returnArr['Distance'] =$distance_google_directions;
            $returnArr['SAddress'] =$sAddress;
            $returnArr['DAddress'] =$dAddress;
            $returnArr['steps'] =$steps;

            return $returnArr;
		}

	}

	function distanceByLocation($lat1, $lon1, $lat2, $lon2, $unit) {
		if((($lat1 == $lat2)  && ($lon1 == $lon2)) || ($lat1 == '' || $lon1 == '' || $lat2 == '' || $lon2 == '')){
			return 0;
		}

	  $theta = $lon1 - $lon2;
	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	  $dist = acos($dist);
	  $dist = rad2deg($dist);
	  $miles = $dist * 60 * 1.1515;
	  $unit = strtoupper($unit);

		  if ($unit == "K") {
			return ($miles * 1.609344);
		  } else if ($unit == "N") {
			  return ($miles * 0.8684);
			} else {
				return $miles;
			 }
	}

	function getLanguageLabelsArr($lCode = '',$directValue=""){
        global $obj;

        /* find default language of website set by admin */
		$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
		$default_label = $obj->MySQLSelect($sql);

		if($lCode == ''){
			$lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode'])?$default_label[0]['vCode']:'EN';
		}


        $sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label`  WHERE  `vCode` = '".$lCode."' ";
        $all_label = $obj->MySQLSelect($sql);

        $x = array();
        for($i=0; $i<count($all_label); $i++){
            $vLabel = $all_label[$i]['vLabel'];
            $vValue = $all_label[$i]['vValue'];
            $x[$vLabel]=$vValue;
		}


		$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label_other`  WHERE  `vCode` = '".$lCode."' ";
        $all_label = $obj->MySQLSelect($sql);

        for($i=0; $i<count($all_label); $i++){
            $vLabel = $all_label[$i]['vLabel'];

			$vValue = $all_label[$i]['vValue'];
            $x[$vLabel]=$vValue;
        }

		$x['vCode'] = $lCode; // to check in which languge code it is loading

		if($directValue == ""){
			$returnArr['Action'] = "1";
			$returnArr['LanguageLabels'] = $x;

			return $returnArr;
		}else{
			return $x;
		}

	}

	function sendEmeSms($toMobileNum,$message){
	global  $generalobj;
		$account_sid = $generalobj->getConfigurations("configurations","MOBILE_VERIFY_SID_TWILIO");
		$auth_token = $generalobj->getConfigurations("configurations","MOBILE_VERIFY_TOKEN_TWILIO");
		$twilioMobileNum= $generalobj->getConfigurations("configurations","MOBILE_NO_TWILIO");

		$client = new Services_Twilio($account_sid, $auth_token);
		try{
			$sms = $client->account->messages->sendMessage($twilioMobileNum,$toMobileNum,$message);
			return 1;
		} catch (Services_Twilio_RestException $e) {
			return 0;
		}
	}

	function converToTz($time,$toTz,$fromTz){
        $date = new DateTime($time, new DateTimeZone($fromTz));
        $date->setTimezone(new DateTimeZone($toTz));
        $time= $date->format('Y-m-d H:i:s');
        return $time;
    }

	/**
		* Sending Push Notification
	*/
    function send_notification($registatoin_ids, $message,$filterMsg = 0) {
        // include config
		// include_once './config.php';
		global $generalobj,$obj;
		$GOOGLE_API_KEY=$generalobj->getConfigurations("configurations","GOOGLE_SEVER_GCM_API_KEY");
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
		'registration_ids' => $registatoin_ids,
		'data' => $message,
        );

        $headers = array(
		'Authorization: key=' .$GOOGLE_API_KEY,
		'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);




		$finalFields = json_encode($fields,JSON_UNESCAPED_UNICODE);


		if($filterMsg == 1){
			$finalFields= stripslashes(preg_replace("/[\n\r]/","",$finalFields));
		}


        curl_setopt($ch, CURLOPT_POSTFIELDS, $finalFields);

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            // die('Curl failed: ' . curl_error($ch));
			$returnArr['Action'] = "0";
			$returnArr['message'] = "GCM_FAILED";
			$returnArr['ERROR'] =  curl_error($ch);
            echo json_encode($returnArr);
            exit;
		}

        // Close connection
        curl_close($ch);
        return $result;
	}

	function sendApplePushNotification($PassengerToDriver = 0,$deviceTokens,$message,$alertMsg,$filterMsg){
		global $generalobj,$obj;

        $passphrase = $generalobj->getConfigurations("configurations","IPHONE_PEM_FILE_PASSPHRASE");
        $APP_MODE = $generalobj->getConfigurations("configurations","APP_MODE");

		$prefix = "";
		$url_apns ='ssl://gateway.sandbox.push.apple.com:2195';
		if($APP_MODE == "Production"){
			$prefix = "PRO_";
			$url_apns ='ssl://gateway.push.apple.com:2195';
		}

        if($PassengerToDriver == 1){
			$name=$generalobj->getConfigurations("configurations",$prefix."PARTNER_APP_IPHONE_PEM_FILE_NAME");
        }else{
			$name=$generalobj->getConfigurations("configurations",$prefix."PASSENGER_APP_IPHONE_PEM_FILE_NAME");
        }
        $ctx = stream_context_create();

        stream_context_set_option($ctx, 'ssl', 'local_cert', $name);

        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        $fp = stream_socket_client(
                                   $url_apns, $err,
                                   $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);


		if (!$fp){
		   $returnArr['Action'] = "0";
			$returnArr['message'] = "APNS_FAILED";
			$returnArr['ERROR'] =  PHP_EOL;
            echo json_encode($returnArr);
            exit;
           // exit("Failed to connect: $err $errstr" . PHP_EOL);
		}

        // Create the payload body
        $body['aps'] = array(
                             'alert' => $alertMsg,
                             'content-available' => 1,
                             'body'  => $message,
                             'sound' => 'default'

                             );

        // Encode the payload as JSON
        $payload = json_encode($body,JSON_UNESCAPED_UNICODE);
//        $payload= stripslashes(preg_replace("/[\n\r]/","",$payload));
        if($filterMsg == 1){
            $payload= stripslashes(preg_replace("/[\n\r]/","",$payload));
        }

        for($device=0; $device < count($deviceTokens); $device++){
            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceTokens[$device]) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

//            print_r($result);

        }
            // Close the connection to the server
        fclose($fp);
    }

	function getOnlineDriverArr($sourceLat,$sourceLon,$iUserId = "",$country="",$city="",$state="",$address=""){
		global $generalobj,$obj;
		
		$sql="select * from restricted_negative_area";
		$db_restri_data_table = $obj->MySQLSelect($sql);
		if(count($db_restri_data_table) > 0){
			if($country == ""){
				$sqlq="select c.vCountry,c.iCountryId from register_user ru join country c on ru.vCountry = c.vCountryCode where ru.iUserId = '$iUserId'";
			}else{
				$sqlq="select iCountryId from country where vCountry = '$country'";
			}
			$data_user= $obj->MySQLSelect($sqlq);
			//print_r($data_user);
			$iCountryId = $data_user[0]['iCountryId'];
			if($state != ""){
				$iStateId=get_value('state', 'iStateId', 'vState',$state,'','');
			}
			if($city != ""){
				$iCityId=get_value('city', 'iCityId', 'vCity',$city,'','');
			}
			
			if($iCountryId != ""){
				$db_restricted_data=get_value('restricted_negative_area', '*', 'iCountryId',$iCountryId,'','');
				//echo "<pre>";print_r($db_restricted_data);exit;
					if(count($db_restricted_data) > 0){
						foreach($db_restricted_data as $val){
							if($val['iStateId'] != 0 ){
								if($iStateId != ""){
									 if($val['iStateId'] == $iStateId){
										if($val['iCityId'] != 0 ){
										  if($iCityId != ""){
												if($val['iCityId'] == $iCityId){
													// if($val['eType'] == "Restricted"){
														if($val['vAddress'] != ""){
															if($vAddress != ""){
																if($val['vAddress'] == $address){
																	if($val['eType'] == "Restricted"){
																		echo "allowed address";
																	}else{
																		echo "not allowed address";
																	}
																}else{
																	echo "allowed address";
																}
															}else{
																echo "allowed addresses";
															}
														}else{
															echo "allowed addresses";
														}
													// }else{
														// echo "not allowed cities";
													// }
												}else{
													echo "allowed city";
												}
										  }else{
											echo "allowed city";
										  }
										}else{
											if($val['eType'] == "Restricted"){
												echo "allowed city";
											}else{
												echo "not allowed cities";
											}
										}
									}else{
										echo "allowed states";
									}
								}else{
									echo "allowed states";
								}
							}else{
								if($val['eType'] == "Restricted"){
									echo "allowed country";
								}else{
									echo "not allowed country";
								}
							}
						}
					}else{
						echo "allowed country";
					}
			}else{
				echo "not allowed";
			}
		}
		exit;
		
		
		
		///////////////////
		
		
		$str_date = @date('Y-m-d H:i:s', strtotime('-1440 minutes'));
		$LIST_DRIVER_LIMIT_BY_DISTANCE = $generalobj->getConfigurations("configurations","LIST_DRIVER_LIMIT_BY_DISTANCE");
		$DRIVER_REQUEST_METHOD = $generalobj->getConfigurations("configurations","DRIVER_REQUEST_METHOD");

		$param = ($DRIVER_REQUEST_METHOD == "Time")? "tOnline":"tLastOnline";
		// if($DRIVER_REQUEST_METHOD == "Time"){
			// $param = " ORDER BY `register_driver`.`tOnline` ASC";
		// }else{
			// $param = " ORDER BY `register_driver`.`tLastOnline` ASC";
		// }

		 $sql = "SELECT ROUND(( 3959 * acos( cos( radians(".$sourceLat.") )
		* cos( radians( vLatitude ) )
		* cos( radians( vLongitude ) - radians(".$sourceLon.") )
		+ sin( radians(".$sourceLat.") )
		* sin( radians( vLatitude ) ) ) ),2) AS distance, register_driver.*  FROM `register_driver`
					WHERE (vLatitude != '' AND vLongitude != '' AND vAvailability = 'Available' AND vTripStatus != 'Active' AND eStatus='active' AND tLastOnline > '$str_date')
					HAVING distance < ".$LIST_DRIVER_LIMIT_BY_DISTANCE." ORDER BY `register_driver`.`".$param."` ASC"; 

		/* $sql = "SELECT ROUND(( 3959 * acos( cos( radians(".$sourceLat.") )
		* cos( radians( vLatitude ) )
		* cos( radians( vLongitude ) - radians(".$sourceLon.") )
		+ sin( radians(".$sourceLat.") )
		* sin( radians( vLatitude ) ) ) ),2) AS distance, register_driver.*  FROM `register_driver`
					WHERE (vLatitude != '' AND vLongitude != '' AND vAvailability = 'Available' AND vTripStatus != 'Active' AND eStatus='active')
					HAVING distance < ".$LIST_DRIVER_LIMIT_BY_DISTANCE." ORDER BY `register_driver`.`".$param."` ASC"; */

					

		$Data = $obj->MySQLSelect($sql);

		return $Data;
	}

	function getAddressFromLocation($latitude,$longitude,$Google_Server_key){
        $location_Address= "";

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".$Google_Server_key;

        try {

            $jsonfile = file_get_contents($url);
            $jsondata = json_decode($jsonfile);
            $address=$jsondata->results[0]->formatted_address;

            $location_Address = $address ;

        } catch (ErrorException $ex) {

            $returnArr['Action'] = "0";
            echo json_encode($returnArr);
            exit;
            // echo 'Site not reachable (' . $ex->getMessage() . ')';
        }

        if($location_Address == ""){
            $returnArr['Action'] = "0";
            echo json_encode($returnArr);
            exit;
        }

        return $location_Address;
    }

	function getLanguageTitle($vLangCode){
        global $obj;

        $sql = "SELECT vTitle FROM language_master WHERE vCode = '".$vLangCode."' ";
        $db_title = $obj->MySQLSelect($sql);

        return $db_title[0]['vTitle'];
	}

	function checkSurgePrice($vehicleTypeID, $selectedDateTime=""){
		$ePickStatus=get_value('vehicle_type', 'ePickStatus', 'iVehicleTypeId',$vehicleTypeID,'','true');
		$eNightStatus=get_value('vehicle_type', 'eNightStatus', 'iVehicleTypeId',$vehicleTypeID,'','true');

		$fPickUpPrice = 1;
		$fNightPrice = 1;

		if($selectedDateTime == ""){
			// $currentTime = @date("Y-m-d H:i:s");
			$currentTime = @date("H:i:s");
			$currentDay = @date("D");
		}else{
			// $currentTime = $selectedDateTime;
			$currentTime = @date("H:i:s",strtotime($selectedDateTime));
			$currentDay = @date("D",strtotime($selectedDateTime));
		}

		if($ePickStatus == "Active" || $eNightStatus == "Active"){

			$startTime_str = "t".$currentDay."PickStartTime";
			$endTime_str = "t".$currentDay."PickEndTime";
			$price_str = "f".$currentDay."PickUpPrice";

			$pickStartTime=get_value('vehicle_type', $startTime_str, 'iVehicleTypeId',$vehicleTypeID,'','true');
			$pickEndTime=get_value('vehicle_type', $endTime_str, 'iVehicleTypeId',$vehicleTypeID,'','true');
			$fPickUpPrice=get_value('vehicle_type', $price_str, 'iVehicleTypeId',$vehicleTypeID,'','true');

			$nightStartTime=get_value('vehicle_type', 'tNightStartTime', 'iVehicleTypeId',$vehicleTypeID,'','true');
			$nightEndTime=get_value('vehicle_type', 'tNightEndTime', 'iVehicleTypeId',$vehicleTypeID,'','true');
			$fNightPrice=get_value('vehicle_type', 'fNightPrice', 'iVehicleTypeId',$vehicleTypeID,'','true');

			if($currentTime > $pickStartTime && $currentTime < $pickEndTime && $ePickStatus == "Active"){

				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_PICK_SURGE_NOTE";
				$returnArr['SurgePrice'] = $fPickUpPrice . "X";
				$returnArr['SurgePriceValue'] = $fPickUpPrice;

			}else if($currentTime > $nightStartTime && $currentTime < $nightEndTime && $eNightStatus == "Active"){

				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_NIGHT_SURGE_NOTE";
				$returnArr['SurgePrice'] = $fNightPrice . "X";
				$returnArr['SurgePriceValue'] = $fNightPrice;

			}else{
				$returnArr['Action'] = "1";
			}

		}else{
			$returnArr['Action'] = "1";
		}

		return $returnArr;
	}

  function check_email_send($iDriverId,$tablename,$field) {
	 global $obj,$generalobj;
	  $sql = "SELECT * FROM ".$tablename." WHERE ".$field."= '" .$iDriverId. "'";
		$db_data =$obj->MySQLSelect($sql);
		//print_r($db_data);//exit;
	  //$valid=0;
	  if($tablename=='register_driver')
	  {
		  //echo "hi";exit;
		  if($db_data[0]['vNoc']!=NULL && $db_data[0]['vLicence'] !=NULL && $db_data[0]['vCerti'] !=NULL)
			{
				//global $generalobj;
						$maildata['USER'] = "Driver";
						$maildata['NAME'] = $db_data[0]['vName'];
						$maildata['EMAIL'] = $db_data[0]['vEmail'];
						$generalobj->send_email_user("PROFILE_UPLOAD",$maildata);
						//header("location:profile.php?success=1&var_msg=" . $var_msg);
						//return;
			}
	  }
	  else
		{
		  if($db_data[0]['vNoc']!=NULL && $db_data[0]['vCerti'] !=NULL)
			{
							$maildata['USER'] = "Company";
							$maildata['NAME'] = $db_data[0]['vName'];
							$maildata['EMAIL'] = $db_data[0]['vEmail'];
              //var_dump($maildata);
              //var_dump(($generalobj));
							$generalobj->send_email_user("PROFILE_UPLOAD",$maildata);
			}
	  }
		return true;
	}

	function checkmemberemailphoneverification($iMemberId, $user_type="Passenger"){
    global $obj;
    if($user_type == "Driver"){
      $EMAIL_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'DRIVER_EMAIL_VERIFICATION','','true');
      $PHONE_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'DRIVER_PHONE_VERIFICATION','','true');
      $eEmailVerified=get_value('register_driver', 'eEmailVerified', 'iDriverId',$iMemberId,'','true');
      $ePhoneVerified=get_value('register_driver', 'ePhoneVerified', 'iDriverId',$iMemberId,'','true');
    }else{
      $EMAIL_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'RIDER_EMAIL_VERIFICATION','','true');
      $PHONE_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'RIDER_PHONE_VERIFICATION','','true');
      $eEmailVerified=get_value('register_user', 'eEmailVerified', 'iUserId',$iMemberId,'','true');
      $ePhoneVerified=get_value('register_user', 'ePhoneVerified', 'iUserId',$iMemberId,'','true');
    }

    $email = $EMAIL_VERIFICATION == "Yes"?  ($eEmailVerified == "Yes"?"true":"false"):"true";
    $phone = $PHONE_VERIFICATION == "Yes"?  ($ePhoneVerified == "Yes"?"true":"false"):"true";

    if($email == "false" && $phone == "false"){
       $returnArr['Action'] = "0";
	   $returnArr['message'] = "DO_EMAIL_PHONE_VERIFY";
       echo json_encode($returnArr);
       exit;
    }else if($email == "true" && $phone == "false"){
       $returnArr['Action'] = "0";
			 $returnArr['message'] = "DO_PHONE_VERIFY";
       echo json_encode($returnArr);
       exit;
    }else if($email == "false" && $phone == "true"){
       $returnArr['Action'] = "0";
			 $returnArr['message'] = "DO_EMAIL_VERIFY";
       echo json_encode($returnArr);
       exit;
    }
  }

  function sendemailphoneverificationcode($iMemberId, $user_type="Passenger",$VerifyType){
     	global $generalobj,$obj;
      if($user_type == "Passenger"){
        $tblname = "register_user";
        $fields = 'iUserId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName';
        $condfield = 'iUserId';
        $vLangCode=get_value('register_user', 'vLang', 'iUserId',$iMemberId,'','true');
      }else{
        $tblname = "register_driver";
        $fields = 'iDriverId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName';
        $condfield = 'iDriverId';
        $vLangCode=get_value('register_driver', 'vLang', 'iDriverId',$iMemberId,'','true');
      }
      if($vLangCode == "" || $vLangCode == NULL){
       $vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
      }
      $languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
  		$prefix = $languageLabelsArr['LBL_VERIFICATION_CODE_TXT'];

      $emailmessage = "";
      $phonemessage = "";
      if($VerifyType == "email" || $VerifyType == "both"){
         $sql="select $fields from $tblname where $condfield = '".$iMemberId."'";
	       $db_member = $obj->MySQLSelect($sql);

         $Data_Mail['vEmailVarificationCode'] = $random = substr(number_format(time() * rand(),0,'',''),0,4);
         $Data_Mail['vEmail'] = isset($db_member[0]['vEmail'])?$db_member[0]['vEmail']:'';
		 $vFirstName = isset($db_member[0]['vName'])?$db_member[0]['vName']:'';
		 $vLastName = isset($db_member[0]['vLastName'])?$db_member[0]['vLastName']:'';
		 $Data_Mail['vName'] = $vFirstName." ".$vLastName ;
		 $Data_Mail['CODE'] = $Data_Mail['vEmailVarificationCode'] ;

		 $sendemail = $generalobj->send_email_user("APP_EMAIL_VERIFICATION_USER",$Data_Mail);
         if($sendemail){
            $emailmessage = $Data_Mail['vEmailVarificationCode'];
         }else{
            $emailmessage ="LBL_EMAIL_VERIFICATION_FAILED_TXT";
         }
      }

      if($VerifyType == "phone" || $VerifyType == "both"){
         $sql="select $fields from $tblname where $condfield = '".$iMemberId."'";
	       $db_member = $obj->MySQLSelect($sql);

         $mobileNo = $db_member[0]['vPhoneCode'].$db_member[0]['vPhone'];
         $toMobileNum= "+".$mobileNo;
		     $verificationCode = mt_rand(1000, 9999);
         $message = $prefix.' '.$verificationCode;
         $result = sendEmeSms($toMobileNum,$message);
         if($result ==0){
      		$phonemessage ="LBL_MOBILE_VERIFICATION_FAILED_TXT";
      	 }else{
      		$phonemessage =$verificationCode;
      	 }
      }

      $returnArr['emailmessage'] = $emailmessage;
      $returnArr['phonemessage'] = $phonemessage;
      return $returnArr;
  }

  function getTripPriceDetails($iTripId,$iMemberId,$eUserType="Passenger"){
			global $obj,$generalobj;
      $returnArr = array();
      if($eUserType == "Passenger"){
        $tblname = "register_user";
        $vLang = "vLang";
        $iUserId = "iUserId";
        $vCurrency = "vCurrencyPassenger";

        $currencycode = get_value("trips", $vCurrency, "iTripId",$iTripId,'','true');
      }else{
        $tblname = "register_driver";
        $vLang = "vLang";
        $iUserId = "iDriverId";
        $vCurrency = "vCurrencyDriver";

        $currencycode = get_value($tblname, $vCurrency, $iUserId,$iMemberId,'','true');
      }
      $userlangcode=get_value($tblname, $vLang, $iUserId,$iMemberId,'','true');
      if($userlangcode == "" || $userlangcode == NULL){
       $userlangcode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
      }

      $languageLabelsArr= getLanguageLabelsArr($userlangcode,"1");
      if($currencycode == "" || $currencycode == NULL){
       $currencycode = get_value('currency', 'vName', 'eDefault','Yes','','true');
      }

      $currencySymbol = get_value('currency', 'vSymbol', 'vName', $currencycode,'','true');

      $sql = "SELECT * from trips WHERE iTripId = '".$iTripId."'";
   		$tripData = $obj->MySQLSelect($sql);

      $priceRatio = $tripData[0]['fRatio_'.$currencycode];

      $returnArr = array_merge($tripData[0], $returnArr);
      if($tripData[0]['iUserPetId'] > 0){
         $petDetails_arr= get_value('user_pets', 'iPetTypeId,vTitle as PetName,vWeight as PetWeight, tBreed as PetBreed, tDescription as PetDescription', 'iUserPetId', $tripData[0]['iUserPetId'],'','');
      }else{
         $petDetails_arr = array();
      }

			if(count($petDetails_arr)>0){
				$petTypeName = get_value('pet_type', 'vTitle_'.$userlangcode, 'iPetTypeId', $petDetails_arr[0]['iPetTypeId'],'','true');
				$returnArr['PetDetails']['PetName'] = $petDetails_arr[0]['PetName'];
				$returnArr['PetDetails']['PetWeight'] = $petDetails_arr[0]['PetWeight'];
				$returnArr['PetDetails']['PetBreed'] = $petDetails_arr[0]['PetBreed'];
				$returnArr['PetDetails']['PetDescription'] = $petDetails_arr[0]['PetDescription'];
				$returnArr['PetDetails']['PetTypeName'] = $petTypeName;
			}else{
				$returnArr['PetDetails']['PetName'] = '';
				$returnArr['PetDetails']['PetWeight'] = '';
				$returnArr['PetDetails']['PetBreed'] = '';
				$returnArr['PetDetails']['PetDescription'] = '';
				$returnArr['PetDetails']['PetTypeName'] = '';
			}

      /* User Wallet Information */
      $returnArr['UserDebitAmount'] = strval($tripData[0]['fWalletDebit']);
      /* User Wallet Information */

      $vVehicleType = get_value('vehicle_type', "vVehicleType_".$userlangcode, 'iVehicleTypeId',$tripData[0]['iVehicleTypeId'],'','true');
      $vVehicleTypeLogo = get_value('vehicle_type', "vLogo", 'iVehicleTypeId',$tripData[0]['iVehicleTypeId'],'','true');
      $iVehicleCategoryId = get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'],'','true');
			$vVehicleCategory=get_value('vehicle_category', 'vCategory_'.$userlangcode, 'iVehicleCategoryId',$iVehicleCategoryId,'','true');


      $TripTime=date('h:iA',strtotime($tripData[0]['tTripRequestDate']));
      $tTripRequestDate=date('dS M \a\t h:i a',strtotime($tripData[0]['tTripRequestDate']));
      $tStartDate = $tripData[0]['tStartDate'];
      $tEndDate = $tripData[0]['tEndDate'];

      $totalTime=0;
      $hours= dateDifference($tStartDate,$tEndDate,'%h');
			$minutes= dateDifference($tStartDate,$tEndDate,'%i');
			$seconds= dateDifference($tStartDate,$tEndDate,'%s');
			if($hours>0){
					 $totalTime = $hours*60;
			}if($minutes>0){
					 $totalTime = $totalTime+$minutes;
			}
			$totalTime = $totalTime.":".$seconds." ".$languageLabelsArr['LBL_MINUTES_TXT'];
      if($totalTime < 1){
        $totalTime = $seconds." ".$languageLabelsArr['LBL_SECONDS_TXT'];
      }

      if($eUserType == "Passenger"){
        $TripRating = get_value('ratings_user_driver', 'vRating1', 'iTripId', $iTripId,' AND eUserType="Driver"','true');
        $returnArr['vDriverImage'] = get_value('register_driver', 'vImage', 'iTripId', $tripData[0]['iDriverId'],'','true');
        $returnArr['carTypeName'] = $vVehicleType;
        $returnArr['carImageLogo'] = $vVehicleTypeLogo;
        $driverDetailArr = get_value('register_driver', '*', 'iDriverId', $tripData[0]['iDriverId']);
      }else{
        $TripRating = get_value('ratings_user_driver', 'vRating1', 'iTripId', $iTripId,' AND eUserType="Passenger"','true');
        $passgengerDetailArr = get_value('register_user', '*', 'iUserId', $tripData[0]['iUserId']);
      }

      if($TripRating == "" || $TripRating == NULL){
         $TripRating = "0";
      }

      $iFare = $tripData[0]['iFare'];
      $fPricePerKM = $tripData[0]['fPricePerKM'] * $priceRatio;
      $iBaseFare = $tripData[0]['iBaseFare'] * $priceRatio;
      $fPricePerMin = $tripData[0]['fPricePerMin'] * $priceRatio;
      $fCommision = $tripData[0]['fCommision'];
      $fDistance = $tripData[0]['fDistance'];
      $vDiscount = $tripData[0]['vDiscount']; // 50 $
      $fDiscount = $tripData[0]['fDiscount']; // 50
      $fMinFareDiff = $tripData[0]['fMinFareDiff'] * $priceRatio;
      $fWalletDebit = $tripData[0]['fWalletDebit'];
      $fSurgePriceDiff = $tripData[0]['fSurgePriceDiff'] * $priceRatio;
      $fTripGenerateFare = $tripData[0]['fTripGenerateFare'] * $priceRatio;
      $fPickUpPrice = $tripData[0]['fPickUpPrice'];
      $fNightPrice = $tripData[0]['fNightPrice'];
			$fTipPrice = $tripData[0]['fTipPrice'] * $priceRatio;

      $returnArr['vVehicleType'] = $vVehicleType;
      $returnArr['vVehicleCategory'] = $vVehicleCategory;
      $returnArr['TripTime'] = $TripTime;
      $returnArr['ConvertedTripRequestDate'] = $tTripRequestDate;
      $returnArr['FormattedTripDate'] = $tTripRequestDate;
      $returnArr['tTripRequestDate'] = $tTripRequestDate;
      $returnArr['TripTimeInMinutes'] = $totalTime;
      $returnArr['TripRating'] = $TripRating;
      $returnArr['CurrencySymbol'] = $currencySymbol;
      $returnArr['TripFare']= formatNum($iFare * $priceRatio);
      $returnArr['iTripId']= $tripData[0]['iTripId'];
      $returnArr['vTripPaymentMode']= $tripData[0]['vTripPaymentMode'];

	  $originalFare = $iFare;
      if($eUserType == "Passenger"){
        $iFare = $iFare;
      }else{
        $iFare = $tripData[0]['fTripGenerateFare'] - $fCommision;
      }
      $surgePrice = 1;
			if($tripData[0]['fPickUpPrice'] > 1){
				$surgePrice=$tripData[0]['fPickUpPrice'];
			}else{
				$surgePrice=$tripData[0]['fNightPrice'];
			}
			$SurgePriceFactor = strval($surgePrice);

      $returnArr['TripFareOfMinutes'] = formatNum($tripData[0]['fPricePerMin'] * $priceRatio);
      $returnArr['TripFareOfDistance'] = formatNum($tripData[0]['fPricePerKM'] * $priceRatio);
      $returnArr['iFare'] = formatNum($iFare * $priceRatio);
      $returnArr['iOriginalFare'] = formatNum($originalFare * $priceRatio);
      $returnArr['TotalFare'] = formatNum($iFare * $priceRatio);
      $returnArr['fPricePerKM'] = formatNum($fPricePerKM);
      $returnArr['iBaseFare'] = formatNum($iBaseFare);
      $returnArr['fPricePerMin'] = formatNum($fPricePerMin);
      $returnArr['fCommision']= formatNum($fCommision * $priceRatio);
      $returnArr['fDistance']= formatNum($fDistance);
      $returnArr['fDiscount']= formatNum($fDiscount * $priceRatio);
      $returnArr['fMinFareDiff']= formatNum($fMinFareDiff);
      $returnArr['fWalletDebit']= formatNum($fWalletDebit * $priceRatio);
      $returnArr['fSurgePriceDiff']= formatNum($fSurgePriceDiff);
      $returnArr['fTripGenerateFare']= formatNum($fTripGenerateFare);
			$returnArr['fTipPrice']= formatNum($fTipPrice);
      $returnArr['SurgePriceFactor'] = $SurgePriceFactor;


      $iDriverId = $tripData[0]['iDriverId'];
      $driverDetails = get_value('register_driver', '*', 'iDriverId', $iDriverId);
      $driverDetails[0]['vImage']=($driverDetails[0]['vImage']!= "" && $driverDetails[0]['vImage']!= "NONE")?"3_".$driverDetails[0]['vImage']:"";
      $returnArr['DriverDetails']=$driverDetails[0];

      $iUserId = $tripData[0]['iUserId'];
      $passengerDetails = get_value('register_user', '*', 'iUserId', $iUserId);
      $passengerDetails[0]['vImgName']=($passengerDetails[0]['vImgName']!= "" && $passengerDetails[0]['vImgName']!= "NONE")?"3_".$passengerDetails[0]['vImgName']:"";
      $returnArr['PassengerDetails']=$passengerDetails[0];

      $iDriverVehicleId = $tripData[0]['iDriverVehicleId'];
      $sql = "SELECT make.vMake, model.vTitle, dv.*  FROM `driver_vehicle` dv, make, model WHERE dv.iDriverVehicleId='".$iDriverVehicleId."' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId`";
			$vehicleDetailsArr = $obj->MySQLSelect($sql);
			$vehicleDetailsArr[0]['vModel']=$vehicleDetailsArr[0]['vTitle'];
      $returnArr['DriverCarDetails']   = $vehicleDetailsArr[0];

      if($eUserType == "Passenger"){
        $tripFareDetailsArr = array();
        $tripFareDetailsArr[0][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType." ".$currencySymbol.$returnArr['iBaseFare'];
        $tripFareDetailsArr[1][$languageLabelsArr['LBL_DISTANCE_TXT']." (".$returnArr['fDistance']." ".$languageLabelsArr['LBL_KM_DISTANCE_TXT'].")"] = $currencySymbol.$returnArr['TripFareOfDistance'];
        $tripFareDetailsArr[2][$languageLabelsArr['LBL_TIME_TXT']." (".$returnArr['TripTimeInMinutes'].")"] = $currencySymbol.$returnArr['TripFareOfMinutes'];
        $i=2;
        if($fMinFareDiff > 0){
           $minimamfare = $iBaseFare+$fPricePerKM+$fPricePerMin+$fMinFareDiff;
           $minimamfare = formatNum($minimamfare);
           $tripFareDetailsArr[$i+1][$currencySymbol.$minimamfare." ".$languageLabelsArr['LBL_MINIMUM']] = $currencySymbol.$returnArr['fMinFareDiff'];
           $returnArr['TotalMinFare'] = $minimamfare;
           $i++;
        }
        if($fSurgePriceDiff > 0){
           $normalfare = $fTripGenerateFare-$fSurgePriceDiff;
           $normalfare = formatNum($normalfare);
           $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_NORMAL_FARE']] = $currencySymbol.$normalfare;$i++;
           $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_SURGE']." x".$SurgePriceFactor] = $currencySymbol.$returnArr['fSurgePriceDiff'];$i++;
        }
        if($fDiscount > 0){
           $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = "- ".$currencySymbol.$returnArr['fDiscount'];$i++;
        }
        if($fWalletDebit > 0){
           $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = "- ".$currencySymbol.$returnArr['fWalletDebit'];$i++;
        }

				if($fTipPrice > 0){
					$tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_TIP_AMOUNT']] = $currencySymbol.$returnArr['fTipPrice'];$i++;
				}



        $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $currencySymbol.$returnArr['iFare'];

		    $returnArr['FareSubTotal'] = $currencySymbol.$returnArr['iOriginalFare'];
        $returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;
        $FareDetailsArr = array();
        foreach ($tripFareDetailsArr as $data) {
          $FareDetailsArr = array_merge($FareDetailsArr, $data);
        }
        $returnArr['FareDetailsArr'] = $FareDetailsArr;
        $returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;
      }else{
        $tripFareDetailsArr = array();
        $tripFareDetailsArr[0][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType." ".$currencySymbol.$returnArr['iBaseFare'];
        $tripFareDetailsArr[1][$languageLabelsArr['LBL_DISTANCE_TXT']." (".$returnArr['fDistance']." ".$languageLabelsArr['LBL_KM_DISTANCE_TXT'].")"] = $currencySymbol.$returnArr['TripFareOfDistance'];
        $tripFareDetailsArr[2][$languageLabelsArr['LBL_TIME_TXT']." (".$returnArr['TripTimeInMinutes'].")"] = $currencySymbol.$returnArr['TripFareOfMinutes'];
		    $i=2;
        if($fMinFareDiff > 0){
           $minimamfare = $iBaseFare+$fPricePerKM+$fPricePerMin+$fMinFareDiff;
           $minimamfare = formatNum($minimamfare);
           $tripFareDetailsArr[$i+1][$currencySymbol.$minimamfare." ".$languageLabelsArr['LBL_MINIMUM']] = $currencySymbol.$returnArr['fMinFareDiff'];
           $returnArr['TotalMinFare'] = $minimamfare;
           $i++;
        }
        if($fSurgePriceDiff > 0){
           $normalfare = $fTripGenerateFare-$fSurgePriceDiff;
           $normalfare = formatNum($normalfare);
           $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_NORMAL_FARE']] = $currencySymbol.$normalfare;$i++;
           $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_SURGE']." x".$SurgePriceFactor] = $currencySymbol.$returnArr['fSurgePriceDiff'];$i++;
        }
        if($fDiscount > 0){
           $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = "- ".$currencySymbol.$returnArr['fDiscount'];$i++;
        }
        if($fWalletDebit > 0){
           $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = "- ".$currencySymbol.$returnArr['fWalletDebit'];$i++;
        }
				if($fTipPrice > 0){
					$tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_TIP_AMOUNT']] = $currencySymbol.$returnArr['fTipPrice'];$i++;
				}
        $returnArr['FareSubTotal'] = $currencySymbol.$returnArr['iOriginalFare'];
        $returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;
        $FareDetailsArr = array();
        foreach ($tripFareDetailsArr as $data) {
          $FareDetailsArr = array_merge($FareDetailsArr, $data);
        }
        $returnArr['FareDetailsArr'] = $FareDetailsArr;
        $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_Commision']] = "-".$currencySymbol.$returnArr['fCommision'];$i++;
        $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_EARNED_AMOUNT']] = $currencySymbol.$returnArr['iFare'];
        $returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;
      }
        $returnArr['FareSubTotal'] = $currencySymbol.$returnArr['iOriginalFare'];
      //passengertripfaredetails

      $HistoryFareDetailsArr = array();
      foreach ($tripFareDetailsArr as $inner) {
        $HistoryFareDetailsArr = array_merge($HistoryFareDetailsArr, $inner);
      }
      $returnArr['HistoryFareDetailsArr'] = $HistoryFareDetailsArr;


      //drivertripfarehistorydetails
      //echo "<pre>";print_r($returnArr);echo "<pre>";print_r($tripData);exit;
      return $returnArr;
	}

  function formatNum($number){
      return strval(number_format($number,2));
  }

  function getUserRatingAverage($iMemberId,$eUserType="Passenger"){
			global $obj,$generalobj;
      if($eUserType == "Passenger"){
        $iUserId = "iDriverId";
        $checkusertype = "Passenger";
      }else{
        $iUserId = "iUserId";
        $checkusertype = "Driver";
      }

      $usertotaltrips = get_value("trips", "iTripId", $iUserId,$iMemberId);
      if(count($usertotaltrips) > 0){
         for($i=0;$i<count($usertotaltrips);$i++){
            $iTripId .= $usertotaltrips[$i]['iTripId'].",";
         }

        $iTripId_str = substr($iTripId,0,-1);
        //echo  $iTripId_str;exit;
        $sql = "SELECT count(iRatingId) as ToTalTrips, SUM(vRating1) as ToTalRatings from ratings_user_driver WHERE iTripId IN (".$iTripId_str.") AND eUserType = '".$checkusertype."'";
        $result_ratings = $obj->MySQLSelect($sql);
        $ToTalTrips = $result_ratings[0]['ToTalTrips'];
        $ToTalRatings = $result_ratings[0]['ToTalRatings'];
        $average_rating =  round($ToTalRatings/$ToTalTrips,2);
      }else{
        $average_rating = 0;
      }
		return $average_rating;
	}

  function deliverySmsToReceiver($iTripId){
		global $obj,$generalobj,$tconfig;

      $sql = "SELECT * from trips WHERE iTripId = '".$iTripId."'";
   		$tripData = $obj->MySQLSelect($sql);

      $SenderName = get_value("register_user", "vName,vLastName", "iUserId",$tripData[0]['iUserId']);
      $SenderName = $SenderName[0]['vName']." ".$SenderName[0]['vLastName'];
      $delivery_address = $tripData[0]['tDaddress'];
      $vDeliveryConfirmCode = $tripData[0]['vDeliveryConfirmCode'];
      $page_link = $tconfig['tsite_url']."trip_tracking.php?iTripId=".$iTripId;
      $page_link = get_tiny_url($page_link);

      $message_deliver = $SenderName." has send you the parcel on below address.".$delivery_address.". Upon Receiving the parcel, please provide below verification code to sender. Verification Code: ".$vDeliveryConfirmCode.". click on link below to track your parcel. ".$page_link;

      //echo $message_deliver;exit;
      return $message_deliver;
	}

  function get_tiny_url($url)  {
  	$ch = curl_init();
  	$timeout = 5;
  	curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);
  	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  	$data = curl_exec($ch);
  	curl_close($ch);
  	return $data;
  }
  
  function addToUserRequest($iUserId,$iDriverId,$message,$iMsgCode){
		global $obj;
		$data['iUserId']=$iUserId;
		$data['iDriverId']=$iDriverId;
		$data['tMessage']=$message;
		$data['iMsgCode']=$iMsgCode;
		$data['dAddedDate']=@date("Y-m-d H:i:s");

	   $dataId = $obj->MySQLQueryPerform("passenger_requests",$data,'insert');
	   
	   return $dataId;
	}
  
  function addToDriverRequest($iDriverId,$iUserId,$iTripId,$eStatus){
		global $obj;
		$data['iDriverId']=$iDriverId;
    $data['iUserId']=$iUserId;
		$data['iTripId']=$iTripId;
		$data['eStatus']=$eStatus;
    $data['tDate']=@date("Y-m-d H:i:s");
    
	  $id = $obj->MySQLQueryPerform("driver_request",$data,'insert');
	   
	  return $id;
	}
  
  function UpdateDriverRequest($iDriverId,$iUserId,$iTripId,$eStatus){
		global $obj;
    
    $sql = "SELECT * FROM `driver_request` WHERE iDriverId = '".$iDriverId."' AND iUserId = '".$iUserId."' AND iTripId = '0' ORDER BY iDriverRequestId DESC LIMIT 0,1";
		$db_sql = $obj->MySQLSelect($sql);
    $request_count = count($db_sql);
    
    if($request_count > 0){
      $where = " iDriverRequestId = '".$db_sql[0]['iDriverRequestId']."'";
			$Data_Update['eStatus']=$eStatus;
      $Data_Update['tDate']=@date("Y-m-d H:i:s");
      $Data_Update['iTripId']=$iTripId;
			$id = $obj->MySQLQueryPerform("driver_request",$Data_Update,'update',$where);
    }
    
    return $request_count;
  } 
?>
