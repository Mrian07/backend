<?php
	
	/* to clean function */
	
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
		if ($memberId != '') {
			
			$sql = "SELECT  `vLanguageCode` FROM  `member` WHERE iMemberId = '" . $memberId . "' AND `eStatus` = 'Active' ";
			$get_vLanguageCode = $obj->MySQLSelect($sql);
			
			if (count($get_vLanguageCode) > 0)
            $lcode = (isset($get_vLanguageCode[0]['vLanguageCode']) && $get_vLanguageCode[0]['vLanguageCode'] != '') ? $get_vLanguageCode[0]['vLanguageCode'] : '';
		}
		
		/* find default language of website set by admin */
		if ($lcode == '') {
			$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
			$default_label = $obj->MySQLSelect($sql);
			
			$lcode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode']) ? $default_label[0]['vCode'] : 'EN';
		}
		
		$lang_code = $lcode;
		$sql = "SELECT  `vLabel` ,  `vValue`  FROM  `language_label`  WHERE  `vCode` = '" . $lcode . "' ";
		$all_label = $obj->MySQLSelect($sql);
		
		for ($i = 0; $i < count($all_label); $i++) {
			$vLabel = $all_label[$i]['vLabel'];
			$vValue = $all_label[$i]['vValue'];
			$lang_label[$vLabel] = $vValue;
		}
		//echo "<pre>"; print_R($lang_label); echo "</pre>";
	}
	
	#function to get value from table can be use for any table - create to get value from configuration
	#$check_phone = get_value('configurations', 'vValue', 'vName', 'PHONE_VERIFICATION_REQUIRED');
	
	function get_value($table, $field_name, $condition_field = '', $condition_value = '', $setParams = '', $directValue = '') {
		global $obj;
		$returnValue = array();
		
		$where = ($condition_field != '') ? ' WHERE ' . clean($condition_field) : '';
		$where .= ($where != '' && $condition_value != '') ? ' = "' . clean($condition_value) . '"' : '';
		
		if ($table != '' && $field_name != '' && $where != '') {
			$sql = "SELECT $field_name FROM  $table $where";
			if ($setParams != '') {
				$sql .= $setParams;
			}
			$returnValue = $obj->MySQLSelect($sql);
			} else if ($table != '' && $field_name != '') {
			$sql = "SELECT $field_name FROM  $table";
			if ($setParams != '') {
				$sql .= $setParams;
			}
			$returnValue = $obj->MySQLSelect($sql);
		}
		if ($directValue == '') {
			return $returnValue;
			} else {
			$temp = $returnValue[0][$field_name];
			return $temp;
		}
	}
	
	function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
		else
        $ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	function createUserLog($userType, $eAutoLogin, $iMemberId, $deviceType) {
		global $generalobj, $obj;
		
		if (SITE_TYPE != "Demo") {
			return "";
		}
		$data['iMemberId'] = $iMemberId;
		$data['eMemberType'] = $userType;
		$data['eMemberLoginType'] = "AppLogin";
		$data['eDeviceType'] = $deviceType;
		$data['eAutoLogin'] = $eAutoLogin;
		$data['vIP'] = get_client_ip();
		
		$id = $obj->MySQLQueryPerform("member_log", $data, 'insert');
	}
	
	function dateDifference($date_1, $date_2, $differenceFormat = '%a') {
		$datetime1 = date_create($date_1);
		$datetime2 = date_create($date_2);
		
		$interval = date_diff($datetime1, $datetime2);
		
		return $interval->format($differenceFormat);
	}
	
	function getVehicleTypes($cityName = "") {
		global $obj;
		$sql_vehicle_type = "SELECT * FROM vehicle_type";
		
		$row_result_vehivle_type = $obj->MySQLSelect($sql_vehicle_type);
		return $row_result_vehivle_type;
	}
	
	function paymentimg($paymentm) {
		global $tconfig;
		if ($paymentm == "Card") {
			// return "webimages/icons/payment_images/ic_payment_type_card.png";
			return $tconfig["tsite_url"] . "webimages/icons/payment_images/ic_payment_type_card.png";
			} else {
			// return "webimages/icons/payment_images/ic_payment_type_cash.png";
			return $tconfig["tsite_url"] . "webimages/icons/payment_images/ic_payment_type_cash.png";
		}
	}
	
	function ratingmark($ratingval) {
		global $tconfig;
		$a = $ratingval;
		$b = explode('.', $a);
		$c = $b[0];
		
		$str = "";
		$count = 0;
		for ($i = 0; $i < 5; $i++) {
			if ($c > $i) {
				$str .= '<img src="' . $tconfig["tsite_url"] . 'webimages/icons/ratings_images/Star-Full.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
				} elseif ($a > $c && $count == 0) {
				$str .= '<img src="' . $tconfig["tsite_url"] . 'webimages/icons/ratings_images/Star-Half-Full.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
				$count = 1;
				} else {
				$str .= '<img src="' . $tconfig["tsite_url"] . 'webimages/icons/ratings_images/Star-blank.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
			}
		}
		return $str;
	}
	
	function getTripFare($Fare_data, $surgePrice) {
		global $generalobj, $obj;
		$ALLOW_SERVICE_PROVIDER_AMOUNT = $generalobj->getConfigurations("configurations", "ALLOW_SERVICE_PROVIDER_AMOUNT");
		
		$fAmount = 0;
		if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes") {
			$iDriverVehicleId = get_value('trips', 'iDriverVehicleId', 'iTripId', $Fare_data[0]['iTripId'], '', 'true');
			$iVehicleTypeId = get_value('trips', 'iVehicleTypeId', 'iTripId', $Fare_data[0]['iTripId'], '', 'true');
			
			$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $iVehicleTypeId . "'";
			$serviceProData = $obj->MySQLSelect($sqlServicePro);
			
			if (count($serviceProData) > 0) {
				$fAmount = $serviceProData[0]['fAmount'];
			}
		}
		if ($surgePrice >= 1) {
			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'] * $surgePrice;
			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'] * $surgePrice;
			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'] * $surgePrice;
			$Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'] * $surgePrice;
		}
		
		if ($Fare_data[0]['eFareType'] == 'Fixed') {
			$Fare_data[0]['fPricePerMin'] = 0;
			$Fare_data[0]['fPricePerKM'] = 0;
			if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes" && $fAmount != 0) {
				$Fare_data[0]['iBaseFare'] = $fAmount * $Fare_data[0]['iQty'];
				} else {
				$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'] * $Fare_data[0]['iQty'];
			}
			} else if ($Fare_data[0]['eFareType'] == 'Hourly') {
			$Fare_data[0]['iBaseFare'] = 0;
			$Fare_data[0]['fPricePerKM'] = 0;
			
			$totalHour = $Fare_data[0]['TripTimeMinutes'] / 60;
			$Fare_data[0]['TripTimeMinutes'] = $totalHour;
			
			if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes" && $fAmount != 0) {
				$Fare_data[0]['fPricePerMin'] = $fAmount;
				} else {
				$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerHour'];
			}
		}
		
		$Minute_Fare = round($Fare_data[0]['fPricePerMin'] * $Fare_data[0]['TripTimeMinutes'], 2);
		$Distance_Fare = round($Fare_data[0]['fPricePerKM'] * $Fare_data[0]['TripDistance'], 2);
		$iBaseFare = round($Fare_data[0]['iBaseFare'], 2);
		$fMaterialFee = round($Fare_data[0]['fMaterialFee'], 2);
		$fMiscFee = round($Fare_data[0]['fMiscFee'],2);
		$fDriverDiscount = round($Fare_data[0]['fDriverDiscount'],2);
		$fVisitFee= round($Fare_data[0]['fVisitFee'],2);
		//  print_r($Fare_data); 
		$total_fare = ($iBaseFare + $Minute_Fare + $Distance_Fare + $fMaterialFee + $fMiscFee + $fVisitFee) - $fDriverDiscount;
		//exit();
		$Commision_Fare = round((($total_fare * $Fare_data[0]['fCommision']) / 100), 2);
		
		$result['FareOfMinutes'] = $Minute_Fare;
		$result['FareOfDistance'] = $Distance_Fare;
		$result['FareOfCommision'] = $Commision_Fare;
		// $result['iBaseFare'] = $iBaseFare;
		$result['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];
		$result['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];
		$result['fCommision'] = $Fare_data[0]['fCommision'];
		$result['FinalFare'] = $total_fare;
		$result['iBaseFare'] = ($Fare_data[0]['eFareType'] == 'Fixed') ? 0 : $iBaseFare;
		$result['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];
		$result['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];
		$result['iMinFare'] = $Fare_data[0]['iMinFare'];
		
		return $result;
	}
	
	function calculateFare($totalTimeInMinutes_trip, $tripDistance, $vehicleTypeID, $iUserId, $priceRatio, $startDate = "", $endDate = "", $couponCode = "", $tripId, $fMaterialFee =0, $fMiscFee =0, $fDriverDiscount =0) {
		global $generalobj, $obj;
		$Fare_data = getVehicleFareConfig("vehicle_type", $vehicleTypeID);
		
		// $defaultCurrency = ($obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault='Yes'")[0]['vName']);
		$defaultCurrency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
		$fPickUpPrice = get_value('trips', 'fPickUpPrice', 'iTripId', $tripId, '', 'true');
		$fNightPrice = get_value('trips', 'fNightPrice', 'iTripId', $tripId, '', 'true');
		$iQty = get_value('trips', 'iQty', 'iTripId', $tripId, '', 'true');
		$eFareType = get_value('trips', 'eFareType', 'iTripId', $tripId, '', 'true');
		$surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);
		$fVisitFee = get_value('trips', 'fVisitFee','iTripId',$tripId,'','true');
		$tripTimeInMinutes = ($totalTimeInMinutes_trip != '') ? $totalTimeInMinutes_trip : 0;
		$fPricePerKM = getVehicleCountryUnit_PricePerKm($vehicleTypeID,$Fare_data[0]['fPricePerKM']);
		$fTollPrice = get_value('trips', 'fTollPrice','iTripId',$tripId,'','true');
		$eTollSkipped = get_value('trips', 'eTollSkipped','iTripId',$tripId,'','true');
    
		if($eTollSkipped == "Yes"){
			$fTollPrice = 0;
		}
		
		$Fare_data[0]['TripTimeMinutes'] = $tripTimeInMinutes;
		$Fare_data[0]['TripDistance'] = $tripDistance;
		$Fare_data[0]['iTripId'] = $tripId;
		$Fare_data[0]['eFareType'] = $eFareType;
		$Fare_data[0]['iQty'] = $iQty;
		$Fare_data[0]['fVisitFee'] =$fVisitFee;
		$Fare_data[0]['fMaterialFee']=$fMaterialFee;
		$Fare_data[0]['fMiscFee']=$fMiscFee;
		$Fare_data[0]['fDriverDiscount'] =$fDriverDiscount;
		$Fare_data[0]['fPricePerKM'] =$fPricePerKM;
		
		
		$result = getTripFare($Fare_data, "1");
		//$resultArr_Orig = getTripFare($Fare_data,"1");
		
		
		$total_fare = $result['FinalFare'];
		$fTripGenerateFare = $result['FinalFare'];
    $fTripGenerateFare_For_Commission = $result['FinalFare'];
    
     
		$iMinFare = $result['iMinFare'];
		
		if ($iMinFare > $fTripGenerateFare) {
			$MinFareDiff = $iMinFare - $total_fare;
			$total_fare = $iMinFare;
			$fTripGenerateFare = $iMinFare;
			$fTripGenerateFare_For_Commission = $iMinFare;
		} else {
			$MinFareDiff = "0";
		}
		
		if($fTollPrice > 0){
			$total_fare = $total_fare+$fTollPrice;
			$fTripGenerateFare = $fTripGenerateFare+$fTollPrice;
		}
	
		$fSurgePriceDiff = round(($fTripGenerateFare * $surgePrice) - $fTripGenerateFare, 2);
		$total_fare = $total_fare + $fSurgePriceDiff;
		$fTripGenerateFare = $fTripGenerateFare + $fSurgePriceDiff;
    //$result['fCommision'] = round((($fTripGenerateFare * $Fare_data[0]['fCommision']) / 100), 2);
    $fTripGenerateFare_For_Commission = $fTripGenerateFare_For_Commission+$fSurgePriceDiff;
    $result['fCommision'] = round((($fTripGenerateFare_For_Commission * $Fare_data[0]['fCommision']) / 100), 2);
		/* Check Coupon Code For Count Total Fare Start */
		$discountValue = 0;
		$discountValueType = "cash";
		if ($couponCode != '') {
			$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode, '', 'true');
			$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode, '', 'true');
		}
		if ($couponCode != '' && $discountValue != 0) {
			if ($discountValueType == "percentage") {
				$vDiscount = round($discountValue, 1) . ' ' . "%";
				$discountValue = round(($total_fare * $discountValue), 1) / 100;
				} else {
				$curr_sym = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');
				if ($discountValue > $total_fare) {
					$vDiscount = round($total_fare, 1) . ' ' . $curr_sym;
					} else {
					$vDiscount = round($discountValue, 1) . ' ' . $curr_sym;
				}
			}
			$fare = $total_fare - $discountValue;
			if ($fare < 0) {
				$fare = 0;
				$discountValue = $total_fare;
			}
			$total_fare = $fare;
			$Fare_data[0]['fDiscount'] = $discountValue;
			$Fare_data[0]['vDiscount'] = $vDiscount;
		}
		/* Check Coupon Code Total Fare  End */
		
		/* Check debit wallet For Count Total Fare  Start */
		$user_available_balance = $generalobj->get_user_available_balance($iUserId, "Rider");
		$user_wallet_debit_amount = 0;
		if ($total_fare > $user_available_balance) {
			$total_fare = $total_fare - $user_available_balance;
			$user_wallet_debit_amount = $user_available_balance;
			} else {
			$user_wallet_debit_amount = $total_fare;
			$total_fare = 0;
		}
		
		// Update User Wallet
		if ($user_wallet_debit_amount > 0) {
			$vRideNo = get_value('trips', 'vRideNo', 'iTripId', $tripId, '', 'true');
			$data_wallet['iUserId'] = $iUserId;
			$data_wallet['eUserType'] = "Rider";
			$data_wallet['iBalance'] = $user_wallet_debit_amount;
			$data_wallet['eType'] = "Debit";
			$data_wallet['dDate'] = date("Y-m-d H:i:s");
			$data_wallet['iTripId'] = $tripId;
			$data_wallet['eFor'] = "Booking";
			$data_wallet['ePaymentStatus'] = "Unsettelled";
			$data_wallet['tDescription'] = "Amount " . $user_wallet_debit_amount . " debited from your account for trip number #" . $vRideNo;
			
			$generalobj->InsertIntoUserWallet($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
			//$obj->MySQLQueryPerform("user_wallet",$data_wallet,'insert');
		}
		/* Check debit wallet For Count Total Fare  End */
		
		if ($Fare_data[0]['eFareType'] == 'Fixed') {
			$Fare_data[0]['iBaseFare'] = 0;
			} else {
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
	
	function calculateFareEstimate($totalTimeInMinutes_trip, $tripDistance, $vehicleTypeID, $iUserId, $priceRatio, $startDate = "", $endDate = "", $surgePrice = 1) {
		global $generalobj, $obj;
		$Fare_data = getVehicleFareConfig("vehicle_type", $vehicleTypeID);
		
		// $defaultCurrency = ($obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault='Yes'")[0]['vName']);
		$defaultCurrency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
		
		if ($surgePrice > 1) {
			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'] * $surgePrice;
			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'] * $surgePrice;
			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'] * $surgePrice;
			$Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'] * $surgePrice;
		}
		
		if ($Fare_data[0]['eFareType'] == 'Fixed') {
			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'];
			$Fare_data[0]['fPricePerMin'] = 0;
			$Fare_data[0]['fPricePerKM'] = 0;
		}
		
		$resultArr = $generalobj->getFinalFare($Fare_data[0]['iBaseFare'], $Fare_data[0]['fPricePerMin'], $totalTimeInMinutes_trip, $Fare_data[0]['fPricePerKM'], $tripDistance, $Fare_data[0]['fCommision'], $priceRatio, $defaultCurrency, $startDate, $endDate);
		
		$resultArr['FinalFare'] = $resultArr['FinalFare'] - $resultArr['FareOfCommision']; // Temporary set: Remove addition of commision from above function
		
		$Fare_data[0]['total_fare'] = $resultArr['FinalFare'];
		
		if ($Fare_data[0]['iMinFare'] > $Fare_data[0]['total_fare']) {
			$Fare_data[0]['MinFareDiff'] = $Fare_data[0]['iMinFare'] - $Fare_data[0]['total_fare'];
			$Fare_data[0]['total_fare'] = $Fare_data[0]['iMinFare'];
			} else {
			$Fare_data[0]['MinFareDiff'] = "0";
		}
		
		if ($Fare_data[0]['eFareType'] == 'Fixed') {
			$Fare_data[0]['iBaseFare'] = 0;
			} else {
			$Fare_data[0]['iBaseFare'] = $resultArr['iBaseFare'];
		}
		$Fare_data[0]['fPricePerMin'] = $resultArr['FareOfMinutes'];
		$Fare_data[0]['fPricePerKM'] = $resultArr['FareOfDistance'];
		$Fare_data[0]['fCommision'] = $resultArr['FareOfCommision'];
		return $Fare_data;
	}
	
	function calculateFareEstimateAll($totalTimeInMinutes_trip, $tripDistance, $vehicleTypeID, $iUserId, $priceRatio, $startDate = "", $endDate = "", $couponCode = "", $surgePrice = 1, $fMaterialFee =0, $fMiscFee =0, $fDriverDiscount =0, $DisplySingleVehicleFare = "",$eUserType = "Passenger", $iQty = 1) {
		global $generalobj, $obj,$tconfig;
		
		if ($eUserType == "Passenger") {
			$vCurrencyPassenger=get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId,'','true');
			$userlangcode = get_value("register_user", "vLang", "iUserId", $iUserId, '', 'true');
			$eUnit = getMemberCountryUnit($iUserId,"Passenger");
			}else{
			$vCurrencyPassenger=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iUserId,'','true');
			$userlangcode = get_value("register_driver", "vLang", "iDriverId", $iUserId, '', 'true');
			$eUnit = getMemberCountryUnit($iUserId,"Driver");
		}
		
		if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {
			$vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
		}
		$priceRatio=get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger,'','true');
		$vSymbol=get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger,'','true');
		
		
		
		if ($userlangcode == "" || $userlangcode == NULL) {
			$userlangcode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
		}
		$eUnit = getMemberCountryUnit($iUserId,"Passenger");
		$languageLabelsArr = getLanguageLabelsArr($userlangcode, "1");
		
		if($DisplySingleVehicleFare == ""){
			$sql_vehicle_type = "SELECT * FROM vehicle_type";
			$Fare_data = $obj->MySQLSelect($sql_vehicle_type);
			$result = array();
			for($i=0;$i<count($Fare_data);$i++){
				$fPickUpPrice = 1;
				$fNightPrice = 1;
				
				$data_surgePrice = checkSurgePrice($Fare_data[$i]['iVehicleTypeId'],"");
				
				if($data_surgePrice['Action'] == "0"){
					if($data_surgePrice['message'] == "LBL_PICK_SURGE_NOTE"){
						$fPickUpPrice=$data_surgePrice['SurgePriceValue'];
						}else{
						$fNightPrice=$data_surgePrice['SurgePriceValue'];
					}
				}
				$surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);
				$Fare_data[$i]['TripTimeMinutes'] = $totalTimeInMinutes_trip;
				$Fare_data[$i]['TripDistance'] = $tripDistance;
				//$result = getTripFare($Fare_data[$i], $surgePrice);
				/** calculate fare **/
				$Fare_data[$i]['iBaseFare'] = $Fare_data[$i]['iBaseFare'] * $surgePrice;
				$Fare_data[$i]['fPricePerMin'] = $Fare_data[$i]['fPricePerMin'] * $surgePrice;
				$Fare_data[$i]['fPricePerKM'] = getVehicleCountryUnit_PricePerKm($Fare_data[$i]['iVehicleTypeId'],$Fare_data[$i]['fPricePerKM']);
				$Fare_data[$i]['fPricePerKM'] = $Fare_data[$i]['fPricePerKM'] * $surgePrice;
				$Fare_data[$i]['iMinFare'] = $Fare_data[$i]['iMinFare'] * $surgePrice;
				$iBaseFare = $Fare_data[$i]['iBaseFare'];
				$fPricePerKM = $Fare_data[$i]['fPricePerKM'];
				$fPricePerMin = $Fare_data[$i]['fPricePerMin'];
				
				if ($Fare_data[$i]['eFareType'] == 'Fixed') {
					$Fare_data[$i]['fPricePerMin'] = 0;
					$Fare_data[$i]['fPricePerKM'] = 0;
					//$Fare_data[$i]['iBaseFare'] = $Fare_data[$i]['fFixedFare'] * $Fare_data[$i]['iQty'];
          $Fare_data[$i]['iBaseFare'] = $Fare_data[$i]['fFixedFare'] * $iQty;
					} else if ($Fare_data[$i]['eFareType'] == 'Hourly') {
					$Fare_data[$i]['iBaseFare'] = 0;
					$Fare_data[$i]['fPricePerKM'] = 0;
					
					$totalHour = $Fare_data[$i]['TripTimeMinutes'] / 60;
					$Fare_data[$i]['TripTimeMinutes'] = $totalHour;
					$Fare_data[$i]['fPricePerMin'] = $Fare_data[$i]['fPricePerHour'];
				}
				
				$Minute_Fare = round(($fPricePerMin * $totalTimeInMinutes_trip) * $priceRatio, 2);
				$Distance_Fare = round(($fPricePerKM * $tripDistance) * $priceRatio, 2);
				$iBaseFare = round($iBaseFare * $priceRatio, 2);
				$fMaterialFee = round($Fare_data[$i]['fMaterialFee'] * $priceRatio, 2);
				$fMiscFee = round($Fare_data[$i]['fMiscFee'] * $priceRatio,2);
				$fDriverDiscount = round($Fare_data[$i]['fDriverDiscount'] * $priceRatio,2);
				$fVisitFee= round($Fare_data[$i]['fVisitFee'] * $priceRatio,2);
				$total_fare = ($iBaseFare + $Minute_Fare + $Distance_Fare + $fMaterialFee + $fMiscFee + $fVisitFee) - $fDriverDiscount;
				$minimamfare = round($Fare_data[$i]['iMinFare'] * $priceRatio, 2);
				if($minimamfare > $total_fare){
					$fMinFareDiff = $minimamfare - $total_fare;
					$total_fare = $minimamfare;
					$Fare_data[$i]['FinalFare'] = $total_fare;
					}else{
					$fMinFareDiff = 0;
				}
				$Commision_Fare = round((($total_fare * $Fare_data[$i]['fCommision']) / 100), 2);
				
				/** calculate fare **/
				$Fare_data[$i]['FareOfMinutes'] = $Minute_Fare;
				$Fare_data[$i]['FareOfDistance'] = $Distance_Fare;
				$Fare_data[$i]['FareOfCommision'] = $Commision_Fare;
				$Fare_data[$i]['fPricePerMin'] = $Fare_data[$i]['fPricePerMin'];
				$Fare_data[$i]['fPricePerKM'] = $Fare_data[$i]['fPricePerKM'];
				$Fare_data[$i]['fCommision'] = $Fare_data[$i]['fCommision'];
				$Fare_data[$i]['FinalFare'] = $total_fare;
				$Fare_data[$i]['iBaseFare'] = ($Fare_data[$i]['eFareType'] == 'Fixed') ? 0 : $iBaseFare;
				$Fare_data[$i]['iMinFare'] = round( $Fare_data[$i]['iMinFare'] * $priceRatio, 2);
				if($Fare_data[$i]['eFareType'] == "Regular"){
					//$Fare_data[$i]['total_fare'] = $vSymbol." ".number_format($total_fare,2);
					$Fare_data[$i]['total_fare'] = $vSymbol." ".number_format($total_fare,2);
					}else{
					$Fare_data[$i]['total_fare'] = $vSymbol." ".number_format($Fare_data[$i]['FinalFare'],2);
				}
				$Fare_data[$i]['iBaseFare'] = $vSymbol." ".number_format($Fare_data[$i]['iBaseFare'],2);
				$Fare_data[$i]['fPricePerMin'] = $vSymbol." ".number_format(round($Fare_data[$i]['fPricePerMin'] * $priceRatio,1),2);
				$Fare_data[$i]['fPricePerKM'] = $vSymbol." ".number_format(round($Fare_data[$i]['fPricePerKM'] * $priceRatio,1),2);
				$Fare_data[$i]['fCommision'] = $vSymbol." ".number_format(round($Fare_data[$i]['fCommision'] * $priceRatio,1),2);
			}
			}else{
			$Fare_data = getVehicleFareConfig("vehicle_type", $vehicleTypeID);
			$fPickUpPrice = 1;
			$fNightPrice = 1;
      		
			$data_surgePrice = checkSurgePrice($Fare_data[0]['iVehicleTypeId'],"");
      		
			if($data_surgePrice['Action'] == "0"){
      			if($data_surgePrice['message'] == "LBL_PICK_SURGE_NOTE"){
      				$fPickUpPrice=$data_surgePrice['SurgePriceValue'];
      				}else{
      				$fNightPrice=$data_surgePrice['SurgePriceValue'];
				}
			}
			$surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);
			$Fare_data[0]['TripTimeMinutes'] = $totalTimeInMinutes_trip;
			$Fare_data[0]['TripDistance'] = $tripDistance;
			//$result = getTripFare($Fare_data[0], $surgePrice);
			/** calculate fare **/
			$Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'] * $surgePrice;
			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'] * $surgePrice;
			$Fare_data[0]['fPricePerKM'] = getVehicleCountryUnit_PricePerKm($Fare_data[0]['iVehicleTypeId'],$Fare_data[0]['fPricePerKM']);
			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'] * $surgePrice;
			$Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'] * $surgePrice;
			$iBaseFare = $Fare_data[0]['iBaseFare'];
			$fPricePerKM = $Fare_data[0]['fPricePerKM'];
			$fPricePerMin = $Fare_data[0]['fPricePerMin'];
			
			if ($Fare_data[0]['eFareType'] == 'Fixed') {
				$Fare_data[0]['fPricePerMin'] = 0;
				$Fare_data[0]['fPricePerKM'] = 0;
				//$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'] * $Fare_data[0]['iQty'];
					$Fare_data[0]['iBaseFare'] = $Fare_data[0]['fFixedFare'] * $iQty;
				} else if ($Fare_data[0]['eFareType'] == 'Hourly') {
				$Fare_data[0]['iBaseFare'] = 0;
				$Fare_data[0]['fPricePerKM'] = 0;
				$totalHour = $Fare_data[0]['TripTimeMinutes'] / 60;
				$Fare_data[0]['TripTimeMinutes'] = $totalHour;
				$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerHour'];
			}
			
			$Minute_Fare = round(($fPricePerMin * $totalTimeInMinutes_trip) * $priceRatio, 2);
			$Distance_Fare = round(($fPricePerKM * $tripDistance) * $priceRatio, 2);
			$iBaseFare = round($iBaseFare * $priceRatio, 2);
			$fMaterialFee = round($Fare_data[0]['fMaterialFee'] * $priceRatio, 2);
			$fMiscFee = round($Fare_data[0]['fMiscFee'] * $priceRatio,2);
			$fDriverDiscount = round($Fare_data[0]['fDriverDiscount'] * $priceRatio,2);
			$fVisitFee= round($Fare_data[0]['fVisitFee'] * $priceRatio,2);
			$total_fare = ($iBaseFare + $Minute_Fare + $Distance_Fare + $fMaterialFee + $fMiscFee + $fVisitFee) - $fDriverDiscount;
			// die;
			$minimamfare = round($Fare_data[0]['iMinFare'] * $priceRatio, 2);
			if($minimamfare > $total_fare){
				$fMinFareDiff = $minimamfare - $total_fare;
				$total_fare = $minimamfare;
				$Fare_data[0]['FinalFare'] = $total_fare;
				}else{
				$fMinFareDiff = 0;
			}
			$Commision_Fare = round((($total_fare * $Fare_data[0]['fCommision']) / 100), 2);
			
      ## Calculate for Discount ##
			//$fSurgePriceDiff = $farewithsurcharge - $minimamfare;
			$fSurgePriceDiff = round(($total_fare * $surgePrice) - $total_fare, 2);
			$SurgePriceFactor = strval($surgePrice);
			$total_fare = $total_fare+$fSurgePriceDiff;
			$discountValue = 0;
			$discountValueType = "cash"; 
			if($couponCode != ""){
				$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode, '', 'true');
				$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode, '', 'true');
				if($discountValueType == "percentage") {
					$vDiscount = round($discountValue, 1) . ' ' . "%";
					$discountValue = round(($total_fare * $discountValue), 1) / 100;
					}else{
					$curr_sym = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');
					if ($discountValue > $total_fare) {
						$vDiscount = round($total_fare, 1) . ' ' . $curr_sym;
						}else{
						$vDiscount = round($discountValue, 1) . ' ' . $curr_sym;
					}
				}
				$total_fare = $total_fare - $discountValue;
				$Fare_data[0]['fDiscount_fixed'] = $discountValue; 
				if ($total_fare < 0) {
					$total_fare = 0;
					//$discountValue = $total_fare;
				}
				if($Fare_data[0]['eFareType'] == "Regular"){
					$Fare_data[0]['fDiscount'] = $discountValue;
					$Fare_data[0]['vDiscount'] = $vDiscount;
					}else{
					$Fare_data[0]['fDiscount'] = $Fare_data[0]['fDiscount_fixed'];
					$Fare_data[0]['vDiscount'] = $vDiscount;
				}
			}
      ## Calculate for Discount ##
			/** calculate fare **/
			$Fare_data[0]['FareOfMinutes'] = $Minute_Fare;
			$Fare_data[0]['FareOfDistance'] = $Distance_Fare;
			$Fare_data[0]['FareOfCommision'] = $Commision_Fare;
			$Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];
			$Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];
			$Fare_data[0]['fCommision'] = $Fare_data[0]['fCommision'];
			$Fare_data[0]['FinalFare'] = $total_fare;
			$Fare_data[0]['iBaseFare'] = ($Fare_data[0]['eFareType'] == 'Fixed') ? 0 : $iBaseFare;
			$Fare_data[0]['iMinFare'] = round( $Fare_data[0]['iMinFare'] * $priceRatio, 2);
			if($Fare_data[0]['eFareType'] == "Regular"){
				//$Fare_data[0]['total_fare'] = $vSymbol." ".number_format($total_fare,2);
				$Fare_data[0]['total_fare'] = $vSymbol." ".number_format($total_fare,2);
				}else{
				$Fare_data[0]['total_fare'] = $vSymbol." ".number_format($Fare_data[0]['FinalFare'],2);
			}
			$Fare_data[0]['iBaseFare'] = $vSymbol." ".number_format($Fare_data[0]['iBaseFare'],2);
			$Fare_data[0]['fPricePerMin'] = $vSymbol." ".number_format(round($Fare_data[0]['fPricePerMin'] * $priceRatio,1),2);
			$Fare_data[0]['fPricePerKM'] = $vSymbol." ".number_format(round($Fare_data[0]['fPricePerKM'] * $priceRatio,1),2);
			$Fare_data[0]['fCommision'] = $vSymbol." ".number_format(round($Fare_data[0]['fCommision'] * $priceRatio,1),2);
			$vVehicleType = get_value('vehicle_type', "vVehicleType_" . $userlangcode, 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			$vVehicleTypeLogo = get_value('vehicle_type', "vLogo", 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			$iVehicleCategoryId = get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			$vVehicleCategoryData = get_value('vehicle_category', 'vLogo,vCategory_' . $userlangcode . ' as vCategory', 'iVehicleCategoryId', $iVehicleCategoryId);
			$Fare_data[0]['vVehicleCategory'] = $vVehicleCategoryData[0]['vCategory'];
			$vVehicleFare = get_value('vehicle_type','fFixedFare', 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			$eType = $Fare_data[0]['eFareType'];
			$tripFareDetailsArr = array();
			// echo "<pre>"; print_r($Fare_data); die;
			$i = 0;
			$countUfx = 0;
			if($eType == "UberX") {
				$tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $Fare_data[0]['vVehicleCategory'] . "-" . $vVehicleType;
				$countUfx = 1;
			}
			if ($eType == "Regular") {
				$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vSymbol .  formatNum($iBaseFare);
				if ($countUfx == 1) {
					$i++;
				}
				if($eUnit == "Miles"){
					$DisplayDistanceTxt = $languageLabelsArr['LBL_MILE_DISTANCE_TXT']; 
					}else{
					$DisplayDistanceTxt = $languageLabelsArr['LBL_KM_DISTANCE_TXT'];
				}
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $tripDistance . " " . $DisplayDistanceTxt . ")"] = $vSymbol . formatNum($Fare_data[0]['FareOfDistance']);
				$i++;
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $totalTimeInMinutes_trip . ")"] = $vSymbol . formatNum($Fare_data[0]['FareOfMinutes']);
				$i++;
				} else if ($eType == "Fixed") {
				$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =   ($Fare_data[0]['iQty'] > 1)?$Fare_data[0]['iQty'].' X '.$vSymbol . $vVehicleFare : $vSymbol . $vVehicleFare;
				if ($countUfx == 1) {
					$i++;
				}
				$total_fare = $vVehicleFare +  $Fare_data[0]['fVisitFee'] - $Fare_data[0]['fDiscount_fixed'];
				$Fare_data[0]['total_fare'] = $vSymbol." ".number_format(round($total_fare * $priceRatio,1),2);
				} else if ($eType == "Hourly") {
				$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $totalTimeInMinutes_trip . ")"] = $vSymbol . $Fare_data[0]['FareOfMinutes'];
				if ($countUfx == 1) {
					$i++;
				}
			}
			$fVisitFee = $Fare_data[0]['fVisitFee'];
			if ($fVisitFee > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = $vSymbol . $fVisitFee;
				$i++;
			}
			if ($fMaterialFee > 0) {                                                                     
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = $vSymbol . $fMaterialFee;
				$i++;
			}
			if ($fMiscFee > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = $vSymbol . $fMiscFee;
				$i++;
			}
			
			if ($fMinFareDiff > 0) {
				//$minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;
				$minimamfare = formatNum($minimamfare * $priceRatio);
				$tripFareDetailsArr[$i + 1][$vSymbol . $minimamfare . " " . $languageLabelsArr['LBL_MINIMUM']] = $vSymbol . formatNum($fMinFareDiff);
				$Fare_data[0]['TotalMinFare'] = $minimamfare;
				$i++;
			}
			
			if ($fSurgePriceDiff > 0) {
				$normalfare = $total_fare-$fSurgePriceDiff+$vDiscount;
				$normalfare = formatNum($normalfare * $priceRatio);
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = $vSymbol . $normalfare;
				$i++;
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = $vSymbol . formatNum($fSurgePriceDiff * $priceRatio);
				$i++;
			} 
			if ($fDriverDiscount > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = "- " . $vSymbol . $fDriverDiscount;
				$i++;
			}
			if ($vDiscount > 0) {
				$farediscount = $vSymbol." ".number_format(round($Fare_data[0]['fDiscount'] * $priceRatio,1),2);
				//$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = "- " . $vSymbol . $Fare_data[0]['fDiscount'];
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = "- " . $farediscount;
				$i++;
			} 
			
			$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $Fare_data[0]['total_fare'];
			//$Fare_data = array_merge($Fare_data[0], $tripFareDetailsArr);
			$Fare_data = $tripFareDetailsArr;
		}
		
		return $Fare_data;
	}
	
	function getVehicleFareConfig($tabelName, $vehicleTypeID) {
		global $obj;
		$sql = "SELECT * FROM `" . $tabelName . "` WHERE iVehicleTypeId='$vehicleTypeID'";
		$Data_fare = $obj->MySQLSelect($sql);
		
		return $Data_fare;
	}
	
	function processTripsLocations($tripId, $latitudes, $longitudes) {
		global $obj;
		$sql = "SELECT * FROM `trips_locations` WHERE iTripId = '$tripId'";
		$DataExist = $obj->MySQLSelect($sql);
		
		if (count($DataExist) > 0) {
			
			$latitudeList = $DataExist[0]['tPlatitudes'];
			$longitudeList = $DataExist[0]['tPlongitudes'];
			
			if ($latitudeList != '') {
				$data_latitudes = $latitudeList . ',' . $latitudes;
				} else {
				$data_latitudes = $latitudes;
			}
			
			if ($longitudeList != '') {
				$data_longitudes = $longitudeList . ',' . $longitudes;
				} else {
				$data_longitudes = $longitudes;
			}
			
			$where = " iTripId = '" . $tripId . "'";
			$Data_tripsLocations['tPlatitudes'] = $data_latitudes;
			$Data_tripsLocations['tPlongitudes'] = $data_longitudes;
			$id = $obj->MySQLQueryPerform("trips_locations", $Data_tripsLocations, 'update', $where);
			} else {
			
			$Data_trips_locations['iTripId'] = $tripId;
			$Data_trips_locations['tPlatitudes'] = $latitudes;
			$Data_trips_locations['tPlongitudes'] = $longitudes;
			
			$id = $obj->MySQLQueryPerform("trips_locations", $Data_trips_locations, 'insert');
		}
		return $id;
	}
	
	function calcluateTripDistance($tripId) {
		global $obj;
		$sql = "SELECT * FROM `trips_locations` WHERE iTripId = '$tripId'";
		$Data_tripsLocations = $obj->MySQLSelect($sql);
		
		$TotalDistance = 0;
		if (count($Data_tripsLocations) > 0) {
			$trip_path_latitudes = $Data_tripsLocations[0]['tPlatitudes'];
			$trip_path_longitudes = $Data_tripsLocations[0]['tPlongitudes'];
			
			$trip_path_latitudes = preg_replace("/[^0-9,.-]/", '', $trip_path_latitudes);
			$trip_path_longitudes = preg_replace("/[^0-9,.-]/", '', $trip_path_longitudes);
			
			$TripPathLatitudes = explode(",", $trip_path_latitudes);
			
			$TripPathLongitudes = explode(",", $trip_path_longitudes);
			
			for ($i = 0; $i < count($TripPathLatitudes) - 1; $i++) {
				$tempLat_current = $TripPathLatitudes[$i];
				$tempLon_current = $TripPathLongitudes[$i];
				$tempLat_next = $TripPathLatitudes[$i + 1];
				$tempLon_next = $TripPathLongitudes[$i + 1];
				
				if ($tempLat_current == '0.0' || $tempLon_current == '0.0' || $tempLat_next == '0.0' || $tempLon_next == '0.0' || $tempLat_current == '-180.0' || $tempLon_current == '-180.0' || $tempLat_next == '-180.0' || $tempLon_next == '-180.0') {
					continue;
				}
				
				$TempDistance = distanceByLocation($tempLat_current, $tempLon_current, $tempLat_next, $tempLon_next, "K");
				
				if (is_nan($TempDistance)) {
					$TempDistance = 0;
				}
				$TotalDistance += $TempDistance;
			}
		}
		
		return round($TotalDistance, 2);
	}
	
	function checkDistanceWithGoogleDirections($tripDistance, $startLatitude, $startLongitude, $endLatitude, $endLongitude, $isFareEstimate = "0", $vGMapLangCode = "") {
		global $generalobj, $obj;
		
		if ($vGMapLangCode == "" || $vGMapLangCode == NULL) {
			$vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');
			$vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];
		}
		
		$GOOGLE_API_KEY = $generalobj->getConfigurations("configurations", "GOOGLE_SEVER_GCM_API_KEY");
		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=" . $startLatitude . "," . $startLongitude . "&destination=" . $endLatitude . "," . $endLongitude . "&sensor=false&key=" . $GOOGLE_API_KEY . "&language=" . $vGMapLangCode;
		
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
		$distance_google_directions = ($jsondata->routes[0]->legs[0]->distance->value) / 1000;
		
		if ($isFareEstimate == "0") {
			$comparedDist = ($distance_google_directions * 85) / 100;
			
			if ($tripDistance > $comparedDist) {
				return $tripDistance;
				} else {
				return round($distance_google_directions, 2);
			}
			} else {
			$duration_google_directions = ($jsondata->routes[0]->legs[0]->duration->value) / 60;
			$sAddress = ($jsondata->routes[0]->legs[0]->start_address);
			$dAddress = ($jsondata->routes[0]->legs[0]->end_address);
			$steps = ($jsondata->routes[0]->legs[0]->steps);
			
			$returnArr['Time'] = $duration_google_directions;
			$returnArr['Distance'] = $distance_google_directions;
			$returnArr['SAddress'] = $sAddress;
			$returnArr['DAddress'] = $dAddress;
			$returnArr['steps'] = $steps;
			
			return $returnArr;
		}
	}
	
	function distanceByLocation($lat1, $lon1, $lat2, $lon2, $unit) {
		if ((($lat1 == $lat2) && ($lon1 == $lon2)) || ($lat1 == '' || $lon1 == '' || $lat2 == '' || $lon2 == '')) {
			return 0;
		}
		
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
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
	
	function getLanguageLabelsArr($lCode = '', $directValue = "") {
		global $obj;
		
		/* find default language of website set by admin */
		$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
		$default_label = $obj->MySQLSelect($sql);
		
		if ($lCode == '') {
			$lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode']) ? $default_label[0]['vCode'] : 'EN';
		}
		
		
		$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label`  WHERE  `vCode` = '" . $lCode . "' ";
		$all_label = $obj->MySQLSelect($sql);
		
		$x = array();
		for ($i = 0; $i < count($all_label); $i++) {
			$vLabel = $all_label[$i]['vLabel'];
			$vValue = $all_label[$i]['vValue'];
			$x[$vLabel] = $vValue;
		}
		
		
		$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label_other`  WHERE  `vCode` = '" . $lCode . "' ";
		$all_label = $obj->MySQLSelect($sql);
		
		for ($i = 0; $i < count($all_label); $i++) {
			$vLabel = $all_label[$i]['vLabel'];
			
			$vValue = $all_label[$i]['vValue'];
			$x[$vLabel] = $vValue;
		}
		
		$x['vCode'] = $lCode; // to check in which languge code it is loading
		
		if ($directValue == "") {
			$returnArr['Action'] = "1";
			$returnArr['LanguageLabels'] = $x;
			
			return $returnArr;
			} else {
			return $x;
		}
	}
	
	function sendEmeSms($toMobileNum, $message) {
		global $generalobj;
		$account_sid = $generalobj->getConfigurations("configurations", "MOBILE_VERIFY_SID_TWILIO");
		$auth_token = $generalobj->getConfigurations("configurations", "MOBILE_VERIFY_TOKEN_TWILIO");
		$twilioMobileNum = $generalobj->getConfigurations("configurations", "MOBILE_NO_TWILIO");
		
		$client = new Services_Twilio($account_sid, $auth_token);
		try {
			$sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $message);
			return 1;
			} catch (Services_Twilio_RestException $e) {
			return 0;
		}
	}
	
	function converToTz($time, $toTz, $fromTz) {
		$date = new DateTime($time, new DateTimeZone($fromTz));
		$date->setTimezone(new DateTimeZone($toTz));
		$time = $date->format('Y-m-d H:i:s');
		return $time;
	}
	
	/**
		* Sending Push Notification
	*/
	function send_notification($registatoin_ids, $message, $filterMsg = 0) {
		// include config
		// include_once './config.php';
		global $generalobj, $obj;
		$GOOGLE_API_KEY = $generalobj->getConfigurations("configurations", "GOOGLE_SEVER_GCM_API_KEY");
		$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
		// Set POST variables
		$url = 'https://android.googleapis.com/gcm/send';
		
		$fields = array(
        'registration_ids' => $registatoin_ids,
        'data' => $message,
		);
		
		$headers = array(
        'Authorization: key=' . $GOOGLE_API_KEY,
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
		$finalFields = json_encode($fields, JSON_UNESCAPED_UNICODE);
		
		
		if ($filterMsg == 1) {
			$finalFields = stripslashes(preg_replace("/[\n\r]/", "", $finalFields));
		}
		
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $finalFields);
		
		// Execute post
		$result = curl_exec($ch);
		
		// echo "<pre>"; print_r($result); die;
		
		if ($result === FALSE) {
			// die('Curl failed: ' . curl_error($ch));
			if($ENABLE_PUBNUB == "No"){ 
				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_SERVER_COMM_ERROR";
				$returnArr['ERROR'] = curl_error($ch);
				echo json_encode($returnArr);
				exit;
			}   
		}
		
		// Close connection
		curl_close($ch);
		return $result;
	}
	
	function sendApplePushNotification($PassengerToDriver = 0, $deviceTokens, $message, $alertMsg, $filterMsg,$fromDepart = '') {
		global $generalobj, $obj;
		
		$passphrase = $generalobj->getConfigurations("configurations", "IPHONE_PEM_FILE_PASSPHRASE");
		$APP_MODE = $generalobj->getConfigurations("configurations", "APP_MODE");
		$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
		
		$prefix = "";
		$url_apns = 'ssl://gateway.sandbox.push.apple.com:2195';
		if ($APP_MODE == "Production") {
			$prefix = "PRO_";
			$url_apns = 'ssl://gateway.push.apple.com:2195';
		}
		
		if ($PassengerToDriver == 1) {
			$name = $generalobj->getConfigurations("configurations", $prefix . "PARTNER_APP_IPHONE_PEM_FILE_NAME");
			} else {
			$name = $generalobj->getConfigurations("configurations", $prefix . "PASSENGER_APP_IPHONE_PEM_FILE_NAME");
		}
		$ctx = stream_context_create();
		
		if($fromDepart == 'admin') { $name = '../'.$name; }
		stream_context_set_option($ctx, 'ssl', 'local_cert', $name);
		
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		$fp = stream_socket_client(
		$url_apns, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
		
		// echo "deviceTokens => <pre>";
		// print_r($deviceTokens);
		// echo "<pre>"; print_r($fp); die;
		if (!$fp) {
			if($ENABLE_PUBNUB == "No"){
				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_SERVER_COMM_ERROR";
				$returnArr['ERROR'] = PHP_EOL;
				echo json_encode($returnArr);
				// exit;
				exit("Failed to connect: $err $errstr" . PHP_EOL);
			}   
		}
		
		// Create the payload body
		$body['aps'] = array(
        'alert' => $alertMsg,
        'content-available' => 1,
        'body' => $message,
        'sound' => 'default'
		);
		
		// Encode the payload as JSON
		$payload = json_encode($body, JSON_UNESCAPED_UNICODE);
		//        $payload= stripslashes(preg_replace("/[\n\r]/","",$payload));
		if ($filterMsg == 1) {
			$payload = stripslashes(preg_replace("/[\n\r]/", "", $payload));
		}
		
		for ($device = 0; $device < count($deviceTokens); $device++) {
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceTokens[$device]) . pack('n', strlen($payload)) . $payload;
			
			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));
		
		}
		// Close the connection to the server
		fclose($fp);
	}
	
	function getOnlineDriverArr($sourceLat, $sourceLon,$address_data=array(),$DropOff="No") {
		global $generalobj, $obj;
		
		$str_date = @date('Y-m-d H:i:s', strtotime('-1440 minutes'));
		$LIST_DRIVER_LIMIT_BY_DISTANCE = $generalobj->getConfigurations("configurations", "LIST_DRIVER_LIMIT_BY_DISTANCE");
		$DRIVER_REQUEST_METHOD = $generalobj->getConfigurations("configurations", "DRIVER_REQUEST_METHOD");
		$COMMISION_DEDUCT_ENABLE=$generalobj->getConfigurations("configurations","COMMISION_DEDUCT_ENABLE");
		$WALLET_MIN_BALANCE=$generalobj->getConfigurations("configurations","WALLET_MIN_BALANCE");

		$param = ($DRIVER_REQUEST_METHOD == "Time") ? "tOnline" : "tLastOnline";
		
		if($DropOff == "No"){
			$address_data['CheckAddress'] = $address_data['PickUpAddress']; 
			$allowed_ans = checkRestrictedArea($address_data,"No");
			$allowed_ans_drop = "Yes";
			}else{
			$address_data['CheckAddress'] = $address_data['PickUpAddress'];
			$allowed_ans = checkRestrictedArea($address_data,"No");
			$address_data['CheckAddress'] = $address_data['DropOffAddress'];
			$allowed_ans_drop = checkRestrictedArea($address_data,"Yes");  
		}
		
		
		if($allowed_ans == 'Yes' && $allowed_ans_drop == 'Yes') {
			$sql = "SELECT ROUND(( 3959 * acos( cos( radians(" . $sourceLat . ") )
			* cos( radians( vLatitude ) )
			* cos( radians( vLongitude ) - radians(" . $sourceLon . ") )
			+ sin( radians(" . $sourceLat . ") )
			* sin( radians( vLatitude ) ) ) ),2) AS distance, concat('+',register_driver.vCode,register_driver.vPhone) as vPhonenumber, register_driver.*  FROM `register_driver`
			WHERE (vLatitude != '' AND vLongitude != '' AND vAvailability = 'Available' AND vTripStatus != 'Active' AND eStatus='active' AND tLastOnline > '$str_date')
			HAVING distance < " . $LIST_DRIVER_LIMIT_BY_DISTANCE . " ORDER BY `register_driver`.`" . $param . "` ASC";
			
			$Data = $obj->MySQLSelect($sql);
			
			for($i=0;$i<count($Data);$i++){
				$Data[$i]['vPhone'] = $Data[$i]['vPhonenumber'];
				
				if($COMMISION_DEDUCT_ENABLE == 'Yes') {
					$user_available_balance = $generalobj->get_user_available_balance($Data[$i]['iDriverId'],"Driver");
					if($WALLET_MIN_BALANCE > $user_available_balance){
						$Data[$i]['REQUIRED_MINIMUM_BALNCE'] = "Yes";
					}else{
						$Data[$i]['REQUIRED_MINIMUM_BALNCE'] = "No";
					}
				}else{
					$Data[$i]['REQUIRED_MINIMUM_BALNCE'] = "No";
				}
			}
			
			$returnData['DriverList'] = $Data;
			$returnData['PickUpDisAllowed'] = $allowed_ans;
			$returnData['DropOffDisAllowed'] = $allowed_ans_drop;
		}else {
			$Data = array();
			$returnData['DriverList'] = $Data;
			$returnData['PickUpDisAllowed'] = $allowed_ans;
			$returnData['DropOffDisAllowed'] = $allowed_ans_drop;
		}
		return $returnData;
	}  
	
	
	
	
	
	function checkRestrictedArea($address_data,$DropOff){
		global $generalobj, $obj;
		$ssql = "";
		if($DropOff == "No"){
			$ssql.= " AND (eRestrictType = 'Pick Up' OR eRestrictType = 'All')";
			}else{
			$ssql.= " AND (eRestrictType = 'Drop Off' OR eRestrictType = 'All')";
		}
		if(!empty($address_data)){
			$pickaddrress = strtolower($address_data['CheckAddress']);
			$pickaddrress = preg_replace('/\d/', '', $pickaddrress);
			$pickaddrress = preg_replace('/\s+/', '', $pickaddrress); 
			//$pickArr = explode(',',$pickaddrress);
			$pickArr = array_map('trim',array_filter(explode(',',$pickaddrress)));
			$sqlaa = "SELECT cr.vCountry,ct.vCity,st.vState,replace(rs.vAddress, ' ','') as vAddress FROM `restricted_negative_area` AS rs
			LEFT JOIN country as cr ON cr.iCountryId = rs.iCountryId
			LEFT JOIN state as st ON st.iStateId = rs.iStateId
			LEFT JOIN city as ct ON ct.iCityId = rs.iCityId
			WHERE eType='Allowed'".$ssql;
			$allowed_data = $obj->MySQLSelect($sqlaa);
			$allowed_ans = 'No';
			if(!empty($allowed_data)){
				foreach($allowed_data as $rds){
					$alwd_country = $alwd_state = $alwd_city = $alwd_address = 'allowed';
					if($rds['vCountry'] != ""){
						//if($rds['vCountry'] == $address_data['countryId']){
						if(in_array(strtolower($rds['vCountry']),$pickArr)){
							$alwd_country = 'allowed';
							}else {
							$alwd_country = 'Disallowed';
						}
					}
					if($rds['vState'] != ""){
						if(in_array(strtolower($rds['vState']),$pickArr)){
							$alwd_state = 'allowed';
							}else {
							$alwd_state = 'Disallowed';
						}
					}
					if($rds['vCity'] != ""){
						if(in_array(strtolower($rds['vCity']),$pickArr)){
							$alwd_city = 'allowed';
							}else{
							$alwd_city = 'Disallowed';
						}
					}
					if($rds['vAddress'] != ""){
						if(strstr(strtolower($pickaddrress), strtolower($rds['vAddress']))){
							$alwd_address = 'allowed';
							}else{
							$alwd_address = 'Disallowed';
						}
					}
					if($alwd_country == 'allowed' && $alwd_state == 'allowed' && $alwd_city == 'allowed' && $alwd_address == 'allowed'){
						$allowed_ans = 'Yes';
						break;
					}
				}
			}    
			
			if($allowed_ans == 'No') {
				//$sqlas = "SELECT * FROM `restricted_negative_area` WHERE (iCountryId='".$address_data['countryId']."' OR iStateId='".$address_data['stateId']."' OR iCityId='".$address_data['cityId']."') AND eType='Disallowed' AND (eRestrictType = 'Pick Up' OR eRestrictType = 'All')";
				$sqlas = "SELECT cr.vCountry,ct.vCity,st.vState,replace(rs.vAddress, ' ','') as vAddress FROM `restricted_negative_area` AS rs
				LEFT JOIN country as cr ON cr.iCountryId = rs.iCountryId
                LEFT JOIN state as st ON st.iStateId = rs.iStateId
                LEFT JOIN city as ct ON ct.iCityId = rs.iCityId
				WHERE eType='Disallowed'".$ssql;
				$restricted_data = $obj->MySQLSelect($sqlas);
				$allowed_ans = 'Yes';
				if(!empty($restricted_data)){
					foreach($restricted_data as $rds){
						$alwd_country = $alwd_state = $alwd_city = $alwd_address = 'Disallowed';   
						if($rds['vCountry'] != ""){                                               
							if(in_array(strtolower($rds['vCountry']),$pickArr)){
								$alwd_country = 'Disallowed';
								}else {
								$alwd_country = 'allowed';
							}
						}
						if($rds['vState'] != ""){
							if(in_array(strtolower($rds['vState']),$pickArr)){
								$alwd_state = 'Disallowed';
								}else {
								$alwd_state = 'allowed';
							}  
						}
						if($rds['vCity'] != ""){
							if(in_array(strtolower($rds['vCity']),$pickArr)){
								$alwd_city = 'Disallowed';
								}else{
								$alwd_city = 'allowed';
							}   
						}
						if($rds['vAddress'] != ""){      
							if(strstr(strtolower($pickaddrress), strtolower($rds['vAddress']))){
								$alwd_address = 'Disallowed';
								}else{
								$alwd_address = 'allowed';
							} 
						}
						if($alwd_country == 'Disallowed' && $alwd_state == 'Disallowed' && $alwd_city == 'Disallowed' && $alwd_address == "Disallowed"){
							$allowed_ans = 'No';
							break;
						}
					}  
				}
			}
		}
		return $allowed_ans;  
	}
	
	function getAddressFromLocation($latitude, $longitude, $Google_Server_key) {
		$location_Address = "";
		
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latitude . "," . $longitude . "&key=" . $Google_Server_key;
		
		try {
			
			$jsonfile = file_get_contents($url);
			$jsondata = json_decode($jsonfile);
			$address = $jsondata->results[0]->formatted_address;
			
			$location_Address = $address;
			} catch (ErrorException $ex) {
			
			$returnArr['Action'] = "0";
			echo json_encode($returnArr);
			exit;
			// echo 'Site not reachable (' . $ex->getMessage() . ')';
		}
		
		if ($location_Address == "") {
			$returnArr['Action'] = "0";
			echo json_encode($returnArr);
			exit;
		}
		
		return $location_Address;
	}
	
	function getLanguageTitle($vLangCode) {
		global $obj;
		
		$sql = "SELECT vTitle FROM language_master WHERE vCode = '" . $vLangCode . "' ";
		$db_title = $obj->MySQLSelect($sql);
		
		return $db_title[0]['vTitle'];
	}
	
	function checkSurgePrice($vehicleTypeID, $selectedDateTime = "") {
		$ePickStatus = get_value('vehicle_type', 'ePickStatus', 'iVehicleTypeId', $vehicleTypeID, '', 'true');
		$eNightStatus = get_value('vehicle_type', 'eNightStatus', 'iVehicleTypeId', $vehicleTypeID, '', 'true');
		
		$fPickUpPrice = 1;
		$fNightPrice = 1;
		
		if ($selectedDateTime == "") {
			// $currentTime = @date("Y-m-d H:i:s");
			$currentTime = @date("H:i:s");
			$currentDay = @date("D");
			} else {
			// $currentTime = $selectedDateTime;
			$currentTime = @date("H:i:s", strtotime($selectedDateTime));
			$currentDay = @date("D", strtotime($selectedDateTime));
		}
		
		if ($ePickStatus == "Active" || $eNightStatus == "Active") {
			
			$startTime_str = "t" . $currentDay . "PickStartTime";
			$endTime_str = "t" . $currentDay . "PickEndTime";
			$price_str = "f" . $currentDay . "PickUpPrice";
			
			$pickStartTime = get_value('vehicle_type', $startTime_str, 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			$pickEndTime = get_value('vehicle_type', $endTime_str, 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			$fPickUpPrice = get_value('vehicle_type', $price_str, 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			
			$nightStartTime = get_value('vehicle_type', 'tNightStartTime', 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			$nightEndTime = get_value('vehicle_type', 'tNightEndTime', 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			$fNightPrice = get_value('vehicle_type', 'fNightPrice', 'iVehicleTypeId', $vehicleTypeID, '', 'true');
			
			if ($currentTime > $pickStartTime && $currentTime < $pickEndTime && $ePickStatus == "Active") {
				
				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_PICK_SURGE_NOTE";
				$returnArr['SurgePrice'] = $fPickUpPrice . "X";
				$returnArr['SurgePriceValue'] = $fPickUpPrice;
				} else if ($currentTime > $nightStartTime && $currentTime < $nightEndTime && $eNightStatus == "Active") {
				
				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_NIGHT_SURGE_NOTE";
				$returnArr['SurgePrice'] = $fNightPrice . "X";
				$returnArr['SurgePriceValue'] = $fNightPrice;
				} else {
				$returnArr['Action'] = "1";
			}
			} else {
			$returnArr['Action'] = "1";
		}
		
		return $returnArr;
	}
	
	function check_email_send($iDriverId, $tablename, $field) {
		global $obj, $generalobj;
		$sql = "SELECT * FROM " . $tablename . " WHERE " . $field . "= '" . $iDriverId . "'";
		$db_data = $obj->MySQLSelect($sql);
		//print_r($db_data);//exit;
		//$valid=0;
		if ($tablename == 'register_driver') {
			//echo "hi";exit;
			if ($db_data[0]['vNoc'] != NULL && $db_data[0]['vLicence'] != NULL && $db_data[0]['vCerti'] != NULL) {
				//global $generalobj;
				$maildata['USER'] = "Driver";
				$maildata['NAME'] = $db_data[0]['vName'];
				$maildata['EMAIL'] = $db_data[0]['vEmail'];
				$generalobj->send_email_user("PROFILE_UPLOAD", $maildata);
				//header("location:profile.php?success=1&var_msg=" . $var_msg);
				//return;
			}
			} else {
			if ($db_data[0]['vNoc'] != NULL && $db_data[0]['vCerti'] != NULL) {
				$maildata['USER'] = "Company";
				$maildata['NAME'] = $db_data[0]['vName'];
				$maildata['EMAIL'] = $db_data[0]['vEmail'];
				//var_dump($maildata);
				//var_dump(($generalobj));
				$generalobj->send_email_user("PROFILE_UPLOAD", $maildata);
			}
		}
		return true;
	}
	
	function checkmemberemailphoneverification($iMemberId, $user_type = "Passenger") {
		global $obj;
		if ($user_type == "Driver") {
			$EMAIL_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'DRIVER_EMAIL_VERIFICATION', '', 'true');
			$PHONE_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'DRIVER_PHONE_VERIFICATION', '', 'true');
			$eEmailVerified = get_value('register_driver', 'eEmailVerified', 'iDriverId', $iMemberId, '', 'true');
			$ePhoneVerified = get_value('register_driver', 'ePhoneVerified', 'iDriverId', $iMemberId, '', 'true');
			} else {
			$EMAIL_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'RIDER_EMAIL_VERIFICATION', '', 'true');
			$PHONE_VERIFICATION = get_value('configurations', 'vValue', 'vName', 'RIDER_PHONE_VERIFICATION', '', 'true');
			$eEmailVerified = get_value('register_user', 'eEmailVerified', 'iUserId', $iMemberId, '', 'true');
			$ePhoneVerified = get_value('register_user', 'ePhoneVerified', 'iUserId', $iMemberId, '', 'true');
		}
		
		$email = $EMAIL_VERIFICATION == "Yes" ? ($eEmailVerified == "Yes" ? "true" : "false") : "true";
		$phone = $PHONE_VERIFICATION == "Yes" ? ($ePhoneVerified == "Yes" ? "true" : "false") : "true";
		
		if ($email == "false" && $phone == "false") {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "DO_EMAIL_PHONE_VERIFY";
			echo json_encode($returnArr);
			exit;
			} else if ($email == "true" && $phone == "false") {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "DO_PHONE_VERIFY";
			echo json_encode($returnArr);
			exit;
			} else if ($email == "false" && $phone == "true") {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "DO_EMAIL_VERIFY";
			echo json_encode($returnArr);
			exit;
		}
	}
	
	function sendemailphoneverificationcode($iMemberId, $user_type = "Passenger", $VerifyType) {
		global $generalobj, $obj;
		if ($user_type == "Passenger") {
			$tblname = "register_user";
			$fields = 'iUserId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName';
			$condfield = 'iUserId';
			$vLangCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
			} else {
			$tblname = "register_driver";
			$fields = 'iDriverId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName';
			$condfield = 'iDriverId';
			$vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
		}
		if ($vLangCode == "" || $vLangCode == NULL) {
			$vLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
		}
		$languageLabelsArr = getLanguageLabelsArr($vLangCode, "1");
		$prefix = $languageLabelsArr['LBL_VERIFICATION_CODE_TXT'];
		
		$emailmessage = "";
		$phonemessage = "";
		if ($VerifyType == "email" || $VerifyType == "both") {
			$sql = "select $fields from $tblname where $condfield = '" . $iMemberId . "'";
			$db_member = $obj->MySQLSelect($sql);
			
			$Data_Mail['vEmailVarificationCode'] = $random = substr(number_format(time() * rand(), 0, '', ''), 0, 4);
			$Data_Mail['vEmail'] = isset($db_member[0]['vEmail']) ? $db_member[0]['vEmail'] : '';
			$vFirstName = isset($db_member[0]['vName']) ? $db_member[0]['vName'] : '';
			$vLastName = isset($db_member[0]['vLastName']) ? $db_member[0]['vLastName'] : '';
			$Data_Mail['vName'] = $vFirstName . " " . $vLastName;
			$Data_Mail['CODE'] = $Data_Mail['vEmailVarificationCode'];
			
			$sendemail = $generalobj->send_email_user("APP_EMAIL_VERIFICATION_USER", $Data_Mail);
			if ($sendemail) {
				$emailmessage = $Data_Mail['vEmailVarificationCode'];
				} else {
				$emailmessage = "LBL_EMAIL_VERIFICATION_FAILED_TXT";
			}
		}
		
		if ($VerifyType == "phone" || $VerifyType == "both") {
			$sql = "select $fields from $tblname where $condfield = '" . $iMemberId . "'";
			$db_member = $obj->MySQLSelect($sql);
			
			$mobileNo = $db_member[0]['vPhoneCode'] . $db_member[0]['vPhone'];
			$toMobileNum = "+" . $mobileNo;
			$verificationCode = mt_rand(1000, 9999);
			$message = $prefix . ' ' . $verificationCode;
			$result = sendEmeSms($toMobileNum, $message);
			if ($result == 0) {
				$phonemessage = "LBL_MOBILE_VERIFICATION_FAILED_TXT";
				} else {
				$phonemessage = $verificationCode;
			}
		}
		
		$returnArr['emailmessage'] = $emailmessage;
		$returnArr['phonemessage'] = $phonemessage;
		return $returnArr;
	}
	
	function getTripPriceDetails($iTripId, $iMemberId, $eUserType = "Passenger", $PAGE_MODE = "HISTORY") {
		global $obj, $generalobj, $tconfig;
		$returnArr = array();
		if ($eUserType == "Passenger") {
			$tblname = "register_user";
			$vLang = "vLang";
			$iUserId = "iUserId";
			$vCurrency = "vCurrencyPassenger";
			
			$currencycode = get_value("trips", $vCurrency, "iTripId", $iTripId, '', 'true');
			} else {
			$tblname = "register_driver";
			$vLang = "vLang";
			$iUserId = "iDriverId";
			$vCurrency = "vCurrencyDriver";
			
			$currencycode = get_value($tblname, $vCurrency, $iUserId, $iMemberId, '', 'true');
		}
		$userlangcode = get_value($tblname, $vLang, $iUserId, $iMemberId, '', 'true');
		if ($userlangcode == "" || $userlangcode == NULL) {
			$userlangcode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
		}
		
		$languageLabelsArr = getLanguageLabelsArr($userlangcode, "1");
		if ($currencycode == "" || $currencycode == NULL) {
			$currencycode = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
		}
		
		$currencySymbol = get_value('currency', 'vSymbol', 'vName', $currencycode, '', 'true');
		
		$sql = "SELECT * from trips WHERE iTripId = '" . $iTripId . "'";
		$tripData = $obj->MySQLSelect($sql);
		$priceRatio = $tripData[0]['fRatio_' . $currencycode];
		$iActive = $tripData[0]['iActive'];
		
		$returnArr = array_merge($tripData[0], $returnArr);
		if ($tripData[0]['iUserPetId'] > 0) {
			$petDetails_arr = get_value('user_pets', 'iPetTypeId,vTitle as PetName,vWeight as PetWeight, tBreed as PetBreed, tDescription as PetDescription', 'iUserPetId', $tripData[0]['iUserPetId'], '', '');
			} else {
			$petDetails_arr = array();
		}
		
		if (count($petDetails_arr) > 0) {
			$petTypeName = get_value('pet_type', 'vTitle_' . $userlangcode, 'iPetTypeId', $petDetails_arr[0]['iPetTypeId'], '', 'true');
			$returnArr['PetDetails']['PetName'] = $petDetails_arr[0]['PetName'];
			$returnArr['PetDetails']['PetWeight'] = $petDetails_arr[0]['PetWeight'];
			$returnArr['PetDetails']['PetBreed'] = $petDetails_arr[0]['PetBreed'];
			$returnArr['PetDetails']['PetDescription'] = $petDetails_arr[0]['PetDescription'];
			$returnArr['PetDetails']['PetTypeName'] = $petTypeName;
			} else {
			$returnArr['PetDetails']['PetName'] = '';
			$returnArr['PetDetails']['PetWeight'] = '';
			$returnArr['PetDetails']['PetBreed'] = '';
			$returnArr['PetDetails']['PetDescription'] = '';
			$returnArr['PetDetails']['PetTypeName'] = '';
		}
		
		/* User Wallet Information */
		$returnArr['UserDebitAmount'] = strval($tripData[0]['fWalletDebit']);
		/* User Wallet Information */
		
		$vVehicleType = get_value('vehicle_type', "vVehicleType_" . $userlangcode, 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');
		$vVehicleTypeLogo = get_value('vehicle_type', "vLogo", 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');
		$iVehicleCategoryId = get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');
		$vVehicleCategoryData = get_value('vehicle_category', 'vLogo,vCategory_' . $userlangcode . ' as vCategory', 'iVehicleCategoryId', $iVehicleCategoryId);
		$vVehicleFare = get_value('vehicle_type','fFixedFare', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');
		
		$TripTime = date('h:iA', strtotime($tripData[0]['tTripRequestDate']));
		$tTripRequestDateOrig = $tripData[0]['tTripRequestDate'];
		$tTripRequestDate = date('dS M Y \a\t h:i a', strtotime($tripData[0]['tTripRequestDate']));
		$tStartDate = $tripData[0]['tStartDate'];
		$tEndDate = $tripData[0]['tEndDate'];
		$totalTime = 0;
		if($tStartDate != '' && $tStartDate != '0000-00-00 00:00:00' && $tEndDate != '' && $tEndDate != '0000-00-00 00:00:00'){
			if ($tripData[0]['eFareType'] == "Hourly") {
				// $hours 		=	0; 
				// $minutes 	=	0;
				$totalSec 	=	0;
				$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$iTripId'";
				$db_tripTimes = $obj->MySQLSelect($sql22);
				
				foreach($db_tripTimes as $dtT){
					if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
						$totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
					}
				}
				
				$years = floor($totalSec / (365*60*60*24)); $months = floor(($totalSec - $years * 365*60*60*24) / (30*60*60*24));
				$days = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				$hours = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
				$minuts = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
				$seconds = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
				
				if ($hours > 0) {
					$totalTime = $hours.':'.$minuts.':'.$seconds;
					}if ($minuts > 0) {
					$totalTime = $minuts.':'.$seconds. " " . $languageLabelsArr['LBL_MINUTES_TXT'];
				}
				if ($totalTime < 1) {
					$totalTime = $seconds . " " . $languageLabelsArr['LBL_SECONDS_TXT'];
				}
				}else {
				$hours = dateDifference($tStartDate, $tEndDate, '%h');
				$minutes = dateDifference($tStartDate, $tEndDate, '%i');
				$seconds = dateDifference($tStartDate, $tEndDate, '%s');
				if ($hours > 0) {
					$totalTime = $hours * 60;
					}if ($minutes > 0) {
					$totalTime = $totalTime + $minutes;
				}
				$totalTime = $totalTime . ":" . $seconds . " " . $languageLabelsArr['LBL_MINUTES_TXT'];
				if ($totalTime < 1) {
					$totalTime = $seconds . " " . $languageLabelsArr['LBL_SECONDS_TXT'];
				}
			}
		}
		$returnArr['carTypeName'] = $vVehicleType;
		$returnArr['carImageLogo'] = $vVehicleTypeLogo;
		if ($eUserType == "Passenger") {
			$TripRating = get_value('ratings_user_driver', 'vRating1', 'iTripId', $iTripId, ' AND eUserType="Driver"', 'true');
			$returnArr['vDriverImage'] = get_value('register_driver', 'vImage', 'iTripId', $tripData[0]['iDriverId'], '', 'true');
			$driverDetailArr = get_value('register_driver', '*', 'iDriverId', $tripData[0]['iDriverId']);
			$eUnit = $tripData[0]['vCountryUnitRider'];
			} else {
			$TripRating = get_value('ratings_user_driver', 'vRating1', 'iTripId', $iTripId, ' AND eUserType="Passenger"', 'true');
			$passgengerDetailArr = get_value('register_user', '*', 'iUserId', $tripData[0]['iUserId']);
			//$eUnit = $tripData[0]['vCountryUnitDriver'];
			$eUnit = $tripData[0]['vCountryUnitRider'];
		}
		
		if($eUnit == "Miles"){
			$DisplayDistanceTxt = $languageLabelsArr['LBL_MILE_DISTANCE_TXT']; 
			}else{
			$DisplayDistanceTxt = $languageLabelsArr['LBL_KM_DISTANCE_TXT'];
		}
		
		if ($TripRating == "" || $TripRating == NULL) {
			$TripRating = "0";
		}
		
		$iFare = $tripData[0]['iFare'];
		//$iFare = $tripData[0]['iFare']+$tripData[0]['fTollPrice'];
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
		$fVisitFee = $tripData[0]['fVisitFee'] * $priceRatio;
		$fMaterialFee = $tripData[0]['fMaterialFee'] * $priceRatio;
		$fMiscFee = $tripData[0]['fMiscFee'] * $priceRatio;
		$fDriverDiscount = $tripData[0]['fDriverDiscount'] * $priceRatio;
		$vVehicleFare = $vVehicleFare * $priceRatio;
		$fCancelPrice = $tripData[0]['fCancellationFare'] * $priceRatio;
		$fTollPrice = $tripData[0]['fTollPrice'] * $priceRatio;
		if($fTollPrice > 0){
			$eTollSkipped = $tripData[0]['eTollSkipped'];
			}else{
			$eTollSkipped = "Yes";
		}
		
		$returnArr['vVehicleType'] = $vVehicleType;
		$returnArr['vVehicleCategory'] = $vVehicleCategoryData[0]['vCategory'];
		$returnArr['TripTime'] = $TripTime;
		$returnArr['ConvertedTripRequestDate'] = $tTripRequestDate;
		$returnArr['FormattedTripDate'] = $tTripRequestDate;
		$returnArr['tTripRequestDateOrig'] = $tTripRequestDateOrig;
		$returnArr['tTripRequestDate'] = $tTripRequestDate;
		$returnArr['TripTimeInMinutes'] = $totalTime;
		$returnArr['TripRating'] = $TripRating;
		$returnArr['CurrencySymbol'] = $currencySymbol;
		$returnArr['TripFare'] = formatNum($iFare * $priceRatio);
		$returnArr['iTripId'] = $tripData[0]['iTripId'];
		$returnArr['vTripPaymentMode'] = $tripData[0]['vTripPaymentMode'];
		$returnArr['eType'] = $tripData[0]['eType'];
		if ($tripData[0]['vBeforeImage'] != "") {
			$returnArr['vBeforeImage'] = $tconfig['tsite_upload_trip_images'] . $tripData[0]['vBeforeImage'];
		}
		if ($tripData[0]['eType'] == "UberX") {
			$returnArr['vLogoVehicleCategoryPath'] = $tconfig['tsite_upload_images_vehicle_category'] . "/" . $iVehicleCategoryId . "/";
			$returnArr['vLogoVehicleCategory'] = $vVehicleCategoryData[0]['vLogo'];
			} else {
			$returnArr['vLogoVehicleCategory'] = "";
			$returnArr['vLogoVehicleCategoryPath'] = "";
		}
		if ($tripData[0]['vAfterImage'] != "") {
			$returnArr['vAfterImage'] = $tconfig['tsite_upload_trip_images'] . $tripData[0]['vAfterImage'];
		}
		$originalFare = $iFare;
		if ($eUserType == "Passenger") {
			$iFare = $iFare;
			} else {
			//$iFare = $tripData[0]['fTripGenerateFare'] - $fCommision;
			//$iFare = $tripData[0]['fTripGenerateFare'] + $tripData[0]['fTipPrice'] - $fCommision;
      // $iFare = $tripData[0]['fTripGenerateFare'] + $tripData[0]['fTipPrice'] - $tripData[0]['fTollPrice'] - $fCommision;
      $iFare = $tripData[0]['fTripGenerateFare'] + $tripData[0]['fTipPrice'] - $fCommision;
		}
		$surgePrice = 1;
		if ($tripData[0]['fPickUpPrice'] > 1) {
			$surgePrice = $tripData[0]['fPickUpPrice'];
			} else {
			$surgePrice = $tripData[0]['fNightPrice'];
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
		$returnArr['fCommision'] = formatNum($fCommision * $priceRatio);
		$returnArr['fDistance'] = formatNum($fDistance);
		$returnArr['fDiscount'] = formatNum($fDiscount * $priceRatio);
		$returnArr['fMinFareDiff'] = formatNum($fMinFareDiff);
		$returnArr['fWalletDebit'] = formatNum($fWalletDebit * $priceRatio);
		$returnArr['fSurgePriceDiff'] = formatNum($fSurgePriceDiff);
		$returnArr['fTripGenerateFare'] = formatNum($fTripGenerateFare);
		if($eTollSkipped == "No"){
			$returnArr['fTollPrice'] = formatNum($fTollPrice);   
		}
		if($fTipPrice > 0){
			$returnArr['fTipPrice'] = $currencySymbol.formatNum($fTipPrice);
		}
		$returnArr['SurgePriceFactor'] = $SurgePriceFactor;
		$returnArr['fVisitFee'] = formatNum($fVisitFee);
		$returnArr['fMaterialFee'] = formatNum($fMaterialFee);
		$returnArr['fMiscFee'] = formatNum($fMiscFee);
		$returnArr['fDriverDiscount'] = formatNum($fDriverDiscount);
		$returnArr['fCancelPrice'] = formatNum($fCancelPrice);
		// echo "<pre>"; print_r($tripData); die;
		
		$iDriverId = $tripData[0]['iDriverId'];
		$driverDetails = get_value('register_driver', '*', 'iDriverId', $iDriverId);
		$driverDetails[0]['vImage'] = ($driverDetails[0]['vImage'] != "" && $driverDetails[0]['vImage'] != "NONE") ? "3_" . $driverDetails[0]['vImage'] : "";
		$driverDetails[0]['vPhone'] = '+'.$driverDetails[0]['vCode'].$driverDetails[0]['vPhone'];
		$returnArr['DriverDetails'] = $driverDetails[0];
		
		$iUserId = $tripData[0]['iUserId'];
		$passengerDetails = get_value('register_user', '*', 'iUserId', $iUserId);
		$passengerDetails[0]['vImgName'] = ($passengerDetails[0]['vImgName'] != "" && $passengerDetails[0]['vImgName'] != "NONE") ? "3_" . $passengerDetails[0]['vImgName'] : "";
		$passengerDetails[0]['vPhone'] = '+'.$passengerDetails[0]['vPhoneCode'].$passengerDetails[0]['vPhone'];
		$returnArr['PassengerDetails'] = $passengerDetails[0];
		
		$iDriverVehicleId = $tripData[0]['iDriverVehicleId'];
		$sql = "SELECT make.vMake, model.vTitle, dv.*  FROM `driver_vehicle` dv, make, model WHERE dv.iDriverVehicleId='" . $iDriverVehicleId . "' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId`";
		$vehicleDetailsArr = $obj->MySQLSelect($sql);
		$vehicleDetailsArr[0]['vModel'] = $vehicleDetailsArr[0]['vTitle'];
		if ($eUserType == "Passenger" && $tripData[0]['eType'] == "UberX") {
			
			$ALLOW_SERVICE_PROVIDER_AMOUNT = $generalobj->getConfigurations("configurations", "ALLOW_SERVICE_PROVIDER_AMOUNT");
			
			
			$fAmount = "0";
			if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes") {
				
				
				$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $tripData[0]['iVehicleTypeId'] . "'";
				$serviceProData = $obj->MySQLSelect($sqlServicePro);
				
				$vehicleTypeData = get_value('vehicle_type', 'eFareType,fPricePerHour,fFixedFare', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId']);
				if ($vehicleTypeData[0]['eFareType'] == "Fixed") {
					$fAmount = $currencySymbol . $vehicleTypeData[0]['fFixedFare'];
					} else if ($vehicleTypeData[0]['eFareType'] == "Hourly") {
					$fAmount = $currencySymbol . $vehicleTypeData[0]['fPricePerHour'] . "/hour";
				}
				
				if (count($serviceProData) > 0) {
					$fAmount = $serviceProData[0]['fAmount'];
					if ($vehicleTypeData[0]['eFareType'] == "Fixed") {
						$fAmount = $currencySymbol . $fAmount;
						} else if ($vehicleTypeData[0]['eFareType'] == "Hourly") {
						$fAmount = $currencySymbol . $fAmount . "/hour";
					}
				}
				
				$vehicleDetailsArr[0]['fAmount'] = strval($fAmount);
			}
		}
		$returnArr['DriverCarDetails'] = $vehicleDetailsArr[0];
		
		if ($eUserType == "Passenger") {
			$tripFareDetailsArr = array();
			if($fCancelPrice > 0){
				$tripFareDetailsArr[0][$languageLabelsArr['LBL_CANCELLATION_FEE']] = $currencySymbol.$returnArr['fCancelPrice'];
                $tripFareDetailsArr[1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $currencySymbol.$returnArr['fCancelPrice'];
				}else{
                $i = 0;
                $countUfx = 0;
                if ($tripData[0]['eType'] == "UberX") {
                    $tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $returnArr['vVehicleCategory'] . "-" . $returnArr['vVehicleType'];
                    $countUfx = 1;
				}
				
                if ($tripData[0]['eFareType'] == "Regular") {
                    //$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType . " " . $currencySymbol . $returnArr['iBaseFare'];
                    $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = ($iActive != "Canceled")? $currencySymbol . $returnArr['iBaseFare']:"--";
                    if ($countUfx == 1) {
                        $i++;
					}
                    //$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $languageLabelsArr['LBL_KM_DISTANCE_TXT'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
                    $i++;
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
                    $i++;
					} else if ($tripData[0]['eFareType'] == "Fixed") {
					//  $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] = $currencySymbol . ($fTripGenerateFare - $fSurgePriceDiff - $fMinFareDiff);
					$SERVICE_COST = ($tripData[0]['iQty'] > 1)?$tripData[0]['iQty'].' X '.$currencySymbol . $vVehicleFare : $currencySymbol . $vVehicleFare;
					$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =  ($iActive != "Canceled")?$SERVICE_COST:"--";
                    if ($countUfx == 1) {
                        $i++;
					}
					} else if ($tripData[0]['eFareType'] == "Hourly") {
                    $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
					
                    if ($countUfx == 1) {
                        $i++;
					}
				}
				
                if ($fVisitFee > 0) {
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fVisitFee']:"--";
                    $i++;
				}
                if ($fMaterialFee > 0) {
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMaterialFee']:"--";
                    $i++;
				}
                if ($fMiscFee > 0) {
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMiscFee']:"--";
                    $i++;
				}
                if ($fDriverDiscount > 0) {
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDriverDiscount']:"--";
                    $i++;
				}
				
				
                // print_r($tripFareDetailsArr);exit;
                // echo $tripData[0]['eFareType'];exit;
                if ($fMinFareDiff > 0) {
                    $minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;
                    $minimamfare = formatNum($minimamfare);
                    $tripFareDetailsArr[$i + 1][$currencySymbol . $minimamfare . " " . $languageLabelsArr['LBL_MINIMUM']] = $currencySymbol . $returnArr['fMinFareDiff'];
                    $returnArr['TotalMinFare'] = ($iActive != "Canceled")?$minimamfare:"--";
                    $i++;
				}
                if ($fSurgePriceDiff > 0) {
                    $normalfare = $fTripGenerateFare - $fSurgePriceDiff;
                    $normalfare = formatNum($normalfare);
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $normalfare:"--";
                    $i++;
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";
                    $i++;
				}
                if($eTollSkipped == "No"){
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TOLL_PRICE_TOTAL']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTollPrice']:"--";
                    $i++;   
				}
                if ($fDiscount > 0) {
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                    $i++;
				}
                if ($fWalletDebit > 0) {
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                    $i++;
				}
				
                /*if ($fTipPrice > 0) {
                    $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIP_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTipPrice']:"--";
                    $i++;
				} */
                
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iFare']:"--";
			}    
			$returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";
			$returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;
			$FareDetailsArr = array();
			foreach ($tripFareDetailsArr as $data) {
				$FareDetailsArr = array_merge($FareDetailsArr, $data);
			}
			$returnArr['FareDetailsArr'] = $FareDetailsArr;
			$returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;
			if ($tripData[0]['eType'] == "UberX") {
				array_splice($returnArr['HistoryFareDetailsNewArr'], 0, 1);
			}
			} else {
			$tripFareDetailsArr = array();
			$i = 0;
			$countUfx = 0;
			if ($tripData[0]['eType'] == "UberX") {
				$tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $returnArr['vVehicleCategory'] . "-" . $returnArr['vVehicleType'];
				$countUfx = 1;
			}
			
			if ($tripData[0]['eFareType'] == "Regular") {
				//$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType . " " . $currencySymbol . $returnArr['iBaseFare'];
				$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iBaseFare']:"--";
				if ($countUfx == 1) {
					$i++;
				}
				//$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $languageLabelsArr['LBL_KM_DISTANCE_TXT'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
				$i++;
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
				$i++;
				} else if ($tripData[0]['eFareType'] == "Fixed") {
				//$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] = $currencySymbol . ($fTripGenerateFare - $fSurgePriceDiff - $fMinFareDiff);
                $SERVICE_COST = ($tripData[0]['iQty'] > 1)?$tripData[0]['iQty'].' X '.$currencySymbol . $vVehicleFare : $currencySymbol . $vVehicleFare;
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =   ($iActive != "Canceled")?$SERVICE_COST:"--";                    
				if ($countUfx == 1) {
					$i++;
				}
				} else if ($tripData[0]['eFareType'] == "Hourly") {
				$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
				
				if ($countUfx == 1) {
					$i++;
				}
			}
			
			if ($fVisitFee > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fVisitFee']:"--";
				$i++;
			}
			if ($fMaterialFee > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMaterialFee']:"--";
				$i++;
			}
			if ($fMiscFee > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMiscFee']:"--";
				$i++;
			}
			if ($fDriverDiscount > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDriverDiscount']:"--";
				$i++;
			}
			
			if ($fMinFareDiff > 0) {
				$minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;
				$minimamfare = formatNum($minimamfare);
				$tripFareDetailsArr[$i + 1][$currencySymbol . $minimamfare . " " . $languageLabelsArr['LBL_MINIMUM']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMinFareDiff']:"--";
				$returnArr['TotalMinFare'] = $minimamfare;
				$i++;
			}
			if ($fSurgePriceDiff > 0) {
				$normalfare = $fTripGenerateFare - $fSurgePriceDiff;
				$normalfare = formatNum($normalfare);
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $normalfare:"--";
				$i++;
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";
				$i++;
			}
			if($eTollSkipped == "No"){
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TOLL_PRICE_TOTAL']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTollPrice']:"--";
				$i++;   
			}
			if($PAGE_MODE == "DISPLAY"){
				if ($fDiscount > 0) {
					$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
					$i++;
				}
				if ($fWalletDebit > 0) {
					$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
					$i++;
				}
			}
			/* if ($fDiscount > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
				$i++;
			}
			if ($fWalletDebit > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
				$i++;
			} */
			
			/*if ($fTipPrice > 0) {
				$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIP_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTipPrice']:"--";
				$i++;
			} */
			$returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";
			$returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;
			$FareDetailsArr = array();
			foreach ($tripFareDetailsArr as $data) {
				$FareDetailsArr = array_merge($FareDetailsArr, $data);
			}
			$returnArr['FareDetailsArr'] = $FareDetailsArr;
			$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_Commision']] = ($iActive != "Canceled")?"-" . $currencySymbol . $returnArr['fCommision']:"--";
			$i++;
			$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_EARNED_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iFare']:"--";
			$returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;
			
			if ($tripData[0]['eType'] == "UberX") {
				array_splice($returnArr['HistoryFareDetailsNewArr'], 0, 1);
			}
		}
		$returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";
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
	 
	function formatNum($number) {
		return strval(number_format($number, 2));
	}
	
	function getUserRatingAverage($iMemberId, $eUserType = "Passenger") {
		global $obj, $generalobj;
		if ($eUserType == "Passenger") {
			$iUserId = "iDriverId";
			$checkusertype = "Passenger";
			} else {
			$iUserId = "iUserId";
			$checkusertype = "Driver";
		}
		
		$usertotaltrips = get_value("trips", "iTripId", $iUserId, $iMemberId);
		if (count($usertotaltrips) > 0) {
			for ($i = 0; $i < count($usertotaltrips); $i++) {
				$iTripId .= $usertotaltrips[$i]['iTripId'] . ",";
			}
			
			$iTripId_str = substr($iTripId, 0, -1);
			//echo  $iTripId_str;exit;
			$sql = "SELECT count(iRatingId) as ToTalTrips, SUM(vRating1) as ToTalRatings from ratings_user_driver WHERE iTripId IN (" . $iTripId_str . ") AND eUserType = '" . $checkusertype . "'";
			$result_ratings = $obj->MySQLSelect($sql);
			$ToTalTrips = $result_ratings[0]['ToTalTrips'];
			$ToTalRatings = $result_ratings[0]['ToTalRatings'];
			$average_rating = round($ToTalRatings / $ToTalTrips, 2);
			} else {
			$average_rating = 0;
		}
		return $average_rating;
	}
	
	function deliverySmsToReceiver($iTripId) {
		global $obj, $generalobj, $tconfig;
		
		$sql = "SELECT * from trips WHERE iTripId = '" . $iTripId . "'";
		$tripData = $obj->MySQLSelect($sql);
		
		$SenderName = get_value("register_user", "vName,vLastName", "iUserId", $tripData[0]['iUserId']);
		$SenderName = $SenderName[0]['vName'] . " " . $SenderName[0]['vLastName'];
		$delivery_address = $tripData[0]['tDaddress'];
		$vDeliveryConfirmCode = $tripData[0]['vDeliveryConfirmCode'];
		$page_link = $tconfig['tsite_url'] . "trip_tracking.php?iTripId=" . $iTripId;
		$page_link = get_tiny_url($page_link);
		
		$message_deliver = $SenderName . " has send you the parcel on below address." . $delivery_address . ". Upon Receiving the parcel, please provide below verification code to sender. Verification Code: " . $vDeliveryConfirmCode . ". click on link below to track your parcel. " . $page_link;
		
		//echo $message_deliver;exit;
		return $message_deliver;
	}
	
	function get_tiny_url($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, 'http://tinyurl.com/api-create.php?url=' . $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	function addToUserRequest($iUserId, $iDriverId, $message, $iMsgCode) {
		global $obj;
		$data['iUserId'] = $iUserId;
		$data['iDriverId'] = $iDriverId;
		$data['tMessage'] = $message;
		$data['iMsgCode'] = $iMsgCode;
		$data['dAddedDate'] = @date("Y-m-d H:i:s");
		
		$dataId = $obj->MySQLQueryPerform("passenger_requests", $data, 'insert');
		
		return $dataId;
	}
	
	function addToDriverRequest($iDriverId, $iUserId, $iTripId, $eStatus) {
		global $obj;
		$data['iDriverId'] = $iDriverId;
		$data['iUserId'] = $iUserId;
		$data['iTripId'] = $iTripId;
		$data['eStatus'] = $eStatus;
		$data['tDate'] = @date("Y-m-d H:i:s");
		
		$id = $obj->MySQLQueryPerform("driver_request", $data, 'insert');
		
		return $id;
	}
	
	function UpdateDriverRequest($iDriverId, $iUserId, $iTripId, $eStatus) {
		global $obj;
		
		$sql = "SELECT * FROM `driver_request` WHERE iDriverId = '" . $iDriverId . "' AND iUserId = '" . $iUserId . "' AND iTripId = '0' ORDER BY iDriverRequestId DESC LIMIT 0,1";
		$db_sql = $obj->MySQLSelect($sql);
		$request_count = count($db_sql);
		
		if ($request_count > 0) {
			$where = " iDriverRequestId = '" . $db_sql[0]['iDriverRequestId'] . "'";
			$Data_Update['eStatus'] = $eStatus;
			$Data_Update['tDate'] = @date("Y-m-d H:i:s");
			$Data_Update['iTripId'] = $iTripId;
			$id = $obj->MySQLQueryPerform("driver_request", $Data_Update, 'update', $where);
		}
		
		return $request_count;
	}
	
	function getDriverStatus($driverId = '') {
		global $generalobj,$obj;
		
		$vLangCode=get_value('register_driver', 'vLang', 'iDriverId',$driverId,'','true');
		if($vLangCode == "" || $vLangCode == NULL){
			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		//$userwaitinglabel = $languageLabelsArr['LBL_TRIP_USER_WAITING'];
		
		$sql1= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='".$driverId."' ) dl on dl.doc_masterid=dm.doc_masterid  
		where dm.doc_usertype='driver' and dm.status='Active' ";
		$db_document = $obj->MySQLSelect($sql1);
		if(count($db_document) > 0){
			for($i=0;$i<count($db_document);$i++){
				if($db_document[$i]['doc_file'] == ""){
					$returnArr['Action'] ="0";
					$returnArr['message'] ="Please upload your ". $db_document[$i]['doc_name'];
					echo json_encode($returnArr);exit; 
				}
				if($db_document[$i]['status'] != "Active"){
					$returnArr['Action'] ="0";
					if($db_document[$i]['status'] == "Inactive"){
						$returnArr['message'] ="Please activate your ". $db_document[$i]['doc_name'];
						echo json_encode($returnArr);exit;
					}
					if($db_document[$i]['status'] == "Deleted"){
						$returnArr['message'] ="Current status is deleted of your". $db_document[$i]['doc_name'];
						echo json_encode($returnArr);exit;
					}
				}
			}
		}
		
		$sql = "SELECT iDriverVehicleId from driver_vehicle WHERE iDriverId = '".$driverId."'";
		$db_drv_vehicle = $obj->MySQLSelect($sql);
		if(count($db_drv_vehicle) == 0){
			$returnArr['Action'] ="0";  # Check For Driver's vehicle added or not #
			$returnArr['message'] ="LBL_INACTIVE_CARS_MESSAGE_TXT";
			echo json_encode($returnArr);exit;
			}else{
			$DriverSelectedVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId',$driverId,'','true');
			if($DriverSelectedVehicleId == 0){
				$returnArr['Action'] ="0"; # Check Driver has selected  vehicle or not if #
				$returnArr['message'] ="LBL_SELECT_CAR_MESSAGE_TXT";
				echo json_encode($returnArr);exit;   
				}else{
				# Check For Driver's selected vehicle's document are upload or not #
				$sql= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='".$DriverSelectedVehicleId."' ) dl on dl.doc_masterid=dm.doc_masterid where dm.doc_usertype='car' and dm.status='Active'";
				$db_selected_vehicle = $obj->MySQLSelect($sql);
				if(count($db_selected_vehicle) > 0){
					for($i=0;$i<count($db_selected_vehicle);$i++){
						if($db_selected_vehicle[$i]['doc_file'] == ""){
							$returnArr['Action'] ="0";
							$returnArr['message'] ="Please upload your ". $db_selected_vehicle[$i]['doc_name'];
							echo json_encode($returnArr);exit; 
						}
					}
				}
				# Check For Driver's selected vehicle's document are upload or not #
				# Check For Driver's selected vehicle status #
				$DriverSelectedVehicleStatus = get_value('driver_vehicle', 'eStatus', 'iDriverVehicleId',$DriverSelectedVehicleId,'','true');
				if($DriverSelectedVehicleStatus == "Inactive" || $DriverSelectedVehicleStatus == "Deleted"){
					$returnArr['Action'] ="0";
					$returnArr['message'] ="LBL_SELECTED_VEHICLE_NOT_ACTIVE";
					echo json_encode($returnArr);exit;
				}
				# Check For Driver's selected vehicle status #
			}
		} 
		
		$sql = "SELECT rd.eStatus as driverstatus,cmp.eStatus as cmpEStatus FROM `register_driver` as rd,`company` as cmp WHERE rd.iDriverId='".$driverId."' AND cmp.iCompanyId=rd.iCompanyId";
		$Data = $obj->MySQLSelect($sql);
		
		if($Data[0]['driverstatus']!="active" || $Data[0]['cmpEStatus']!="Active"){
			
			$returnArr['Action'] ="0";
			
			if($Data[0]['cmpEStatus']!="Active"){
				$returnArr['message'] ="LBL_CONTACT_US_STATUS_NOTACTIVE_COMPANY";
				}else if($Data[0]['driverstatus']=="Deleted"){
				$returnArr['message'] ="LBL_ACC_DELETE_TXT";
				}else{
				$returnArr['message']="LBL_CONTACT_US_STATUS_NOTACTIVE_DRIVER";
			}
			
			echo json_encode($returnArr);exit;
		}
		
	}
	
	function fetch_address_geocode($address) {
		global $generalobj;
		$address = str_replace(" ", "+", "$address");
		$GOOGLE_SEVER_API_KEY_WEB=$generalobj->getConfigurations("configurations","GOOGLE_SEVER_API_KEY_WEB");
		//$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=".$GOOGLE_SEVER_API_KEY_WEB;
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
		
		// if(($city != '') && ($state != '') && ($country != '')) 
		// $address = $city.', '.$state.', '.$country;
		// else if (($city != '') && ($state != ''))
		// $address = $city.', '.$state;
		// else if (($state != '') && ($country != ''))
		// $address = $state.', '.$country;
		// else if ($country != '')
		// $address = $country;
		
		$returnArr = array('city'=>$city,'state'=> $state,'country'=>$country,'country_code'=>$country_code);
		
		
		return $returnArr;
	}
	
	function get_address_geocode($address) {
		global $generalobj;
		$address = str_replace(" ", "+", "$address");
		$GOOGLE_SEVER_API_KEY_WEB=$generalobj->getConfigurations("configurations","GOOGLE_SEVER_API_KEY_WEB");
		$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=".$GOOGLE_SEVER_API_KEY_WEB;
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
	
	function UploadUserImage($iMemberId,$UserType="Passenger",$eSignUpType,$vFbId){
		global $generalobj,$tconfig;
		$vimage = "";
		if($UserType == "Passenger"){
			$Photo_Gallery_folder =$tconfig["tsite_upload_images_passenger_path"]."/".$iMemberId."/";
			$OldImage=get_value('register_user', 'vImgName', 'iUserId', $iMemberId,'','true');
			}else{
			$Photo_Gallery_folder =$tconfig["tsite_upload_images_driver_path"]."/".$iMemberId."/";
			$OldImage=get_value('register_driver', 'vImage', 'iDriverId', $iMemberId,'','true');
		}
		unlink($Photo_Gallery_folder.$OldImage);
		unlink($Photo_Gallery_folder."1_".$OldImage);
		unlink($Photo_Gallery_folder."2_".$OldImage);
		unlink($Photo_Gallery_folder."3_".$OldImage);   
		unlink($Photo_Gallery_folder."4_".$OldImage);   
		if(!is_dir($Photo_Gallery_folder)) {                  
			mkdir($Photo_Gallery_folder, 0777);
		}
		if($eSignUpType == "Facebook"){
			//$baseurl =  "http://graph.facebook.com/".$vFbId."/picture?type=large";
			$baseurl =  "http://graph.facebook.com/".$vFbId."/picture?width=256";
			//$url = $vFbId."_".time().".jpg";
			$url = time().".jpg";
			/* file_get_content */
			$profile_Image = $baseurl;
			$userImage = $url;
			$thumb_image = file_get_contents($baseurl);
			$thumb_file = $Photo_Gallery_folder . $url;
			$image_name = file_put_contents($thumb_file, $thumb_image);
			/* file_get_content  ends*/
			if(is_file($Photo_Gallery_folder.$url)) {
				$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");
				$vimage = $imgname; 
			}  
		}
		if($eSignUpType == "Google"){
			$GOOGLE_SEVER_API_KEY_WEB = $generalobj->getConfigurations("configurations", "GOOGLE_SEVER_API_KEY_WEB");
			//$baseurl1 =  "https://www.googleapis.com/plus/v1/people/114434193354602240754?fields=image&key=AIzaSyB7_FaMl2gU1ItcomolF2S1Fzh8prnvNNw";
			$baseurl1 =  "https://www.googleapis.com/plus/v1/people/".$vFbId."?fields=image&key=".$GOOGLE_SEVER_API_KEY_WEB;
			//$url = $vFbId."_".time().".jpg";
			$url = time().".jpg";
			try{
				$jsonfile = file_get_contents($baseurl1);
				$jsondata = json_decode($jsonfile);
				$baseurl = $jsondata->image->url;
				$baseurl = str_replace("?sz=50","?sz=256",$baseurl);
				}catch (ErrorException $ex) {
				$imgname = "";
				$vimage = $imgname; 
			}
			/* file_get_content */
			$profile_Image = $baseurl;
			$userImage = $url;
			$thumb_image = file_get_contents($baseurl);
			$thumb_file = $Photo_Gallery_folder . $url;
			$image_name = file_put_contents($thumb_file, $thumb_image);
			/* file_get_content  ends*/
			if(is_file($Photo_Gallery_folder.$url)) {
				$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");
				$vimage = $imgname; 
			}
		}
		if($eSignUpType == "Twitter"){
			require_once('assets/libraries/twitter/TwitterAPIExchange.php');
			$TWITTER_OAUTH_ACCESS_TOKEN = $generalobj->getConfigurations("configurations", "TWITTER_OAUTH_ACCESS_TOKEN");
			$TWITTER_OAUTH_ACCESS_TOKEN_SECRET = $generalobj->getConfigurations("configurations", "TWITTER_OAUTH_ACCESS_TOKEN_SECRET");
			$TWITTER_CONSUMER_KEY = $generalobj->getConfigurations("configurations", "TWITTER_CONSUMER_KEY");
			$TWITTER_CONSUMER_SECRET = $generalobj->getConfigurations("configurations", "TWITTER_CONSUMER_SECRET");
			$settings = array(
            'oauth_access_token' => $TWITTER_OAUTH_ACCESS_TOKEN,
            'oauth_access_token_secret' => $TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
            'consumer_key' => $TWITTER_CONSUMER_KEY,
            'consumer_secret' => $TWITTER_CONSUMER_SECRET
			);
			$url = 'https://api.twitter.com/1.1/users/show.json';
			$getfield = '?user_id='.$vFbId;
			$requestMethod = 'GET';
			$twitter = new TwitterAPIExchange($settings);
			$twitterArr = $twitter->setGetfield($getfield)
			->buildOauth($url, $requestMethod)
			->performRequest();
			$jsondata = json_decode($twitterArr); //echo "<pre>";print_r($jsondata);exit;   
			$profile_image_url = $jsondata->profile_image_url;
			$baseurl = str_replace("_normal","",$profile_image_url);
			//$url = $vFbId."_".time().".jpg";
			$url = time().".jpg";       
			/* file_get_content */
			$profile_Image = $baseurl;
			$userImage = $url;
			$thumb_image = file_get_contents($baseurl);
			$thumb_file = $Photo_Gallery_folder . $url;
			$image_name = file_put_contents($thumb_file, $thumb_image);
			/* file_get_content  ends*/
			if(is_file($Photo_Gallery_folder.$url)) {
				$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");
				$vimage = $imgname; 
			}  
		}
		return $vimage;
	}
	
	function getMemberCountryUnit($iMemberId,$UserType="Passenger"){
		global $generalobj,$obj;
		
		if ($UserType == "Passenger") {
			$tblname = "register_user";
			$vCountryfield = "vCountry";
			$iUserId = "iUserId";
			} else {
			$tblname = "register_driver";
			$vCountryfield = "vCountry";
			$iUserId = "iDriverId";
		}
		$vCountry = get_value($tblname, $vCountryfield, $iUserId, $iMemberId, '', 'true'); 
		
		if($vCountry == "" || $vCountry == NULL){
			$vCountryCode = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
			}else{
			$vCountryCode = get_value("country", "eUnit", "vCountryCode", $vCountry, '', 'true');
		}
		return $vCountryCode;
	}
	
	function getVehicleCountryUnit_PricePerKm($vehicleTypeID,$fPricePerKM){
		global $generalobj,$obj;
		
		$iCountryId = get_value("vehicle_type", "iCountryId", "iVehicleTypeId", $vehicleTypeID, '', 'true');
		if($iCountryId == "-1"){
			$eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
			}else{
			$eUnit = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');
		}
		
		if($eUnit == "" || $eUnit == NULL){
			$eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
		}
		
		if($eUnit == "Miles"){
			$PricePerKM = $fPricePerKM * 1.60934; 
			}else{
			$PricePerKM = $fPricePerKM;
		}
		
		return  $PricePerKM;
	}
	function TripCollectTip($iMemberId,$iTripId,$fAmount){
		global $generalobj,$obj;
		$tbl_name = "register_user";
		$currencycode = "vCurrencyPassenger";
		$iUserId = "iUserId";
		$eUserType = "Rider";
		if($iMemberId == "") {
			$iMemberId = get_value('trips', 'iUserId', 'iTripId', $iTripId,'','true');
		}
		$vStripeCusId = get_value($tbl_name, 'vStripeCusId', $iUserId, $iMemberId,'','true');
		$vStripeToken = get_value($tbl_name, 'vStripeToken', $iUserId, $iMemberId,'','true');
		$userCurrencyCode = get_value($tbl_name, $currencycode, $iUserId, $iMemberId,'','true');
		$currencyCode = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
		$currencyratio = get_value('currency', 'Ratio', 'vName', $userCurrencyCode,'','true');
		//$price = $fAmount*$currencyratio;
		$price = $fAmount/$currencyratio;
		$price_new = $price * 100;
		$price_new = round($price_new);
		if($vStripeCusId == "" || $vStripeToken == ""){
			$returnArr["Action"] = "0";
			$returnArr['message']="LBL_NO_CARD_AVAIL_NOTE";
			echo json_encode($returnArr);exit;
		}
		$dDate = Date('Y-m-d H:i:s');
		$eFor = 'Deposit';
		$eType = 'Credit';
		$tDescription = "Amount debited";
		$ePaymentStatus = 'Unsettelled';
		$userAvailableBalance = $generalobj->get_user_available_balance($iMemberId,$eUserType);
		if($userAvailableBalance > $price){
			$where = " iTripId = '$iTripId'";
			$data['fTipPrice']= $price;
			$id = $obj->MySQLQueryPerform("trips",$data,'update',$where);
			$vRideNo = get_value('trips', 'vRideNo', 'iTripId',$tripId,'','true');
			$data_wallet['iUserId']=$iUserId;
			$data_wallet['eUserType']="Rider";
			$data_wallet['iBalance']=$price;
			$data_wallet['eType']="Debit";
			$data_wallet['dDate']=date("Y-m-d H:i:s");
			$data_wallet['iTripId']=$iTripId;
			$data_wallet['eFor']="Booking";
			$data_wallet['ePaymentStatus']="Unsettelled";
			$data_wallet['tDescription']="Amount ".$fAmount." debited from your account for trip number #".$vRideNo;
			$generalobj->InsertIntoUserWallet($data_wallet['iUserId'],$data_wallet['eUserType'],$data_wallet['iBalance'],$data_wallet['eType'],$data_wallet['iTripId'],$data_wallet['eFor'],$data_wallet['tDescription'],$data_wallet['ePaymentStatus'],$data_wallet['dDate']);
			//$returnArr["Action"] = "1";
			//echo json_encode($returnArr);exit;
			}else if($price > 0.51){
			try{
				$charge_create = Stripe_Charge::create(array(
				"amount" => $price_new,
				"currency" => $currencyCode,
				"customer" => $vStripeCusId,
				"description" =>  $tDescription
				));
				$details = json_decode($charge_create);
				$result = get_object_vars($details);
				//echo "<pre>";print_r($result);exit;
				if($result['status']=="succeeded" && $result['paid']=="1"){
					$where = " iTripId = '$iTripId'";
					$data['fTipPrice']= $price;
					$id = $obj->MySQLQueryPerform("trips",$data,'update',$where);
					//$returnArr["Action"] = "1";
					//echo json_encode($returnArr);exit;
					}else{
  					$returnArr['Action'] = "0";
  					$returnArr['message']="LBL_TRANS_FAILED";
  					echo json_encode($returnArr);exit;
				}
				}catch(Exception $e){
				//echo "<pre>";print_r($e);exit;
  				$returnArr["Action"] = "0";
				$returnArr['message']="LBL_TRANS_FAILED";
  				echo json_encode($returnArr);exit;
			}
			}else{
  			$returnArr["Action"] = "0";
  			$returnArr['message']="LBL_REQUIRED_MINIMUM_AMOUT";
  			$returnArr['minValue'] = strval(round(51 * $currencyratio));
  			echo json_encode($returnArr);exit;
		}
		return $iTripId;
	}
	function GenerateHailTrip($iUserId,$driverId,$selectedCarTypeID,$PickUpLatitude,$PickUpLongitude,$PickUpAddress,$DestLatitude,$DestLongitude,$DestAddress){
		global $generalobj,$obj;
		$Data['vRideNo'] = rand ( 10000000 , 99999999 );
		$Data['iVerificationCode'] = rand ( 1000 , 9999 );
		$Data['iUserId'] = $iUserId;
		$Data['iDriverId'] = $driverId;
		$Data['tTripRequestDate'] = @date("Y-m-d H:i:s");
		$Data['iVehicleTypeId'] = $selectedCarTypeID;
		$Data['iDriverVehicleId'] =get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $driverId,'','true');
		$Data['iActive']='On Going Trip';
        $Data['tStartDate']=@date("Y-m-d H:i:s");
		$Data['tStartLat']=$PickUpLatitude;
		$Data['tStartLong']=$PickUpLongitude;
		$Data['tSaddress']=$PickUpAddress;
		$Data['tEndLat']=$DestLatitude;
		$Data['tEndLong']=$DestLongitude;
		$Data['tDaddress']=$DestAddress;
		$Data['eFareType'] = get_value('vehicle_type', 'eFareType', 'iVehicleTypeId', $selectedCarTypeID,'','true');
		$Data['fVisitFee'] = get_value('vehicle_type', 'fVisitFee', 'iVehicleTypeId', $selectedCarTypeID,'','true');
		$Data['vTripPaymentMode']="Cash";
		$Data['eType']="Ride";
		$Data['eHailTrip']="Yes";
		$Data['eFareType']="Regular";
		$Data['vCountryUnitRider']=getMemberCountryUnit($iUserId,"Passenger");
		$Data['vCountryUnitDriver']=getMemberCountryUnit($driverId,"Driver");
		$currencyList = get_value('currency', '*', 'eStatus', 'Active');
		for($i=0;$i<count($currencyList);$i++){
			$currencyCode = $currencyList[$i]['vName'];
			$Data['fRatio_'.$currencyCode] = $currencyList[$i]['Ratio'];
		}
		$Data['vCurrencyPassenger']=get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId, '', 'true'); 
		$Data['vCurrencyDriver']=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $driverId, '', 'true');
		$Data['fRatioPassenger']=get_value('currency', 'Ratio', 'vName', $Data['vCurrencyPassenger'],'','true');
		$Data['fRatioDriver']=get_value('currency', 'Ratio', 'vName', $Data['vCurrencyDriver'],'','true');
		$id = $obj->MySQLQueryPerform("trips",$Data,'insert');
		return $id;
	}
	function sendTripMessagePushNotification($iFromMemberId, $UserType, $iToMemberId, $iTripId, $tMessage) {
		global $generalobj, $obj;
		$FIREBASE_API_ACCESS_KEY = $generalobj->getConfigurations("configurations", "FIREBASE_API_ACCESS_KEY");
		if($UserType == "Passenger"){
			$tblname = "register_driver";
			$condfield = 'iDriverId';
			$field = 'vFirebaseDeviceToken';
			$Fromtblname = "register_user";
			$Fromcondfield = 'iUserId';
			$pemFileIdentifier = 1;
			$vImageName = "vImgName";
		}else{
			$tblname = "register_user";
			$condfield = 'iUserId';
			$field = 'vFirebaseDeviceToken';
			$Fromtblname = "register_driver";
			$Fromcondfield = 'iDriverId';
			$pemFileIdentifier = 0;
			$vImageName = "vImage";
		}
		$vFirebaseDeviceToken = get_value($tblname, $field, $condfield, $iToMemberId, '', 'true');
		$iGcmRegId = get_value($tblname, "iGcmRegId", $condfield, $iToMemberId, '', 'true');
		$eDeviceType = get_value($tblname, "eDeviceType", $condfield, $iToMemberId, '', 'true');
		$MemberName = get_value($Fromtblname, 'vName,vLastName', $Fromcondfield, $iFromMemberId);
		$FromMemberImageName = get_value($Fromtblname, $vImageName, $Fromcondfield, $iFromMemberId, '', 'true');
		$FromMemberName = $MemberName[0]['vName'];
			// ." ".$MemberName[0]['vLastName']
		if($eDeviceType == "Ios"){
			$msg_encode['Msg'] = $tMessage;
			$msg_encode['MsgType'] = "CHAT";
			$msg_encode['iFromMemberId'] = strval($iFromMemberId);
			$msg_encode['iTripId'] = strval($iTripId);
			$msg_encode['FromMemberName'] = strval($FromMemberName);
			$msg_encode['FromMemberImageName'] = strval($FromMemberImageName);
			$msg_encode  = json_encode($msg_encode,JSON_UNESCAPED_UNICODE);
			$deviceTokens_arr_ios = array();
			array_push($deviceTokens_arr_ios, $iGcmRegId);
			sendApplePushNotification($pemFileIdentifier,$deviceTokens_arr_ios,$msg_encode,$tMessage,0);
		}else{
			
			 
			$registrationIds = (array)$vFirebaseDeviceToken;
			$msg['aps'] = array
			(       
			'iFromMemberId' => $iFromMemberId,
			'iTripId' => $iTripId,
			'FromMemberName' => $FromMemberName,
			'Msg' => $tMessage,
			'MsgType' => "CHAT",
			'FromMemberImageName' => $FromMemberImageName
			//'title'	=> 'Title Of Notification',
			//'icon'	=> 'myicon',/*Default Icon*/
			//'sound' => 'mySound'/*Default sound*/
			);
			$fields = array
			(
			'registration_ids'  => $registrationIds,
			'click_action' => ".MainActivity",      
			'priority' => "high",
			//'data'          => $msg
			'data'         => array ("message" => $msg['aps']) 
			);

			$headers = array
			(
			'Authorization: key=' . $FIREBASE_API_ACCESS_KEY,
			'Content-Type: application/json',
			);
			//Setup headers:
			// echo "<pre>";print_r($headers);exit;
			//Setup curl, add headers and post parameters.
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true  );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    
			//Send the request
			$response = curl_exec($ch); //echo "<pre>";print_r($response);exit;
			$responseArr = json_decode($response);
			//echo "<pre>";print_r($responseArr);exit;
			$success = $responseArr->success; 
			//Close request
			curl_close($ch);
			return $success;
		}
		
		
	}
	function UpdateOtherLanguage($vLabel,$vValue,$vLangCode,$tablename){
		global $generalobj, $obj;
		$sql = "SELECT vCode,vLangCode FROM `language_master` where vCode!='".$vLangCode."' ORDER BY `iDispOrder`";
		$db_master = $obj->MySQLSelect($sql);
		$count_all = count($db_master);
		if($count_all > 0){
			for($i=0;$i<$count_all;$i++) {
				$vCode = $db_master[$i]['vCode'];
				$vGmapCode = $db_master[$i]['vLangCode'];
				$url = 'http://api.mymemory.translated.net/get?q='.urlencode($vValue).'&de=harshilmehta1982@gmail.com&langpair=en|'.$vGmapCode;
				$result = file_get_contents($url);
				$finalResult = json_decode($result);
				$getText = $finalResult->responseData;
				$resulttext = $getText->translatedText;
				if($resulttext == ""){
					$resulttext = $vValue;
				}
				$sql = "SELECT LanguageLabelId FROM $tablename where vLabel = '".$vLabel."' AND vCode = '".$vCode."'";
				$db_language_label = $obj->MySQLSelect($sql);
				$count = count($db_language_label);
				if($count > 0){
					$where = " LanguageLabelId = '".$db_language_label[0]['LanguageLabelId']."'";
					$data_update['vValue']=$resulttext;
					$obj->MySQLQueryPerform($tablename,$data_update,'update',$where);
				}
			}
		}
		return $count_all;
	}
	function get_currency($from_Currency, $to_Currency, $amount) {
		$amount = urlencode($amount);
		$from_Currency = urlencode($from_Currency);
		$to_Currency = urlencode($to_Currency);
		$url = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
		$ch = curl_init();
		$timeout = 0;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$rawdata = curl_exec($ch);
		curl_close($ch);
		$data = explode('bld>', $rawdata);
		$data = explode($to_Currency, $data[1]);
		return round($data[0], 2);
	}
?>