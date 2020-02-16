<?
	include_once('common.php');
	$tbl_name 	= 'trips';

	$generalobj->check_member_login();
	$_REQUEST['iTripId'] = base64_decode(base64_decode(trim($_REQUEST['iTripId'])));
	$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';
	$script="Trips";
	$sdsql = "";
	if($_SESSION['sess_user']== "driver")
	{
		$sess_iUserId = $_SESSION['sess_iUserId'];
		$sdsql = " AND iDriverId = '".$sess_iUserId."' ";
	}
	
	if($_SESSION['sess_user']== "rider")
	{
		$sess_iUserId = $_SESSION['sess_iUserId'];
		$sdsql = " AND iUserId = '".$sess_iUserId."' ";
	}
	
	$sql = "select trips.*,vVehicleType as eCarType from trips left join vehicle_type on vehicle_type.iVehicleTypeId=trips.iVehicleTypeId where iTripId = '".$iTripId."'" . $sdsql;
	$db_trip = $obj->MySQLSelect($sql);
	
	$sql = "SELECT vt.*,vc.vCategory_EN as vehcat from vehicle_type as vt LEFT JOIN vehicle_category as vc ON vc.iVehicleCategoryId = vt.iVehicleCategoryId where iVehicleTypeId = '".$db_trip[0]['iVehicleTypeId']."'";
	$db_vtype = $obj->MySQLSelect($sql);
	 if($db_vtype[0]['vehcat'] != ""){
		   $car = ' - '.$db_vtype[0]['vVehicleType'];
    }else{
       $car = $db_vtype[0]['vVehicleType_'.$_SESSION['sess_lang']];
    }

	//echo '<pre>'; print_R($db_trip); echo '</pre>';
	/* #echo '<pre>'; print_R($db_trip); echo '</pre>';
		$to_time = @strtotime($db_trip[0]['tStartDate']);
		$from_time = @strtotime($db_trip[0]['tEndDate']);
		$diff=round(abs($to_time - $from_time) / 60,2);
		$db_trip[0]['starttime'] = $generalobj->DateTime($db_trip[0]['tStartDate'],18);
		$db_trip[0]['endtime'] = $generalobj->DateTime($db_trip[0]['tEndDate'],18);
		$db_trip[0]['triptime'] = $diff;
	*/
	$sql = "select * from ratings_user_driver where iTripId = '".$iTripId."' AND eUserType='Driver'";
	$db_ratings = $obj->MySQLSelect($sql);
	//echo"<pre>";print_r($db_ratings);exit;

	$rating_width = ($db_ratings[0]['vRating1'] * 100) / 5;
	$db_ratings[0]['vRating1'] = '<span style="display: block; width: 65px; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 0;">
		<span style="float: left !important; margin: 0;display: block; width: '.$rating_width.'%; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 -13px;"></span>
		</span>';
		//echo"<pre>";print_r($db_ratings);exit;
	$sql = "select * from register_driver where iDriverId = '".$db_trip[0]['iDriverId']."' LIMIT 0,1";
	$db_driver = $obj->MySQLSelect($sql);

	$sql = "select * from register_user where iUserId = '".$db_trip[0]['iUserId']."' LIMIT 0,1";
	$db_user = $obj->MySQLSelect($sql);
	
	$sql = "SELECT Ratio, vName, vSymbol FROM currency WHERE vName='".$db_user[0]['vCurrencyPassenger']."'";
    $db_curr_ratio = $obj->MySQLSelect($sql);

	$tripcursymbol=$db_curr_ratio[0]['vSymbol'];
	$tripcur=$db_curr_ratio[0]['Ratio'];
	$tripcurname=$db_curr_ratio[0]['vName'];
	
	$ts1 = strtotime($db_trip[0]['tStartDate']);
	$ts2 = strtotime($db_trip[0]['tEndDate']);
	$diff = abs($ts2 - $ts1);
	if ($db_trip[0]['eFareType'] == "Hourly") {
		$diff 	=	0;
		$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$iTripId'";
		$db_tripTimes = $obj->MySQLSelect($sql22);

		foreach($db_tripTimes as $dtT){
			if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
				$diff += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
			}
		}
		$diff = abs($diff);
	}
	
	$years = floor($diff / (365*60*60*24)); $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
	$minuts = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
	$diffss = $hours.':'.$minuts.':'.$seconds;
	$totalTimeInMinutes_trip=@round(($diff) / 60,2);

	 //$distance=$db_trip[0]['fPricePerKM']*$db_trip[0]['fDistance'];
	 //$time=$db_trip[0]['fPricePerMin']*$totalTimeInMinutes_trip;
	//$total_fare=$db_trip[0]['iBaseFare']+($time)+($distance);
	//$commision=($total_fare*$db_trip[0]['fCommision'])/100;
	//$tot = $total_fare + ($commision);
	
	if($_SESSION['sess_user']== "company")
	{
		$sql = "select iCompanyId from register_driver where iDriverId = '".$db_trip[0]['iDriverId']."' LIMIT 0,1";
		$db_check = $obj->MySQLSelect($sql);
		if($db_check[0]['iCompanyId'] != $_SESSION['sess_iCompanyId'])
			$db_trip = array();
	}
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_MYEARNING_INVOICE']; ?></title>
    <!-- Default Top Script and css -->
    <?php include_once("top/top_script.php");?>
   
     <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>

    <!-- End: Default Top Script and css-->
