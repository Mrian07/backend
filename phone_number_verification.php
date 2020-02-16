<?php
	
	include_once 'common.php';
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	
	}
		
	$_REQUEST['action']=(base64_decode(base64_decode(trim($_REQUEST['action'])))); 
	$_REQUEST['id'] = $generalobj->decrypt($_REQUEST['id']);	
	$action =  $_REQUEST['action']; 
	$userid = isset($_REQUEST['id'])?$_REQUEST['id']:''; 	
	$vPhone = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
	$vPhoneCode = isset($_POST['vPhoneCode']) ? $_POST['vPhoneCode'] : '';
	$vCountry = isset($_POST['vCountry']) ? $_POST['vCountry'] : '';
	$_SESSION['sess_iUserId'] =$userid;
	
	if (isset($_POST['submit'])) {	
		if($action == 'rider'){			
			$data['vPhone']= $vPhone;
			$data['vPhoneCode']= $vPhoneCode;
			$data['vCountry']= $vCountry;
			$where = " iUserId = '$userid'";
			$table = "register_user";
			$_SESSION["sess_user"] = "rider";
		}else{		
			$data['vPhone']= $vPhone;
			$data['vCode']= $vPhoneCode;
			$data['vCountry']= $vCountry;
			$where = "iDriverId = '$userid'";
			$table = "register_driver";
			$_SESSION["sess_user"] = "driver";
		}			
		$id = $obj->MySQLQueryPerform($table,$data,'update',$where);
		
		if($id > 0){
		
			$db_sql = "select * from $table WHERE $where";
			$db_user = $obj->MySQLSelect($db_sql);				
			$_SESSION['sess_iMemberId']=$userid;
			//$_SESSION['sess_iUserId'] =$userid;
			$_SESSION["sess_vName"]= $db_user[0]['vName'];
			$_SESSION["sess_vLastName"]= $db_user[0]['vLastName'];
			$_SESSION["sess_vEmail"] = $db_user[0]['vEmail'];
			$_SESSION["sess_eGender"]= $db_user[0]['eGender'];
			
			if($action == 'rider'){
				$_SESSION["sess_vImage"]= $db_user[0]['vImgName'];
				$filename="profile_rider.php";
			}else{
				$_SESSION["sess_vImage"]= $db_user[0]['vImage'];
				$filename="profile.php";
			}
			$link = $tconfig["tsite_url"].$filename;
			
			
		}
	}
	
	$generalobj->go_to_home();
	
	$sql = "select * from country where eStatus='Active'";
	$db_country = $obj->MySQLSelect($sql);

?>
<!DOCTYPE html>
	<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
 <!--   <title><?=$SITE_NAME?> | Login Page</title>-->
   <title><?php echo $meta_arr['meta_title'];?></title>
    <!-- Default Top Script and css -->
    <?php include_once("top/top_script.php");?>
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
		<div class="page-contant reset-password">
			<div class="page-contant-inner">
				<h2 class="header-page-a"><?=$langage_lbl['LBL_COMPLATE_PROCESS_TXT'];?>
				<?if(SITE_TYPE =='Demo'){?>
				<p><?=$langage_lbl['LBL_SINCE_IT_IS_DEMO'];?></p>
				<?}?>
				</h2>
				<div class="login-form">	
				<?php	  
							
					if($success == 1) { ?>
					 <div class="alert alert-danger alert-dismissable">
					  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
					 <?php echo $_REQUEST['var_msg']; ?>
				 </div><br/>
				 <? } ?>
				
						<div class="login-form-left reset-password-page">
							<form name="resetpassword" action="" class="form-signin" method = "post" id="resetpassword" >	
							<input type="hidden" value="<?=$action; ?>" id="action" name="action12" >
								<span class="newrow">
								<b>						
								<label><?= $langage_lbl['LBL_COUNTRY_TXT'] ?></label>
								<select class="select-reset-password"  name ="vCountry" id="vCountry" onChange="changeCode(this.value);" required>
									<? for($i=0;$i<count($db_country);$i++){ ?>
									<option value ="<?= $db_country[$i]['vCountryCode'] ?>" <?php if($db_country[$i]['vCountryCode'] == 'US'){echo "selected";}?>><?= $db_country[$i]['vCountry'] ?></option>
									<? } ?>
							</select></b>
								</span>
								<span class="newrow">
								<b>
								<label><?=$langage_lbl['LBL_SIGNUP_777-777-7777']; ?></label>
									<input name="vPhoneCode" id="code" type="text" class="login-input-a" value="" required />
									<input name="vPhone" id="vPhone" type="text" placeholder="<?=$langage_lbl['LBL_SIGNUP_777-777-7777']; ?>"class="login-input-b" value="" required />
								</b> 
								</span>
								<b>
								<input type="submit" class="submit-but" name="submit" value="<?=$langage_lbl['LBL_SIGN_UP']; ?>" />								
								</b> 
							</form>								
						</div>	
						
				</div>				
				<div style="clear:both;"></div>
			</div>
		</div>
	<!-- footer part -->
    <?php include_once('footer/footer_home.php');?>
    <!-- footer part end -->
    		<!-- -->
            <div style="clear:both;"></div>
	</div>
	<!-- home page end-->
    <!-- Footer Script -->
    <?php include_once('top/footer_script.php');?>
    <!-- End: Footer Script -->
  <script type="text/javascript" src="assets/js/validation/jquery.validate.min.js" ></script>
	<script type="text/javascript" src="assets/js/validation/additional-methods.js" ></script>
    <script>
	$('#resetpassword').validate({
		onsubmit: true,
		ignore: 'input[type=hidden]',
		errorClass: 'help-block',
		errorElement: 'span',
		errorPlacement: function (error, e) {
			e.parents('.newrow > b').append(error);
		},
		highlight: function (e) {
			$(e).closest('.newrow').removeClass('has-success has-error').addClass('has-error');
			$(e).closest('.newrow b input').addClass('has-shadow-error');
			$(e).closest('.help-block').remove();
		},
		success: function (e) {
			e.prev('input').removeClass('has-shadow-error');
			e.closest('.newrow').removeClass('has-success has-error');
			e.closest('.help-block').remove();
			e.closest('.help-inline').remove();
		},
		rules: {			
			vPhone: {required: true, phonevalidate: true,
						remote: {
							url: 'ajax_mobile_new_check.php',
							type: "post",						
							data: {userType:function () {return $("#action").val();}},
						}
			}
			
		},
		messages: {
			vPhone: {remote: 'Phone Number is already exists.'}
		}
	});
	
	function changeCode(id) {
		var request = $.ajax({
			type: "POST",
			url: 'change_code.php',
			data: 'id=' + id,
			success: function (data)
			{
				document.getElementById("code").value = data;
			}
		});
	}
	
	$(document).ready(function () {
		changeCode("US");
	});
		
	</script>
	
</body>
</html>