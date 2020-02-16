<?php
	include_once('common.php');
	
	$ssql="";
	$ssql1="";
	$usertype = $_SESSION['sess_user'];
	//echo $_REQUEST['uid'];die;
	if($_REQUEST['uid'] != "" && $usertype == 'company')
	{
		$ssql="and iCompanyId != '".$_REQUEST['uid']."'";
	}
	else if($_REQUEST['uid'] != "" && $usertype == 'driver'){
		$ssql1="and iDriverId != '".$_REQUEST['uid']."'";
	}
	
	else if($_REQUEST['uid'] != "" && $usertype == 'rider'){
		$ssql2="and iUserId != '".$_REQUEST['uid']."'";
	}
	
	
	if(isset($_REQUEST['id']))
	{
		$email=$_REQUEST['id'];
		if($usertype == 'company') {
			$sql = "SELECT vEmail,eStatus FROM company WHERE vEmail = '".$email."' $ssql";
			$db_user = $obj->MySQLSelect($sql);
		}
		if($usertype == 'driver'){
			$sql = "SELECT vEmail,eStatus FROM register_driver WHERE vEmail = '".$email."' $ssql1";
			$db_user = $obj->MySQLSelect($sql);
		}
		if($usertype == 'rider'){
		    $sql4 = "SELECT vEmail,eStatus FROM register_user WHERE vEmail = '".$email."'".$ssql2; //exit;
			$db_user = $obj->MySQLSelect($sql4);
		}
			//echo "<pre>";print_r($db_comp);print_r($db_driver);
		if(count($db_user)>0)
		{
			if($db_user[0]['eStatus']=='Deleted')
				{
						echo 2;
				}
				else
				{
						echo 0;
				}
		}
		else 
		{
			echo 1;
		}
		
	}
?>