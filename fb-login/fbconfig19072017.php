<?php
	
error_reporting(E_ALL);
ob_start();
session_start();
// added in v4.0.0

include_once('../common.php');
include_once('../assets/libraries/class.general.php');
$generalobj = new General();

require_once 'autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;


$sql="SELECT vValue FROM configurations WHERE vName='FACEBOOK_APP_ID'";
$db_appid=$obj->MySQLSelect($sql);

$sql="SELECT vValue FROM configurations WHERE vName='FACEBOOK_APP_SECRET_KEY'";
$db_key=$obj->MySQLSelect($sql);

$appId = $db_appid[0]['vValue'];
$appsecretkey = $db_key[0]['vValue'];

$userType = (isset($_REQUEST['userType'])) ? $_REQUEST['userType'] : '';
if(!isset($_SESSION['fb_user']) || $_SESSION['fb_user'] == '') {
	$_SESSION['fb_user'] = $userType;
	$_SESSION['fb_user'] = ($_SESSION['fb_user'] == '') ? 'rider' : $_SESSION['fb_user'];
}
// init app with app id and secret
// echo "<pre>"; print_r($_SESSION); die;
FacebookSession::setDefaultApplication( $appId ,$appsecretkey );
// login helper with redirect_uri
    $helper = new FacebookRedirectLoginHelper($tconfig['tsite_url'].'fb-login/fbconfig.php');
try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
} catch( Exception $ex ) {
  // When validation fails or other local issues
}

// see if we have a session
include_once($tconfig["tsite_libraries_v"]."/Imagecrop.class.php");
$thumb = new thumbnail();
$temp_gallery = $tconfig["tsite_temp_gallery"];

include_once($tconfig["tsite_libraries_v"]."/SimpleImage.class.php");
$img = new SimpleImage();

$ctype=$_REQUEST['ctype'];

