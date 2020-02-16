<?php
	include_once('../common.php');

	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();

	$id 		= isset($_REQUEST['restricted_id'])?$_REQUEST['restricted_id']:'';
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$action 	= ($id != '')?'Edit':'Add';

	$tbl_name 	= 'restricted_negative_area';
	$script 	= 'Restricted Area';

	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';

	// set all variables with either post (when submit) either blank (when insert)
	$vCountry = isset($_POST['vCountry'])?$_POST['vCountry']:'';
	$vState = isset($_POST['vState'])?$_POST['vState']:'';
	$vCity = isset($_POST['vCity'])?$_POST['vCity']:'';
	$vAddress = isset($_POST['vAddress'])?$_POST['vAddress']:'';
	$eRestrictType = isset($_POST['eRestrictType'])?$_POST['eRestrictType']:'All';
	$eType = isset($_POST['eType'])?$_POST['eType']:'Allowed';
	$eStatus_check = isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	$eStatus = ($eStatus_check == 'on')?'Active':'Inactive';
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';

	if(isset($_POST['submit'])) {
		if(SITE_TYPE=='Demo' && $id != '')
		{
				$_SESSION['success'] = 2;
				header("Location:restricted_area.php");
				exit;
		}

		$q = "INSERT INTO ";
		$where = '';

		if($id != '' ){
			$q = "UPDATE ";
			$where = " WHERE `iRestrictedNegativeId` = '".$id."'";
		}
		
		$query = $q ." `".$tbl_name."` SET
		`iCountryId` = '".$vCountry."',
		`iStateId` = '".$vState."',
		`iCityId` = '".$vCity."',
		`vAddress` = '".$vAddress."',
		`eRestrictType` = '".$eRestrictType."',
		`eType` = '".$eType."',
		`eStatus` = '".$eStatus."'"
		.$where;

		$obj->sql_query($query);
		$id = ($id != '')?$id:mysql_insert_id();
		if ($action == "Add") {
			$_SESSION['success'] = '1';
			$_SESSION['var_msg'] = 'Restricted Area Insert Successfully.';
		} else {
			$_SESSION['success'] = '1';
			$_SESSION['var_msg'] = 'Restricted Area Updated Successfully.';
		}
		header("Location:".$backlink);exit;
	}

	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iRestrictedNegativeId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);

		// echo "<pre>"; print_r($db_data); die;
		
		$vLabel = $id;
		if(count($db_data) > 0) {
			foreach($db_data as $key => $value) {
				$iCountryId	 = $value['iCountryId'];
				$iStateId	 = $value['iStateId'];
				$iCityId	 = $value['iCityId'];
				$vAddress	 = $value['vAddress'];
				$eRestrictType	 = $value['eRestrictType'];
				$eType	 = $value['eType'];
				$eStatus = $value['eStatus'];
			}
		}
	}
	
	$sql_country = "SELECT * FROM country";
	$db_data_country = $obj->MySQLSelect($sql_country);
	if($iCountryId != ''){
		$sql_state = "SELECT * FROM state where iCountryId='".$iCountryId."'";
		$db_data_state = $obj->MySQLSelect($sql_state);
	}
	if($iStateId != ''){
		$sql_city = "SELECT * FROM city where iStateId='".$iStateId."'";
		$db_data_city = $obj->MySQLSelect($sql_city);
	}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Restricted Area <?=$action;?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="css/bootstrap-select.css" rel="stylesheet" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<? include_once('global_files.php');?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
	</head>
	<!-- END  HEAD-->
	<!-- BEGIN BODY-->
	<body class="padTop53 " >

		<!-- MAIN WRAPPER -->
		<div id="wrap">
			<? include_once('header.php'); ?>
			<? include_once('left_menu.php'); ?>
			<!--PAGE CONTENT -->
			<div id="content">
				<div class="inner">
					<div class="row">
						<div class="col-lg-12">
							<h2><?=$action;?> Restricted Area</h2>
							<a class="back_link" href="restricted_area.php">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
					<hr />
					<div class="body-div">
						<div class="form-group">
							<? if($success == 1) { ?>
								<div class="alert alert-success alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									Record Updated successfully.
								</div><br/>
								<? }elseif ($success == 2) { ?>
									<div class="alert alert-danger alert-dismissable">
											 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
											 "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
									</div><br/>
								<? }?>
								<form id="_restricted_form" name="_restricted_form" method="post" action="">
								<input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="restricted_area.php"/>
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Country Name<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<select id="lunch" onChange="showState(this.value);" name="vCountry" class="selectpicker" data-live-search="true" required="required">
											<option selected="selected" value="">Select Country</option>
											<?php
											foreach($db_data_country as $country):?>
											<?php if($country['iCountryId']==$iCountryId):?>
											<option selected="selected" value="<?php echo $country['iCountryId'];?>"><?php echo $country['vCountry'];?></option>
											<?php else:?>
											<option value="<?php echo $country['iCountryId'];?>"><?php echo $country['vCountry'];?></option>
											<?php endif;?>
											<?php endforeach;?>
										</select>
									</div>
								</div>
								
								<div class="row">
									<div class="col-lg-12">
										<label>State Name<span class="red"> </span></label>
									</div>
									<div class="col-lg-6">
											<select  id="state" name="vState" onChange="showCity(this.value);" class="selectpicker" data-live-search="true" >
										
												
											<?php
											foreach($db_data_state as $state):?>
												<?php if($state['iStateId']==$iStateId):?>
											<option selected="selected" value="<?php echo $state['iStateId'];?>"><?php echo $state['vState'];?></option>
												<?php else:?>
												<option value="<?php echo $state['iStateId'];?>"><?php echo $state['vState'];?></option>
												<?php endif;?>
												<?php endforeach;?>
												
											</select>
											
										</div>
								</div>

								
								<div class="row">
									 <div class="col-lg-12">
										  <label><?php echo "City";?><span class="red"> </span></label>
									 </div>
									<div class="col-lg-6">
									<select id="city" name="vCity" class="selectpicker" data-live-search="true" >
									<?php
										foreach($db_data_city as $city):?>
										<?php if($city['iCityId'] == $iCityId):?>
										<option selected="selected" value="<?php echo $city['iCityId'];?>"><?php echo $city['vCity'];?></option>
										<?php else:?>
										<option value="<?php echo $city['iCityId'];?>"><?php echo $city['vCity'];?></option>
										<?php endif;?>
										<?php endforeach;?>
										</select>
									</div>
								</div>
								
								<div class="row">
									<div class="col-lg-12">
										<label>Area<span class="red"></span></label>
									</div>
									<div class="col-lg-4">
										<input type="text" class="form-control" name="vAddress"  id="vAddress" value="<?=$vAddress;?>" placeholder="Area address">
									</div>
								</div>
								
								<div class="row">
								 <div class="col-lg-12">
									  <label>Restrict area <span class="red"> *</span></label>
								 </div>
								 <div class="col-lg-3">
									  <select class="form-control" name = 'eRestrictType' id="eRestrictType" >
										   <option value="All" <?php if($eRestrictType == 'All') { ?> selected <?php } ?> >All</option>
										   <option value="Pick Up" <?php if($eRestrictType == 'Pick Up') { ?> selected <?php } ?>>Pick Up</option>
										   <option value="Drop Off" <?php if($eRestrictType == 'Drop Off') { ?> selected <?php } ?>>Drop Off</option>
									  </select>
								 </div>
								</div>
								
								<div class="row">
								 <div class="col-lg-12">
									  <label>Restrict Type <span class="red"> *</span></label>
								 </div>
								 <div class="col-lg-3">
									  <select class="form-control" name = 'eType' id="eType" >
										   <option value="Disallowed" <?php if($eType == 'Disallowed') { ?> selected <?php } ?>>Disallowed</option>
										   <option value="Allowed" <?php if($eType == 'Allowed') { ?> selected <?php } ?>>Allowed</option>
									  </select>
								 </div>
								</div>
								
								
								<div class="row">
									<div class="col-lg-12">
										<label>Status</label>
									</div>
									<div class="col-lg-6">
										<div class="make-switch" data-on="success" data-off="warning">
											<input type="checkbox" name="eStatus" <?=($id != '' && $eStatus == 'Inactive')?'':'checked';?>/>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?= $action; ?> Area" >
                                        <a href="javascript:void(0);" onclick="reset_form('_restricted_form');" class="btn btn-default">Reset</a>
                                        <a href="restricted_area.php" class="btn btn-default back_link">Cancel</a>
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

