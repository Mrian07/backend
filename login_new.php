<?php
include_once 'common.php';
$generalobj->go_to_home();
$action = isset($_GET['action'])?$_GET['action']:'';
$forpsw =  isset($_REQUEST['forpsw'])?$_REQUEST['forpsw']:'';
$forgetPWd =  isset($_REQUEST['forgetPWd'])?$_REQUEST['forgetPWd']:'';
$depart = '';
if(isset($_REQUEST['depart'])) {
	$_SESSION['sess_depart'] = $_REQUEST['depart'];
	$depart = $_SESSION['sess_depart'];
}else {
	if(isset($_REQUEST['depart'])) { unset($_SESSION['sess_depart']); }
}

if($action == 'driver'){
	$meta_arr = $generalobj->getsettingSeo(9);		
}elseif($action == 'rider'){	
	$meta_arr = $generalobj->getsettingSeo(8);		
}
if($host_system == "carwash"){
	$rider_email="user@demo.com";
	$driver_email="washer@demo.com";	
}elseif($host_system == "beautician"){
	$rider_email="user@demo.com";
	$driver_email="beautician@demo.com";	
}elseif($host_system == "tutors"){
	$rider_email="student@demo.com";
	$driver_email="tutor@demo.com";	
}elseif($host_system == "ufxforall"){
	$rider_email="provider@demo.com";
	$driver_email="user@demo.com";	
}else{
	$rider_email="rider@gmail.com";
	$driver_email="driver@gmail.com";
}
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
		<div class="page-contant">
			<div class="page-contant-inner">
				<h2 class="header-page"><?=$langage_lbl['LBL_SIGN_IN_TXT'];?>
				<?if(SITE_TYPE =='Demo'){?>
				<p><?=$langage_lbl['LBL_SINCE_IT_IS_DEMO'];?></p>
				<?}?>
				</h2>
				<!-- login in page -->
				<div class="login-form">
				<div class="login-err">
				<p id="errmsg" style="display:none;" class="text-muted btn-block btn btn-danger btn-rect error-login-v"></p>
				<p style="display:none;" class="btn-block btn btn-rect btn-success error-login-v" id="success" ></p>
				</div>
						<div class="login-form-left"> <form action="<?=($action == 'rider')?'mytrip.php':'profile.php';?>" class="form-signin" method = "post" id="login_box" onSubmit="return chkValid('<?=$action?>');" >	
							<b>
								
								<input type="hidden" name="action" value="<?echo $action?>"/>
								
                                <label><?=$langage_lbl['LBL_EMAIL_MOBILE_NO_TXT_MSG']; ?></label>
								<input name="vEmail" type="text" placeholder="<?=$langage_lbl['ENTER_EMAIL_ID_OR_MOBILE_TXT']; ?>" class="login-input" id="vEmail" value="<?=(SITE_TYPE == 'Demo') ? (($action == 'rider') ? $rider_email:$driver_email) : '';?>" required /></b>
                                <b>
								
								
                                 <label><?=$langage_lbl['LBL_COMPANY_DRIVER_PASSWORD']; ?></label>
								<input name="vPassword" type="password" placeholder="<?=$langage_lbl['LBL_PASSWORD_LBL_TXT']; ?>" class="login-input" id="vPassword" value="<?=(SITE_TYPE == 'Demo') ? '123456' : ''?>" required />
							</b> 
							<b>
								<input type="submit" class="submit-but" value="<?=$langage_lbl['LBL_SIGN_IN_TXT'];?>" />
								<a onClick="change_heading('forgot')"><?=$langage_lbl['LBL_FORGET_PASS_TXT'];?></a>
							</b> </form>
						
					
					
						
                        <form action="" method="post" class="form-signin" id="frmforget" onSubmit="return forgotPass();" style="display: none;">
                         
							<input type="hidden" name="action" id="action" value="<?=$action?>">
							<b>
                            <label><?=$langage_lbl['LBL_EMAIL_LBL_TXT']; ?></label>
								<input name="femail" type="text" placeholder="<?=$langage_lbl['LBL_EMAIL_LBL_TXT']; ?>" class="login-input" id="femail" value="" required />
							</b>
							<b>
								<input type="submit" class="submit-but" value="<?=$langage_lbl['LBL_Recover_Password']; ?>" />
								<a onClick="change_heading('login')"><?=$langage_lbl['LBL_LOGIN'];?></a>
							</b>	 </form>	
						</div>					
					
					<div class="login-form-right login-form-right1">
					<div class="login-form-right1-inner">
						      <h3><?=$langage_lbl['LBL_DONT_HAVE_ACCOUNT'];?></h3>
						      <span><a href="<?=($action == 'rider')?'sign-up-rider':'sign-up';?>"><?=$langage_lbl['LBL_LOGIN_NEW_SIGN_UP'];?></a></span> 
				</div>
				<div class="login-form-right1-inner">
                 	<h3><?=$langage_lbl['LBL_REGISTER_WITH_ONE_CLICK'];?></h3>
                  <!--span class="fb-login"><a href="facebook"><img alt="" src="assets/img/reg-fb.jpg"><?//=$langage_lbl['LBL_SIGN_UP_WITH_FACEBOOK'];?></a></span-->
					<span class="login-socials">
						<a href="facebook/<?=$action?>" class="fa fa-facebook"></a>
						<a href="twitter/<?=$action?>" class="fa fa-twitter"></a>
						<a href="google/<?=$action?>" class="fa fa-google"></a>
					</span>
				  
    					</div>
				</div>   
				</div>
					
				<div style="clear:both;"></div>
				<?php
					if(SITE_TYPE == 'Demo'){
					 	if($action=='rider'){
		 		?>
				     
		     	<div class="text-center" style="text-align:left;">
					<?if($host_system == "carwash"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new user, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Rider : </b><br />
					Username: user@demo.com<br />
					Password: 123456
					</p>
					<?}elseif($host_system == "beautician"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new user, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>User : </b><br />
					Username: user@demo.com<br />
					Password: 123456
					</p>
					<?}elseif($host_system == "tutors"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new student, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Student : </b><br />
					Username: student@demo.com<br />
					Password: 123456
					</p>
					<?}else{?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new Rider, use your registered Email Id and Password to view the detail of your Rides.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Rider : </b><br />
					Username: rider@gmail.com<br />
					Password: 123456
					</p>

					<?}?>
				<!--<h4 ><?=$langage_lbl['LBL_PLEASE_USE_BELOW'];?> </h4>
						<h5>
							<p><?=$langage_lbl['LBL_IF_YOU_HAVE_REGISTER'];?></p>
							<p><b><?=$langage_lbl['LBL_USER_NAME_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_USERNAME'];?></p> 
							<p><b><?=$langage_lbl['LBL_PASSWORD_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_PASSWORD'];?> </p>
						</h5>
						-->
				</div>
		     	<?php 
		     			}else{ 
 				?>
		     	<div class="text-center" style="text-align:left;">
					<?if($host_system == "carwash"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new Washer, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Washer : </b><br />
					Username: washer@demo.com<br />
					Password: 123456
					</p>
					<?}elseif($host_system == "beautician"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new beautician service provider, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Beautician : </b><br />
					Username: beautician@demo.com<br />
					Password: 123456
					</p>
					<?}elseif($host_system == "tutors"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new Tutor, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Tutor : </b><br />
					Username: tutor@demo.com<br />
					Password: 123456
					</p>
					<?}else{?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new Driver, use your registered Email Id and Password to view the detail of your Rides.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Driver : </b><br />
					Username: driver@gmail.com<br />
					Password: 123456
					</p>

					<?}?>
					<p>
					<br /><b>Company : </b><br />
					Username: company@gmail.com<br />
					Password: 123456
					</p>
					<!--<h4 ><?=$langage_lbl['LBL_PLEASE_USE_BELOW_DRIVER'];?> </h4>
					<h5 >
						<p><?=$langage_lbl['LBL_IF_YOU_HAVE_REGISTER_DRIVER'];?></p>
						<p><b><?=$langage_lbl['LBL_USER_NAME_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_USERNAME_DRIVER'];?></p>
						<p><b><?=$langage_lbl['LBL_PASSWORD_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_PASSWORD'];?> </p>
					</h5>
					<h4 ><?=$langage_lbl['LBL_PLEASE_USE_BELOW_DEMO'];?></h4>
					<h5 >
						<p><?=$langage_lbl['LBL_IF_YOU_HAVE_REGISTER_COMPANY'];?></p>
						<p><b><?=$langage_lbl['LBL_USER_NAME_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_USERNAME_COMPANY'];?></p>
						<p><b><?=$langage_lbl['LBL_PASSWORD_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_PASSWORD'];?> </p>
					</h5> -->
				</div>
				<?
						}
					}
				?>
				
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
    <script>
		<?php if($forgetPWd==1){ ?>
				$('#frmforget').show();
				$('#login_box').hide();
		<?php } ?>
		
		function change_heading(type)
		{
			$('.error-login-v').hide();
			if(type=='forgot'){
				$('#frmforget').show();
				$('#login_box').hide();
			}				
			else{
				$('#frmforget').hide();
				$('#login_box').show();
			}
		}
		function chkValid(login_type)
		{
			var id = document.getElementById("vEmail").value;
			var pass = document.getElementById("vPassword").value;
			if(id == '' || pass == '')
			{
				document.getElementById("errmsg").innerHTML = 'Please enter Email Id and/or password';
				document.getElementById("errmsg").style.display = '';
				return false;
			}
			else
			{
				var request = $.ajax({
					type: "POST",
					url: 'ajax_login_action.php',
					data: $("#login_box").serialize(),

					success: function(data)
					{
						if(data == 1){
							document.getElementById("errmsg").innerHTML = 'You are not active. Please contact administrator to activate your account.';
							document.getElementById("errmsg").style.display = '';
							return false;
						}
						else if(data == 2){
							document.getElementById("errmsg").style.display = 'none';
							departType = '<?php echo $depart; ?>';
							if(login_type == 'rider' && departType == 'mobi')
								window.location = "mobi";
							else if(login_type == 'rider')
								window.location = "profile_rider.php";
							else if(login_type == 'driver')
								window.location = "profile.php";

							return true; // success registration
						}
						else if(data == 3) {
							document.getElementById("errmsg").innerHTML = 'Invalid Email/Mobile or Password';
							document.getElementById("errmsg").style.display = '';
						   return false;

						}
						else {
							document.getElementById("errmsg").innerHTML = 'Invalid Email/Mobile or Password';
							document.getElementById("errmsg").style.display = '';
							//setTimeout(function() {document.getElementById('errmsg1').style.display='none';},2000);
							return false;
						}
					}
				});

				request.fail(function(jqXHR, textStatus) {
					alert( "Request failed: " + textStatus );
					return false;
				});
				return false;
			}
		}
		function forgotPass()
		{
			$('.error-login-v').hide();
			var site_type='<?echo SITE_TYPE;?>';
			var id = document.getElementById("femail").value;
			if(id == '')
			{
				document.getElementById("errmsg").style.display = '';
				document.getElementById("errmsg").innerHTML = 'Please enter Email Address';
			}
			else {
				var request = $.ajax({
					type: "POST",
					url: 'ajax_fpass_action.php',
					data: $("#frmforget").serialize(),
					dataType: 'json',
					beforeSend:function()
					{
						//alert(id);
						},
					success: function(data)
					{

						if(data.status == 1)
						{
							change_heading('login');
							document.getElementById("success").innerHTML = data.msg;
							document.getElementById("success").style.display = '';
							
						}
						else
						{
							document.getElementById("errmsg").innerHTML = data.msg;
							document.getElementById("errmsg").style.display = '';
						}
						
					}
				});

				request.fail(function(jqXHR, textStatus) {
					alert( "Request failed: " + textStatus );
				});

				
			}
			return false;
		}

		function fbconnect()
	{
		javscript:window.location='fbconnect.php';
	}
	</script>
	<?php 
	if($forpsw == 1){ ?>
		<script>
			change_heading('forgot');
		</script>
	<?php }

	?>
</body>
</html>
