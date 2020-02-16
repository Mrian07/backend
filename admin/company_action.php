<?php
include_once('../common.php');
require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$ksuccess = isset($_REQUEST['ksuccess']) ? $_REQUEST['ksuccess'] : 0;
$action = ($id != '') ? 'Edit' : 'Add';

$tbl_name = 'company';
$script = 'Company';

$sql = "select iCountryId,vCountry,vCountryCode from country";
$db_country = $obj->MySQLSelect($sql);

// set all variables with either post (when submit) either blank (when insert)
$vName = isset($_POST['vName'])?$_POST['vName']:'';
$vLastName = isset($_POST['vLastName'])?$_POST['vLastName']:'';
$vEmail = isset($_POST['vEmail'])?$_POST['vEmail']:'';
$vCompany = isset($_POST['vCompany'])?$_POST['vCompany']:'';
$vPassword = isset($_POST['vPassword'])?$_POST['vPassword']:'';
$vPhone = isset($_POST['vPhone'])?$_POST['vPhone']:'';
$vCaddress = isset($_POST['vCaddress'])?$_POST['vCaddress']:'';
$vCadress2 = isset($_POST['vCadress2'])?$_POST['vCadress2']:'';
$vCity = isset($_POST['vCity'])?$_POST['vCity']:'';
$vInviteCode = isset($_POST['vInviteCode'])?$_POST['vInviteCode']:'';
$vPass =$generalobj->encrypt_bycrypt($vPassword);
$vVatNum=isset($_POST['vVatNum'])?$_POST['vVatNum']:'';
$vCountry=isset($_POST['vCountry'])?$_POST['vCountry']:'';
$vState=isset($_POST['vState'])?$_POST['vState']:'';
$vCode=isset($_POST['vCode'])?$_POST['vCode']:'';
$vLang=isset($_POST['vLang'])?$_POST['vLang']:'';
$backlink=isset($_POST['backlink'])?$_POST['backlink']:'';
$previousLink=isset($_POST['backlink'])?$_POST['backlink']:'';
$vPass = ($vPassword != "") ? $generalobj->encrypt_bycrypt($vPassword) : '';

if($action == 'Add'){
	$vCountry = $DEFAULT_COUNTRY_CODE_WEB;
}

if (isset($_POST['submit'])) {
    if (SITE_TYPE == 'Demo') {
        header("Location:company_action.php?id=" . $id . '&success=2');
        exit;
    }
    //Add Custom validation
    require_once("library/validation.class.php");
    $validobj = new validation();
    $validobj->add_fields($_POST['vCompany'], 'req', 'Company Name is required');
    $validobj->add_fields($_POST['vEmail'], 'req', 'Email Address is required.');
    $validobj->add_fields($_POST['vEmail'], 'email', 'Please enter valid Email Address.');
    if ($action == "Add") {
		$validobj->add_fields($_POST['vPassword'], 'req', 'Password is required.');
	}
    $validobj->add_fields($_POST['vPhone'], 'req', 'Phone Number is required.');
    $validobj->add_fields($_POST['vCaddress'], 'req', 'Address Line 1 is required.');
    $validobj->add_fields($_POST['vVatNum'], 'req', 'Vat Number is required.');
    $validobj->add_fields($_POST['vCountry'], 'req', 'Country is required.');
    $error = $validobj->validate();

    //Other Validations
    if ($vEmail != "") {
        if ($id != "") {
            $msg1 = $generalobj->checkDuplicateAdminNew('iCompanyId', 'company', Array('vEmail'), $id, "");
        } else {
            $msg1 = $generalobj->checkDuplicateAdminNew('vEmail', 'company', Array('vEmail'), "", "");
        }
        if ($msg1 == 1) {
            $error .= 'Email Address is already exists.<br>';
        }
    }

    if ($error) {
        $success = 3;
        $newError = $error;
        //exit;
    } else {
        //echo '<pre>'; print_r($_POST); exit;

        $q = "INSERT INTO ";
        $where = '';
        if ($action == 'Edit') {
            $str = ", eStatus = 'Inactive' ";
        } else {
            $str = '';
        }
        if ($id != '') {
            $q = "UPDATE ";
            $where = " WHERE `iCompanyId` = '" . $id . "'";
        }
		
		$passPara = '';
		if($vPass != ""){
			$passPara = "`vPassword` = '" . $vPass . "',";
		}

        $query = $q ." `".$tbl_name."` SET
			`vName` = '".$vName."',
			`vLastName` = '".$vLastName."',
			`vEmail` = '".$vEmail."',
			`vCaddress` = '".$vCaddress."',
			`vCadress2` = '".$vCadress2."',
			$passPara
			`vPhone` = '".$vPhone."',
			`vLang` = '".$vLang."',
			`vCity` = '".$vCity."',
			`vState` = '".$vState."',
			`vCompany` = '".$vCompany."',
			`vInviteCode` = '".$vInviteCode."',
			`vVat` = '".$vVatNum."',
			`vCountry` = '".$vCountry."'"
			.$where;
        $obj->sql_query($query);
        $id = ($id != '') ? $id : mysql_insert_id();
        if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record Inserted Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record Updated Successfully.';
        }
        header("location:".$backlink);
    }
}
// for Edit

