<?php
error_reporting(0);

// Report runtime errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Report all errors
error_reporting(E_ALL);

// Same as error_reporting(E_ALL);
ini_set("error_reporting", E_ALL);

// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

include_once('../common.php');
include_once(TPATH_CLASS.'/class.general.php');
include_once(TPATH_CLASS.'/configuration.php');
include_once('../generalFunctions.php');

function send_notification_fun($registation_ids_new,$deviceTokens_arr_ios,$message,$userType) {
		$alertMsg = $message;

		// echo "registation_ids_new => <pre>";
		// print_r($registation_ids_new);
		// echo "deviceTokens_arr_ios => <pre>";
		// print_r($deviceTokens_arr_ios);
		// exit;
		if(!empty($registation_ids_new)){
			$newArr = array();
			$newArr = array_chunk($registation_ids_new, 999);
			foreach($newArr as $newRegistration_ids){
				$Rmessage         = array("message" => $message);
				$result = send_notification($newRegistration_ids, $Rmessage,0);
			}
		}
		if(!empty($deviceTokens_arr_ios)){
			if($userType == "rider") {
				$result = sendApplePushNotification(0,$deviceTokens_arr_ios,$message,$alertMsg,0,'admin');
			}else {
				$result = sendApplePushNotification(1,$deviceTokens_arr_ios,$message,$alertMsg,0,'admin');
			}
		}
		$_SESSION['success'] = '1';
		$_SESSION['var_msg'] = 'Push Notification send successfully.';
		header("location:send_notifications.php");
		exit;
}

if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$sql = "select concat(vName,' ',vLastName) as DriverName,iDriverId from register_driver where eStatus = 'Active' order by vName";
$db_drv_list = $obj->MySQLSelect($sql);

$sql = "select concat(vName,' ',vLastName) as riderName,iUserId from register_user where eStatus = 'Active' order by vName";
$db_rdr_list = $obj->MySQLSelect($sql);

$tbl_name = 'pushnotification_log';
$script = 'Push Notification';

// set all variables with either post (when submit) either blank (when insert)
$eUserType = isset($_POST['eUserType']) ? $_POST['eUserType'] : '';
$iDriverId = isset($_POST['iDriverId']) ? $_POST['iDriverId'] : '';
$iRiderId = isset($_POST['iRiderId']) ? $_POST['iRiderId'] : '';
$tMessage = isset($_POST['tMessage']) ? $_POST['tMessage'] : '';
$dDate = date("Y-m-d H:i:s");
$ipAddress = $_SERVER['REMOTE_HOST'];

