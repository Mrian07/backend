<?php
include_once('../common.php');

if(!isset($generalobjAdmin)){
	require_once(TPATH_CLASS."class.general_admin.php");
	$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
$iVehicleTypeId = isset($_REQUEST['iVehicleTypeId']) ? $_REQUEST['iVehicleTypeId'] : '';

$ssql = " AND rd.eStatus='Active'";
if($keyword != "") {
	$ssql .= " AND CONCAT(rd.vName,' ',rd.vLastName) like '%$keyword%'";
}

$sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, CONCAT(rd.vName,' ',rd.vLastName) AS FULLNAME,rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType FROM register_driver AS rd
LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId
WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
$db_records = $obj->MySQLSelect($sql);

// echo "<pre>"; 
// print_r($db_records); die;

$dbDrivers = array();
for($i=0;$i<count($db_records);$i++){
	$newArray = array();
	$newArray = explode(',',$db_records[$i]['vCarType']);
	if($iVehicleTypeId == '' || (!empty($newArray) && in_array($iVehicleTypeId,$newArray))) {
	if ($db_records[$i]['vImage'] != 'NONE' && $db_records[$i]['vImage'] != '' && file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_records[$i]['iDriverId'] . '/2_'.$db_records[$i]['vImage'])) { 
		$DriverImage = $tconfig["tsite_upload_images_driver"]. '/' . $db_records[$i]['iDriverId'] . '/2_'.$db_records[$i]['vImage'];
	}else{
		$DriverImage = $tconfig["tsite_url"]."assets/img/profile-user-img.png";
	}
	$db_records[$i]['vImageDriver'] = $DriverImage;
	$time = time();
	$last_online_time = strtotime($db_records[$i]['tLastOnline']);
	$time_difference = $time-$last_online_time;
	if($time_difference <= 300 && $db_records[$i]['vAvailability'] == "Available"){
		$db_records[$i]['vAvailability'] = "Available";
		$dbDrivers[$i] = $db_records[$i];
	}else{
	 $vTripStatus = $db_records[$i]['vTripStatus'];
	  if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
		 $db_records[$i]['vAvailability'] = $vTripStatus;
	  }else{
		$db_records[$i]['vAvailability'] = "Not Available";
	  }
	  $dbDrivers[$i] = $db_records[$i];
	}
	}
}
// echo "<pre>";
// print_r($dbDrivers); die;
#marker Add
$con = "";
foreach ($dbDrivers as $key => $value) {
		if($value['vAvailability'] == "Available") {
			$statusIcon = "../assets/img/green-icon.png";
		}else if($value['vAvailability'] == "Active") {
			$statusIcon = "../assets/img/red.png";
		}else if($value['vAvailability'] == "On Going Trip") {
			$statusIcon = "../assets/img/yellow.png";
		}else if($value['vAvailability'] == "Arrived"){
			$statusIcon = "../assets/img/blue.png";
		}else {
			$statusIcon = "../assets/img/offline-icon.png";
		}
		$con .= '<li onclick="showPopupDriver('.$value['iDriverId'].');"><label class="map-tab-img"><label class="map-tab-img1"><img src="'.$value['vImageDriver'].'"></label><img src="'.$statusIcon.'"></label><p class="driver_'.$value['iDriverId'].'">'.$generalobjAdmin->clearName($value['FULLNAME']).' <b>+'.$generalobjAdmin->clearPhone($value['vCode'].$value['vPhone']).'</b></p><a href="javascript:void(0)" class="btn btn-success assign-driverbtn" onClick=\'AssignDriver('.$value['iDriverId'].');\'>Assign Driver</a></li>';
}
echo $con; exit;
?>