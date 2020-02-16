<?php
   include_once("common.php");
   $generalobj->go_to_home();
   $meta_arr = $generalobj->getsettingSeo(6);
   $sql = "SELECT * from language_master where eStatus = 'Active'" ;
   $db_lang = $obj->MySQLSelect($sql);
   $sql = "SELECT * from country where eStatus = 'Active'" ;
   $db_code = $obj->MySQLSelect($sql);
   //For Currency
   $sql="select * from  currency where eStatus='Active'";
   $db_currency=$obj->MySQLSelect($sql);
   //echo "<pre>";print_r($db_lang);
   $script="Rider Sign-Up";
   
	if(isset($_REQUEST['depart'])) {
		$_SESSION['sess_depart'] = $_REQUEST['depart'];
	}else {
		if(isset($_REQUEST['depart'])) { unset($_SESSION['sess_depart']); }
	}
   
	$Mobile=$MOBILE_VERIFICATION_ENABLE;
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
   <!-- <title><?=$COMPANY_NAME?>| Signup</title>-->
	<title><?php echo $meta_arr['meta_title'];?></title>
	<meta name="keywords" value="<?=$meta_arr['meta_keyword'];?>"/>
	<meta name="description" value="<?=$meta_arr['meta_desc'];?>"/>
    <!-- Default Top Script and css -->
    <?php include_once("top/top_script.php");?>
    <link href="assets/css/checkbox.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/radio.css" rel="stylesheet" type="text/css" />
    <?php include_once("top/validation.php");?>
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
            <h2 class="header-page trip-detail"><?=$langage_lbl['LBL_SIGN_UP']; ?>
                <p><?=$langage_lbl['LBL_TELL_US_A_BIT_ABOUT_YOURSELF']; ?></p>
            </h2>
            <!-- trips detail page -->
            <form name="frmsignup" id="frmsignup" method="post" action="signuprider_a.php">
				<input type="hidden" name="depart" value="<?php echo isset($_REQUEST['depart']) ? $_REQUEST['depart'] : ''; ?>" >
                <div class="driver-signup-page">
                 <?php
                if ($_REQUEST['error']) {
                ?>
                    <div class="row">
                        <div class="col-sm-12 alert alert-danger">
                             <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?=$_REQUEST['var_msg']; ?>
                        </div>
                    </div>
                <?php 
                    }
                ?>
                    <div class="create-account line-dro">
                        <h3><?=$langage_lbl['LBL_CREATE_ACCOUNT']; ?></h3>
                        <span class="newrow">
                            <strong id="emailCheck"><label><?=$langage_lbl['LBL_EMAIL_TEXT_SIGNUP'];?></label>
								            <?php /*<input type="hidden" name="mobile_verification"  id="mobile_verification" value="<?=$Mobile;?>"> */ ?>
								            <input type="text" placeholder="<?=$langage_lbl['LBL_PROFILE_RIDER_YOUR_EMAIL_ID']; ?>" name="vEmail" id="vEmail_verify" class="create-account-input"/></strong>
                            <strong><label><?=$langage_lbl['LBL_PASSWORD']; ?></label>
                            <input id="pass" type="password" name="vPassword" placeholder="<?=$langage_lbl['LBL_PASSWORD']; ?>" class="create-account-input create-account-input1" value="" /></strong>
                        </span>
                         <?php 

                        if($REFERRAL_SCHEME_ENABLE == 'Yes'){ ?>
                         <span class="newrow" style="margin:0px;">
                         <strong id="refercodeCheck">
                         <input id="vRefCode" type="text" name="vRefCode" placeholder="<?=$langage_lbl['LBL_REFERAL_CODE']; ?>" class="create-account-input create-account-input1 vRefCode_verify" value=""  onBlur=" validate_refercode(this.value)"/>  </strong>
                            <input type="hidden" placeholder="" name="iRefUserId" id="iRefUserId"  class="create-account-input" value="" />
                            <input type="hidden" placeholder="" name="eRefType" id="eRefType" class="create-account-input" value=""  />
                         <!--strong>
                         <input id="cpass" type="password" name="vConfirmPassword" placeholder="<?//=$langage_lbl['LBL_PASSWORD']; ?>" class="create-account-input create-account-input1" value="" /></strong-->
                       </span>  
                         <?php }
                        ?>
                    </div>
                    <div class="create-account">
                        <h3><?=$langage_lbl['LBL_HEADER_PROFILE_TXT']; ?></h3>
                        <span class="newrow">
                            <strong><label><?=$langage_lbl['LBL_YOUR_FIRST_NAME'];?></label>
                            <input name="vName" type="text" class="create-account-input" placeholder="<?=$langage_lbl['LBL_FIRST_NAME_HEADER_TXT']; ?>" id="vName"/></strong>
                            <strong><label><?=$langage_lbl['LBL_YOUR_LAST_NAME'];?></label>
                            <input name="vLastName" type="text" class="create-account-input create-account-input1" placeholder="<?=$langage_lbl['LBL_LAST_NAME_HEADER_TXT']; ?>" id="vLastName"/></strong>
                        </span>   
                        <span class="c_country newrow">
                            <strong>
                            <label><?=$langage_lbl['LBL_SELECT_CONTRY']; ?> * </label>
                                <select name="vCountry" class="custom-select-new" onChange="changeCode(this.value); " required>
                                    
                                    <? for($i=0;$i<count($db_code);$i++) { ?>
                                    <option value="<?=$db_code[$i]['vCountryCode']?>"<? if($db_code[$i]['vCountryCode']== $DEFAULT_COUNTRY_CODE_WEB){echo 'selected';}?>>
                                    <?=$db_code[$i]['vCountry']?>
                                    </option>
                                    <? } ?>
                                </select>
                            </strong>
                        </span>  
                        <span class="c_code_ph_no newrow">
                            <strong class="c_code"><label><?=$langage_lbl['LBL_PHONE_CODE']; ?></label>
                            <input type="text"  name="vPhoneCode" readonly  class="create-account-input" id="code" /></strong>
                            <strong class="ph_no" id="mobileCheck"><label>&amp; <?=$langage_lbl['LBL_NUMBER_TXT']; ?></label>
                            <input type="text"  id="vPhone" placeholder="<?=$langage_lbl['LBL_777-777-7777']; ?>" class="create-account-input create-account-input1 vPhone_verify" name="vPhone" /></strong>
                            <!-- <strong id="mobileCheck"></strong> -->
                        </span> 
                        <span class="newrow">
						<?php if(count($db_lang) <=1){ ?>
							 <strong>
							  <input name="vLang" type="hidden" class="create-account-input" value="<?php echo $db_lang[0]['vCode'];?>" id="vName"/>
							  <? if(count($db_currency) > 1){?>
                            <label><?=$langage_lbl['LBL_SELECT_CURRENCY']; ?></label>
                                <select class="custom-select-new " name = 'vCurrencyPassenger'>
                                    
                                    <? for($i=0;$i<count($db_currency);$i++){ ?>
                                    <option value = "<?= $db_currency[$i]['vName'] ?>" <?if($vCurrencyPassenger==$db_currency[$i]['vName']){?>selected<? } else if($db_currency[$i]['eDefault']=="Yes"){?>selected<?} ?>><?= $db_currency[$i]['vName'] ?></option>
                                    <? } ?>
                                </select>
                            </strong>
							  <? } else{?>
								<input name="vCurrencyPassenger" type="hidden" class="create-account-input" value="<?= $db_currency[0]['vName'] ?>" id="vCurrencyPassenger"/>
							
							  <?php } }else{ ?>
                            <strong>
                            <label><?=$langage_lbl['LBL_SELECT_LANGUAGE']; ?></label>
                                <select name="vLang" class="custom-select-new ">
                                    <? for($i=0;$i<count($db_lang);$i++) { ?>
                                    <option value="<?=$db_lang[$i]['vCode']?>" <? if($db_lang[$i]['eDefault']=='Yes'){echo 'selected';}?>>
                                    <?=$db_lang[$i]['vTitle']?>
                                    </option>
                                    <? } ?>
                                </select>
                            </strong>
							<? if(count($db_currency) > 1){?>
							 <strong>
                            <label><?=$langage_lbl['LBL_SELECT_CURRENCY']; ?></label>
                                <select class="custom-select-new " name = 'vCurrencyPassenger'>
                                    
                                    <? for($i=0;$i<count($db_currency);$i++){ ?>
                                    <option value = "<?= $db_currency[$i]['vName'] ?>" <?if($vCurrencyPassenger==$db_currency[$i]['vName']){?>selected<? } else if($db_currency[$i]['eDefault']=="Yes"){?>selected<?} ?>><?= $db_currency[$i]['vName'] ?></option>
                                    <? } ?>
                                </select>
                            </strong>
							<?php
								}else{?>
								<input name="vCurrencyPassenger" type="hidden" class="create-account-input" value="<?= $db_currency[0]['vName'] ?>" id="vCurrencyPassenger"/>
							<?
							  }	}
							?>
                        </span> 
						
						
                        <span class='gender-span001 newrow'>
							<strong><div class="radio-but"> 
							<em><?=$langage_lbl['LBL_GENDER_TXT']; ?>: </em>
                            <b><input id="r4" name="eGender" type="radio" value="Male" >
                                <label for="r4"><?=$langage_lbl['LBL_MALE_TXT']; ?></label></b><b> <input id="r5" name="eGender" type="radio" value="Female" class="required">
                                <label for="r5"><?=$langage_lbl['LBL_FEMALE_TXT']; ?></label></b></div></strong>
                        </span>
						
						<span class="newrow">
                            <strong class="captcha-signup"><label><?=$langage_lbl['LBL_CAPTCHA_SIGNUP']; ?></label>
                            <input id="POST_CAPTCHA" class="create-account-input" size="5" maxlength="5" name="POST_CAPTCHA" type="text">
                            <em class="captcha-dd"><img src="captcha_code_file.php?rand=<?php echo rand(); ?>" id='captchaimg' alt="" class="chapcha-img" />&nbsp;Can't read the image? <a href='javascript: refreshCaptcha();'>Click here.</a></em>
                             </strong>
                           
                        </span>
						<span class="newrow">
						 <strong class="captcha-signup1">
							<abbr><?=$langage_lbl['LBL_SIGNUP_Agree_to']; ?> <a href="terms_condition.php" target="_blank"><?=$langage_lbl['LBL_TERMS_AND_CONDITION']; ?></a>
                                <div class="checkbox-n">
                                    <input id="c1" name="remember-me" type="checkbox" class="termscheckbox" value="remember">
                                    <label for="c1"></label>
                                </div>
                            </abbr>
                            </strong>
						 </span>
						
                    <p><button type="submit" class="submit" name="SUBMIT"><?=$langage_lbl['LBL_BTN_SUBMIT_TXT']; ?></button></p>
                    </div>
                </div>
            </form>
			  <div class="col-lg-12">
                <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="H2"><?=$langage_lbl['LBL_SIGNUP_PHONE_VERI']; ?></h4>
                            </div>
                            <div class="modal-body">
                                <form role="form" name="verification" id="verification">
                                    <p class="help-block">To complete the driver registration process, you must have to enter the verification code sent to your registered phone number. </p>
                                    <div class="form-group">
                                        <label><?=$langage_lbl['LBL_SIGNUP_ENTER_CODE']; ?></label>
                                        <input class="form-control" type="text" id="vCode1"/>
                                    </div>
                                    <p class="help-block" id="verification_error"></p>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" onClick="check_verification('verify')">Verify</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
            <!-- -->
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
	<script type="text/javascript" src="assets/js/validation/jquery.validate.min.js" ></script>
	<script type="text/javascript" src="assets/js/validation/additional-methods.js" ></script>
    <script>
	
	$('#frmsignup').validate({
		ignore: 'input[type=hidden]',
		errorClass: 'help-block',
		errorElement: 'span',
		errorPlacement: function (error, e) {
			e.parents('.newrow > strong').append(error);
		},
		highlight: function (e) {
			$(e).closest('.newrow').removeClass('has-success has-error').addClass('has-error');
			$(e).closest('.newrow strong input').addClass('has-shadow-error');
			$(e).closest('.help-block').remove();
		},
		success: function (e) {
			e.prev('input').removeClass('has-shadow-error');
			e.closest('.newrow').removeClass('has-success has-error');
			e.closest('.help-block').remove();
			e.closest('.help-inline').remove();
		},
		rules: {
			vEmail: {required: true, email: true,
					remote: {
							url: 'ajax_validate_email_new.php',
							type: "post",
							data: {iUserId: ''},
						}
			},
			// eGender: {required: true},
			vPassword: {required: true, minlength: 6},
			vConfirmPassword: {required: true, minlength: 6, equalTo:"#pass" },
			vPhone: {required: true, phonevalidate: true,
						remote: {
							url: 'ajax_rider_mobile_new.php',
							type: "post",
							data: {iUserId: ''},
						}
			},
			vName: {required: true, minlength: 2},
			vLastName: {required: true, minlength: 2},
			POST_CAPTCHA: {required: true, remote: {
							url: 'ajax_captcha_new.php',
							type: "post",
							data: {iDriverId: ''},
						}},
			'remember-me': {required: true},
		},
		messages: {
			vEmail: {remote: 'Email address is already exists.'},
			'remember-me': {required: 'Please agree to the Terms & Conditions.'},
			vPhone: {remote: 'Phone Number is already exists.'},
			POST_CAPTCHA: {remote: 'Captcha did not match.'}
		}
	});
	
	$('#verification').bind('keydown',function(e){
        if(e.which == 13){
            check_verification('verify'); return false;
        }
    });
	
	function check_verification(request_type)
    {
        if(request_type=='send'){
            code=$("#code").val();
        }
        else{
            code=$("#vCode1").val();
            if(code==''){
                $("#verification_error").html('<i class="icon icon-remove alert" style="display:inline-block;color:red;padding:0px;">Please Enter verification code</i>');
                return false;
            }
        }
        phone=$("#vPhone").val();
		
        email=$("#vEmail").val();
        name=$("#vFirstName").val();
        name+=' '+$("#vLastName").val();
		//alert(request_type);
        var request = $.ajax({
            type: "POST",
            url: 'ajax_driver_verification.php',
            dataType: "json",
            data: {'vPhone':phone,
                'vCode':code,
                'type':request_type,
                'name':name,
                'vEmail':email},
            success: function (data)
            {
                console.log(data['code']); console.log(data['action']);


                if(data['type']=='send'){
                    if(data['action']==0)
                    {
                        $("#mobileCheck").html('<i class="icon icon-remove alert-danger alert">mobile no,Already Exist</i>');
                        $("#mobileCheck").show();
                        $('input[type="submit"]').attr('disabled','disabled');
                        return false;
                    }
                    else{
                        return true;
                    }
                }
                else if(data['type']=='verify'){
                    if(data['0']==1){
                        $("#verification_error").html('');
                        document.frmsignup.submit();
                    }
                    else if(data['0']==0){
                        $("#verification_error").html('');
                        $("#verification_error").html('<i class="icon icon-remove alert" style="display:inline-block;color:red;" >Invalid Verification code, please try again.</i>');
                    }
                    else{
                        $("#verification_error").html('');
                        $("#verification_error").html('<i class="icon icon-remove alert" style="display:inline-block;color:red;">Error in verification. please try again.</i>');
                    }
                }
            }
        });
    }

		
    </script>
    <script type="text/javascript">
        
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
        
        function fbconnect()
        {
            javscript:window.location='fbconnect.php';
        }
        
		
		function validate_refercode(id){
            if(id == ""){
                return true;
            }else{
            
                var request = $.ajax({
                    type: "POST",
                    url: 'ajax_validate_refercode.php',
                    data: 'refcode=' +id,
                    success: function (data)
                    { 
                        
                        if(data == 0){
						$("#referCheck").remove();
                        $(".vRefCode_verify").addClass('required-active');
						$('#refercodeCheck').append('<div class="required-label" id="referCheck" >*Refer code Not Found.</div>');
                        $('#vRefCode').attr("placeholder", "Referal Code (Optional)");
                        $('#vRefCode').val("");
                        return false;
                        }else{
                            var reponse = data.split('|');              
                            $('#iRefUserId').val(reponse[0]);
                            $('#eRefType').val(reponse[1]);
                        }                   
                        
                    }
                });
            }
        }
		
		function refreshCaptcha()
		{
			var img = document.images['captchaimg'];
			img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
		}

    </script>
    <!-- End: Footer Script -->
</body>
</html>