<?
include_once("common.php");
$iDriverId = isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:'';
$vCarType = isset($_REQUEST['selected'])?$_REQUEST['selected']:'';
$vCarTyp = explode(",", $vCarType);
if($iDriverId != '')
{
	$userSQL = "SELECT c.iCountryId from register_driver AS rd LEFT JOIN country AS c ON c.vCountryCode=rd.vCountry where rd.iDriverId='".$iDriverId."'";
	$drivers = $obj->MySQLSelect($userSQL);

	$iCountryId = $drivers[0]['iCountryId'];
	if($iCountryId != ''){
		$Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ;	
		if($Vehicle_type_name == "Ride-Delivery"){
			$vehicle_type_sql = "SELECT * from  vehicle_type where(eType ='Ride' or eType ='Deliver') AND iCountryId='".$iCountryId."'";
			$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);
		}else{
			if($Vehicle_type_name == 'UberX'){
				$vehicle_type_sql = "SELECT vt.*,vc.iVehicleCategoryId,vc.vCategory_EN from  vehicle_type as vt  left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId where vt.eType='".$Vehicle_type_name."' AND vt.iCountryId='".$iCountryId."'";
				$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);
			}else{
				$vehicle_type_sql = "SELECT * from  vehicle_type where eType='".$Vehicle_type_name."' AND iCountryId='".$iCountryId."'";		
				$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);
			}
		}
		
		foreach ($vehicle_type_data as $key => $value) { 
			if($Vehicle_type_name =='UberX'){
				$vname = $value['vCategory_EN'].'-'.$value['vVehicleType'];
			}else{
				$vname= $value['vVehicleType'];	
			}
			 ?>
			<div class="col-lg-2">										
				<?php echo $vname;?>
			</div>
			<div class="col-lg-2">
				<div class="make-switch make-swith001" data-on="success" data-off="warning">
					<input type="checkbox" class="chk" name="vCarType[]" <?php if(in_array($value['iVehicleTypeId'],$vCarTyp)){?>checked<?php } ?> value="<?=$value['iVehicleTypeId'] ?>"/>
				</div>
			</div>
<?php } } } ?>
<script>
	$(".make-swith001").bootstrapSwitch();
</script>