if ($action == 'Edit') {
    $sql = "SELECT * FROM " . $tbl_name . " WHERE iCompanyId = '" . $id . "'";
    $db_data = $obj->MySQLSelect($sql);
    //echo "<pre>";print_R($db_data);echo "</pre>";
    // $vPass = $generalobj->decrypt($db_data[0]['vPassword']);
    $vLabel = $id;
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vName	 = $value['vName'];
				$vLastName = $generalobjAdmin->clearName(" ".$value['vLastName']);
				$vEmail = $generalobjAdmin->clearEmail($value['vEmail']);
				$vCompany = $generalobjAdmin->clearCmpName($value['vCompany']);
				$vCaddress = $value['vCaddress'];
				$vCadress2 = $value['vCadress2'];
				$vPassword = $value['vPassword'];
				$vPhone = $generalobjAdmin->clearPhone($value['vPhone']);
				$vCity = $value['vCity'];
				$vState = $value['vState'];
				$vInviteCode = $value['vInviteCode'];
				$vVatNum=$value['vVat'];
				$vCountry=$value['vCountry'];
        }
    }
}
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME?> | Company <?= $action; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
							<h2><?= $action; ?> Company <?= $vCompany; ?></h2>
							<a class="back_link" href="company.php">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <? if ($success == 2) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                "Edit / Delete Record Feature" has been disabled on the Demo Company Panel. This feature will be enabled on the main script we will provide you.
                            </div><br/>
                            <?} ?>
                            <? if ($success == 3) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                            <?php print_r($error); ?>
                            </div><br/>
                            <?} ?>
                            <form name="_company_form" id="_company_form" method="post" action="" enctype="multipart/form-data">
								<input type="hidden" name="actionOf" id="actionOf" value="<?php echo $action; ?>"/>
                                <input type="hidden" name="id" id="iCompanyId" value="<?php echo $id; ?>"/>
                                <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                <input type="hidden" name="backlink" id="backlink" value="company.php"/>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Company Name<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="vCompany"  id="vCompany" value="<?= $vCompany; ?>" placeholder="Company Name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Email<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="vEmail" id="vEmail" value="<?= $vEmail; ?>" placeholder="Email">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Password<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="password" class="form-control" name="vPassword"  id="vPassword" value="" placeholder="Password">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Phone<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="vPhone"  id="vPhone" value="<?= $vPhone; ?>" placeholder="Phone" >
                                    </div>
                                </div>
								<div class="row">
								 <div class="col-lg-12">
									  <label>Country <span class="red"> *</span></label>
								 </div>
								 <div class="col-lg-6">
									  <select class="form-control" name = 'vCountry' id="vCountry" onChange="setState(this.value,'');" required>
										   <option value="">Select</option>
										   <? for($i=0;$i<count($db_country);$i++){ ?>
										   <option value = "<?= $db_country[$i]['vCountryCode'] ?>" <?if($DEFAULT_COUNTRY_CODE_WEB == $db_country[$i]['vCountryCode'] && $action == 'Add') { ?> selected <?php } else if($vCountry==$db_country[$i]['vCountryCode']){?>selected<? } ?>><?= $db_country[$i]['vCountry'] ?></option>
										   <? } ?>
									  </select>
								 </div>
								</div>
								
								<div class="row">
								 <div class="col-lg-12">
									  <label>State </label>
								 </div>
								 <div class="col-lg-6">
									  <select class="form-control" name = 'vState' id="vState" onChange="setCity(this.value,'');" >
										   <option value="">Select</option>
									  </select>
								 </div>
								</div>
								
								<div class="row">
								 <div class="col-lg-12">
									  <label>City </label>
								 </div>
								 <div class="col-lg-6">
									  <select class="form-control" name = 'vCity' id="vCity"  >
										   <option value="">Select</option>
									  </select>
								 </div>
								</div>
							
								<div class="row">
									<div class="col-lg-12">
										<label>Address Line 1<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vCaddress"  id="vCaddress" value="<?=$vCaddress;?>" placeholder="Address Line 1" >
									</div>
								</div>


								<div class="row">
									<div class="col-lg-12">
										<label>Address Line 2</label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vCadress2"  id="vCadress2" value="<?=$vCadress2;?>" placeholder="Address Line 2" >
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12">
										<label>Vat Number</label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vVatNum"  id="vVatNum" value="<?=$vVatNum;?>" placeholder="VAT Number">
									</div>
								</div>
								
                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?= $action; ?> Company" >
                                        <a href="javascript:void(0);" onclick="reset_form('_company_form');" class="btn btn-default">Reset</a>
                                        <a href="company.php" class="btn btn-default back_link">Cancel</a>
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
        <?
        include_once('footer.php');
        ?>
    </body>
    <!-- END BODY-->
</html>
<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){
		referrer =  document.referrer;
	}else {
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "company.php";
	}else {
		$("#backlink").val(referrer);
	}
	$(".back_link").attr('href',referrer);
});

function setCity(id,selected)
{
	var fromMod = 'company';
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
	var fromMod = 'company';
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
	
setState('<?php echo $vCountry; ?>','<?php echo $vState; ?>');
setCity('<?php echo $vState; ?>','<?php echo $vCity; ?>');
</script>