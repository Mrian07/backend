<?php
include_once('common.php');

$keywords =isset($_REQUEST['keywords'])?$_REQUEST['keywords']:'';
$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
$ssql ="";
if($id !=""){
$ssql =	 "AND iHelpsId=".$id;
	
}

	$con='';
	
		$sql = "SELECT * FROM helps where vTitle LIKE '%".$keywords."%'".$ssql;
		$db_data = $obj->MySQLSelect($sql);
		//print_r($db_data);
		if(count($db_data) > 0){
			foreach($db_data as $val){ 
				$con .='<h3>'.$val['vTitle'].'</h3>
				<div class="custom-error-right-part-box">
					<p>'.$val['tDescription'].'</p>					
			 </div>'; 	
			}
		}else{	
		//echo "dfgdf"; exit;
		$con .='<h3> No Result Found.</h3>';
				
		}
		
		echo $con; 
	exit;
		
	
?>