if (isset($_POST['submit'])) {
	//echo "sd";
     // echo "<pre>"; print_r($_REQUEST); die;
		if(SITE_TYPE =='Demo'){
			$_SESSION['success'] = 2;
			header("Location:send_notifications.php");exit;
		}
		
		if($eUserType == 'driver'){
			$set_table = 'register_driver';
			$set_userId = 'iDriverId';
			if(!empty($iDriverId)) {
				$userArr = $iDriverId;
			}else {
				foreach($db_drv_list as $dbd) {
					$userArr[] = $dbd['iDriverId'];
				}
			}
		}else {
			$set_table = 'register_user';
			$set_userId = 'iUserId';
			if(!empty($iRiderId)){
				$userArr = $iRiderId;
			}else {
				foreach($db_rdr_list as $dbr) {
					$userArr[] = $dbr['iUserId'];
				}
			}
		}
		$deviceTokens_arr_ios = array();
		$registation_ids_new = array();
		// echo "<pre>"; print_r($userArr); die;
		foreach($userArr as $usAr){
			//send_notification_fun($usAr);
			$q = "INSERT INTO ";
			$query = $q . " `" . $tbl_name . "` SET
			`eUserType` = '" . $eUserType . "',
			`iUserId` = '" . $usAr . "',
			`tMessage` = '" . $tMessage . "',
			`dDateTime` = '" . $dDate . "',
			`IP_ADDRESS` = '" . $ipAddress . "'";
			$responce = $obj->sql_query($query);
			
			$gcmIds = get_value($set_table, 'eDeviceType,iGcmRegId', $set_userId,$usAr);
			if($gcmIds[0]['iGcmRegId'] != '' && strlen($gcmIds[0]['iGcmRegId']) > 15){
				if($gcmIds[0]['eDeviceType'] == 'Android') {
					array_push($registation_ids_new, $gcmIds[0]['iGcmRegId']);
				}else {
					array_push($deviceTokens_arr_ios, $gcmIds[0]['iGcmRegId']);
				}
			}
		}
		$tMessage=str_replace('\r\n','\n',$tMessage);
		send_notification_fun($registation_ids_new,$deviceTokens_arr_ios,$tMessage,$eUserType);
}
?>
<!DOCTYPE html>
<html lang="en">
     <head>
          <meta charset="UTF-8" />
          <title><?=$SITE_NAME?> | Send Push-Notification </title>
          <meta content="width=device-width, initial-scale=1.0" name="viewport" />
          <?
          include_once('global_files.php');
          ?>
          <!-- On OFF switch -->
          <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
          <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
     </head>
     <!-- END  HEAD-->
     <!-- BEGIN BODY-->
     <body class="padTop53 " >

          <!-- MAIN WRAPPER -->
          <div id="wrap">
               <?
               include_once('header.php');
               include_once('left_menu.php');
               ?>
               <!--PAGE CONTENT -->
               <div id="content">
                    <div class="inner">
                         <div class="row">
                              <div class="col-lg-12">
                                   <h2>Send Push-Notification </h2>
                                   <!--a href="driver_subscriptions.php" class="back_link">
                                        <input type="button" value="Back to Listing" class="add-btn">
                                   </a-->
                              </div>
                         </div>
                         <hr />
                         <div class="body-div">
                              <div class="form-group">
									<?php include('valid_msg.php'); ?>
									<form id="_notification_form" name="_notification_form" method="post" action="javascript:void(0);" >
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Select User Type<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select class="form-control" name = 'eUserType' id="eUserType" onChange="showUsers(this.value);">
													<option value="driver">All Drivers</option>
													<option value="rider">All Riders</option>
												</select>
                                             </div>
                                        </div>
                                        <div class="row set-dd-css" id="driverRw">
                                             <div class="col-lg-12">
                                                  <label>Select Driver<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select class="form-control filter-by-text" name = 'iDriverId[]' id="iDriverId" multiple data-text="All Drivers">
													<? for($i=0;$i<count($db_drv_list);$i++){ ?>
													<option value = "<?= $db_drv_list[$i]['iDriverId'] ?>" ><?= $generalobjAdmin->clearName($db_drv_list[$i]['DriverName']); ?></option>
													<? } ?>
												</select>
                                             </div>
                                        </div>
                                        <div class="row set-dd-css" id="riderRw" style="display:none;">
                                             <div class="col-lg-12">
                                                  <label>Select Rider<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select class="form-control filter-by-text" name = 'iRiderId[]' id="iRiderId" multiple data-text="All Riders">
													<? for($i=0;$i<count($db_rdr_list);$i++){ ?>
													<option value = "<?= $db_rdr_list[$i]['iUserId'] ?>" ><?= $generalobjAdmin->clearName($db_rdr_list[$i]['riderName']); ?></option>
													<? } ?>
												</select>
                                             </div>
                                        </div>
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Message<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <textarea name="tMessage" class="form-control" id="tMessage" required maxlength="100" ></textarea>
                                             </div>
                                        </div>
                                       
                                        <div class="row">
											 <div class="col-lg-12">
												<input type="submit" class="btn btn-default" name="submit" id="submit" onClick="submit_form();" value="Send Notification" >
											</div>
                                        </div>
                                   </form>
                              </div>
                         </div>
                    </div>
               </div>
               <!--END PAGE CONTENT -->
          </div>
          <!--END MAIN WRAPPER -->

		<?php include_once('footer.php'); ?>
		<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
		<link rel="stylesheet" href="css/select2/select2.min.css" type="text/css" >
		
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
		<script type="text/javascript" src="js/plugins/select2.min.js"></script>
		<script>
		
		function submit_form(){
			var joinTxt = '';
			if( $("#_notification_form").valid() ) {
				var userType = $("#eUserType").val();
				if(userType == 'rider'){
					if($("#iRiderId").val() == '' || $("#iRiderId").val() == null){
						joinTxt = 'All Riders';
					}else {
						var len = $('#iRiderId option:selected').length;
						joinTxt = 'Selected '+len+' Rider(s)';
					}
				}else {
					if($("#iDriverId").val() == '' || $("#iDriverId").val() == null){
						joinTxt = 'All Drivers';
					}else {
						var len = $('#iDriverId option:selected').length;
						joinTxt = 'Selected '+len+' Driver(s)';
					}
				}
				
				if(confirm("Are you	sure to send push notification to "+joinTxt+"?")){
					$("#_notification_form").attr('action','');
					$("#_notification_form").submit();
				}else {
					
				}
			}
		}
		
		
		$(function () {
		  $("select.filter-by-text").each(function(){
			  $(this).select2({
					placeholder: $(this).attr('data-text'),
					allowClear: true
			  }); //theme: 'classic'
			});
		});
		
		function showUsers(userType) {
			if(userType == 'driver'){
				$("#driverRw").show();
				$("#riderRw").hide();
			}else {
				$("#riderRw").show();
				$("#driverRw").hide();
			}
		}
		</script>
</body>
<!-- END BODY-->
</html>
