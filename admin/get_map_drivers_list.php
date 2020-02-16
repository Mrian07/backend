<?php
include_once('../common.php');
header('Content-Type: text/html; charset=utf-8');
if(!isset($generalobjAdmin)){
	require_once(TPATH_CLASS."class.general_admin.php");
	$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$type = $_REQUEST['type'];

$ssql = " AND rd.eStatus='Active'";

$sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType FROM register_driver AS rd
LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId
WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
$db_records = $obj->MySQLSelect($sql);

// echo "<pre>"; print_r($db_records); die;

for($i=0;$i<count($db_records);$i++){
	if ($db_records[$i]['vImage'] != 'NONE' && $db_records[$i]['vImage'] != '') { 
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
	}else{
	  $vTripStatus = $db_records[$i]['vTripStatus'];
	  //if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
	  if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
			$db_records[$i]['vAvailability'] = $vTripStatus;
	  }else{
			$db_records[$i]['vAvailability'] = "Not Available";
	  }
	}
}
$locations = array();
// if($type != "") {
// }
#marker Add
foreach ($db_records as $key => $value) {
	if($value['vAvailability'] == "Available") {
		$statusIcon = "../webimages/upload/mapmarker/available.png";
	}else if($value['vAvailability'] == "Active") {
		$statusIcon = "../webimages/upload/mapmarker/enroute.png";
	}else if($value['vAvailability'] == "Arrived") {
		$statusIcon = "../webimages/upload/mapmarker/reached.png";
	}else if($value['vAvailability'] == "On Going Trip"){
		$statusIcon = "../webimages/upload/mapmarker/started.png";
	}else {
		$statusIcon = "../webimages/upload/mapmarker/offline.png";
	}
  	$locations[] = array(
  		'google_map' => array(
  			'lat' => $value['vLatitude'],
  			'lng' => $value['vLongitude'],
  		),
		'location_icon' => $statusIcon,
  		'location_address' => $value['vServiceLoc'],
  		'location_image'    => $value['vImageDriver'],
  		'location_mobile'    => $generalobjAdmin->clearPhone($value['vCode'].$value['vPhone']),
  		'location_ID'    => $generalobjAdmin->clearEmail($value['vEmail']),
  		'location_type'    => $value['vAvailability'],
  		'location_online_status'    => $value['vAvailability'],
  		'location_carType'    => $value['vCarType'],
  		'location_driverId'    => $value['iDriverId'],
  	);
}

$returnArr['Action'] = "0";
$returnArr['locations'] = $locations;
$returnArr['db_records'] = $db_records;
$returnArr['newStatus'] = $newStatus;

// echo "<pre>"; print_r($returnArr); die;
echo json_encode($returnArr);exit;
?>