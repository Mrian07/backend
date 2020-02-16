<?php
	include_once('common.php');
	if(isset($_REQUEST['vPhone']))
	{
			$vPhone=$_REQUEST['vPhone'];
			$sql = "SELECT vPhone FROM register_driver WHERE vPhone = '".$vPhone."' ";
			$db_comp = $obj->MySQLSelect($sql);
			
		if(count($db_comp)>0)
		{
				echo 'false';
		}
		else
		{	
				echo 'true';
		}
	}
?>