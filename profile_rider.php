<?php
	include_once('common.php');
	$generalobj->check_member_login();
	
	$script='Profile';
	$success = isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$abc = 'rider';
	$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$generalobj->setRole($abc,$url);
	$user = $_SESSION["sess_user"];
	
	$sql = "select * from register_user where iUserId = '".$_SESSION['sess_iUserId']."'";
	$db_user = $obj->MySQLSelect($sql);
	//print_r($db_user[0]['vFbId']); 
	//print_r($db_user[0]['vPassword']); exit;
	
	
	$sql = "select * from language_master where eStatus = 'Active'";
	$db_lang = $obj->MySQLSelect($sql);
	
	$sql = "select * from country where eStatus = 'Active'";
	$db_country = $obj->MySQLSelect($sql);
	$sql = "select * from currency where eStatus = 'Active'";
	$db_currency = $obj->MySQLSelect($sql);
	
	for($i=0;$i<count($db_lang);$i++)
	{
		if($db_user[0]['vLang'] == $db_lang[$i]['vCode'])
		{
			$lang = $db_lang[$i]['vTitle'];
		}
	}
	for($i=0;$i<count($db_country);$i++)
	{
		if($db_user[0]['vCountry'] == $db_country[$i]['vCountryCode'])
		{
			$country = $db_country[$i]['vCountry'];
		}
	}
	//echo '------->'.$id;
	//echo "<pre>";print_r($db_user);echo "</pre>";
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title><?=$SITE_NAME?> |<?=$langage_lbl['LBL_HEADER_PROFILE_TXT']; ?></title>
		
		<!-- Default Top Script and css -->
		<?php include_once("top/top_script.php");?>
		<link rel="stylesheet" href="assets/css/bootstrap-fileupload.min.css" >
		<link rel="stylesheet" href="assets/validation/validatrix.css" />
		<!-- End: Default Top Script and css-->
	</head>
	<body>
		<!-- home page -->
		<div id="main-uber-page">
			<!-- Left Menu -->
			<?php include_once("top/left_menu.php");?>
			<!-- End: Left Menu-->
			<!-- Top Menu -->
			<?php include_once("top/header_topbar.php");?>
			<!-- End: Top Menu-->
			<!-- contact page-->
			<div class="page-contant">
				<div class="page-contant-inner">
                    <h2 class="header-page"><?=$langage_lbl['LBL_PROFILE_TITLE_TXT']; ?></h2>
                    <!-- profile page -->
                    <div class="driver-profile-page">                    
						<?php if ($success ==1) { ?>
							<div class="demo-success">
								<button class="demo-close" type="button">×</button>
								<?=$langage_lbl['LBL_PROFILE_UPDATED']; ?>
							</div>
							<?php }
							else if($success ==2)
							{
							?>
							<div class="demo-danger">
								<button class="demo-close" type="button">×</button>
								"Edit / Delete  Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
							</div>
						<?php }?>
						<div class="driver-profile-top-part" id="hide-profile-div">
							<div class="driver-profile-img">
								<span>
									<? if($db_user[0]['vImgName'] != '' && file_exists($tconfig["tsite_upload_images_passenger_path"]. '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_user[0]['vImgName'])){?>
										<img src = "<?= $tconfig["tsite_upload_images_passenger"]. '/' . $_SESSION['sess_iUserId'] . '/2_' .$db_user[0]['vImgName'] ?>" style="height:150px;"/>
										<? }else{ ?>
										<img src="assets/img/profile-user-img.png" alt="">
									<? } ?>
								</span>
                                <b>
									<a data-toggle="modal" data-target="#uiModal_4"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								</b>
							</div>
							<div class="driver-profile-info">
								<h3><?=$generalobj->cleanall(htmlspecialchars($db_user[0]['vName'])) . ' ' . $generalobj->cleanall(htmlspecialchars($db_user[0]['vLastName'])); ?></h3>
								<?php 
									if($country != ""){ ?>
										<p><i class="icon-map-marker"></i>&nbsp;<?= $country ?></p>
								<?php } ?>
								<?php 
									
									if($REFERRAL_SCHEME_ENABLE == 'Yes'){ ?>
									
									<p><?php echo  $langage_lbl['MY_RIDER_REFERAL_CODE'];?>&nbsp; : <?= $db_user[0]['vRefCode'] ?></p>
									<?php }
								?>
								
								<span><a id="show-edit-profile-div"><i class="fa fa-pencil" aria-hidden="true"></i><?=$langage_lbl['LBL_EDIT']; ?></a></span>
							</div>
						</div>
						<!-- form -->
						<div class="edit-profile-detail-form" id="show-edit-profile" style="display: none;">
							<form id="frm1" method="post" action="javascript:void(0);" class="profile-rider-form">
								<input  type="hidden" class="edit" name="action" value="login">
								<div class="edit-profile-detail-form-inner">
									<span><?php //echo '----->'.$_SESSION['sess_iUserId'];?>
									
									
                                    <label><?=$langage_lbl['LBL_PROFILE_YOUR_EMAIL_ID']; ?> *</label>
										<input type="hidden" name="uid" id="u_id1" value="<?=$_SESSION['sess_iUserId'];?>">
										<input type="email" id="in_email" class="edit-profile-detail-form-input" placeholder="<?=$langage_lbl['LBL_RIDER_PROFILE_YOUR_EMAIL_ID']; ?>" value = "<?= $db_user[0]['vEmail'] ?>" name="email" <?= isset($db_user[0]['vEmail']) ? '' : ''; ?>  required onKeyUp="validate_email(this.value,'<?php echo $id; ?>')"  >
										<div class="required-label" id="emailCheck"></div>
									</span> 
									
									<span>
									
                                     <label><?=$langage_lbl['LBL_RIDER_YOUR_FIRST_NAME']; ?>*</label>
										<input type="text" class="edit-profile-detail-form-input" placeholder="<?=$langage_lbl['LBL_RIDER_YOUR_FIRST_NAME']; ?>" value = "<?= $generalobj->cleanall(htmlspecialchars($db_user[0]['vName'])) ?>" name="fname" required>
									</span> 
									<span>
                                     <label><?=$langage_lbl['LLBL_RIDER_YOUR_LAST_NAME']; ?> *</label>
										<input type="text" class="edit-profile-detail-form-input" placeholder="<?=$langage_lbl['LBL_RIDER_YOUR_LAST_NAME']; ?>" value = "<?=  $generalobj->cleanall(htmlspecialchars($db_user[0]['vLastName'])); ?>" name="lname" required>
									</span> 
									
									
									<span>
									
                                     <label><?=$langage_lbl['LBL_PROFILE_SELECT_COUNTRY']; ?> *</label>
										<select class="custom-select-new" name = 'country' required>
											<option value="">--<?=$langage_lbl['LBL_SELECT_CONTRY']; ?>--</option>
											<? for($i=0;$i<count($db_country);$i++){ ?>
												<option value = "<?= $db_country[$i]['vCountryCode'] ?>" <?
												if($db_user[0]['vCountry']==$db_country[$i]['vCountryCode']){?>selected<? }?>><?= $db_country[$i]['vCountry'] ?></option>
											<? } ?>
										</select>
									</span>
									<?php 
									if(count($db_lang) <= 1){ ?>
									 <input name="lang1" type="hidden" class="create-account-input" value="<?php echo $db_lang[0]['vCode'];?>"/>
										
									<?php }else{ ?>
									<span>
									
                                     <label><?=$langage_lbl['LBL_PROFILE_SELECT_LANGUAGE']; ?> *</label>
										<select name="lang1" required class="custom-select-new">
											<option value=""><?=$langage_lbl['LBL_SELECT_LANGUAGE_HINT_TXT']; ?></option>
											<? for($i=0;$i<count($db_lang);$i++) {?>
												<option value="<?= $db_lang[$i]['vCode'] ?>" <?if($db_lang[$i]['vCode']==$db_user[0]['vLang']) {?> selected <? } ?>><?= $db_lang[$i]['vTitle'] ?></option>
											<? } ?>
										</select>
									</span>
									<?php  } ?>
									<? if(count($db_currency) > 1){?>
									<span>
									
                                     <label><?=$langage_lbl['LBL_PROFILE_SELECT_CURRENCY']; ?> *</label>
										<select class="custom-select-new" name = 'vCurrencyPassenger' required>
											<option value="">--<?=$langage_lbl['LBL_SELECT_CURRENCY']; ?>--</option>
											<? for($i=0;$i<count($db_currency);$i++){ ?>
												<option value = "<?= $db_currency[$i]['vName'] ?>" <?if($db_user[0]['vCurrencyPassenger']==$db_currency[$i]['vName']){?>selected<? } ?>><?= $db_currency[$i]['vName'] ?></option>
											<? } ?>
										</select>
									</span>
									<? }else{ ?>
										<input name="vCurrencyPassenger" type="hidden" class="create-account-input" value="<?= $db_currency[0]['vName'] ?>" />
									<? } ?>
									
									<p>
										<input name="save" type="submit" value="<?=$langage_lbl['LBL_RIDER_Save']; ?>" class="save-but" onclick = "return validate_email_rider('login');">
										<input name="" id="hide-edit-profile-div" type="button" value="<?=$langage_lbl['LBL_BTN_PROFILE_RIDER_CANCEL_TRIP_TXT']; ?>" class="cancel-but">
									</p>
									<div style="clear:both;"></div>
								</div>                        
							</form>
						</div>
						<!-- from -->
						<div class="driver-profile-mid-part">
							<ul>
								<li>
									<div class="driver-profile-mid-inner">
										<div class="profile-icon"><i class="fa fa-map-marker" aria-hidden="true"></i></div>
										<h3><?=$langage_lbl['LBL_PROFILE_RIDER_EMAIL_LBL_TXT']; ?></h3>
										<p><?= $db_user[0]['vEmail'] ?></p>
										<span><!--<a id="show-edit-vemail-div" class="hide-vemail-div hidev"><i class="fa fa-pencil" aria-hidden="true"></i><?=$langage_lbl['LBL_RIDER_EDIT']; ?></a>--></span> 
									</div>                            
								</li>
								<li>
									<div class="driver-profile-mid-inner">
										<div class="profile-icon"><i class="fa fa-unlock-alt" aria-hidden="true"></i></div>
										<h3><?=$langage_lbl['LBL_PROFILE_RIDER_PASSWORD']; ?></h3>
										<?php /*<p><? for ($i = 0; $i < strlen($generalobj->decrypt($db_user[0]['vPassword'])); $i++)
                                            echo '*'; ?></p> */ ?>
										<span><a id="show-edit-password-div" class="hide-password-div hidev"><i class="fa fa-pencil" aria-hidden="true"></i><?=$langage_lbl['LBL_RIDER_EDIT']; ?></a></span> 
									</div>
								</li>
								<li>
									<div class="driver-profile-mid-inner">
										<div class="profile-icon"><i class="fa fa-mobile" aria-hidden="true"></i></div>
										<h3><?=$langage_lbl['LBL_MOBILE_NUMBER_HINT_TXT']; ?></h3>
										<p><?= $db_user[0]['vPhone'] ?></p>
										<span><a id="show-edit-language-div" class="hide-language-div hidev"><i class="fa fa-pencil" aria-hidden="true"></i><?=$langage_lbl['LBL_RIDER_EDIT']; ?></a></span> 
									</div>
								</li>
							</ul>
						</div>
						<!-- Email form -->
						<div class="profile-Password showV" id="show-edit-vemail" style="display: none;">
							<form id = "frm7" method="post" onSubmit="return editProE('vemail')">
								<p class="vemail-pointer"><img src="assets/img/pas-img1.jpg" alt=""></p>
								<h3><i class="fa fa-envelope" aria-hidden="true"></i><?php echo 'Email';//=$langage_lbl['LBL_PHONE']; ?></h3>
								<input type = "hidden" name="action" value = "email"/>
								<div class="edit-profile-detail-form-password-inner profile-language-part">
									<span>
										<!--<input type="text" pattern=".{10}" class="input-phNumber1" id="code" name="vCode" value="<?= $db_user[0]['vCode'] ?>" readonly >-->
                                        <label><?=$langage_lbl['LBL_PROFILE_YOUR_EMAIL_ID']; ?> *</label>
										<input name="email" type="text" id="email" value="<?= $db_user[0]['vEmail'] ?>" class="edit-profile-detail-form-input " placeholder="<?=$langage_lbl['LBL_RIDER_Phone_Number']; ?>" maxlength="30" title="Please enter proper Email." required />
									</span>
								</div> 
                                <span>                                
                                    <b>
                                        <input name="save" type="submit" value="<?=$langage_lbl['LBL_RIDER_Save']; ?>" class="profile-Password-save" >
                                        <input name="" id="hide-edit-vemail-div" type="button" value="<?=$langage_lbl['LBL_BTN_PROFILE_RIDER_CANCEL_TRIP_TXT']; ?>" class="profile-Password-cancel">
									</b>
								</span>
                                <div style="clear:both;"></div>
								
							</form>
						</div>
						<!-- End: Email Form -->					
						
						<!-- Password form -->                    
						<div class="profile-Password showV" id="show-edit-password" style="display: none;">
							<form id="frm6" method="post" action="javascript:void(0);" onsubmit="return <?=($db_user[0]['vFbId'] >= 0 && $db_user[0]['vPassword'] != "" )?'validate_password()':'validate_password_fb()';?>"  >
								<p class="password-pointer"><img src="assets/img/pas-img1.jpg" alt=""></p>
								<h3><i class="fa fa-unlock-alt" aria-hidden="true"></i><?=$langage_lbl['LBL_PROFILE_RIDER_PASSWORD']; ?></h3>
								<input type="hidden" name="action" id="action" value = "pass"/>
								
								<div class="row">
									<?php if($db_user[0]['vFbId'] >= 0 && $db_user[0]['vPassword'] != ""){ ?>
										<div class="col-sm-4">
                                        <span>
											<label><?=$langage_lbl['LBL_RIDER_CURR_PASS_HEADER']; ?> *</label>
											<input type="password" class="input-box" placeholder="<?=$langage_lbl['LBL_RIDER_CURR_PASS_HEADER']; ?>" name="cpass" id="cpass" required>  
                                            </span>
										</div> 
									<?php } ?> 
									<div class="col-sm-4">
                                    <span>
										<label><?=$langage_lbl['LBL_RIDER_UPDATE_PASSWORD_HEADER_TXT']; ?> *</label>
										<input type="password" class="input-box" placeholder="<?=$langage_lbl['LBL_RIDER_UPDATE_PASSWORD_HEADER_TXT']; ?>" name="npass" id="npass" required>									</span>
									</div> 
									<div class="col-sm-4">
                                    <span>
										<label><?=$langage_lbl['LBL_RIDER_Confirm_New_Password']; ?> *</label>
										<input type="password" class="input-box" placeholder="<?=$langage_lbl['LBL_RIDER_Confirm_New_Password']; ?>" name="ncpass" id="ncpass" required>
                                     </span>
									</div>  
								</div><br><br><br>
								<span>
									<b>
										<input name="save" type="submit" value="<?=$langage_lbl['LBL_RIDER_Save']; ?>" class="profile-Password-save">
										<input name="" id="hide-edit-password-div" type="button" value="<?=$langage_lbl['LBL_BTN_PROFILE_RIDER_CANCEL_TRIP_TXT']; ?>" class="profile-Password-cancel">
									</b>
								</span>
								<div style="clear:both;"></div>
							</form>
						</div>
						
						<!-- End: Password Form -->
						<!-- Phone form -->
						<div class="profile-Password showV" id="show-edit-language" style="display: none;">
							<form id = "frm5" method="post" onSubmit="return editPro('phone')">
								<p class="language-pointer"><img src="assets/img/pas-img1.jpg" alt=""></p>
								<h3><i class="fa fa-mobile" aria-hidden="true"></i><?=$langage_lbl['LBL_PHONE']; ?></h3>
								<input type = "hidden" name="action" value = "phone"/>
								<div class="edit-profile-detail-form-password-inner profile-language-part">
									<span>
										<!--<input type="text" pattern=".{10}" class="input-phNumber1" id="code" name="vCode" value="<?= $db_user[0]['vCode'] ?>" readonly >-->
                                        
										<label><?=$langage_lbl['LBL_RIDER_Phone_Number']; ?> *</label>
										<input name="phone" type="text" id="phone" required value="<?= $db_user[0]['vPhone'] ?>" class="edit-profile-detail-form-input input-phNumber2" placeholder="<?=$langage_lbl['LBL_RIDER_Phone_Number']; ?>" maxlength="15" title="Please enter proper mobile number." />
									</span>
								</div> 
                                <span>                                
                                    <b>
                                        <input name="save" type="submit" value="<?=$langage_lbl['LBL_RIDER_Save']; ?>" class="profile-Password-save" >
                                        <input name="" id="hide-edit-language-div" type="button" value="<?=$langage_lbl['LBL_BTN_PROFILE_RIDER_CANCEL_TRIP_TXT']; ?>" class="profile-Password-cancel">
									</b>
								</span>
                                <div style="clear:both;"></div>
								
							</form>
						</div>
						<!-- End: Language Form -->
						
						<div class="col-lg-12">
							<div class="modal fade" id="uiModal_4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-content image-upload-1 popup-box3">
									<div class="upload-content">
										<h4><?=$langage_lbl['LBL_RIDER_PROFILE_PICTURE']; ?></h4>
										<form class="form-horizontal" id="frm9" method="post" enctype="multipart/form-data" action="upload_pic.php" name="frm9">
											<input type="hidden" name="action" value ="photo"/>
											<input type="hidden" name="img_path" value ="<?=  $tconfig["tsite_upload_images_passenger_path"]; ?>" />
											<div class="form-group">
												<div class="col-lg-12">
													<div class="fileupload fileupload-new" data-provides="fileupload">
														<div class="fileupload-preview thumbnail" >
															<?php if ($db_user[0]['vImgName'] == '') { ?>
                                                                <img src="assets/img/profile-user-img.png" alt="">
                                                                <? } else { ?>
                                                                <img src = "<?= $tconfig["tsite_upload_images_passenger"]. '/' . $_SESSION['sess_iUserId'] . '/2_' .$db_user[0]['vImgName'] ?>" />
															<? } ?>
														</div>
														<div>
															<span class="btn btn-file btn-success"><span class="fileupload-new"><?=$langage_lbl['LBL_UPLOAD_PHOTO']; ?></span><span class="fileupload-exists">
															<?=$langage_lbl['LBL_Driver_document_CHANGE']; ?></span><input type="file" name="photo"/></span>
															<a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">x</a>
														</div>
													</div>
												</div>
											</div>
                                            <input type="submit" class="save" name="save" value="<?=$langage_lbl['LBL_RIDER_Save']; ?>">
                                            <input type="button" class="cancel" data-dismiss="modal" name="<?=$langage_lbl['LBL_BTN_PROFILE_RIDER_CANCEL_TRIP_TXT']; ?>" value="Cancel">
										</form>
										
										<div style="clear:both;"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				
			</div> 
			<!-- footer part -->
			<?php include_once('footer/footer_home.php');?>
			<!-- footer part end -->
            <!-- -->
			<div  class="clearfix"></div>
		</div>
		<!-- home page end-->
		<!-- Footer Script -->
		<?php include_once('top/footer_script.php');?>
		<script src="assets/plugins/jasny/js/bootstrap-fileupload.js"></script>
		<!-- End: Footer Script -->
		<script type="text/javascript">
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
			$(document).ready(function () {
				$("#show-edit-address-div").click(function () {
					$('.hidev').show();
					$('.showV').hide();
                    $(".hide-address-div").hide();
                    $("#show-edit-address").show();
				});
				$("#hide-edit-address-div").click(function () {
					$('.hidev').show();
					$('.showV').hide();
                    $("#show-edit-address").hide();
                    $(".hide-address-div").show();
				});
			});
		</script>
		<!--  code for email update  -->
		<script type="text/javascript">
			$(document).ready(function () {
				$("#show-edit-vemail-div").click(function () {
					$('.hidev').show();
					$('.showV').hide();
                    $(".hide-vemail-div").hide();
                    $("#show-edit-vemail").show();
				});
				$("#hide-edit-vemail-div").click(function () {
					$('.hidev').show();
					$('.showV').hide();
                    $("#show-edit-vemail").hide();
                    $(".hide-vemail-div").show();
				});
			});
		</script>
		<!--  code for email update  -->
		<script type="text/javascript">
			$(document).ready(function () {
				$("#show-edit-password-div").click(function () {
					$('.hidev').show();
					$('.showV').hide();
                    $(".hide-password-div").hide();
                    $("#show-edit-password").show();
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
                    $("#show-edit-language").show();
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
				
				$('.demo-close').click(function(e){
					$(this).parent().hide(1000);
				});
			});
		</script>
		<script>
			
			
			function validate_password() {
				var cpass = document.getElementById('cpass').value;
				var npass = document.getElementById('npass').value;
				var ncpass = document.getElementById('ncpass').value;
				var err = '';
				
				//alert("here");
				// if (pass == '') {
					// err += "Something went wrong in Password.<BR>";
				// }
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
					// if (pass != cpass)
					// err += "Current password is incorrect.<BR>";
					if (npass != ncpass)
					err += "New Password do not match.<BR>";
				}
				if (err == "")
				{
					// ajax_check_password_a.php
					
					$.ajax({
						type: "POST",
						url: 'ajax_check_password_a.php',
						data: {cpass: cpass, user: 'rider'},
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
					
					// editProfile('pass');
					// return false;
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
			
			function validate_password_fb() {
				//var cpass = document.getElementById('cpass').value;
				var npass = document.getElementById('npass').value;
				var ncpass = document.getElementById('ncpass').value;
				// var pass = '<?= $newp ?>';
				var err = '';
				
				//alert("here");
				
				
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
					editProfile('pass');
					return false;
				}
				else {
					
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
			<!---- Email ---->
			function editProE(action)
			{     
                //alert('email-id');
                var email = document.getElementById('email').value;
                var err = '';
                /* if (email.length < 1) {
					err += "Please Enter Proper Email.";					
				} 
				if(!/^[0-9]+$/.test(email)){
                     err += "Please only enter numeric characters only for your Mobile no! (Allowed input:0-9)";
                    
				 }  */

var eml=/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
								result=eml.test(email);
								//alert(result);
								if(result==true)
								{  
                                   // $('#emailCheck').html('<i class="icon icon-ok alert-success alert"> Valid</i>');
								   err += "Email Already Exist, Please try another";
									setTimeout(function() {
									$('#emailCheck').html('');
									}, 3000);
                                    $('input[type="submit"]').removeAttr('disabled');
									
								}
                                else
								{
									err += "Enter Proper Email";
								}


				 
				if (err == "")
				{     
					//editProfile('phone');
					editProfile(action);
					return false;
				}
				else {    
                    $('#email').val('');
                    bootbox.dialog({
						message: "<h3>"+err+"</h3>",
						buttons: {
                            danger: {
								label: "Ok",
								className: "btn-danger",
							},
						}
					});
					//editProfile(action);
					return false;
				} 
			} 
			<!---- Email ---->
			
			function editPro(action)
			{     
                
                var phone = document.getElementById('phone').value;
                var err = '';
                if (phone.length < 1) {
					err += "Please Enter Proper Mobile No.";
					
				}
                if(!/^[0-9]+$/.test(phone)){
                    err += "Please only enter numeric characters only for your Mobile no! (Allowed input:0-9)";
                    
				}    
				if (err == "")
				{     
					//editProfile('phone');
					editProfile(action);
					return false;
				}
				else {    
                    $('#mobno').val('');
                    bootbox.dialog({
						message: "<h3>"+err+"</h3>",
						buttons: {
                            danger: {
								label: "Ok",
								className: "btn-danger",
							},
						}
					});
					//editProfile(action);
					return false;
				} 
			} 
			function editProfile(action)
			{ //alert('eeephonn');
				var chk='<?echo SITE_TYPE?>';
				
				// if(chk=='Demo')
                // {
                    // window.location = 'profile_rider.php?success=2';
                    // return;
				// }
				
				if (action == 'login')
				{       
					data = $("#frm1").serialize();
				}
				if (action == 'email')
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
				if (action == 'phone')
				{
					data = $("#frm5").serialize();
				}
				if (action == 'vemail')
				{
					data = $("#frm7").serialize();
				}
				
				/* if(action == 'licence')
					{
					data = $("#frm6").serialize();
					alert(data);
				}    */
				//alert(data);
				var request = $.ajax({
					type: "POST",
					url: 'ajax_profile_rider_a.php',
					data: data,
					success: function (data)
					{      //alert(data);return false;
						//alert('dsa');
						window.location = 'profile_rider.php?success=1';
					}
				});
				
				request.fail(function (jqXHR, textStatus) {
					alert("Request failed: " + textStatus);
				});
				
			}
			$("#in_email").bind("keypress click", function(){
						$('#emailCheck').html('');
						$("#in_email").removeClass( 'required-active' );
					});
			
			function validate_email_rider(act1)
			{
					var nr2 = "0";
					$('#frm1').find('input,select').each(function(){
						if($(this).attr('required')){
							if($(this).val() == ""){
								 nr2 = "1";
								 return false;
								}
							}
					});
					
					if(nr2 != "1"){
					
							var uid = $("#u_id1").val();
							var umail = $("#in_email").val();
							var action = act1;
							
							var request = $.ajax({
								type: "POST",
								url: 'ajax_rider_email.php',
								data: 'id='+umail+'&uid='+uid,
								success: function (data)
								{	
								
									if(data==0)
									{
										$('#emailCheck').html('*Invalid Email, Already Exist');
										$("#in_email").focus();
										window.scrollTo(0, 0);
										$("#in_email").addClass( 'required-active' );
										return false;
									}
									else if(data==1)
									{
										var eml=/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
										
										result=eml.test(umail);
										// alert(result);
										if(result == true)
										{
											editProfile(action);
										}
										else
										{
											
											$('#emailCheck').html('*Enter Proper Email');
											window.scrollTo(0, 0);
											$("#in_email").focus();
											$("#in_email").addClass( 'required-active' );
											return false;
										}
									}
									else if(data==2)
									{
										
										$('#emailCheck').html('*Your account is deleted,Please contact admin.');
										
										window.scrollTo(0, 0);
										$("#in_email").focus();
										$("#in_email").addClass( 'required-active' );
										return false;
									}
									
								}
							});
					}
			}
			h = window.innerHeight;
			$("#page_height").css('min-height', Math.round(h - 99) + 'px');
			
			
			function validate_email(id)
				{ 
				    
					var uid = $("#u_id1").val();
					//alert(id);
					var request = $.ajax({
						type: "POST",
						url: 'ajax_validate_email.php',
						data: 'id=' +id+'&uid='+uid,
						success: function (data)
						{
							//console.log(data);
							//alert(data);
							if(data==0)
							{
								$('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Invalid Email,Already Exist</i>');
								
								$('input[type="submit"]').attr('disabled','disabled');
								
								return false;
							}
							else if(data==1)
							{
								//var eml=/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
								var eml=/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
								result=eml.test(id);
								//alert(result);
								if(result==true)
								{
                                    $('#emailCheck').html('<i class="icon icon-ok alert-success alert"> Valid</i>');
									setTimeout(function() {
									$('#emailCheck').html('');
									}, 3000);
                                    $('input[type="submit"]').removeAttr('disabled');
									
								}
								else
								{
									//alert('asda');
									$('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Enter Proper Email</i>');
									$('input[type="submit"]').attr('disabled','disabled');
									return false;
								}
							}
							else if(data==2)
							{
								$('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Your Account is deleted please contact admin</i>');
								
								$('input[type="submit"]').attr('disabled','disabled');
								
								return false;
							}
						}
						
					});
				}
				$("#in_email").bind("keypress click", function(){
						$('#emailCheck').html('');
						$("#in_email").removeClass( 'required-active' );
					});
		</script>
	</body>
</html>
