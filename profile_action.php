<?php
	include_once('common.php');
	
	$action =isset($_REQUEST['action'])?$_REQUEST['action']:''; 

    $iCompanyId = $_SESSION['sess_iCompanyId'];
	$iDriverId = $_SESSION['sess_iUserId'];
	if($_SESSION['sess_user'] == 'driver')
	{
		$tbl = 'register_driver';
		$where = " WHERE `iDriverId` = '".$iDriverId."'";
		//$str = ", eStatus = 'inactive'";
		$str = '';
	}
	if($_SESSION['sess_user'] == 'company')
	{
		$tbl = 'company';
		$where = " WHERE `iCompanyId` = '".$iCompanyId."'";
		$str = '';
	}

	if($action == 'login')
	{
		$phone = isset($_REQUEST['phone'])?$_REQUEST['phone']:'';
		$email = isset($_POST['email'])?$_POST['email']:'';
		$username = isset($_POST['username'])?$_POST['username']:'';
		$name = isset($_POST['name'])?$_POST['name']:'';
		$tProfileDescription = isset($_POST['tProfileDescription'])?$_POST['tProfileDescription']:'';
		$lname = isset($_POST['lname'])?$_POST['lname']:'';
		$vCountry = isset($_POST['vCountry'])?$_POST['vCountry']:'';
		$vCode = isset($_POST['vCode'])?$_POST['vCode']:'';
		$vCurrencyDriver = isset($_POST['vCurrencyDriver']) ? $_POST['vCurrencyDriver'] : '';
		$vCompany=isset($_POST['vCompany']) ? $_POST['vCompany'] : '';
		$_SESSION["sess_vCurrency"] = $vCurrencyDriver;
		if($_SESSION['sess_user'] == 'driver')
		{

		//$str = ",`vCurrencyDriver`='" . $vCurrencyDriver . "', eStatus = 'active'";
		$str = ",`tProfileDescription` = '".$tProfileDescription."',`vCurrencyDriver`='" . $vCurrencyDriver . "'";
		}
		else
		{
		$str = ",`vCompany`='" . $vCompany . "'";
		}
		
		
		$q = "UPDATE ";

		$sql="select * from ".$tbl .$where;
		$edit_data=$obj->sql_query($sql);
		
		if($_SESSION['sess_user'] == 'driver' && $_REQUEST['email'] != $edit_data[0]['vEmail'])
		{
			$query = $q ." `".$tbl."` SET `eEmailVerified` = 'No' ".$where;
			$obj->sql_query($query);
			
		}
		if($_SESSION['sess_user'] == 'driver' && $_REQUEST['phone'] != $edit_data[0]['vPhone'])
		{
			$query = $q ." `".$tbl."` SET `ePhoneVerified` = 'No' ".$where;
			$obj->sql_query($query);
			
		}
		if($_SESSION['sess_user'] == 'driver' && $APP_TYPE == 'UberX')
		{
			$query = $q ." `".$tbl."` SET `tProfileDescription` = 'No' ".$where;
			$obj->sql_query($query);
			
		}

		$query = $q ." `".$tbl."` SET
			`vEmail` = '".$email."',
			`vLoginId` = '".$username."',
			`vName` = '".$name."' ,
			`vLastName` = '".$lname."',
			`vCountry` = '".$vCountry."',
			`vCode` = '".$vCode."',
			`vPhone` = '".$phone."' $str".$where;
		$obj->sql_query($query);
		echo $var_msg = "Profile Update successfully";
		return $var_msg;
		exit;
	}
	if($action == 'address')
	{	
	
		$address1 = isset($_REQUEST['address1'])?$_REQUEST['address1']:'';
		$address2 = isset($_POST['address2'])?$_POST['address2']:'';
		$vCountry = isset($_POST['vCountry'])?$_POST['vCountry']:'';
		$vCity = isset($_POST['vCity'])?$_POST['vCity']:'';
		$vState = isset($_POST['vState'])?$_POST['vState']:'';
		$zipcode = isset($_POST['vZipcode'])?$_POST['vZipcode']:'';

		$q = "UPDATE ";
		$query = $q ." `".$tbl."` SET
			`vCaddress` = '".$address1."',
			`vCadress2` = '".$address2."',
			`vCity` = '".$vCity."',
			`vCountry` = '".$vCountry."',
			`vState` = '".$vState."',
			`vZip` = '".$zipcode."' $str".$where;
		$obj->sql_query($query);

		echo $var_msg = "Address Update successfully";
		return $var_msg;
		exit;
	}
	if($action == 'pass')
	{
		$npass = isset($_REQUEST['npass'])?$_REQUEST['npass']:'';
		$npass = $generalobj->encrypt_bycrypt($npass);

		$q = "UPDATE ";
		$query = $q ." `".$tbl."` SET
			`vPassword` = '".$npass."' $str".$where;
		$obj->sql_query($query);

		echo $var_msg = "Password uploaded successfully";
		return $var_msg;
		exit;
	}
	
	if($action == 'lang1')
	{
		$lang = isset($_REQUEST['lang1'])?$_REQUEST['lang1']:'';

	 	$q = "UPDATE ";
		$query = $q ." `".$tbl."` SET
			`vLang` = '".$lang."' $str".$where;
		$obj->sql_query($query);

		echo $var_msg = "Language Update successfully";
		return $var_msg;
		exit;
	}
	if($action == 'vat')
	{
		$vat = isset($_REQUEST['vat'])?$_REQUEST['vat']:'';
		$q = "UPDATE ";
		$query = $q ." `".$tbl."` SET
			`vVat` = '".$vat."' $str".$where;
		$obj->sql_query($query);

		echo $var_msg = "Vat Update successfully";
		return $var_msg;
		exit;
	}

	if($action == 'access')
	{
		$access = isset($_REQUEST['access'])?$_REQUEST['access']:'';

		$q = "UPDATE ";
		$query = $q ." `".$tbl."` SET
			`eAccess` = '".$access."' $str".$where;
		$obj->sql_query($query);

		echo $var_msg = "Accessibility Update successfully";
		return $var_msg;
		exit;
	}

	if($action == 'bankdetail')
	{
		
		$vAccountNumber = isset($_POST['vAccountNumber'])?$_POST['vAccountNumber']:'';
		$vBIC_SWIFT_Code = isset($_POST['vBIC_SWIFT_Code'])?$_POST['vBIC_SWIFT_Code']:'';
		$vBankAccountHolderName = isset($_POST['vBankAccountHolderName'])?$_POST['vBankAccountHolderName']:'';
		$vBankLocation = isset($_POST['vBankLocation'])?$_POST['vBankLocation']:'';
		$vBankName = isset($_POST['vBankName'])?$_POST['vBankName']:'';
		$vPaymentEmail = isset($_POST['vPaymentEmail'])?$_POST['vPaymentEmail']:'';
		

		$q = "UPDATE ";

		 $query = $q ." `".$tbl."` SET
			`vAccountNumber` = '".$vAccountNumber."',
			`vBIC_SWIFT_Code` = '".$vBIC_SWIFT_Code."',
			`vBankAccountHolderName` = '".$vBankAccountHolderName."' ,
			`vBankLocation` = '".$vBankLocation."',
			`vBankName` = '".$vBankName."',			
			`vPaymentEmail` = '".$vPaymentEmail."' $str".$where; 
		$obj->sql_query($query);
		echo $var_msg = "Bank Detail Update successfully";
		return $var_msg;
		exit;

		
	}

?>
