<?php
	include_once('common.php');
	
	$tableName = isset($_REQUEST['tableName']) ? $_REQUEST['tableName'] : "";
	$tableId = isset($_REQUEST['tableId']) ? $_REQUEST['tableId'] : "";
	
	if($tableName != "" && $tableId != "") {	
		$sql = "SELECT ".$tableId.",vPassword FROM ".$tableName." WHERE CHAR_LENGTH(vPassword) < 50;";
		$test = $obj->MySQLSelect($sql);
		foreach($test as $tst){
			if($tst['vPassword'] != ""){
				$data = array();
				$where = " ".$tableId ."= '".$tst["$tableId"]."'";
				$password = $generalobj->decrypt($tst['vPassword']);
				$data['vPassword'] = $generalobj->encrypt_bycrypt($password);
				$id = $obj->MySQLQueryPerform($tableName,$data,'update',$where);
			}
		}
	}
?>