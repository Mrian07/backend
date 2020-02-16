<?php
include_once('common.php');
//	echo $APP_TYPE;exit;
// echo $system_status;
// echo"<pre>";print_r($_SESSION);exit;
$script = "Profile";
$user = isset($_SESSION["sess_user"]) ? $_SESSION["sess_user"] : '';
$success = isset($_REQUEST["success"]) ? $_REQUEST["success"] : '';
$var_msg = isset($_REQUEST["var_msg"]) ? $_REQUEST["var_msg"] : '';
$new = '';

if(isset($_SESSION['sess_new'])){
$new = $_SESSION['sess_new'];
unset($_SESSION['sess_new']);
}
$generalobj->check_member_login();
// Start :: Get country name
$sql = "select * from country";
$db_country = $obj->MySQLSelect($sql);
 
$sql = "select * from currency where eStatus = 'Active'";
$db_currency = $obj->MySQLSelect($sql);
// Start :: Get country name
$access = 'company,driver';
$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$generalobj->setRole($access, $url);
if ($_SESSION['sess_user'] == 'company') {
$sql = "select * from company where iCompanyId = '" . $_SESSION['sess_iUserId'] . "'";
$db_user = $obj->MySQLSelect($sql);

$sql = "SELECT vNoc,vCerti from company where iCompanyId = '" . $_SESSION['sess_iUserId'] . "'";
$db_doc = $obj->MySQLSelect($sql);

$sql= "SELECT dm.doc_masterid masterid, dm.doc_usertype ,dm.doc_name_".$_SESSION['sess_lang']."  as d_name , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file , dl.status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $_SESSION['sess_iUserId'] ."' ) dl on dl.doc_masterid=dm.doc_masterid  
where dm.doc_usertype='company' and dm.status='Active' and (dm.country ='".$db_user[0]['vCountry']."' OR dm.country ='All')";

$db_userdoc = $obj->MySQLSelect($sql);
$count_all_doc = count($db_userdoc);

}
if ($_SESSION['sess_user'] == 'driver') {
	$sql = "select * from register_driver where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
	$db_user = $obj->MySQLSelect($sql);

	 $sql= "SELECT dm.doc_masterid masterid, dm.doc_usertype ,dm.doc_name_".$_SESSION['sess_lang']."  as d_name , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file , dl.status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $_SESSION['sess_iUserId'] . "' ) dl on dl.doc_masterid=dm.doc_masterid  
	where dm.doc_usertype='driver' and dm.status='Active' and (dm.country ='".$db_user[0]['vCountry']."' OR dm.country ='All')";
	$db_userdoc = $obj->MySQLSelect($sql);
	
	$count_all_doc = count($db_userdoc);
	
	if($SITE_VERSION == "v5"){
		$data_driver_pref = $generalobj->Get_User_Preferences($_SESSION['sess_iUserId']);
	}
}
//echo '<pre>'; print_r($db_doc); exit;
if (count($db_doc) > 0) {
$noc = $db_doc[0]['vNoc'];
$certi = $db_doc[0]['vCerti'];
if ($_SESSION['sess_user'] == 'driver')
$licence = $db_doc[0]['vLicence'];
} else {
$noc = '';
$certi = '';
$licence = '';
}
$sql = "select * from language_master where eStatus = 'Active'";
$db_lang = $obj->MySQLSelect($sql);
$lang = "";
for ($i = 0;
$i < count($db_lang);
$i++) {
if ($db_user[0]['vLang'] == $db_lang[$i]['vCode']) {
$lang_user = $db_lang[$i]['vTitle'];
}
}

