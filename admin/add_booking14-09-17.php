<?php
	include_once('../common.php');
	
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$script = "booking";
	
	$tbl_name = 'cab_booking';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : '';
	$var_msg = isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : '';
	$iCabBookingId = isset($_REQUEST['booking_id']) ? $_REQUEST['booking_id'] : '';
	$action = ($iCabBookingId != '') ? 'Edit' : 'Add';

	//For Country
	$sql = "SELECT vCountryCode,vCountry from country where eStatus = 'Active'";
	$db_code = $obj->MySQLSelect($sql);
	
	$sql="select cn.vCountryCode,cn.vPhoneCode from country cn inner join 
	configurations c on c.vValue=cn.vCountryCode where c.vName='DEFAULT_COUNTRY_CODE_WEB'";
	$db_con = $obj->MySQLSelect($sql);
	
	$vPhoneCode = $generalobjAdmin->clearPhone($db_con[0]['vPhoneCode']);
	$vCountry = $db_con[0]['vCountryCode'];
	
	$dBooking_date = "";
	if ($action == 'Edit') {
		$sql = "SELECT * FROM " . $tbl_name . " LEFT JOIN register_user on register_user.iUserId=" . $tbl_name . ".iUserId WHERE " . $tbl_name . ".iCabBookingId = '" . $iCabBookingId . "'";
		$db_data = $obj->MySQLSelect($sql);

		$vLabel = $id;
		if (count($db_data) > 0) {
			foreach ($db_data as $key => $value) {
				$iUserId = $value['iUserId'];
        $iDriverId = $value['iDriverId'];
				$vDistance = $value['vDistance'];
				$vDuration = $value['vDuration'];
				$dBooking_date = $value['dBooking_date'];
				$vSourceAddresss = $value['vSourceAddresss'];
				$tDestAddress = $value['tDestAddress'];
				$iVehicleTypeId = $value['iVehicleTypeId'];
				$vPhone = $value['vPhone'];
				$vName = $value['vName'];
				$vLastName =  $generalobjAdmin->clearName(" ".$value['vLastName']);
				$vEmail = $generalobjAdmin->clearEmail($value['vEmail']);
				$vPhoneCode = $generalobjAdmin->clearPhone($value['vPhoneCode']);
				$vCountry = $value['vCountry'];
				$eStatus = $value['eStatus'];
				$tPackageDetails = $value['tPackageDetails'];
				$tDeliveryIns = $value['tDeliveryIns'];
				$tPickUpIns = $value['tPickUpIns'];
				$from_lat_long = '('.$value['vSourceLatitude'].', '.$value['vSourceLongitude'].')';
				$from_lat = $value['vSourceLatitude'];
				$from_long = $value['vSourceLongitude'];
				$to_lat_long = '('.$value['vDestLatitude'].', '.$value['vDestLongitude'].')';
				$to_lat = $value['vDestLatitude'];
				$to_long = $value['vDestLongitude'];
				$eAutoAssign = $value['eAutoAssign'];
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME;?> | Manual<?php echo $langage_lbl_admin['LBL_TEXI_ADMIN']; ?>Dispatch</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link rel="stylesheet" href="css/select2/select2.min.css" type="text/css" >
        <? include_once('global_files.php');?>
        <script src="http://maps.google.com/maps/api/js?sensor=true&key=<?= $GOOGLE_SEVER_API_KEY_WEB ?>&libraries=places" type="text/javascript"></script>
        <script type='text/javascript' src='../assets/map/gmaps.js'></script>
        <script type='text/javascript' src='../assets/js/jquery-ui.min.js'></script>
	</head>
    <body class="padTop53 " >
        <div id="wrap">
            <? include_once('header.php'); ?>
            <? include_once('left_menu.php'); ?>
            <div id="content">
                <div class="inner" style="min-height: 700px;">
                    <div class="row">
                        <div class="col-lg-8">
                            <h1> Manual <?php echo $langage_lbl_admin['LBL_TEXI_ADMIN']; ?> Dispatch </h1>
						</div>
						<div class="col-lg-4">
						<? if($APP_TYPE != "UberX"){ ?>
							<h1 class="float-right"><a class="btn btn-primary how_it_work_btn" data-toggle="modal" data-target="#myModal"><i class="fa fa-question-circle" style="font-size: 18px;"></i> How it works?</a></h1>
						<? } ?>
						</div>
					</div>
                    <hr />
					<form name="add_booking_form" id="add_booking_form" method="post" action="action_booking.php" >
                    <div class="form-group">
                        <?php if ($success == "1") {?>
							<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
								<?php
									echo ($vassign != "1")?'Booking Has Been Added Successfully.':$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Has Been Assigned Successfully.';
								?>
							</div>
							<br/>
						<?php } ?>
                        <?php if ($success == 2) 
							{
							?>
							<div class="alert alert-danger alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
								"Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you. </div>
							<br/>
						<?php } ?>
                        <?php if ($success == 0 && $var_msg != "") 
							{
							?>
							<div class="alert alert-danger alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
								<?= $var_msg; ?>
							</div>
							<br/>
						<?php } ?>
							<input type="hidden" name="previousLink" id="previousLink" value=""/>
							<input type="hidden" name="backlink" id="backlink" value="cab_booking.php"/>
                            <input type="hidden" name="distance" id="distance" value="<?= $vDistance; ?>">
                            <input type="hidden" name="duration" id="duration" value="<?= $vDuration; ?>">
                            <input type="hidden" name="from_lat_long" id="from_lat_long" value="<?= $from_lat_long; ?>" >
                            <input type="hidden" name="from_lat" id="from_lat" value="<?= $from_lat; ?>" >
                            <input type="hidden" name="from_long" id="from_long" value="<?= $from_long; ?>" >
                            <input type="hidden" name="to_lat_long" id="to_lat_long" value="<?= $to_lat_long; ?>" >
                            <input type="hidden" name="to_lat" id="to_lat" value="<?= $to_lat; ?>" >
                            <input type="hidden" name="to_long" id="to_long" value="<?= $to_long; ?>" >
                            <input type="hidden" value="1" id="location_found" name="location_found">
                            <input type="hidden" value="" id="user_type" name="user_type" >
                            <input type="hidden" value="<?= $iUserId; ?>" id="iUserId" name="iUserId" >
                            <input type="hidden" value="<?= $eStatus; ?>" id="eStatus" name="eStatus" >
                            <input type="hidden" value="<?= $iCabBookingId; ?>" id="iCabBookingId" name="iCabBookingId" >
                            <input type="hidden" value="<?= $GOOGLE_SEVER_API_KEY_WEB; ?>" id="google_server_key" name="google_server_key" >
                            <input type="hidden" value="" id="getradius" name="getradius" >
                            <input type="hidden" value="KMs" id="eUnit" name="eUnit" >
                            <div class="add-booking-form-taxi add-booking-form-taxi1 col-lg-12"> <span class="col0">
								<select name="vCountry" id="vCountry" class="form-control form-control-select" onChange="changeCode(this.value);" required>
									<option value="">Select Country</option>
									<? for($i=0;$i<count($db_code);$i++) { ?>
                                        <option value="<?= $db_code[$i]['vCountryCode'] ?>" 
										<?php if ($db_code[$i]['vCountryCode'] == $vCountry) { echo "selected"; } ?> >
											<?= $db_code[$i]['vCountry']; ?>
										</option>
									<? } ?>
								</select>
                                </span> <span class="col6">
								<input type="text" class="form-control add-book-input" name="vPhoneCode" id="vPhoneCode" value="<?= $vPhoneCode; ?>" readonly>
                                </span><span class="col2">
								<input type="text" pattern="[0-9]{1,}" title="Enter Mobile Number." class="form-control add-book-input" name="vPhone"  id="vPhone" value="<?= $vPhone; ?>" placeholder="Enter Phone Number" onKeyUp="return isNumberKey(event)"  required style="">
                                </span> <span class="col3">
								<input type="text" class="form-control first-name1" name="vName"  id="vName" value="<?= $vName; ?>" placeholder="First Name" required>
								<input type="text" class="form-control last-name1" name="vLastName"  id="vLastName" value="<?= $vLastName; ?>" placeholder="Last Name" required>
                                </span> <span class="col4">
								<input type="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$" class="form-control" name="vEmail" id="vEmail" value="<?= $vEmail; ?>" placeholder="Email" required >
								<div id="emailCheck"></div>
							</span>
                            </div>
					</div>
                    <div class="map-main-page-inner">
                        <div class="map-main-page-inner-tab">
                            <div class="col-lg-12 map-live-hs-mid">
								<span class="col5">
									<? if($APP_TYPE != "UberX"){ ?>
										<input type="text" class="ride-location1 highalert txt_active form-control first-name1" name="vSourceAddresss"  id="from" value="<?= $vSourceAddresss; ?>" placeholder="<?= ucfirst(strtolower($langage_lbl_admin['LBL_PICKUP_LOCATION_HEADER_TXT'])); ?>" required>
									<? } ?>
									<input type="text" class="ride-location1 highalert txt_active form-control last-name1" name="tDestAddress"  id="to" value="<?= $tDestAddress; ?>" placeholder="Drop Off Location" required>
								</span>
                                <span>
                                    <input type="text" class="form-control form-control14" name="dBooking_date"  id="datetimepicker4" value="<?= $dBooking_date; ?>" placeholder="Select Date / Time" required >
									</span><span>
                                    <select class="form-control form-control-select" name='iVehicleTypeId' id="iVehicleTypeId" required onChange="showAsVehicleType(this.value)">
                                        <option value="" >Select <?php echo $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']; ?></option>
                                        <?php
											$sql1 = "SELECT iVehicleTypeId, vVehicleType FROM `vehicle_type` WHERE 1";
											$db_carType = $obj->MySQLSelect($sql1);
											foreach ($db_carType as $db_car) {
											?>
                                            <option value="<?php echo $db_car['iVehicleTypeId']; ?>" <?php if ($iVehicleTypeId == $db_car['iVehicleTypeId']) {
												echo "selected";
											} ?> ><?php echo $db_car['vVehicleType']; ?></option>
										<?php } ?>
									</select>
                                    <?php $radius_driver = array(5, 10, 20, 30); ?>
                                    <select class="form-control form-control-select" name='radius-id' id="radius-id" onChange="play(this.value)">
										<option value=""> Select Radius </option>
										<?php foreach ($radius_driver as $value) { ?>
                                            <option value="<?php echo $value ?>"><?php echo $value . ' km Radius'; ?></option>
										<?php } ?>
									</select>
									</span>
									<span class="auto_assign001">
                                    <input type="checkbox" name="eAutoAssign" id="eAutoAssign" value="Yes" <?php if ($eAutoAssign == 'Yes') echo 'checked'; ?>>
                                    <p>Auto Assign
										<?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>
									</p>
									</span>
									<span class="auto_assignOr">
										<h3>OR</h3>
									</span>
									<span id="showdriverSet001" style="display:none;"><p class="margin-right5">Assigned Driver: </p><p id="driverSet001"></p></span>
							</div>
                            <span>
                                <input name="" type="text" placeholder="Search driver" id="name_keyWord" onKeyUp="get_drivers_list(this.value)">
							</span>
                            <ul id="driver_main_list" style="display:none">
							</ul>
							<input type="text" name="iDriverId" id="iDriverId" value="" required oninvalid="this.setCustomValidity('Please select a Driver from list.')"; class="form-control height-1" >
						</div>
                        <div class="map-page">
                            <div class="panel-heading location-map" style="background:none;">
                                <div class="google-map-wrap">
                                    <div class="map-color-code">
									<label>Select Driver Availability: </label>
									<span class="select-map-availability"><select onChange="setNewDriverLocations(this.value)" id="newSelect02">
										<option value='' data-id=""><?php echo $langage_lbl['LBL_ALL']; ?></option>
										<option value="Available" data-id="img/green-icon.png"><?= $langage_lbl['LBL_AVAILABLE']; ?></option>
										<option value="Active" data-id="img/red.png"><?php echo $langage_lbl['LBL_ENROUTE_TO']; ?></option>
										<option value="Arrived" data-id="img/blue.png"><?php echo $langage_lbl['LBL_REACHED_PICKUP']; ?></option>
										<option value="On Going Trip" data-id="img/yellow.png"><?php echo $langage_lbl['LBL_JOURNEY_STARTED']; ?></option>
										<option value="Not Available" data-id="img/offline-icon.png"><?= $langage_lbl['LBL_OFFLINE']; ?></option>
									</select></span>
									<?php /*<ul>
										<li class="color1"> 
											<a href="javascript:void(0);" onClick="setNewDriverLocations('Active', 'set1')"><img src="../assets/img/red.png">
												<h2>
													<button type="button" class="btn btn-default setclass" id="set1"><?php echo $langage_lbl['LBL_ENROUTE_TO']; ?></button>
												</h2>
											</a> 
										</li>
										<li class="color2"> 
											<a  href="javascript:void(0);" onClick="setNewDriverLocations('Arrived', 'set2')"><img src="../assets/img/blue.png">
												<h2>
													<button type="button" class="btn btn-default setclass" id="set2"><?php echo $langage_lbl['LBL_REACHED_PICKUP']; ?></button>
												</h2>
											</a>
										</li>
										<li class="color3"> 
											<a href="javascript:void(0);" onClick="setNewDriverLocations('On Going Trip', 'set3')"><img src="../assets/img/yellow.png">
												<h2>
													<button type="button" class="btn btn-default setclass" id="set3"><?php echo $langage_lbl['LBL_JOURNEY_STARTED']; ?></button>
												</h2>
											</a>
										</li>
										<li class="color4"> 
											<a href="javascript:void(0);" onClick="setNewDriverLocations('Available', 'set4')"><img src="../assets/img/green.png">
												<h2>
													<button type="button" class="btn btn-default setclass"  id="set4">
														<?= $langage_lbl['LBL_AVAILABLE']; ?>
													</button>
												</h2>
											</a>
										</li>
										<li class="color5">  <a href="javascript:void(0);" onClick="setNewDriverLocations('', 'set5')">
											<h2>
												<button type="button" class="btn raised setclass active" id="set5"><?php echo $langage_lbl['LBL_ALL']; ?></button>
											</h2>
										</a> 
										</li>
									</ul> */ ?>
									</div>
                                    <div id="map-canvas" class="google-map" style="width:100%; height:500px;"></div>
								</div>
							</div>
						</div>
                        <!-- popup -->
                        <div class="map-popup" style="display:none" id="driver_popup"></div>
                        <!-- popup end -->
					</div>
                    <input type="hidden" name="newType" id="newType" value="">
                    <div style="clear:both;"></div>
                     <div class="book-now-reset"><span>
						<input type="submit" class="save btn-info button-submit" name="submit" id="submit" value="Book Now" >
						<input type="reset" class="save btn-info button-submit" name="reset" id="reset12" value="Reset" >
					</span></div>
					</form>
                    <? if($APP_TYPE != 'UberX'){ ?>
						<div class="total-price total-price1"> <b>Fare Estimation</b>
							<hr>
							<ul>
								<li><b>Minimum Fare</b> :
									<?php echo $generalobj->symbol_currency(); ?>
								<em id="minimum_fare_price">0</em></li>
								<li><b>Base Fare</b> :
									<?php echo $generalobj->symbol_currency(); ?>
								<em id="base_fare_price">0</em></li>
								<li><b>Distance (<em id="dist_fare">0</em> <em id="change_eUnit">KMs</em>)</b> :
									<?php echo $generalobj->symbol_currency(); ?>
								<em id="dist_fare_price">0</em></li>
								<li><b>Time (<em id="time_fare">0</em> Minutes)</b> :
									<?php echo $generalobj->symbol_currency(); ?>
								<em id="time_fare_price">0</em></li>
							</ul>
							<span>Total Fare<b>
								<?php echo $generalobj->symbol_currency(); ?>
							<em id="total_fare_price">0</em></b></span> </div>
					<? } ?>
                   
				</div>
                <!--END PAGE CONTENT -->
			</div>
            <? include_once('footer.php'); ?>
            <div style="clear:both;"></div>
			<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
			<script type="text/javascript" src="js/moment.min.js"></script>
			<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
			<script type="text/javascript" src="js/plugins/select2.min.js"></script>
			<script>
			
				function formatData(state) {
					if (!state.id) { return state.text; }
						// alert(state.attr('data-id'));
					  // var img = state.attr('data-id');
						var optimage = $(state.element).data('id'); 
						if(!optimage){
							return state.text;
						} else {
							var $state = $(
							'<span class="userName"><img src="' + optimage + '" class="mpLocPic" /> ' + $(state.element).text() + '</span>'
							);
							return $state;
						}
				}
			
				$("#newSelect02").select2({
					templateResult: formatData,
					templateSelection: formatData
				});
			
				var eTypeQ11 = 'yes';
				var map;
				var geocoder;
				var circle;
				var markers = [];
				var driverMarkers = [];
				var bounds = [];
				var newLocations;
				var autocomplete_from;
				var autocomplete_to;
				var geocoder = new google.maps.Geocoder();
				var directionsService = new google.maps.DirectionsService(); // For Route Services on map
				var directionsOptions = {  // For Polyline Route line options on map
					polylineOptions: {
						strokeColor: '#FF7E00',
						strokeWeight: 5
					}
				};
				var directionsDisplay = new google.maps.DirectionsRenderer(directionsOptions);
				
				function setDriverListing(iVehicleTypeId) {
					keyword = $("#name_keyWord").val();
					$.ajax({
						type: "POST",
						url: "get_available_driver_list.php",
						dataType: "html",
						data: {type: '',iVehicleTypeId: iVehicleTypeId,keyword: keyword},
						success: function (dataHtml2) {
							$('#driver_main_list').show();
							$('#driver_main_list').html(dataHtml2);
							if($("#eAutoAssign").is(':checked')){
								$(".assign-driverbtn").attr('disabled','disabled');
							}
						}, error: function (dataHtml2) {

						}
					});
				}
				
				function AssignDriver(driverId){
					$('#iDriverId').val(driverId);
					$("#showdriverSet001").show();
					$("#driverSet001").html($('.driver_'+driverId).html());
				}
				
				function setDriversMarkers(flag) {
					newType = $("#newType").val();
					// alert(newType);
					vType = $("#iVehicleTypeId").val();
                    $.ajax({
                        type: "POST",
                        url: "get_map_drivers_list.php",
                        dataType: "json",
                        data: {type: newType,iVehicleTypeId: vType},
                        success: function (dataHtml) {
                            for (var i = 0; i < driverMarkers.length; i++) {
                                driverMarkers[i].setMap(null);
                            }
                            newLocations = dataHtml.locations;
							var infowindow = new google.maps.InfoWindow();
                            for (var i = 0; i < newLocations.length; i++) {
                                if (newType == newLocations[i].location_type || newType == "") {
									var str33 = newLocations[i].location_carType;
									if(vType == "" || (str33 != null && str33.indexOf(vType) != -1)){
                                    newName = newLocations[i].location_name;
                                    newOnlineSt = newLocations[i].location_online_status;
                                    newLat = newLocations[i].google_map.lat;
                                    newLong = newLocations[i].google_map.lng;
                                    newDriverImg = newLocations[i].location_image;
                                    newMobile = newLocations[i].location_mobile;
                                    newDriverID = newLocations[i].location_ID;
									newImg = newLocations[i].location_icon;
									driverId = newLocations[i].location_driverId;
                                    latlng = new google.maps.LatLng(newLat, newLong);
                                    // bounds.push(latlng);
									// alert(newImg);
									content = '<table><tr><td rowspan="4"><img src="' + newDriverImg + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + newDriverID + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + newMobile + '</b></td></tr></table>';
									//+'<br> '+'<a href="javascript:void(0)" onClick="AssignDriver('+driverId+');">Assign Driver</a>'
                                    var drivermarker = new google.maps.Marker({
										map: map,
										//animation: google.maps.Animation.DROP,
										position: latlng,
                                        icon: newImg
                                    });
									google.maps.event.addListener(drivermarker,'click', (function(drivermarker,content,infowindow){ 
										return function() {
											infowindow.setContent(content);
											infowindow.open(map,drivermarker);
										};
									})(drivermarker,content,infowindow));
									// alert(content);
                                    driverMarkers.push(drivermarker);
                                }
								}
                            }
							//var markers = [];//some array
							if(flag != 'test') {
								var bounds = new google.maps.LatLngBounds();
								for (var i = 0; i < driverMarkers.length; i++) {
									bounds.extend(driverMarkers[i].getPosition());
								}
								map.fitBounds(bounds);
							}
                            setDriverListing(vType);
                        },
                        error: function (dataHtml) {

                        }
                    });
				}
				
				function initialize() {
					var thePoint = new google.maps.LatLng('20.1849963', '64.4125062');
					var mapOptions = {
						zoom: 4,
						center: thePoint
					};
					map = new google.maps.Map(document.getElementById('map-canvas'),
					mapOptions);
					
					circle = new google.maps.Circle({radius: 25, center: thePoint}); 
					// map.fitBounds(circle.getBounds());
					<?php if($action == "Edit") { ?>
						// callEditFundtion();
						routeDirections();
					<?php } ?>
						setDriversMarkers();
				}
				
				$(document).ready(function () {
					google.maps.event.addDomListener(window, 'load', initialize);
				});
				
				function play(radius){
					// return Math.round(14-Math.log(radius)/Math.LN2);
					var pt = new google.maps.LatLng($("#from_lat").val(),$("#from_long").val());
					map.setCenter(pt);
					var newRadius = Math.round(24-Math.log(radius)/Math.LN2);
					// alert(newRadius);
					// alert("alert: "+radius);
					newRadius = newRadius-9;
					map.setZoom(newRadius);
				}
								
				// function play(radius){
					// circle.setRadius(radius * 10);
					// circle.setCenter(map.getCenter());
					// map.fitBounds(circle.getBounds());
					// map.circleRadius = radius;
					// var newRadius = Math.round(14-Math.log(radius)/Math.LN2);
				// }
				
				function DeleteMarkers(newId) {
					//Loop through all the markers and remove
					for (var i = 0; i < markers.length; i++) {
						if(newId != '') {
							if(markers[i].id == newId) {
								markers[i].setMap(null);
							}
							}else {
							markers[i].setMap(null);
						}
					}
					if(newId == '') { markers = []; }
				};
				
				function routeDirections() {
					directionsDisplay.setMap(null); // Remove Previous Route.
					
					if($("#from").val() != "" && $("#from_lat_long").val() != ""){
						DeleteMarkers('from');
						var postitions = new google.maps.LatLng($("#from_lat").val(),$("#from_long").val());
						var newIcon = '../webimages/upload/mapmarker/PinFrom.png';
						var marker = new google.maps.Marker({
							map: map,
							animation: google.maps.Animation.DROP,
							position: postitions,
							icon: newIcon
						});
						marker.id = 'from';
						markers.push(marker);
						map.setCenter(postitions);
						map.setZoom(25);
						// showAvailableCar();
					}
					
					if($("#to").val() != "" && $("#to_lat_long").val() != ""){
						DeleteMarkers('to');
						var postitions = new google.maps.LatLng($("#to_lat").val(),$("#to_long").val());
						var newIcon = '../webimages/upload/mapmarker/PinTo.png';
						var marker = new google.maps.Marker({
							map: map,
							animation: google.maps.Animation.DROP,
							position: postitions,
							icon: newIcon
						});
						marker.id = 'to';
						markers.push(marker);
						map.setCenter(postitions);
						map.setZoom(25);
					}

					if(($("#from").val() != "" && $("#from_lat_long").val() != "") && ($("#to").val() != "" && $("#to_lat_long").val() != "")) {

						var newFrom = $("#from_lat").val()+", "+$("#from_long").val();
						var newTo = $("#to_lat").val()+", "+$("#to_long").val();

						//Make an object for setting route
						var request = {
							origin: newFrom, // From locations latlongs
							destination: newTo, // To locations latlongs
							travelMode: google.maps.TravelMode.DRIVING // Set the Path of Driving
						};

						//Draw route from the object
						directionsService.route(request, function(response, status){
							if(status == google.maps.DirectionsStatus.OK){
								console.log(response);
								directionsDisplay.setMap(map);
								directionsDisplay.setOptions( { suppressMarkers: true } ); //, preserveViewport: true, suppressMarkers: false for setting auto markers from google api
								directionsDisplay.setDirections(response); // Set route
								var route = response.routes[0];
								for (var i = 0; i < route.legs.length; i++) {
									$("#distance").val(route.legs[i].distance.value);
									$("#duration").val(route.legs[i].duration.value);
								}

								var dist_fare = parseInt($("#distance").val(), 10) / parseInt(1000, 10);
								alert(dist_fare);
								if($("#eUnit").val() != 'KMs') {
									dist_fare = dist_fare * 0.621371;
								}
								alert(dist_fare);
								$('#dist_fare').text(Math.round(dist_fare));
								var time_fare = parseInt($("#duration").val(), 10) / parseInt(60, 10);
								$('#time_fare').text(Math.round(time_fare));
								var vehicleId = $('#iVehicleTypeId').val();
								$.ajax({
									type: "POST",
									url: 'ajax_find_rider_by_number.php',
									data: 'vehicleId=' + vehicleId,
									success: function (dataHtml)
									{
										if (dataHtml != "") {
											var result = dataHtml.split(':');
											$('#minimum_fare_price').text(parseFloat(result[3]).toFixed(2));
											$('#base_fare_price').text(parseFloat(result[0]).toFixed(2));
											$('#dist_fare_price').text(parseFloat(result[1]*$('#dist_fare').text()).toFixed(2));
											$('#time_fare_price').text(parseFloat(result[2]*$('#time_fare').text()).toFixed(2));
											var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
											if(parseInt(totalPrice) >= parseInt($('#minimum_fare_price').text())) {
												$('#total_fare_price').text(totalPrice);
												}else {
												$('#total_fare_price').text($('#minimum_fare_price').text());
											}
										}else {
											$('#minimum_fare_price').text('0');
											$('#base_fare_price').text('0');
											$('#dist_fare_price').text('0');
											$('#time_fare_price').text('0');
											$('#total_fare_price').text('0');
										}
									}
								});
							} else { alert("Directions request failed: " + status); }
						});
					}
				}
				
				$(function () {
					newDate = new Date('Y-M-D');
					$('#datetimepicker4').datetimepicker({
						format: 'YYYY-MM-DD HH:mm:ss',
						minDate: moment().add(1, 'hour'),
						ignoreReadonly: true,
						sideBySide: true,
					});
					
					var from = document.getElementById('from');
					autocomplete_from = new google.maps.places.Autocomplete(from);
					google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
						var place = autocomplete_from.getPlace();
						$("#from_lat_long").val(place.geometry.location);
						$("#from_lat").val(place.geometry.location.lat());
						$("#from_long").val(place.geometry.location.lng());
						routeDirections();
					});
					
					var to = document.getElementById('to');
					autocomplete_to = new google.maps.places.Autocomplete(to);
					google.maps.event.addListener(autocomplete_to, 'place_changed', function() {
						var place = autocomplete_to.getPlace();
						$("#to_lat_long").val(place.geometry.location);
						$("#to_lat").val(place.geometry.location.lat());
						$("#to_long").val(place.geometry.location.lng());
						routeDirections();
					});
				});
				
				
				function isNumberKey(evt){
					showPhoneDetail();
					var charCode = (evt.which) ? evt.which : evt.keyCode
					if (charCode > 31 && (charCode < 35 || charCode > 57))
						return false;
					return true;
				}
				
				function changeCode(id) {
					$.ajax({
						type: "POST",
						url: 'change_code.php',
						dataType: 'json',
						data: {id: id,eUnit: 'yes'},
						success: function (dataHTML)
						{
							document.getElementById("vPhoneCode").value = dataHTML.vPhoneCode;
							document.getElementById("eUnit").value = dataHTML.eUnit;
							$("#change_eUnit").text(dataHTML.eUnit);
							showPhoneDetail();
						}
					});
				}
				
				function showPopupDriver(driverId) {
                    if ($("#driver_popup").is(":visible") && $('#driver_popup ul').attr('class') == driverId) {
                        $("#driver_popup").hide("slide", {direction: "right"}, 700);
                    } else {
                        //alert(driverId);
                        $("#driver_popup").hide();
                        $.ajax({
                            type: "POST",
                            url: "get_driver_detail_popup.php",
                            dataType: "html",
                            data: {driverId: driverId},
                            success: function (dataHtml2) {
                                $('#driver_popup').html(dataHtml2);
                                $("#driver_popup").show("slide", {direction: "right"}, 700);
                            }, error: function (dataHtml2) {

                            }
                        });
                    }
                }


                $(document).mouseup(function (e)
                {
                    var container = $("#driver_popup");
                    var container1 = $("#driver_main_list");

                    if (!container.is(e.target) && !container1.is(e.target) // if the target of the click isn't the container...
                            && container.has(e.target).length === 0 && container1.has(e.target).length === 0) // ... nor a descendant of the container
                    {
                        container.hide("slide", {direction: "right"}, 700);
                    }
                });
				
				function showPhoneDetail() {
					var phone = $('#vPhone').val();
					var phoneCode = $('#vPhoneCode').val();
					if(phone != "" && phoneCode != ""){
						$.ajax({
							type: "POST",
							url: 'ajax_find_rider_by_number.php',
							data: {phone: phone,phoneCode: phoneCode},
							success: function (dataHtml)
							{
								if (dataHtml != "") {
									$("#user_type").val('registered');
									var result = dataHtml.split(':');
									$('#vName').val(result[0]);
									$('#vLastName').val(result[1]);
									$('#vEmail').val(result[2]);
									$('#iUserId').val(result[3]);
									$('#eStatus').val(result[4]);
								}else {
									$("#user_type").val('');
									$('#vName').val('');
									$('#vLastName').val('');
									$('#vEmail').val('');
									$('#iUserId').val('');
									$('#eStatus').val('');
								}
							}
						
						});
					}else {
							$("#user_type").val('');
							$('#vName').val('');
							$('#vLastName').val('');
							$('#vEmail').val('');
							$('#iUserId').val('');
							$('#eStatus').val('');
					}
				}
				
				function setNewDriverLocations(type) {
					// alert(type);
					$("#newType").val(type);
					vType = $("#iVehicleTypeId").val();
					for (var i = 0; i < driverMarkers.length; i++) {
					  driverMarkers[i].setMap(null);
					}
					//console.log(newLocations);
					//return false;
					var infowindow = new google.maps.InfoWindow();
					for (var i = 0; i < newLocations.length; i++) {
						if (type == newLocations[i].location_type || type == "") {
							var str33 = newLocations[i].location_carType;
							if(vType == "" || (str33 != null && str33.indexOf(vType) != -1)){
								newName = newLocations[i].location_name;
								newOnlineSt = newLocations[i].location_online_status;
								newLat = newLocations[i].google_map.lat;
								newLong = newLocations[i].google_map.lng;
								newDriverImg = newLocations[i].location_image;
								newMobile = newLocations[i].location_mobile;
								newDriverID = newLocations[i].location_ID;
								newImg = newLocations[i].location_icon;
								latlng = new google.maps.LatLng(newLat, newLong);
								// bounds.push(latlng);
								// alert(newImg);
								content = '<table><tr><td rowspan="4"><img src="' + newDriverImg + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + newDriverID + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + newMobile + '</b></td></tr></table>';
								var drivermarker = new google.maps.Marker({
									map: map,
									//animation: google.maps.Animation.DROP,
									position: latlng,
									icon: newImg
								});
								google.maps.event.addListener(drivermarker,'click', (function(drivermarker,content,infowindow){ 
									return function() {
										infowindow.setContent(content);
										infowindow.open(map,drivermarker);
									};
								})(drivermarker,content,infowindow));
								// alert(content);
								driverMarkers.push(drivermarker);
							}
						}
					}
					//var markers = [];//some array
					// var bounds = new google.maps.LatLngBounds();
					// for (var i = 0; i < driverMarkers.length; i++) {
						// bounds.extend(driverMarkers[i].getPosition());
					// }

					// map.fitBounds(bounds);
					setDriverListing(vType);
				}
				
				function getFarevalues(vehicleId) {
					$.ajax({
						type: "POST",
						url: 'ajax_find_rider_by_number.php',
						data: 'vehicleId=' + vehicleId,
						success: function (dataHtml)
						{
							console.log(dataHtml);
							if (dataHtml != "") {
								var result = dataHtml.split(':');
								$('#minimum_fare_price').text(parseFloat(result[3]).toFixed(2));
								$('#base_fare_price').text(parseFloat(result[0]).toFixed(2));
								$('#dist_fare_price').text(parseFloat(result[1]*$('#dist_fare').text()).toFixed(2));
								$('#time_fare_price').text(parseFloat(result[2]*$('#time_fare').text()).toFixed(2));
								var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
								if(parseInt(totalPrice) >= parseInt($('#minimum_fare_price').text())) {
									$('#total_fare_price').text(totalPrice);
								}else {
									$('#total_fare_price').text($('#minimum_fare_price').text());
								}
							}else {
								$('#minimum_fare_price').text('0');
								$('#base_fare_price').text('0');
								$('#dist_fare_price').text('0');
								$('#time_fare_price').text('0');
								$('#total_fare_price').text('0');
							}
						}
					});
					// setDriverListing(vehicleId);
					// getDriversList(vehicleId);
				}
				
				function showAsVehicleType(vType) {
					var type = $("#newType").val();
					for (var i = 0; i < driverMarkers.length; i++) {
					  driverMarkers[i].setMap(null);
					}
					//console.log(newLocations);
					//return false;
					var infowindow = new google.maps.InfoWindow();
					for (var i = 0; i < newLocations.length; i++) {
						if (type == newLocations[i].location_type || type == "") {
							var str33 = newLocations[i].location_carType;
							if(vType == "" || (str33 != null && str33.indexOf(vType) != -1)){
							newName = newLocations[i].location_name;
							newOnlineSt = newLocations[i].location_online_status;
							newLat = newLocations[i].google_map.lat;
							newLong = newLocations[i].google_map.lng;
							newDriverImg = newLocations[i].location_image;
							newMobile = newLocations[i].location_mobile;
							newDriverID = newLocations[i].location_ID;
							newImg = newLocations[i].location_icon;
							latlng = new google.maps.LatLng(newLat, newLong);
							// bounds.push(latlng);
							// alert(newImg);
							content = '<table><tr><td rowspan="4"><img src="' + newDriverImg + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + newDriverID + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + newMobile + '</b></td></tr></table>';
							var drivermarker = new google.maps.Marker({
								map: map,
								//animation: google.maps.Animation.DROP,
								position: latlng,
								icon: newImg
							});
							google.maps.event.addListener(drivermarker,'click', (function(drivermarker,content,infowindow){ 
								return function() {
									infowindow.setContent(content);
									infowindow.open(map,drivermarker);
								};
							})(drivermarker,content,infowindow));
							// alert(content);
							driverMarkers.push(drivermarker);
						}
					}
					}
					//var markers = [];//some array
					// var bounds = new google.maps.LatLngBounds();
					// for (var i = 0; i < driverMarkers.length; i++) {
						// bounds.extend(driverMarkers[i].getPosition());
					// }

					// map.fitBounds(bounds);
					setDriverListing(vType);
					getFarevalues(vType);
				}
				
				setInterval(function() {
					if(eTypeQ11 == 'yes') {
						setDriversMarkers('test');
					}
				},10000);
				
				
				function setFormBook(){
					var statusVal = $('#vEmail').val();
					if(statusVal != ''){
						$.ajax({
							type: "POST",
							url: 'ajax_checkBooking_email.php',
							data: 'vEmail=' + statusVal,
							success: function (dataHtml)
							{
								var testEstatus = dataHtml.trim();
								if(testEstatus != 'Active' && testEstatus != '') {
									if(confirm("The selected user account is in 'Inactive / Deleted' mode. Do you want to Active this User ?'")){
										eTypeQ11 = 'no';
										$("#add_booking_form").attr('action','action_booking.php');
										$( "#submit" ).trigger( "click" );
										// e.stopPropagation();
										// e.preventDefault();
										return false;
									}else {
										$("#vEmail").focus();
										return false;
									}
								}else {
									eTypeQ11 = 'no';
									$("#add_booking_form").attr('action','action_booking.php');
									$( "#submit" ).trigger( "click" );
									// e.stopPropagation();
									// e.preventDefault();
									return false;
								}
							}
						});	
					}else {
						return false;
					}
				}
				
				function get_drivers_list(keyword) {
					vType = $("#iVehicleTypeId").val();
					$.ajax({
						type: "POST",
						url: "get_available_driver_list.php",
						dataType: "html",
						data: {keyword: keyword,iVehicleTypeId:vType},
						success: function(dataHtml2){
							$('#driver_main_list').show();
							$('#driver_main_list').html(dataHtml2);
							if($("#eAutoAssign").is(':checked')){
								$(".assign-driverbtn").attr('disabled','disabled');
							}
						},error: function(dataHtml2) {
							
						}
					});
				}
				
				$("#eAutoAssign").on('change', function(){
					if($(this).prop('checked')) {
						$("#iDriverId").val('');
						$("#iDriverId").attr('disabled','disabled');
						$(".assign-driverbtn").attr('disabled','disabled');
						$("#showdriverSet001").hide();
					}else {
						$("#iDriverId").removeAttr('disabled');
						$(".assign-driverbtn").removeAttr('disabled');
					}
				});
				var bookId = '<?php echo $iCabBookingId; ?>';
				if(bookId != "") {
					if($("#eAutoAssign").prop('checked')) {
						$("#iDriverId").val('');
						$("#iDriverId").attr('disabled','disabled');
					}else {
						$("#iDriverId").removeAttr('disabled');
					}
				}
				
				$(document).ready(function() {
					var referrer;
					if($("#previousLink").val() == "" ){
						referrer =  document.referrer;	
						//alert(referrer);
					}else { 
						referrer = $("#previousLink").val();
					}
					if(referrer == "") {
						referrer = "cab_booking.php";
					}else {
						$("#backlink").val(referrer);
					}
					// $(".back_link").attr('href',referrer);
				});
				
				$('#datetimepicker4').keydown(function(e) {
				   e.preventDefault();
				   return false;
				});
				
			</script>
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
				<h4 class="modal-title" id="myModalLabel"> How It Works?</h4>
			</div>
			<div class="modal-body">
				<p><b>Flow </b>: Through "Manual Taxi Dispatch" Feature, you can book Rides for customers who ordered for a Ride by calling you. There will be customers who may not have iPhone or Android Phone or may not have app installed on their phone. In this case, they will call Taxi Company (your company) and order Ride which may be needed immediately or after some time later.</p>
				<p>Here, you will fill their info in the form and dispatch book a taxi ride for him.</p>
				<p>The Driver will receive info on his App and will pickup the rider at the scheduled time.</p>
				<p>- If the customer is already registered with us, just enter his phone number and his info will be fetched from the database when "Get Details" button is clicked. Else fill the form.</p>
				<p>- Once the Trip detail is added, Fare estimate will be calculated based on Pick-Up Location, Drop-Off Location and Car Type.</p>
				<p>- Admin will need to communicate & confirm with Driver and then select him as Driver so the Ride can be allotted to him. </p>
				<p>- Clicking on "Book" Button, the Booking detail will be saved and will take Administrator to the "Ride Later Booking" Section. This page will show all such bookings.</p>
				<p>- The assigned Driver can see the upcoming Bookings from his App under "My Bookings" section.</p>
				<p>- Driver will have option to "Start Trip" when he reaches the Pickup Location at scheduled time or "Cancel Trip" if he cannot take the ride for some reason. If the Driver clicks on "Cancel Trip", a notification will be sent to Administrator so he can make alternate arrangements.</p>
				<p>- Upon clicking on "Start Trip", the ride will start in driver's App in regular way.</p>
				<p><span><img src="images/mobile_app_booking.png"></img></span></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>