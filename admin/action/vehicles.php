<?php
include_once('../../common.php');

if (!isset($generalobjDriver)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjDriver = new General_admin();
}
$generalobjDriver->check_member_login();

$reload = $_SERVER['REQUEST_URI']; 

$urlparts = explode('?',$reload);
$parameters = $urlparts[1];

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? $_REQUEST['iDriverVehicleId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
 // echo "<pre>"; print_r($_REQUEST);
// die;
 //Start make deleted
if ($method == 'delete' && $iDriverVehicleId != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE driver_vehicle SET eStatus = 'Deleted' WHERE iDriverVehicleId = '" . $iDriverVehicleId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Taxi deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."vehicles.php?".$parameters); exit;
}
//End make deleted

//Start Change single Status
if ($iDriverVehicleId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE driver_vehicle SET eStatus = '" . $status . "' WHERE iDriverVehicleId = '" . $iDriverVehicleId . "'";
            $obj->sql_query($query);
			if($SEND_TAXI_EMAIL_ON_CHANGE == 'Yes') {
				$sql23 = "SELECT m.vMake, md.vTitle,rd.vEmail, rd.vName, rd.vLastName, c.vCompany as companyFirstName
						FROM driver_vehicle dv, register_driver rd, make m, model md, company c
						WHERE
						  dv.eStatus != 'Deleted'
						  AND dv.iDriverId = rd.iDriverId
						  AND dv.iCompanyId = c.iCompanyId
						  AND dv.iModelId = md.iModelId
						  AND dv.iMakeId = m.iMakeId AND dv.iDriverVehicleId = '".$iDriverVehicleId."'";
				$data_email_drv = $obj->MySQLSelect($sql23);
				$maildata['EMAIL'] =$data_email_drv[0]['vEmail'];
				$maildata['NAME'] = $data_email_drv[0]['vName'];
				$maildata['DETAIL']="Your ".$langage_lbl_admin['LBL_TEXI_ADMIN']." ".$data_email_drv[0]['vTitle']." For COMPANY ".$data_email_drv[0]['companyFirstName'] ." is temporarly ".$status;
				$generalobj->send_email_user("ACCOUNT_STATUS",$maildata);
			}
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Taxi activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Taxi inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."vehicles.php?".$parameters);
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE driver_vehicle SET eStatus = '" . $statusVal . "' WHERE iDriverVehicleId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 if($SEND_TAXI_EMAIL_ON_CHANGE == 'Yes') {
			foreach($checkbox as $iDriverVehicleId){
				$sql23 = "SELECT m.vMake, md.vTitle,rd.vEmail, rd.vName, rd.vLastName, c.vCompany as companyFirstName
						FROM driver_vehicle dv, register_driver rd, make m, model md, company c
						WHERE
						  dv.eStatus != 'Deleted'
						  AND dv.iDriverId = rd.iDriverId
						  AND dv.iCompanyId = c.iCompanyId
						  AND dv.iModelId = md.iModelId
						  AND dv.iMakeId = m.iMakeId AND dv.iDriverVehicleId = '".$iDriverVehicleId."'";
				$data_email_drv = $obj->MySQLSelect($sql23);
				$maildata['EMAIL'] =$data_email_drv[0]['vEmail'];
				$maildata['NAME'] = $data_email_drv[0]['vName'];
				$maildata['DETAIL']="Your ".$langage_lbl_admin['LBL_TEXI_ADMIN']." ".$data_email_drv[0]['vTitle']." For COMPANY ".$data_email_drv[0]['companyFirstName'] ." is temporarly ".$statusVal;
				$generalobj->send_email_user("ACCOUNT_STATUS",$maildata);
			}
		}
		 
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Taxi(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."vehicles.php?".$parameters);
        exit;
}
//End Change All Selected Status

//if ($iDriverVehicleId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE driver_vehicle SET eStatus = '" . $status . "' WHERE iDriverVehicleId = '" . $iDriverVehicleId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = "Driver " . $status . " Successfully.";
//        header("Location:".$tconfig["tsite_url_main_admin"]."driver.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."driver.php?".$parameters);
//        exit;
//    }
//}
?>