</head>
<body>
     <!-- home page -->
    <div id="main-uber-page">
   <!-- Left Menu -->
    <?php include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
        <!-- Top Menu -->
        <?php include_once("top/header_topbar.php");?>
        <!-- End: Top Menu-->
        <!-- contact page-->
        <div class="page-contant">
    		<div class="page-contant-inner page-trip-detail">
          		<h2 class="header-page trip-detail"><?=$langage_lbl['LBL_RIDER_Invoice']; ?>
          			<a href="javascript:void(0);" onClick="history.go(-1)"><img src="assets/img/arrow-white.png" alt="" /><?=$langage_lbl['LBL_RIDER_back_to_listing']; ?></a>
					<?php if(count($db_trip) > 0){?>
            		<p><?=$langage_lbl['LBL_RIDER_RATING_PAGE_HEADER_TXT']; ?> <strong><?=@date('h:i A',@strtotime($db_trip[0]['tStartDate']));?> on <?=@date('d M Y',@strtotime($db_trip[0]['tStartDate']));?></strong></p>
					<?php }?>
          		</h2>
          		<!-- trips detail page -->
				<?php 
				if(count($db_trip) > 0)	
				{
				?>
          		<div class="trip-detail-page">
                <div class="trip-detail-page-inner">
            		<div class="trip-detail-page-left">
              			<div class="trip-detail-map"><div id="map-canvas" class="gmap3" style="width:100%;height:200px;margin-bottom:10px;"></div></div>
              			<div class="map-address">
                			<ul>
                  				<li> 
                  					<b><i aria-hidden="true" class="fa fa-map-marker fa-22 green-location"></i></b> 
              						<span>
                    					<h3><?=@date('h:i A',@strtotime($db_trip[0]['tStartDate']));?></h3>
                						<?=$db_trip[0]['tSaddress'];?>
            						</span> 
        						</li>
        						<?php if($APP_TYPE != 'UberX'){ ?> 
              					<li> 
              						<b><i aria-hidden="true" class="fa fa-map-marker fa-22 red-location"></i></b> 
          							<span>
                    					<h3><?=@date('h:i A',@strtotime($db_trip[0]['tEndDate']));?></h3>
                    					<?=$db_trip[0]['tDaddress'];?>
                    				</span> 
                				</li>
                				<?php } ?> 
                			</ul>
              			</div>
              			<?php 
              			if($APP_TYPE == 'UberX'){

              				$class_name = 'location-time location-time-second';

              			}else{

              				$class_name = 'location-time';
              			}
              			?>
              			<div class="<?php echo $class_name;?>">
	            			<ul>
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_RIDER_RIDER_INVOICE_Car']; ?></h3>
	                    				<?//=$db_vtype[0]['vehcat'].$car;?>
										<?=$car;?>
	            				</li>
	            					<?php if($APP_TYPE != 'UberX'){ ?> 
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_RIDER_DISTANCE_TXT']; ?></h3>
	                    			<?=$db_trip[0]['fDistance'];?> (<?=$langage_lbl['LBL_KM_TXT']; ?>)
	                			</li>
	                			<?php } ?>
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_RIDER_Trip_time']; ?></h3>
	                    			<?echo $diffss;?>
	                			</li>
	                		</ul>
              			</div>
            		</div>
            		<div class="trip-detail-page-right">
              			<div class="driver-info">
              				<div class="driver-img">
              					<span class="invoice-img">
													<? if($db_driver[0]['vImage'] != '' && file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_driver[0]['iDriverId'] . '/2_' . $db_driver[0]['vImage'])){?>
													<img src = "<?= $tconfig["tsite_upload_images_driver"]. '/' . $db_driver[0]['iDriverId'] . '/2_' .$db_driver[0]['vImage'] ?>" style="height:150px;"/>
													<? }else{ ?>
													<img src="assets/img/profile-user-img.png" alt="">
													<? } ?></span>
              				</div>
                			<h3><?=$langage_lbl['LBL_RIDER_You_ride_with']; ?> <?= $generalobj->clearName($db_driver[0]['vName'].' '.$db_driver[0]['vLastName']);?></h3>
                			<p><b><?=$langage_lbl['LBL_RIDER_Rate_Your_Ride']; ?>:</b><?=$db_ratings[0]['vRating1'];?></p>
              			</div>
          				<div class="fare-breakdown">
                			<div class="fare-breakdown-inner">
                  				<h3><?=$langage_lbl['LBL_RIDER_FARE_BREAK_DOWN_TXT']; ?></h3>
                  				<ul>
									<?
									if($db_trip[0]['eFareType'] != 'Fixed')
									{
										?>
										<li><strong><?=$langage_lbl['LBL_RIDER_Basic_Fare']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['iBaseFare'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<li><strong><?=$langage_lbl['LBL_RIDER_DISTANCE_TXT']; ?> (<?=$db_trip[0]['fDistance'];?> <?=$langage_lbl['LBL_KM_TXT']; ?>)</strong><b><?=$generalobj->trip_currency($db_trip[0]['fPricePerKM'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<li><strong><?=$langage_lbl['LBL_RIDER_TIME_TXT']; ?> (<?echo $diffss;?>)</strong><b><?=$generalobj->trip_currency($db_trip[0]['fPricePerMin'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?
									}
									else
									{
										if($db_trip[0]['eFareType'] == 'Hourly') { ?>													
											<li><strong><?=$langage_lbl['LBL_RIDER_Total_Fare']; ?> (Hourly)</strong><b><?=$generalobj->trip_currency($db_trip[0]['fPricePerMin'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<? }else{?>	
											<li><strong><?=$langage_lbl['LBL_RIDER_Total_Fare']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['iFare'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<? }
									}
									if($db_trip[0]['fWalletDebit'] > 0)
									{
										?>
											<li><strong><?=$langage_lbl['LBL_RIDER_WALLET_DEBIT_MONEY']; ?></strong><b> - <?=$generalobj->trip_currency($db_trip[0]['fWalletDebit'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?> </b></li>
											<?
									}
									if($db_trip[0]['fDiscount'] > 0)
									{
										?>
										<li><strong><?=$langage_lbl['LBL_RIDER_DISCOUNT']; ?> </strong><b> - <?=$generalobj->trip_currency($db_trip[0]['fDiscount'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?
									}
									if($db_trip[0]['fSurgePriceDiff'] > 0)
										{
											?>
											<li><strong><?=$langage_lbl['LBL_RIDER_SURGE_MONEY']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fSurgePriceDiff'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
											<?
										}
									?>

				                    <!-- <li><strong><?=$langage_lbl['LBL_RIDER_Commision']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fCommision'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li> -->							
									<?php 
										if($db_trip[0]['fVisitFee'] > 0){ ?> 
										<li><strong><?=$langage_lbl['LBL_VISIT_FEE']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fVisitFee'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php } ?>	
										<?php 
										if($db_trip[0]['fMaterialFee'] > 0){ ?> 
										<li><strong><?=$langage_lbl['LBL_MATERIAL_FEE']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fMaterialFee'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php } ?>	
										<?php 
										if($db_trip[0]['fMiscFee'] > 0){ ?> 
										<li><strong><?=$langage_lbl['LBL_MISC_FEE']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fMiscFee'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php } ?>	
										<?php 
										if($db_trip[0]['fDriverDiscount'] > 0){ ?> 
										<li><strong><?=$langage_lbl['LBL_PROVIDER_DISCOUNT']; ?></strong><b> - <?=$generalobj->trip_currency($db_trip[0]['fDriverDiscount'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php } ?>
				                     <?php if($db_trip[0]['fMinFareDiff']!="" && $db_trip[0]['fMinFareDiff'] > 0){
			                            //$minimum_fare=round($db_trip[0]['fMinFareDiff'] * $db_trip[0]['fRatioPassenger'],1);
										$minimum_fare=$db_trip[0]['iBaseFare']+$db_trip[0]['fPricePerKM']+$db_trip[0]['fPricePerMin']+$db_trip[0]['fMinFareDiff'];
			                            ?>

			                           <li><strong><?=$generalobj->trip_currency($minimum_fare,$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b> <?=$langage_lbl['LBL_RIDER_MINIMUM']; ?>
			                              </strong><b>
			                              <?=$generalobj->trip_currency($db_trip[0]['fMinFareDiff'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
			                          

			                          <?php }
			                          ?>
                  				</ul>
                  				<span>
								<?php $paymentMode = ($db_trip[0]['vTripPaymentMode'] == 'Cash')? $langage_lbl['LBL_VIA_CASH_TXT']: $langage_lbl['LBL_VIA_CARD_TXT']?>
                  					<h4><?=$langage_lbl['LBL_RIDER_Total_Fare']; ?> (<?=$paymentMode;?>)</h4>
                  					<em><?=$generalobj->trip_currency($db_trip[0]['iFare'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></em>
              					</span>
								<?php if($db_trip[0]['fTipPrice'] > 0)
								{ ?>
									<ul><li><strong><?=$langage_lbl['LBL_TIP_GIVEN_TXT']; ?></strong><b> <?=$generalobj->trip_currency($db_trip[0]['fTipPrice']);?></b></li></ul>
								<?} ?>
                  				<div style="clear:both;"></div>

                  				<?php if($db_trip[0]['eType'] == 'Deliver'){ ?>
			                          <br>
			                        <h3><?=$langage_lbl['LBL_DELIVERY_DETAILS']; ?></h3><hr/>

			                        <ul style="border-bottom:none">
			                            <li><strong><?=$langage_lbl['LBL_RECEIVER_NAME']; ?> </strong><b><?=$db_trip[0]['vReceiverName'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_RECEIVER_MOBILE']; ?> </strong><b><?=$db_trip[0]['vReceiverMobile'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_PICK_UP_INS']; ?> </strong><b><?=$db_trip[0]['tPickUpIns'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_DELIVERY_INS']; ?> </strong><b><?=$db_trip[0]['tDeliveryIns'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_PACKAGE_DETAILS']; ?></strong><b><?=$db_trip[0]['tPackageDetails'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_DELIVERY_CONFIRMATION_CODE_TXT']; ?> </strong><b><?=$db_trip[0]['vDeliveryConfirmCode'];?></b></li>       
			                          
			                        </ul>

			                        <?php } ?>

                       				 <div style="clear:both;"></div>
                       				 <?php if($APP_TYPE == 'UberX' && $db_trip[0]['vBeforeImage'] != ''){
									 ?> 
										<h3 style="float:left; width:100%; margin:0 0 10px;"><?php echo $langage_lbl_admin['LBL_TRIP_DETAIL_HEADER_TXT'];?></h3>
										<div class="invoice-right-bottom-img">
											<div class="col-sm-6">											
												<h4>
												<?php														
												$img_path = $tconfig["tsite_upload_trip_images"];
												echo $langage_lbl_admin['LBL_SERVICE_BEFORE_TXT_ADMIN'];?></h4>
												 <b><a href="<?= $img_path .$db_trip[0]['vBeforeImage'] ?>" target="_blank" ><img src = "<?= $img_path.$db_trip[0]['vBeforeImage'] ?>" style="width:200px;" alt ="Before Images"/></b></a>
											</div>
											<div class="col-sm-6">
												<h4><?php echo $langage_lbl_admin['LBL_SERVICE_AFTER_TXT_ADMIN'];?></h4>
												 <b><a href="<?= $img_path .$db_trip[0]['vBeforeImage'] ?>" target="_blank" ><img src = "<?= $img_path.$db_trip[0]['vAfterImage'] ?>" style="width:200px;" alt ="After Images"/></b></a>
											</div>
										</div>

										<?php } ?>
                			</div>
              			</div>
            		</div>
                    </div>
            		<!-- -->
        		 	<? //if(SITE_TYPE=="Demo"){?>
            		<!-- <div class="record-feature"> 
            			<span><strong>“Edit / Delete Record Feature”</strong> has been disabled on the Demo Admin Version you are viewing now.
              			This feature will be enabled in the main product we will provide you.</span> 
              		</div> -->
              		<? //}?>
        		<!-- -->
          		</div>
				<?php
				}
				else
				{
				?>
				<div class="trip-detail-page">
                <div class="trip-detail-page-inner">
					We could not find INVOICE details for this Trip. Please click browser's back button and check again.
				</div>
				</div>
				<?php }?>
        	</div>
  		</div>
    <!-- footer part -->
    <?php include_once('footer/footer_home.php');?>
    <!-- footer part end -->
    <!-- End:contact page-->
    <div style="clear:both;"></div>
    </div>
    <!-- home page end-->
    <!-- Footer Script -->
    <?php include_once('top/footer_script.php');?>
    <script src="assets/js/gmap3.js"></script>
    <script type="text/javascript">
		h = window.innerHeight;
		$("#page_height").css('min-height', Math.round( h - 99)+'px');

		function from_to(){

			$("#map-canvas").gmap3({
				getroute:{
					options:{
						origin:'<?= $db_trip[0]['tSaddress']?>',
						destination:'<?= $db_trip[0]['tDaddress']?>',
						travelMode: google.maps.DirectionsTravelMode.DRIVING
					},
					callback: function(results){
						if (!results) return;
						$(this).gmap3({
							map:{
								options:{
									zoom: 13,
									center: [-33.879, 151.235]
								}
							},
							directionsrenderer:{
								options:{
									directions:results
								}
							}
						});
					}
				}
			});
		}
		from_to();
	</script>
    <!-- End: Footer Script -->
</body>
</html>