<? include_once('footer.php');?>

<script src="http://maps.google.com/maps/api/js?sensor=true&key=<?= $GOOGLE_SEVER_API_KEY_WEB ?>&libraries=places" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});

$(function () {
	var from = document.getElementById('vAddress');
	autocomplete_from = new google.maps.places.Autocomplete(from);
	google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
		setCityValues($("#vAddress").val());
		$("#vAddress").val('');
	});
});

function showState(id){
	$.ajax({
		type: "POST",
		url: "functions_area.php",
		data: "country_id_not_required="+id,
		success: function(data){
			if(data.success){
				document.write
			} else {			
				//alert(data);
				// $('#msg').html(data).fadeIn('slow');
				$('#state').html(data); //also show a success message 
				$('#state').selectpicker('refresh');
				CityId=$('#state option:selected').val();
				/*  var json_obj = $.parseJSON(data);//parse JSON
					alert(json_obj.json);
				 */	// var a=JSON.stringify(data);
				//alert(a);
				//showCity(CityId);
			}
		}
	});
}

function showCity(id){
	$.ajax({
		type: "POST",
		url: "functions_area.php",
		data: "state_id_not_required="+id,
		success: function(data){
			if(data.success){           
				document.write//alert(data.status);
			}else {
				$('#city').html(data); //also show a success message 
				$('#city').selectpicker('refresh');
			}
		}
	});
}

function setCityValues(address) {
	$.ajax({
		type: "POST",
		url: "set_city_values.php",
		data: "address="+address,
		success: function(dataHtml){
			$("#body_data").html(dataHtml);
			$("#myModal").modal('show');
		},
		error: function(dataHtml){
			
		}
	});
	
}

function getTheSelected() {
	var area = $('input[name=setArea]:checked').val();
	$("#vAddress").val(area);
	$("#myModal").modal('hide');
	// alert(area);
}

$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){
		referrer =  document.referrer;
	}else {
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "restricted_area.php";
	}else {
		$("#backlink").val(referrer);
	}
	$(".back_link").attr('href',referrer);
});

</script>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script src="js/bootstrap-select.js"></script>
</body>
<!-- END BODY-->
</html>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-large">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">x</span>
				</button>
				<h4 class="modal-title" id="myModalLabel"> Please select an area </h4>
			</div>
			<div class="modal-body" id="body_data">
				
			</div>
			<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button><a class="btn btn-success btn-ok " onClick="getTheSelected();" >Select</a></div>
		</div>
	</div>
</div>