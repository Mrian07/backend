<?
include_once('common.php');
include_once('generalFunctions.php');

getVehicleCountryUnit_PricePerKm1("93","2");
function getVehicleCountryUnit_PricePerKm1($vehicleTypeID,$fPricePerKM){
    global $generalobj,$obj;
    
    $iCountryId = get_value("vehicle_type", "iCountryId", "iVehicleTypeId", $vehicleTypeID, '', 'true');
    if($iCountryId == "-1"){
       $eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
    }else{
       $eUnit = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');
    }
    
    if($eUnit == "" || $eUnit == NULL){
        $eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
    }
    
    if($eUnit == "Miles"){
       $PricePerKM = $fPricePerKM * 1.60934; 
    }else{
       $PricePerKM = $fPricePerKM;
    }
    echo $PricePerKM;exit;
    return  $PricePerKM;
    
}

//getMemberCountryUnit1("17");
function getMemberCountryUnit1($iMemberId,$UserType="Passenger"){
    global $generalobj,$obj;
                    
    if ($UserType == "Passenger") {
        $tblname = "register_user";
        $vCountryfield = "vCountry";
        $iUserId = "iUserId";
    } else {
        $tblname = "register_driver";
        $vCountryfield = "vCountry";
        $iUserId = "iDriverId";
    }        
    $vCountry = get_value($tblname, $vCountryfield, $iUserId, $iMemberId, '', 'true');    
               
    if($vCountry == "" || $vCountry == NULL){
        $vCountryCode = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
    }else{
        $vCountryCode = get_value("country", "eUnit", "vCountryCode", $vCountry, '', 'true');
    }
    echo $vCountryCode;exit;
    return $vCountryCode;
}
?>