if ($action='document' && isset($_POST['doc_type'])) {
    $expDate=$_POST['dLicenceExp'];
    if (SITE_TYPE == 'Demo') {
        header("location:profile.php?success=2&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        exit;
    }
		$user=$_POST['user'];
        $masterid= $_REQUEST['master'];

    if (isset($_POST['doc_path'])) {
         $doc_path = $_POST['doc_path'];
    }
    $temp_gallery = $doc_path . '/';
     $image_object = $_FILES['driver_doc']['tmp_name'];
     $image_name = $_FILES['driver_doc']['name'];

    if($expDate != ""){

              // $sql = "select ex_date from document_list where doc_userid='".$_REQUEST['id']."' and doc_masterid='".$masterid."'"; 
              // $query = mysql_query($sql);
              // $fetch = mysql_fetch_array($query);
              // if($fetch['ex_date'] != "0000-00-00"){ 

                    // if($fetch['ex_date'] != $expDate){    
                    
                      $sql="UPDATE `document_list` SET  ex_date='".$expDate."' WHERE doc_userid='".$_REQUEST['id']."' and doc_masterid='".$masterid."'";
                      $query= mysql_query($sql);
                  
                  // }else{
                         // if ($image_name == "") {
            
                             // $var_msg = "Please Upload valid file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png";
                             // header("location:profile.php?success=0&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
                    
                // }

                // }
             // }    
        }

    if ($image_name != "") {
        // $check_file_query = "select iDriverId,vNoc from company where iDriverId=" . $_REQUEST['id'];
        // $check_file = $obj->sql_query($check_file_query);
        // $check_file['vNoc'] = $doc_path . '/' . $_REQUEST['id'] . '/' . $check_file[0]['vNoc'];


        /* if ($check_file['vNoc'] != '' && file_exists($check_file['vNoc'])) {
          unlink($doc_path . '/' . $_REQUEST['id'] . '/' . $check_file[0]['vNoc']);
          unlink($doc_path . '/' . $_REQUEST['id'] . '/1_' . $check_file[0]['vNoc']);
          unlink($doc_path . '/' . $_REQUEST['id'] . '/2_' . $check_file[0]['vNoc']);
          } */

        $filecheck = basename($_FILES['driver_doc']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "pdf" && $ext != "doc" && $ext != "docx") {
            $flag_error = 1;
            $var_msg = "You have selected wrong file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png";
        }
        /* if ($_FILES['noc']['size'] > 1048576) {
          $flag_error = 1;
          $var_msg = "Image Size is too Large";
          } */
        if ($flag_error == 1) {
            //header("location:driver_document_action.php?success=1&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
            //$generalobj->getPostForm($_POST, $var_msg, "driver_document_action.php?success=1&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);

            exit;
        } /* else if($_REQUEST['id'] != '') {
          header("location:driver_document_action.php?success=0&var_msg=something went wrong. Try again");

          exit;
          } */ else {
             $Photo_Gallery_folder = $doc_path . '/' . $_REQUEST['id'] . '/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            //$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
            $vImage = $vFile[0];
            $var_msg = "File uploaded successfully";
            $tbl = 'document_list';
			if($user =='company'){
				
            echo  $sql = "select doc_id from  ".$tbl."  where doc_userid='".$_REQUEST[id]."' and doc_usertype='company'  and doc_masterid=".$_REQUEST['doc_type'] ;
            }else{
			$sql = "select doc_id from  ".$tbl."  where doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'] ;
			}
			$db_data = $obj->MySQLSelect($sql);
            $q = "INSERT INTO ";
            $where = '';
			
            if (count($db_data) > 0) {
				
				if($user =='company'){
					
				  $query="UPDATE `".$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$expDate."' WHERE doc_userid='".$_REQUEST[id]."' and doc_usertype='company'  and doc_masterid=".$_REQUEST['doc_type'];
				
					$q = "UPDATE ";
					$where = " WHERE `iDriverId` = '" . $_REQUEST['id'] . "'";
					
				}else{
				
					$query="UPDATE `".$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$expDate."' WHERE doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
						$q = "UPDATE ";
						$where = " WHERE `iDriverId` = '" . $_REQUEST['id'] . "'";
					
				}
			}else {
					
				if($user =='company'){
						
					$query =" INSERT INTO `".$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) "
                   . "VALUES ". "( '".$_REQUEST['doc_type']."', 'company', '".$_REQUEST['id']."', '".$expDate."', '".$vImage."', 'Inactive', CURRENT_TIMESTAMP)";
					
					}else{
						
						$query =" INSERT INTO `".$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) "
                   . "VALUES ". "( '".$_REQUEST['doc_type']."', 'driver', '".$_REQUEST['id']."', '".$expDate."', '".$vImage."', 'Inactive', 	CURRENT_TIMESTAMP)";
					}
			}	
		    
          //  ECHO $query = $q . " `" . $tbl . "` SET `vNoc` = '" . $vImage . "'" . $where;
            $obj->sql_query($query);

            //Start :: Log Data Save
            if (empty($check_file[0]['vNoc'])) {
                $vNocPath = $vImage;
            } else {
                $vNocPath = $check_file[0]['vNoc'];
            }
            $generalobj->save_log_data($_SESSION['sess_iUserId'], $_REQUEST['id'], 'company', 'noc', $vNocPath);
            //End :: Log Data Save
            // Start :: Status in edit a Document upload time
            // $set_value = "`eStatus` ='inactive'";
            //$generalobj->estatus_change('register_driver','iDriverId',$_REQUEST['id'],$set_value);
            // End :: Status in edit a Document upload time
            header("location:profile.php?success=1&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        }
    }
}
//echo "<pre>";print_r($db_user);echo "</pre>";  
?>
<!DOCTYPE html>
<html lang="en" dir="<?= (isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr'; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?= $SITE_NAME ?> |<?=$langage_lbl['LBL_HEADER_PROFILE_TXT']; ?> </title>
        <!-- Default Top Script and css -->
        <?php include_once("top/top_script.php"); ?>
        <link rel="stylesheet" href="assets/css/bootstrap-fileupload.min.css" >
        <!-- End: Default Top Script and css-->   
    </head>
    <body>
        <!-- home page -->
        <div id="main-uber-page">
            <!-- Left Menu -->
            <?php include_once("top/left_menu.php"); ?>
            <!-- End: Left Menu-->
            <!-- Top Menu -->
            <?php include_once("top/header_topbar.php"); ?>
            <!-- End: Top Menu-->
            <!-- contact page--> 
            <div class="page-contant">
                <div class="page-contant-inner">
                    <h2 class="header-page"><?= $langage_lbl['LBL_PROFILE_HEADER_PROFILE_TXT']; ?></h2>
                    <?php
                    if($_SESSION['sess_user'] == 'company') {
                    if(SITE_TYPE == 'Demo'){
                    ?><div class="demo-warning">
                        <p><?= $langage_lbl['LBL_WE_SEE_YOU_HAVE_REGISTERED_AS_A_COMPANY']; ?></p>
                        <p><?= $langage_lbl['LBL_SINCE_IT_IS_DEMO_VERSION']; ?></p>

                        <p><?= $langage_lbl['LBL_STEP1']; ?></p>
                        <!--	<p><?= $langage_lbl['LBL_STEP2']; ?></p>-->
                        <p><?= $langage_lbl['LBL_STEP3']; ?></p>

                        <p><?= $langage_lbl['LBL_HOWEVER_IN_REAL_SYSTEM']; ?></p>
                    </div>
                    <?}else{
                    ?><div class="demo-warning">
                        <p>
                            <?= $langage_lbl['LBL_WE_SEE_YOU_HAVE_REGISTERED_AS_A_COMPANY']; ?> 
                            <? if ($db_user[0]['vCerti'] == '' || $db_user[0]['vNoc'] == ''){ ?>
                            <?= $langage_lbl['LBL_KINDLY_PROVIDE_BELOW']; ?>
                            <? } ?>
                        <p><?= $langage_lbl['LBL_ALSO_ADD_DRIVERS']; ?></p>
                        <p><?= $langage_lbl['LBL_EITHER_YOU_AS_A_COMPANY_DRIVER']; ?></p>
                    </div>
                    <?php
                    }
                    }
                    else {
                    ?>
                    <? if(SITE_TYPE=='Demo'){?>
                    <div class="demo-warning">
                        <p><?= $langage_lbl['LBL_PROFILE_WE_SEE_YOU_HAVE_REGISTERED_AS_A_DRIVER']; ?></p>
                        <p><?= $langage_lbl['LBL_SINCE_IT_IS_DEMO_VERSION_ADDVEHICLE']; ?></p>

                        <p><?= $langage_lbl['LBL_HOWEVER_IN_REAL_SYSTEM_DRIVER']; ?></p>
                    </div>
                    <?}else{
                    if ($db_user[0]['vLicence'] == '' || $db_user[0]['vCerti'] == '' || $db_user[0]['vNoc'] == ''){
                    ?><div class="demo-warning">
                        <p>
                            <?php
                            if(isset($_REQUEST['first']) && $_REQUEST['first'] == 'yes') { echo $langage_lbl['LBL_PROFILE_WE_SEE_YOU_HAVE_REGISTERED_AS_A_DRIVER'];
                            }
                            ?> <?= $langage_lbl['LBL_KINDLY_PROVIDE_BELOW_VISIBLE']; ?>
                        </p>
                    </div>
                    <?php
                    }
                    }
                    }
                    ?>
                    <!-- profile page -->
                    <div class="driver-profile-page">
                        <?php if ($success == 1) { ?>
                        <div class="demo-success msgs_hide">
                            <button class="demo-close" type="button">×</button>
                            <?= $var_msg ?>
                        </div>
                        <?php
                        }
                        else if($success == 2)
                        {
                        ?>
                        <div class="demo-danger msgs_hide">
                            <button class="demo-close" type="button">×</button>
                            <?= $langage_lbl['LBL_EDIT_DELETE_RECORD']; ?>
                        </div>
                        <?php
                        }
                        else if($success == 0 && $var_msg != "")
                        {
                        ?>
                        <div class="demo-danger msgs_hide">
                            <button class="demo-close" type="button">×</button>
                            <?= $var_msg; ?>
                        </div>
                        <?php } ?>
                        <div class="driver-profile-top-part" id="hide-profile-div">
                            <div class="driver-profile-img">
                                <span>
                                    <?php
                                    if ($db_user[0]['vImage'] == 'NONE' || $db_user[0]['vImage'] == '')
                                    {
                                    ?>
                                    <img src="assets/img/profile-user-img.png" alt="">
                                    <? }else{
                                    if($_SESSION['sess_user'] == 'company'){
                                    $img_path = $tconfig["tsite_upload_images_compnay"];
                                    }else if($_SESSION['sess_user'] == 'driver'){
                                    $img_path = $tconfig["tsite_upload_images_driver"];
                                    }?>
                                    <img src = "<?= $img_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImage'] ?>" style="height:150px;"/>
                                    <?php } ?>
                                </span>
                                <b>
                                    <a data-toggle="modal" data-target="#uiModal_4"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                </b>
                            </div>
                            <div class="driver-profile-info">
                                <h3><?if ($_SESSION['sess_user'] == 'driver') { echo  $generalobj->cleanall(htmlspecialchars($db_user[0]['vName'] . " " . $db_user[0]['vLastName'])); }?><?if ($_SESSION['sess_user'] == 'company') { echo  $generalobj->cleanall(htmlspecialchars($db_user[0]['vCompany'])); }?></h3>

                                <p><?= $db_user[0]['vEmail'] ?></p>
                                <p><?= $db_user[0]['vPhone'] ?></p>
                                <?if ($_SESSION['sess_user'] == 'driver') { 
                                if($REFERRAL_SCHEME_ENABLE == 'Yes'){ 

                                ?>
                                <p> <?php echo $langage_lbl['MY_REFERAL_CODE']; ?>&nbsp; : <?= $db_user[0]['vRefCode']; ?></p>

                                <?php
                                }
                                }
                                ?>
                                <span><a id="show-edit-profile-div"><i class="fa fa-pencil" aria-hidden="true"></i><?= $langage_lbl['LBL_PROFILE_EDIT']; ?></a></span>
                            </div>
                        </div>
                        <!-- form -->
                        <div class="edit-profile-detail-form" id="show-edit-profile" style="display: none;">
                            <form id="frm1" method="post" action="javascript:void(0);" >
                                <input  type="hidden" class="edit" name="action" value="login">
                                <div class="show-edit-profile-part">
                                    <span>
									    <label><?=$langage_lbl['LBL_PROFILE_YOUR_EMAIL_ID']; ?> *</label>
                                        <input type="hidden" name="uid" id="u_id1" value="<?= $_SESSION['sess_iUserId']; ?>">
                                        <input type="email" id="in_email" class="edit-profile-detail-form-input" placeholder="<?= $langage_lbl['LBL_PROFILE_YOUR_EMAIL_ID']; ?>" value = "<?= $db_user[0]['vEmail'] ?>" name="email" <?= isset($db_user[0]['vEmail']) ? '' : ''; ?>  required onChange="validate_email(this.value,'<?php echo $id; ?>')"  > 
                                        <div class="required-label" id="emailCheck"></div>
                                    </span> 
                                    <?php
                                    if ($_SESSION['sess_user'] == 'driver') {
                                    ?>
                                    <span>
									
									
                                        <label><?=$langage_lbl['LBL_SIGN_UP_FIRST_NAME_HEADER_TXT']; ?> *</label>
                                        <input type="text" class="edit-profile-detail-form-input" placeholder="<?= $langage_lbl['LBL_YOUR_FIRST_NAME']; ?>" value = "<?= $generalobj->cleanall(htmlspecialchars($db_user[0]['vName'])); ?>" name="name" required>
                                    </span> 
                                    <span>
                                        <label><?=$langage_lbl['LBL_YOUR_LAST_NAME']; ?> *</label>
                                        <input type="text" class="edit-profile-detail-form-input" placeholder="<?= $langage_lbl['LBL_YOUR_LAST_NAME']; ?>" value = "<?= $generalobj->cleanall(htmlspecialchars($db_user[0]['vLastName'])); ?>" name="lname" required>
                                    </span> 
                                    <?
                                    }
                                    else if ($_SESSION['sess_user'] == 'company') {
                                    ?>
                                    <span>
                                        <label><?=$langage_lbl['LBL_COMPANY_SIGNUP']; ?> *</label>
                                        <input type="text" class="edit-profile-detail-form-input"  placeholder="<?= $langage_lbl['LBL_PROFILE_Company_name']; ?>" value = "<?=$generalobj->cleanall(htmlspecialchars($db_user[0]['vCompany'])); ?>" name="vCompany" required>
                                    </span> 
                                    <?
                                    }


                                    ?>

                                    <?php
                                    if ($_SESSION['sess_user'] == 'driver') {
                                    ?>
                                    <span>
                                        <label<?=$langage_lbl['LBL_PROFILE_BIRTHDAY_TXT']; ?> *</label>
                                        <input type="text" class="edit-profile-detail-form-input" value = "<?= $db_user[0]['dBirthDate'] ?>" name="dBirthDate" style="background: #e1e1e1;" o disabled>
                                    </span>
                                    <? } ?>

                                    <span>
                                        <label><?=$langage_lbl['LBL_SELECT_CONTRY']; ?> *</label>
                                        <select class="custom-select-new" name = 'vCountry' onChange="changeCode(this.value);" required>
                                            <option value="">--select--</option>
                                            <? for($i=0;$i<count($db_country);$i++){ ?>
                                            <option value = "<?= $db_country[$i]['vCountryCode'] ?>" <?
                                                    if($db_user[0]['vCountry']== $db_country[$i]['vCountryCode']){?>selected<? }?>><?= $db_country[$i]['vCountry'] ?></option>
                                            <? } ?>
                                        </select>
                                    </span>
                                    <span>
                                        <label><?=$langage_lbl['LBL_Phone_Number']; ?> *</label>
                                        <input type="text" pattern=".{10}" class="input-phNumber1" id="code" name="vCode" value="<?= $db_user[0]['vCode'] ?>" readonly >
                                        <input name="phone" type="text" value="<?= $db_user[0]['vPhone'] ?>" class="edit-profile-detail-form-input input-phNumber2" placeholder="<?= $langage_lbl['LBL_Phone_Number']; ?>"  pattern="[0-9]{1,}" title="Please enter proper phone number." required/>
                                    </span>
                                    <?php if ($_SESSION['sess_user'] == 'driver') { ?>
									<? if(count($db_currency) > 1){?>
                                    <span>
                                        <label><?=$langage_lbl['LBL_SELECT_CURRENCY']; ?></label>
                                        <select class="custom-select-new" name = 'vCurrencyDriver' required>
                                            <option value="">--select--</option>
                                            <? for($i=0;$i<count($db_currency);$i++){ ?>
                                            <option value = "<?= $db_currency[$i]['vName'] ?>" <?if($db_user[0]['vCurrencyDriver']==$db_currency[$i]['vName']){?>selected<? } ?>><?= $db_currency[$i]['vName'] ?></option>
                                            <? } ?>
                                        </select>
                                    </span>
                                    <?php 
										}else{
											?><input name="vCurrencyDriver" type="hidden" class="create-account-input" value="<?= $db_currency[0]['vName'] ?>" id="vCurrencyDriver"/>
									<?	}
									} ?>
                                    <?php
                                    if ($_SESSION['sess_user'] == 'driver' && $APP_TYPE == 'UberX') {
                                    ?>
                                    <span>
                                        <label><?=$langage_lbl['LBL_PROFILE_DESCRIPTION']; ?></label>                                            
                                        <textarea name="tProfileDescription" rows="3" cols="40" class="form-control" id="tProfileDescription" placeholder="<?= $langage_lbl['LBL_PROFILE_DESCRIPTION']; ?>"><?= $db_user[0]['tProfileDescription'] ?></textarea>
                                    </span>
                                    <?php } ?>
                                    <p>
                                        <input name="save" type="submit" value="<?= $langage_lbl['LBL_Save']; ?>" class="save-but" onClick="return validate_email_submit('login')">
                                        <input name="" id="hide-edit-profile-div" type="button" value="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" class="cancel-but">
                                    </p>
                                    <div style="clear:both;"></div>
                                </div>                        
                            </form>
                        </div>
                        <!-- from -->
                        <div <?php if($_SESSION['sess_user'] == 'driver'){ ?> class="detail-driver driver-profile-mid-part"  <?php }else{ ?> class='driver-profile-mid-part' <?php } ?>>
                            <ul >
                                <li <?php if($_SESSION['sess_user'] == 'driver'){ ?> class='driver-profile-mid-part-details' <?php }else{ ?> class='company-profile-mid-part-details' <?php } ?> >
                                    <div class="driver-profile-mid-inner">
                                        <div class="profile-icon"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
                                        <h3><?= $langage_lbl['LBL_PROFILE_ADDRESS']; ?></h3>
                                        <p><?php echo $generalobj->cleanall(htmlspecialchars($db_user[0]['vCaddress'])) ; echo ($db_user[0]['vCadress2'] != "") ? ',' . 
											$generalobj->cleanall(htmlspecialchars($db_user[0]['vCadress2'])) : ''; ?></p>
                                        <span><a id="show-edit-address-div" class="hide-address-div hidev"><i class="fa fa-pencil" aria-hidden="true"></i><?= $langage_lbl['LBL_PROFILE_EDIT']; ?></a></span> 
                                    </div> 
                                </li>
                                <li <?php if($_SESSION['sess_user'] == 'driver'){ ?> class='driver-profile-mid-part-details' <?php }else{ ?>class='company-profile-mid-part-details' <?php } ?>>
                                    <div class="driver-profile-mid-inner">
                                        <div class="profile-icon"><i class="fa fa-unlock-alt" aria-hidden="true"></i></div>
                                        <h3><?= $langage_lbl['LBL_PROFILE_PASSWORD_LBL_TXT']; ?></h3>
                                        <?php /*<p><? for ($i = 0; $i < strlen($generalobj->decrypt($db_user[0]['vPassword'])); $i++)
                                            echo '*'; ?></p> */ ?>
                                        <span><a id="show-edit-password-div" class="hide-password-div hidev"><i class="fa fa-pencil" aria-hidden="true"></i><?= $langage_lbl['LBL_PROFILE_EDIT']; ?></a></span> 
                                    </div>
                                </li>
								<? if(count($db_lang) > 1){ ?>
                                <li <?php if($_SESSION['sess_user'] == 'driver'){ ?> class='driver-profile-mid-part-details' <?php }else{ ?>class='company-profile-mid-part-details'<?php } ?>>
                                    <div class="driver-profile-mid-inner">
                                        <div class="profile-icon"><i class="fa fa-language" aria-hidden="true"></i></div>
                                        <h3><?= $langage_lbl['LBL_PROFILE_LANGUAGE_TXT']; ?></h3>
                                        <p><?= $lang_user ?></p>
                                        <span><a id="show-edit-language-div" class="hide-language-div hidev"><i class="fa fa-pencil" aria-hidden="true"></i><?= $langage_lbl['LBL_PROFILE_EDIT']; ?></a></span> 
                                    </div>
                                </li>
								<? } ?>
                                <?php if($_SESSION['sess_user'] == 'driver'){ ?> 
                                <li <?php if($_SESSION['sess_user'] == 'driver'){ ?> class='driver-profile-mid-part-details' <?php }else{ ?>class='company-profile-mid-part-details' <?php } ?>>
                                    <div class="driver-profile-mid-inner">
                                        <div class="profile-icon"><i class="fa fa-bank" aria-hidden="true"></i></div>
                                        <h3><?= $langage_lbl['LBL_BANK_DETAILS_TXT']; ?></h3>
                                        <p><?= $db_user[0]['vBankName'] ?></p>
                                        <span><a id="show-edit-bankdetail-div" class="hide-bankdetail-div hidev"><i class="fa fa-pencil" aria-hidden="true"></i><?= $langage_lbl['LBL_PROFILE_EDIT']; ?></a></span> 
                                    </div>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>

                        <!-- Address form -->
                        <div class="profile-Password showV" id="show-edit-address" style="display: none;">
                            <form id = "frm2" method = "post" onsubmit ="return editPro('address')">
                                <p class="address-pointer" ><img src="assets/img/pas-img1.jpg" alt=""></p>
                                <h3><i class="fa fa-map-marker" aria-hidden="true"></i><?= $langage_lbl['LBL_PROFILE_ADDRESS']; ?></h3>
                                <input  type="hidden" class="edit" name="action" value="address">
                                <div class="profile-address-part">
                                    <span>
                                        <label><?= $langage_lbl['LBL_ADDRESS_SIGNUP'] ?>*</label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_PROFILE_ADDRESS']; ?> 1" value="<?= $generalobj->cleanall(htmlspecialchars($db_user[0]['vCaddress'])); ?>" name="address1" required>
                                    </span> 
                                    <span>
                                        <label><?= $langage_lbl['LBL_PROFILE_ADDRESS2'] ?></label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_LINE']; ?> 2" value="<?=  $generalobj->cleanall(htmlspecialchars($db_user[0]['vCadress2'])); ?>" name="address2"></span>

                                    <span>
										<label><?= $langage_lbl['LBL_COUNTRY_TXT'] ?> * </label>
										
											<select class="form-control" name = 'vCountry' id="vCountry" onChange="setState(this.value,'');" required>
											<option value="">Select</option>
													<? for($i=0;$i<count($db_country);$i++){ ?>
													<option  <?if($db_user[0]['vCountry']== $db_country[$i]['vCountryCode']){?>selected<? } ?> value = "<?= $db_country[$i]['vCountryCode'] ?>"><?= $db_country[$i]['vCountry'] ?></option>
													<? } ?>
											</select>
									</span>		
									<span>			
									<label><?= $langage_lbl['LBL_STATE_TXT'] ?> </label>
												<select class="form-control" name = 'vState' id="vState" onChange="setCity(this.value,'');" >
													<option value="">Select</option>
												</select>
									</span>	
									<span>
									<label><?= $langage_lbl['LBL_CITY_TXT'] ?></label>
												<select class="form-control" name = 'vCity' id="vCity"  >
													<option value="">Select</option>
												</select>
									</span>		
									<!--<span>
                                        <label>City</label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_City']; ?> 1" value="<?= $db_user[0]['vCity'] ?>" name="vCity" required>
                                    </span> -->
									
                                    <span>
                                        <label>Zip</label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_ZIP_CODE']; ?> 2" value="<?= $db_user[0]['vZip'] ?>" name="vZipcode" required></span>
                                </div>

                                <span>
                                    <b>
                                        <input name="save" type="submit" value="<?= $langage_lbl['LBL_Save']; ?>" class="profile-Password-save">
                                        <input name="" id="hide-edit-address-div" type="button" value="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" class="profile-Password-cancel">
                                    </b>
                                </span>
                                <div style="clear:both;"></div>

                            </form>
                        </div>
                        <!-- End: Address Form -->
                        <!-- Password form -->                    
                        <div class="profile-Password showV" id="show-edit-password" style="display: none;">
                            <form id="frm3" method="post" action="javascript:void(0);" onSubmit="return validate_password()">
                                <p class="password-pointer"><img src="assets/img/pas-img1.jpg" alt=""></p>
                                <h3><i class="fa fa-unlock-alt" aria-hidden="true"></i><?= $langage_lbl['LBL_PROFILE_PASSWORD_LBL_TXT']; ?></h3>
                                <input type="hidden" name="action" id="action" value = "pass"/>
                                <div class="row">
								<?php if($db_user[0]['vFbId'] >= 0 && $db_user[0]['vPassword'] != ""){ ?>
                                    <div class="col-md-4">
                                        <span>
                                            <label><?=$langage_lbl['LBL_CURR_PASS_HEADER']; ?> *</label>
                                            <input type="password" class="input-box" placeholder="<?= $langage_lbl['LBL_CURR_PASS_HEADER']; ?>" name="cpass" id="cpass" required>
                                        </span>
                                    </div>
									<?php } ?> 
                                    <div class="col-md-4">
                                        <span>
                                            <label><?=$langage_lbl['LBL_NEW_PASSWORD_TXT']; ?> * </label>
                                            <input type="password" class="input-box" placeholder="<?= $langage_lbl['LBL_UPDATE_PASSWORD_HEADER_TXT']; ?>" name="npass" id="npass" required>
                                        </span>
                                    </div>
                                    <div class="col-md-4">
                                        <span>
                                            <label><?=$langage_lbl['LBL_Confirm_New_Password']; ?> *</label>
                                            <input type="password" class="input-box" placeholder="<?= $langage_lbl['LBL_Confirm_New_Password']; ?>" name="ncpass" id="ncpass" required>
                                        </span>
                                    </div>
                                </div>
                                <span>
                                    <b>
                                        <input name="save" type="submit" value="<?= $langage_lbl['LBL_Save']; ?>" class="profile-Password-save">
                                        <input name="" id="hide-edit-password-div" type="button" value="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" class="profile-Password-cancel">
                                    </b>
                                </span>
                                <div style="clear:both;"></div>
                            </form>
                        </div>

                        <!-- End: Password Form -->
                        <!-- Language form -->
                        <div class="profile-Password showV" id="show-edit-language" style="display: none;">
                            <form id="frm4" method = "post">
                                <p class="language-pointer"><img src="assets/img/pas-img1.jpg" alt=""></p>
                                <h3><i class="fa fa-language" aria-hidden="true"></i><?= $langage_lbl['LBL_PROFILE_LANGUAGE_TXT']; ?></h3>
                                <input type="hidden" value= "lang1" name="action">
                                <div class="edit-profile-detail-form-password-inner profile-language-part">
                                    <span>
                                        <label><?=$langage_lbl['LBL_PROFILE_SELECT_LANGUAGE']; ?></label>
                                        <select name="lang1" class="custom-select-new profile-language-input">
                                            <?php
                                            for ($i = 0;
                                            $i < count($db_lang);
                                            $i++) {
                                            ?>
                                            <option value="<?= $db_lang[$i]['vCode'] ?>" <? if ($db_user[0]['vLang'] == $db_lang[$i]['vCode']) { ?> selected <?
                                                    } ?> ><? echo $db_lang[$i]['vTitle']; ?></option>
                                                    <?php } ?>
                                        </select>
                                    </span>
                                </div> 
                                <span>                                
                                    <b>
                                        <input name="save" type="button" value="<?= $langage_lbl['LBL_Save']; ?>" class="profile-Password-save" onClick="editProfile('lang');">
                                        <input name="" id="hide-edit-language-div" type="button" value="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" class="profile-Password-cancel">
                                    </b>
                                </span>
                                <div style="clear:both;"></div>

                            </form>
                        </div>
                        <!-- End: Language Form -->

                        <!-- bank detail -->
                        <?php if($_SESSION['sess_user'] == 'driver'){ ?> 

                        <div class="profile-Password showV" id="show-edit-bankdeatil" style="display: none;">
                            <form id = "frm6" method = "post" onsubmit ="return editPro('bankdetail')">
                                <p class="bankdeail-pointer"><img src="assets/img/pas-img1.jpg" alt=""></p>
                                <h3><i class="fa fa-bank" aria-hidden="true"></i><?= $langage_lbl['LBL_BANK_DETAILS_TXT']; ?></h3>
                                <input  type="hidden" class="edit" name="action" value="bankdetail">
                                <div class="profile-address-part">
                                    <span>
                                        <label><?=$langage_lbl['LBL_PAYMENT_EMAIL_TXT']; ?> *</label>
                                        <input type="email" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_PAYMENT_EMAIL_TXT']; ?>" value="<?= $db_user[0]['vPaymentEmail'] ?>" name="vPaymentEmail" required>
                                    </span> 
                                    <span>
                                        <label><?=$langage_lbl['LBL_PROFILE_BANK_HOLDER_TXT']; ?>  </label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_ACCOUNT_HOLDER_NAME']; ?>" value="<?= $db_user[0]['vBankAccountHolderName'] ?>"
                                               name="vBankAccountHolderName" ></span>
                                    <span>
                                        <label><?=$langage_lbl['LBL_ACCOUNT_NUMBER']; ?></label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_ACCOUNT_NUMBER']; ?>" value="<?= $db_user[0]['vAccountNumber'] ?>" 
                                               name="vAccountNumber" ></span>
                                    <span>
                                        <label><?=$langage_lbl['LBL_BANK_NAME']; ?>	</label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_NAME_OF_BANK']; ?>" value="<?= $db_user[0]['vBankName'] ?>" name="vBankName" ></span>
                                    <span>
                                        <label><?=$langage_lbl['LBL_BANK_LOCATION']; ?></label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_BANK_LOCATION']; ?>" value="<?= $db_user[0]['vBankLocation'] ?>" name="vBankLocation" >
                                    </span>
                                    <span>
                                        <label><?=$langage_lbl['LBL_BIC_SWIFT_CODE']; ?></label>
                                        <input type="text" class="profile-address-input" placeholder="<?= $langage_lbl['LBL_BIC_SWIFT_CODE']; ?>" value="<?= $db_user[0]['vBIC_SWIFT_Code'] ?>"
                                               name="vBIC_SWIFT_Code" ></span>
                                </div>

                                <span>
                                    <b>
                                        <input name="save" type="submit" value="<?= $langage_lbl['LBL_Save']; ?>" class="profile-Password-save">
                                        <input name="" id="hide-edit-bankdetail-div" type="button" value="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" class="profile-Password-cancel">
                                    </b>
                                </span>
                                <div style="clear:both;"></div>                                
                            </form>
                        </div>

                        <?php
                        }

                        if($APP_TYPE == 'UberX'){

                        $class_name = 'driver-profile-bottom-part required-documents-bottom-part two-part-document';

                        }else{

                        $class_name = 'driver-profile-bottom-part required-documents-bottom-part';

                        }
                        ?>
                        <!-- end bank detail -->

						   <!-- end bank detail -->
						  <? if($SITE_VERSION == "v5" && $_SESSION['sess_user'] == "driver"){ ?>
						 <div class="<?php echo $class_name; ?>">
                            <h3><?=$langage_lbl['LBL_PREFERENCES_TEXT']?> </h3>
							<p>
								<div class="driver-profile-info-aa col-md-5"> <? foreach($data_driver_pref as $val){?>
											<img data-toggle="tooltip" class="borderClass-aa border_class-bb" title="<?=$val['pref_Title']?>" src="<?=$tconfig["tsite_upload_preference_image_panel"].$val['pref_Image']?>">
													<? } ?>
									</div>
                                    
									<span class="col-md-5"><a href="preferences.php" id="show-edit-language-div" class="hide-language">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                    <?=$langage_lbl['LBL_PROFILE_EDIT']?></a></span>
                                    
                                   
							</p>
                            
							</div>
						<? } ?>
                        <div class="<?php echo $class_name; ?>">
                            <h3><?= $langage_lbl['LBL_REQUIRED_DOCS']; ?></h3>
                            <div class="profile-req-doc">
                            <div class="profile-req-doc-inner">
                             <?php for ($i = 0; $i < $count_all_doc; $i++) { ?>
                             <div class="panel panel-default upload-clicking">
                                            <input  type="hidden" id="ex_status" value="<?php echo $db_userdoc[$i]['ex_status']; ?>">
                                            <div class="panel-heading"><?php echo $db_userdoc[$i]['d_name']; ?> </div>
                                            <input type="hidden" id="doc_id" value="<?php  $db_userdoc[$i]['doc_file']; ?>">
                                            <div class="panel-body">
                                                <?php if ($db_userdoc[$i]['doc_file'] != '') { ?>
                                                    <?php
                                                    $file_ext = $generalobj->file_ext($db_userdoc[$i]['doc_file']);
                                                    if ($file_ext == 'is_image') {
														
														    if ($_SESSION['sess_user'] == 'company') {
																
																 $path=$tconfig["tsite_upload_compnay_doc"];
																
															}else{
																
																 $path=$tconfig["tsite_upload_driver_doc"];
															}
	
														?>
                                                        <span><img src = "<?= $path. '/' .$_SESSION['sess_iUserId']. '/' . $db_userdoc[$i]['doc_file'] ?>" style="width:200px;cursor:pointer;" alt ="<?= $db_userdoc[$i]['d_name']; ?> Image" data-toggle="modal" data-target="#myModallicence"/></span>
														
                                                    <?php } else { 
																
															if ($_SESSION['sess_user'] == 'company') {
																
																$tconfig=$tconfig["tsite_upload_compnay_doc"];
																
															}else{
																
																$tconfig=$tconfig["tsite_upload_driver_doc"];
															}
													?>
                                                        <a href="<?= $tconfig. '/' .$_SESSION['sess_iUserId'].'/' . $db_userdoc[$i]['doc_file'] ?>" target="_blank"><?php echo $db_userdoc[$i]['d_name']; ?></a>
                                                    
													
													<?php } ?>
                                                    <?php
                                                } else {
                                                    echo $db_userdoc[$i]['d_name'] . ' not found';
                                                }
    											?>
											   <b><button class="btn btn-info" data-toggle="modal" data-target="#uiModal" id="custId"  onClick="setModel001('<?php echo $db_userdoc[$i]['masterid']; ?>')" >
	
	                                                    <?php
                                                        if ($db_userdoc[$i]['doc_file'] != '') {
                                                            echo $db_userdoc[$i]['d_name'];
                                                        } else {
                                                            echo $db_userdoc[$i]['d_name'];
                                                        }
                                                        ?>
                                                    </button></b>
                                            </div>
                                        </div>
                             <?php } ?>
                             </div>  </div>
                             </div>
                            <div class="col-lg-12">
                                    <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-content image-upload-1">
                                            <div class="fetched-data"></div>
                                        </div>
                                    </div>
                            </div>
						
						
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
			<div class="col-lg-12">
				<div class="modal fade" id="uiModal_4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-content image-upload-1 popup-box3">
						<div class="upload-content">
							<h4><?=$langage_lbl['LBL_PROFILE_PICTURE'];?></h4>
							<form class="form-horizontal" id="frm9" method="post" enctype="multipart/form-data" action="upload_doc.php" name="frm9">
								<input type="hidden" name="action" value ="photo"/>
								<input type="hidden" name="img_path" value ="
								<?php
									if ($_SESSION['sess_user'] == 'company') {
										echo $tconfig["tsite_upload_images_compnay_path"];
										} else if ($_SESSION['sess_user'] == 'driver') {
										echo $tconfig["tsite_upload_images_driver_path"];
									}
								?>"/>
								<div class="form-group">
									<div class="col-lg-12">
										<div class="fileupload fileupload-new" data-provides="fileupload">
											<div class="fileupload-preview thumbnail">
												<?php if ($db_user[0]['vImage'] == 'NONE' || $db_user[0]['vImage'] == '') { ?>
													
													<img src="assets/img/profile-user-img.png" alt="">
													
													<? } else {
														if($_SESSION['sess_user'] == 'company'){
															$img_path = $tconfig["tsite_upload_images_compnay"];
															}else if($_SESSION['sess_user'] == 'driver'){
															$img_path = $tconfig["tsite_upload_images_driver"];
														}?>
														<img src = "<?= $img_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImage'] ?>" style="height:150px;"/>
												<?php } ?>
											</div>
											<div>
												<span class="btn btn-file btn-success"><span class="fileupload-new"><?=$langage_lbl['LBL_UPLOAD_PHOTO']; ?></span><span class="fileupload-exists"><?=$langage_lbl['LBL_CHANGE']; ?></span><input type="file" name="photo"/></span>
												<a href="#" class="btn btn-danger" data-dismiss="fileupload">X</a>
											</div>
										</div>
									</div>
								</div>
								<input type="submit" class="save" name="save" value="<?=$langage_lbl['LBL_Save']; ?>"><input type="button" class="cancel" data-dismiss="modal" name="cancel" value="<?=$langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>">
							</form>
							
							<div style="clear:both;"></div>
						</div>
					</div>
				</div>
			</div>
		
            <div class="col-lg-12">
			
				<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content modal-content-profile">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="H2"><?= $langage_lbl['LBL_NOTE_FOR_DEMO']; ?></h4>
                            </div>
                            <div class="modal-body">
                                <form role="form" name="verification" id="verification">
                                    <?if($_SESSION['sess_user']=='driver'){?>
                                    <p><?= $langage_lbl['LBL_PROFILE_WE_SEE_YOU_HAVE_REGISTERED_AS_A_DRIVER']; ?></p>
                                    <p><?= $langage_lbl['LBL_SINCE_IT_IS_DEMO_VERSION_ADDVEHICLE']; ?></p>

                                    <p><?= $langage_lbl['LBL_HOWEVER_IN_REAL_SYSTEM_DRIVER']; ?></p>
                                    <?}else{?>
                                    <p><?= $langage_lbl['LBL_WE_SEE_YOU_HAVE_REGISTERED_AS_A_COMPANY']; ?></p>
                                    <p><?= $langage_lbl['LBL_SINCE_IT_IS_DEMO_VERSION']; ?></p>

                                    <p><?= $langage_lbl['LBL_STEP1']; ?></p>
                                    <!--p><?//= $langage_lbl['LBL_STEP2']; ?></p-->
                                    <p><?= $langage_lbl['LBL_STEP3']; ?></p>

                                    <p><?= $langage_lbl['LBL_HOWEVER_IN_REAL_SYSTEM']; ?></p>
                                    <?}?>
                                    <div class="form-group">

                                    </div>
                                    <p class="help-block" id="verification_error"></p>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- footer part -->
            <?php include_once('footer/footer_home.php'); ?>
            <!-- footer part end -->
            <!-- -->
            <div style="clear:both;"></div>
        </div>
        <!-- home page end-->
        <!-- Footer Script -->
        <?php include_once('top/footer_script.php'); ?>
		<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css" />
       <script src="assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
        
	   <link rel="stylesheet" href="assets/validation/validatrix.css" />
      
        <script src="assets/plugins/jasny/js/bootstrap-fileupload.js"></script>


        <!-- End: Footer Script -->
        <script type="text/javascript">

                                                                //$(".demo-success").hide(1000);
                                                                //var successMsg = '<?php echo $var_msg; ?>';
                                                                var successMSG1 = '<?php echo $success; ?>';

                                                                if (successMSG1 != '') {
                                                                    setTimeout(function () {
                                                                        $(".msgs_hide").hide(1000)
                                                                    }, 5000);
                                                                }



                                                                $("#dp3").datepicker();
                                                                $("#dp3").datepicker({
                                                                    dateFormat: "yy-mm-dd",
                                                                    changeYear: true,
                                                                    changeMonth: true,
                                                                    yearRange: "-100:+10"
                                                                });
                                                                $(document).ready(function () {
                                                                    $("#show-edit-profile-div").click(function () {
                                                                        $("#hide-profile-div").hide();
                                                                        $("#show-edit-profile").show();
                                                                    });
                                                                    $("#hide-edit-profile-div").click(function () {
                                                                        $("#show-edit-profile").hide();
                                                                        $("#hide-profile-div").show();
                                                                    });
                                                                });
        </script>
        <script type="text/javascript">
		
		 function setModel001(idVal) {
            // $('#uiModal').on('show.bs.modal', function (e) {
                // var rowid = $(e.relatedTarget).data('id');
                var id = '<?php echo $_SESSION['sess_iUserId']; ?>';
				 var user = '<?php echo $_SESSION['sess_user']; ?>';
             
            $.ajax({
                type: 'post',
                url: 'driver_document_fetch.php', //Here you will fetch records 
                data: 'rowid=' + idVal + '-' + id+'-'+user, //Pass $id
                success: function (data) {
                
                    $('#uiModal').modal('show');
                    $('.fetched-data').html(data);//Show fetched data from database
                    
                }
            });
        }
		
		    $(document).ready(function () {
                $("#show-edit-address-div").click(function () {
                    $('.hidev').show();
                    $('.showV').hide();
                    $(".hide-address-div").hide();
                    $("#show-edit-address").show(300);
                });
                $("#hide-edit-address-div").click(function () {
                    $('.hidev').show();
                    $('.showV').hide();
                    $("#show-edit-address").hide();
                    $(".hide-address-div").show();
                });
            });
        </script>
        <script type="text/javascript">

            $(document).ready(function () {
                $("#show-edit-password-div").click(function () {
                    $('.hidev').show();
                    $('.showV').hide();
                    $(".hide-password-div").hide();
                    $("#show-edit-password").show(300);
                });
                $("#hide-edit-password-div").click(function () {
                    $('.hidev').show();
                    $('.showV').hide();
                    $("#show-edit-password").hide();
                    $(".hide-password-div").show();
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#show-edit-language-div").click(function () {
                    $('.hidev').show();
                    $('.showV').hide();
                    $(".hide-language-div").hide();
                    $("#show-edit-language").show(300);
                });
                $("#hide-edit-language-div").click(function () {
                    $('.hidev').show();
                    $('.showV').hide();
                    $("#show-edit-language").hide();
                    $(".hide-language-div").show();
                });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function () {
                $("#show-edit-bankdetail-div").click(function () {
                    $('.hidev').show();
                    $('.showV').hide();
                    $(".hide-bankdetail-div").hide();
                    $("#show-edit-bankdeatil").show(300);
                });
                $("#hide-edit-bankdetail-div").click(function () {
                    $('.hidev').show();
                    $('.showV').hide();
                    $("#show-edit-bankdeatil").hide();
                    $(".hide-bankdetail-div").show();
                });
            });
        </script>



        <script type="text/javascript">
            $(document).ready(function () {
                $("#show-edit-vat-div").click(function () {
                    $("#hide-vat-div").hide();
                    $("#show-edit-vat").show();
                });
                $("#hide-edit-vat-div").click(function () {
                    $("#show-edit-vat").hide();
                    $("#hide-vat-div").show();
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#show-edit-accessibility-div").click(function () {
                    $("#hide-accessibility-div").hide();
                    $("#show-edit-accessibility").show();
                });
                $("#hide-edit-accessibility-div").click(function () {
                    $("#show-edit-accessibility").hide();
                    $("#hide-accessibility-div").show();
                });

                $('.demo-close').click(function (e) {
                    $(this).parent().hide(1000);
                });
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                var user = '<?= SITE_TYPE; ?>';
                console.log(user);
                if (user == 'Demo') {
                    var a = '<?= $new; ?>';
                    if (a != undefined && a != '') {
                        $('#formModal').modal('show');
                    }
                    //$('#formModal').modal('show');
                }
            });

            function validate_password() {
				var cpass = document.getElementById('cpass').value;
				var npass = document.getElementById('npass').value;
				var ncpass = document.getElementById('ncpass').value;
				var err = '';
				
				//alert("here");
				if (cpass == '') {
					err += "Please Enter Current Password.<BR>";
				}
				if (npass == '') {
					err += "Please Enter New Password.<BR>";
				}
				if (npass.length < 6) {
					err += "Please Enter Minimum Length of 6.<BR>";
				}
				if (ncpass == '') {
					err += "Please Re-Enter New Password.<BR>";
				}
				
				if (err == "") {
					if (npass != ncpass)
					err += "New Password do not match.<BR>";
				}
				if (err == "")
				{
					$.ajax({
						type: "POST",
						url: 'ajax_check_password_a.php',
						data: {cpass: cpass},
						success: function (dataHtml)
						{
							if(dataHtml.trim() == 1){
								editProfile('pass');
								return false;
							}else {
								err += "Current password is incorrect.<BR>";
								$('#cpass').val('');
								$('#npass').val('');
								$('#ncpass').val('');
								bootbox.dialog({
									message: "<h3>"+err+"</h3>",
									buttons: {
										danger: {
											label: "Ok",
											className: "btn-danger",
										},
									}
								});
								return false;
							}
						}
					});
				}
				else {
					$('#cpass').val('');
					$('#npass').val('');
					$('#ncpass').val('');
					bootbox.dialog({
						message: "<h3>"+err+"</h3>",
						buttons: {
							danger: {
								label: "Ok",
								className: "btn-danger",
							},
						}
					});
					//document.getElementById("err_password").innerHTML = '<div class="alert alert-danger">' + err + '</div>';
					return false;
				}
			}
            function editPro(action)
            {
				editProfile(action);
                return false;
            }
            function editProfile(action)
            {
			    //alert('hi');
                var chk = '<?php echo SITE_TYPE; ?>';

                // if(chk=='Demo')
                // {
                // window.location = 'profile.php?success=2';
                // return;
                // }
                // else
                // {
                //alert(action);
                if (action == 'login')
                {
                    data = $("#frm1").serialize();
                }
                if (action == 'address')
                {
                    data = $("#frm2").serialize();
                }
                if (action == 'pass')
                {
                    data = $("#frm3").serialize();
                }
                if (action == 'lang')
                {
                    data = $("#frm4").serialize();
                }
                if (action == 'vat')
                {
                    data = $("#frm5").serialize();
                }
                if (action == 'access')
                {
                    data = $("#frm10").serialize();
                }
                if (action == 'bankdetail')
                {
                    //alert(action);

                    data = $("#frm6").serialize();
                }
                var request = $.ajax({
                    type: "POST",
                    url: 'profile_action.php',
                    data: data,
                    success: function (data)
                    {

                        window.location = 'profile.php?success=1&var_msg=' + data;
                        return false;

                    }
                });

                request.fail(function (jqXHR, textStatus) {
                    alert("Request failed: " + textStatus);
                    return true;
                });
                // }

            }

            function changeCode(id)
            {
                var request = $.ajax({
                    type: "POST",
                    url: 'change_code.php',
                    data: 'id=' + id,
                    success: function (data)
                    {
                        document.getElementById("code").value = data;
                        //window.location = 'profile.php';
                    }
                });
            }
			// $(document).ready(function () {
				// var data = document.getElementById('vCountry').value;
				// setState(data);
				
				// });
		
		
		
		function setCity(id,selected)
		{
			var fromMod = 'driver';
			var request = $.ajax({
				type: "POST",
				url: 'change_stateCity.php',
				data: {stateId: id, selected: selected,fromMod:fromMod},
				success: function (dataHtml)
				{
					$("#vCity").html(dataHtml);
				}
			});
		}
		
		function setState(id,selected)
		{
			var fromMod = 'driver';
			var request = $.ajax({
				type: "POST",
				url: 'change_stateCity.php',
				data: {countryId: id, selected: selected,fromMod:fromMod},
				success: function (dataHtml)
				{
					$("#vState").html(dataHtml);
					if(selected == '')
						setCity('',selected);
				}
			});
		}
		
		setState('<?php echo $db_user[0]['vCountry']; ?>','<?php echo $db_user[0]['vState']; ?>');
		setCity('<?php echo $db_user[0]['vState']; ?>','<?php echo $db_user[0]['vCity']; ?>');
		
            function validate_email(id)
            {
                var uid = $("#u_id1").val();
                var request = $.ajax({
                    type: "POST",
                    url: 'ajax_validate_email.php',
                    data: 'id=' + id + '&uid=' + uid,
                    success: function (data)
                    {
                        //console.log(data);

                        if (data == 0)
                        {
                            $('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Invalid Email,Already Exist</i>');

                            $('input[type="submit"]').attr('disabled', 'disabled');

                            return false;
                        } else if (data == 1)
                        {
                            //var eml=/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                            var eml = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                            result = eml.test(id);
                            //alert(result);
                            if (result == true)
                            {
                                $('#emailCheck').html('<i class="icon icon-ok alert-success alert"> Valid</i>');
                                $('input[type="submit"]').removeAttr('disabled');

                            } else
                            {
                                //alert('asda');
                                $('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Enter Proper Email</i>');
                                $('input[type="submit"]').attr('disabled', 'disabled');
                                return false;
                            }
                        } else if (data == 2)
                        {
                            $('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Your Account is deleted please contact admin</i>');

                            $('input[type="submit"]').attr('disabled', 'disabled');

                            return false;
                        }
                    }

                });
            }
            $("#in_email").bind("keypress click", function () {
                $('#emailCheck').html('');
                $("#in_email").removeClass('required-active');
            });

            function validate_email_submit(id2)
            {
                var nr2 = "0";
                $('#frm1').find('input,select').each(function () {
                    if ($(this).attr('required')) {
                        if ($(this).val() == "") {
                            nr2 = "1";
                            return false;
                        }
                    }
                });
                if (nr2 != "1") {

                    var uid = $("#u_id1").val();
                    var umail = $("#in_email").val();
                    var action = id2;

                    var request = $.ajax({
                        type: "POST",
                        url: 'ajax_validate_email.php',
                        data: 'id=' + umail + '&uid=' + uid,
                        success: function (data)
                        {
                            if (data == 0)
                            {
                                $('#emailCheck').html('*Invalid Email, Already Exist');
                                $("#in_email").focus();
                                window.scrollTo(0, 0);
                                $("#in_email").addClass('required-active');
                                return false;
                            } else if (data == 1)
                            {
                                var eml = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                                result = eml.test(umail);
                                //alert(result);
                                if (result == true)
                                {
                                    editProfile(action);
                                } else
                                {

                                    $('#emailCheck').html('*Enter Proper Email');
                                    window.scrollTo(0, 0);
                                    $("#in_email").focus();
                                    $("#in_email").addClass('required-active');
                                    return false;
                                }
                            } else if (data == 2)
                            {

                                $('#emailCheck').html('*Your account is deleted,Please contact admin.');

                                window.scrollTo(0, 0);
                                $("#in_email").focus();
                                $("#in_email").addClass('required-active');
                                return false;
                            }

                        }
                    });
                }
            }
			
			$(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();
			});
		
            // function del_docImage(frmid)
            // {
            ////var frm=frmid;
            // var data=$("#"+frmid).serialize();
            ////alert(data);
            // ans=confirm('Are you sure?You want to delete document?');
            ////alert(ans);
            // if(ans == true)
            // {
            ////alert('true');
            // var request=$.ajax({
            // type: "POST",
            // url: "ajax_delete_docimage.php",
            // data: data+"&doc_type=common",
            // success: function(data){
            // var url      = window.location.href; 
            // $("#"+frmid).load(url+" #"+frmid);
            // $("#NOC_"+frmid).load(url+" #NOC_"+frmid);
            // $("#NOC_link_"+frmid).load(url+" #NOC_link_"+frmid);	
            // }
            // });
            // }
            // else
            // {
            // return false;
            // }
            // }
        </script>
    </body>
</html>