if($ctype == ''){
    $ctype = "fblogin";
}
// echo "<pre>";print_r($_REQUEST);exit;
if($ctype == "fblogin"){
    if (isset( $session )) {		// print_r( $session ); 
		try{
			$request = new FacebookRequest( $session, 'GET', '/me?locale=en_US&fields=id,picture,first_name,last_name,email,location,gender' );
			// echo "come"; echo "<pre>";print_r($request, 1); die;
			$response = $request->execute();
			// $graphObject = $response->getGraphObject();  
			$graphObject = $response->getGraphObject();
			
			// or convert the base object previously accessed
			// $loc = $object->cast(GraphLocation::className());
			//exit;
			$fbid = $graphObject->getProperty('id');              // To Get Facebook ID
			$fbfirstname = $graphObject->getProperty('first_name'); // To Get Facebook first name
			$fblastname = $graphObject->getProperty('last_name'); // To Get Facebook last name
			$femail = $graphObject->getProperty('email');    // To Get Facebook email ID
			$flocation = $graphObject->getProperty('location');    // To Get Facebook location
			$fhometown = $graphObject->getProperty('hometown');    // To Get Facebook hometown
			$fgender = $graphObject->getProperty('gender');    // To Get Facebook user gender
			$fbimage = $graphObject->getProperty('picture');              // To Get Facebook ID
			//$country = $graphObject->getProperty('country'); // To Get Facebook last name
			// $user_location = $fb->api('/'.$fbid.'?fields=location'); 
			 
			$db_user = array();
			
			if($_SESSION['fb_user'] == 'rider') {
			
				if($femail != "") {
					$sql = "SELECT iUserId,vImgName FROM register_user WHERE ((vEmail='".$femail."') OR (vFbId = '".$fbid."' AND eSignUpType = 'Facebook')) and eStatus != 'Deleted'";
					$db_user = $obj->MySQLSelect($sql);
				}

				if(count($db_user) > 0){
				 
					$_SESSION['sess_iMemberId']=$db_user[0]['iUserId'];
					$_SESSION['sess_iUserId'] =$db_user[0]['iUserId'];
					$_SESSION["sess_vFirstName"]= isset($fbfirstname)?ucfirst($fbfirstname):'';
					$_SESSION["sess_vLastName"]= isset($fblastname)?ucfirst($fblastname):'';
					$_SESSION["sess_vEmail"]= isset($femail)?$femail:'';
					$_SESSION["sess_eGender"]= isset($fgender)?$fgender:'';

					$Photo_Gallery_folder =$tconfig["tsite_upload_images_passenger_path"]."/".$_SESSION['sess_iMemberId']."/";
				
					unlink($Photo_Gallery_folder.$db_user[0]['vImgName']);
					unlink($Photo_Gallery_folder."1_".$db_user[0]['vImgName']);
					unlink($Photo_Gallery_folder."2_".$db_user[0]['vImgName']);
					unlink($Photo_Gallery_folder."3_".$db_user[0]['vImgName']);   
					unlink($Photo_Gallery_folder."4_".$db_user[0]['vImgName']);   
				
					if(!is_dir($Photo_Gallery_folder)) {                  
						mkdir($Photo_Gallery_folder, 0777);
					}
					
					$baseurl =  "http://graph.facebook.com/".$fbid."/picture?type=large";
					$url = $fbid.".jpg";
					$image_name = system("wget --no-check-certificate -O ".$Photo_Gallery_folder.$url." ".$baseurl);
				
					if(is_file($Photo_Gallery_folder.$url)) {
				 
					list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
					if($width < $height){
						$final_width = $width;
					}else{
						$final_width = $height;
					}       
					$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
					
					$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
					}  
				
					$sql = "UPDATE register_user set vFbId='".$user."', vImgName='".$imgname."',eGender='".$_SESSION['sess_eGender']."',eSignUpType = 'Facebook' WHERE iUserId='".$_SESSION['sess_iMemberId']."'";
					$obj->sql_query($sql); 

					$db_sql = "select * from register_user WHERE iUserId='".$_SESSION['sess_iMemberId']."'";
					$db_data = $obj->MySQLSelect($db_sql);
					$_SESSION["sess_vImage"]= $db_data[0]['vImgName'];  
					$_SESSION["sess_user"]= 'rider';   

					if(SITE_TYPE=='Demo'){
					  $login_sql = "insert into member_log (iMemberId, eMemberType, eMemberLoginType,vIP) VALUES ('".$_SESSION["sess_iUserId"]."', 'Passenger', 'WebLogin','".$_SERVER['REMOTE_ADDR']."')";
					  $obj->sql_query($login_sql);
					}
				
					$link = $tconfig["tsite_url"]."profile_rider.php";
				
					header("Location:".$link);
					exit;
		
				}else{

					 $sql = "select * from currency where eDefault = 'Yes'";
					  $db_curr = $obj->MySQLSelect($sql);
					//print_r($db_curr);
					$curr = $db_curr[0]['vName'];
					$sql = "select * from language_master where eDefault = 'Yes'";
					$db_lang = $obj->MySQLSelect($sql);
					$lang = $db_lang[0]['vCode'];
								$eReftype = "Rider";
					$refercode = $generalobj->ganaraterefercode($eReftype);
					$dRefDate  = Date('Y-m-d H:i:s');
					  
					if($femail != "") {
					$graph = $response->getResponse();
					$image_name = $graph->picture->data->url;
					$sql = "insert into register_user (vFbId ,vName, vLastName, vEmail, eStatus,vImgName,eGender,vLang,vCurrencyPassenger,vRefCode,dRefDate) VALUES ('".$fbid."', '".$fbfirstname."', '".$fblastname."', '".$femail."', 'Active','".$image_name."','".$fgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."')";
					  
					  $iUserId =$obj->MySQLInsert($sql);
					 // echo "sasa";print_r($obj);die;

					} else {
					  $sql = "insert into register_user (vFbId, vImgName, vName, vLastName, vEmail,eStatus,eGender,vLang,vCurrencyPassenger,vRefCode,dRefDate) VALUES ('".$fbid."','".$image_name."', '".$fbfirstname."', '".$fblastname."', '".$femail."','Active','".$fgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."')";
					  $iUserId =  $obj->MySQLInsert($sql);
					}
					
					$_SESSION['sess_iMemberId']= $iUserId ;
					$_SESSION['sess_iUserId'] =  $_SESSION['sess_iMemberId'] ;
					$_SESSION["sess_vFirstName"]=$fbfirstname;
					$_SESSION["sess_vLastName"]=$fblastname;
					$_SESSION["sess_vEmail"]=$femail;  
					$_SESSION["sess_eGender"]=$fgender;
					$_SESSION["sess_user"]= 'rider';   

					
					$Photo_Gallery_folder =$tconfig["tsite_upload_images_passenger_path"]."/".$_SESSION['sess_iMemberId']."/";
					
					if(!is_dir($Photo_Gallery_folder)) {                  
						mkdir($Photo_Gallery_folder, 0777);
					}
					
					$baseurl =  "http://graph.facebook.com/".$fbid."/picture?type=large";
					$url = $fbid.".jpg";
					$image_name = system("wget --no-check-certificate -O ".$Photo_Gallery_folder.$url." ".$baseurl);
				
					if(is_file($Photo_Gallery_folder.$url)) {
				 
					list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
					if($width < $height){
						$final_width = $width;
					}else{
						$final_width = $height;
					}       
					$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
					
					$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
					}  
				
					$sql = "UPDATE register_user set vFbId='".$fbid."', vImgName='".$imgname."',eGender='".$_SESSION['sess_eGender']."',eSignUpType = 'Facebook' WHERE iUserId='".$_SESSION['sess_iMemberId']."'";
					$obj->sql_query($sql); 

					$db_sql = "select * from register_user WHERE iUserId='".$_SESSION['sess_iMemberId']."'";
					$db_data = $obj->MySQLSelect($db_sql);
					$_SESSION["sess_vImage"]= $db_data[0]['vImgName'];  
					$_SESSION["sess_user"]= 'rider';   
					 
					 /*$Data_not['iUserId'] = $id;
					 $Data_not['eSuccessPublish'] = 'Yes';
					 $Data_not['eSuccessUpdate'] = 'Yes';
					 $Data_not['ePrivateMessage'] = 'Yes';
					 $Data_not['eRatePassenger'] = 'Yes';
					 $Data_not['eNewRating'] = 'Yes';
					 $Data_not['eOtherInformation'] = 'Yes';
					 $Data_not['dAddedDate'] = date("Y-m-d H:i:s");*/
					// $id_not = $obj->MySQLQueryPerform("member_email_notification",$Data_not,'insert');
					
					$link = $tconfig["tsite_url"]."profile_rider.php";
					//echo $link;
					//echo "come"; die;
					header("Location:".$link);
					exit;
				}
		 
			}else {
				
				if($femail != '') {
					$sql = "SELECT iDriverId,vImage FROM register_driver WHERE ((vEmail='".$femail."') OR (vFbId = '".$fbid."' AND eSignUpType = 'Facebook')) and eStatus != 'Deleted'";
					$db_user = $obj->MySQLSelect($sql);
				}
				
				if(count($db_user) > 0){
					$_SESSION['sess_iMemberId']=$db_user[0]['iDriverId'];
					$_SESSION['sess_iUserId'] =$db_user[0]['iDriverId'];
					$_SESSION["sess_vName"]= isset($fbfirstname)?ucfirst($fbfirstname):'';
					$_SESSION["sess_vLastName"]= isset($fblastname)?ucfirst($fblastname):'';
					$_SESSION["sess_vEmail"] = isset($femail) ? $femail :'';
					$_SESSION["sess_eGender"]= isset($fbgender)?ucfirst($fbgender):'';
					$Photo_Gallery_folder =$tconfig["tsite_upload_images_driver_path"]."/".$_SESSION['sess_iMemberId']."/";
					
					unlink($Photo_Gallery_folder.$db_user[0]['vImage']);
					unlink($Photo_Gallery_folder."1_".$db_user[0]['vImage']);
					unlink($Photo_Gallery_folder."2_".$db_user[0]['vImage']);
					unlink($Photo_Gallery_folder."3_".$db_user[0]['vImage']);   
					unlink($Photo_Gallery_folder."4_".$db_user[0]['vImage']);   
				
					if(!is_dir($Photo_Gallery_folder)) { 
						mkdir($Photo_Gallery_folder, 0777); 
					}
					$baseurl =  "http://graph.facebook.com/".$fbid."/picture?type=large";
					$url = $fbid.".jpg";
					$image_name =  system("wget --no-check-certificate -O ".$Photo_Gallery_folder.$url." ".$baseurl);
				
					if(is_file($Photo_Gallery_folder.$url)) {
				 
						list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
						if($width < $height){
							$final_width = $width;
						}else{
							$final_width = $height;
						}       
						$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
						$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
					}

					$sql = "UPDATE register_driver set vFbId='".$fbid."', vImage='".$imgname."',eGender='".$_SESSION['sess_eGender']."',eSignUpType = 'Facebook' WHERE iDriverId='".$_SESSION['sess_iMemberId']."'";
					$obj->sql_query($sql); 

					$db_sql = "select * from register_driver WHERE iDriverId='".$_SESSION['sess_iMemberId']."'";
					$db_data = $obj->MySQLSelect($db_sql);
					$_SESSION["sess_vImage"]= $db_data[0]['vImage'];  
					$_SESSION["sess_user"]= 'driver';   

					if(SITE_TYPE=='Demo'){
					  $login_sql = "insert into member_log (iMemberId, eMemberType, eMemberLoginType,vIP) VALUES ('".$_SESSION["sess_iUserId"]."', 'Passenger', 'WebLogin','".$_SERVER['REMOTE_ADDR']."')";
					  $obj->sql_query($login_sql);
					}
					$link = $tconfig["tsite_url"]."profile_rider.php";
					header("Location:".$link);
					exit;
				}else{

					$sql = "select * from currency where eDefault = 'Yes'";
					$db_curr = $obj->MySQLSelect($sql);
					$curr = $db_curr[0]['vName'];
					
					$sql = "select * from language_master where eDefault = 'Yes'";
					$db_lang = $obj->MySQLSelect($sql);
					
					$lang = $db_lang[0]['vCode'];
					$eReftype = "Driver";
					$refercode = $generalobj->ganaraterefercode($eReftype);
					$dRefDate  = Date('Y-m-d H:i:s');	
					if($femail != "") {
						$sql = "insert INTO register_driver (vFbId,vName, vLastName, vEmail, eStatus,vImage,eGender,vLang,vCurrencyDriver,vRefCode,dRefDate) VALUES ('".$fbid."','".$fbfirstname."', '".$fblastname."', '".$femail."', 'Active','','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."')";
						$iDriverId =$obj->MySQLInsert($sql);
					} else {
						$sql = "INSERT INTO register_driver (vFbId,vName, vLastName, vEmail, eStatus,vImage,eGender,vLang,vCurrencyDriver,vRefCode,dRefDate) VALUES ('".$fbid."','', '".$fbfirstname."', '".$fblastname."', '".$femail."','Active','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."')";
						$iDriverId =  $obj->MySQLInsert($sql);
					}
					
					if($APP_TYPE == 'UberX'){
						$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";
						$result = $obj->MySQLSelect($query);
						
						$Drive_vehicle['iDriverId'] = $iDriverId;
						$Drive_vehicle['iCompanyId'] = "1";
						$Drive_vehicle['iMakeId'] = "3";
						$Drive_vehicle['iModelId'] = "1";
						$Drive_vehicle['iYear'] = Date('Y');
						$Drive_vehicle['vLicencePlate'] = "My Services";
						$Drive_vehicle['eStatus'] = "Active";
						$Drive_vehicle['eCarX'] = "Yes";
						$Drive_vehicle['eCarGo'] = "Yes";		
						$Drive_vehicle['vCarType'] = $result[0]['countId'];
						$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');
						$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$iDriverId."'";
						$obj->sql_query($sql);
						
						if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){
							$sql="select iVehicleTypeId,iVehicleCategoryId,eFareType,fFixedFare,fPricePerHour from vehicle_type where 1=1";
							$data_vehicles = $obj->MySQLSelect($sql);
							//echo "<pre>";print_r($data_vehicles);exit;
							
							if($data_vehicles[$i]['eFareType'] != "Regular")
							{
								for($i=0 ; $i < count($data_vehicles); $i++){
									$Data_service['iVehicleTypeId'] = $data_vehicles[$i]['iVehicleTypeId'];
									$Data_service['iDriverVehicleId'] = $iDriver_VehicleId;
									
									if($data_vehicles[$i]['eFareType'] == "Fixed"){
										$Data_service['fAmount'] = $data_vehicles[$i]['fFixedFare'];
									}
									else if($data_vehicles[$i]['eFareType'] == "Hourly"){
										$Data_service['fAmount'] = $data_vehicles[$i]['fPricePerHour'];
									}
									$data_service_amount = $obj->MySQLQueryPerform('service_pro_amount',$Data_service,'insert');
								}
							}
						}
					}
					else
					{
						if(SITE_TYPE=='Demo')
						{
							$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";
							$result = $obj->MySQLSelect($query);
							$Drive_vehicle['iDriverId'] = $iDriverId;
							$Drive_vehicle['iCompanyId'] = "1";
							$Drive_vehicle['iMakeId'] = "5";
							$Drive_vehicle['iModelId'] = "18";
							$Drive_vehicle['iYear'] = "2014";
							$Drive_vehicle['vLicencePlate'] = "CK201";
							$Drive_vehicle['eStatus'] = "Active";
							$Drive_vehicle['eCarX'] = "Yes";
							$Drive_vehicle['eCarGo'] = "Yes";		
							$Drive_vehicle['vCarType'] = $result[0]['countId'];
							$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');
							$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$iDriverId."'";
							$obj->sql_query($sql);
						}		
					}
					
					$_SESSION['sess_iMemberId']= $iDriverId ;
					$_SESSION['sess_iUserId'] =  $_SESSION['sess_iMemberId'] ;
					$_SESSION["sess_vName"]=$fbfirstname;
					$_SESSION["sess_vLastName"]=$fblastname;
					$_SESSION["sess_vEmail"]=$femail;  
					$_SESSION["sess_eGender"]=$dRefDate;
					$_SESSION["sess_user"]= 'driver';   

					$Photo_Gallery_folder = $tconfig["tsite_upload_images_driver_path"]."/". $iDriverId . '/';
				   
					@unlink($Photo_Gallery_folder.$db_user[0]['vImage']);
					@unlink($Photo_Gallery_folder."1_".$db_user[0]['vImage']);
					@unlink($Photo_Gallery_folder."2_".$db_user[0]['vImage']);
					@unlink($Photo_Gallery_folder."3_".$db_user[0]['vImage']);   
					@unlink($Photo_Gallery_folder."4_".$db_user[0]['vImage']);   
			
					if(!is_dir($Photo_Gallery_folder))
					{
						mkdir($Photo_Gallery_folder, 0777);
					}
		  
					$baseurl =  "http://graph.facebook.com/".$fbid."/picture?type=large";
					$url = $fbid.".jpg";
					$image_name =  system("wget --no-check-certificate -O ".$Photo_Gallery_folder.$url." ".$baseurl);
				  
					if(is_file($Photo_Gallery_folder.$url)) {
					 
						list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
						if($width < $height){
							$final_width = $width;
						}else{
							$final_width = $height;
						}       
						$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
						$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
					}  
					 
					$sql = "UPDATE register_driver set vImage='".$imgname."',eSignUpType = 'Facebook' WHERE iDriverId='".$_SESSION['sess_iMemberId']."'";
					$obj->sql_query($sql); 
					
					$db_sql = "select * from register_driver WHERE iDriverId='".$_SESSION['sess_iMemberId']."'";
					$db_data = $obj->MySQLSelect($db_sql);
					
					$_SESSION["sess_vImage"]= $db_data[0]['vImage'];
					$_SESSION["sess_eGender"]=$db_data[0]['eGender'];
					 
					$link = $tconfig["tsite_url"]."profile.php";
					//echo $link;
					header("Location:".$link);
					exit;
				}
		 
		 
			}
		}catch (FacebookApiException $e) { 
        #print_r($e);exit;
        error_log($e);
        $user = null;
      }
			 
	}

}

if ($session) {
  $logoutUrl = $facebook->getLogoutUrl();
  $user_friends = $facebook->api('/me/friends');
  //$friends_count = count($data['data']);
}
else {
	$loginUrl = $helper->getLoginUrl(array('scope' => 'email'));
	header("Location: ".$loginUrl);
}
?>