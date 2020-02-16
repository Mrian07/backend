<?php
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'CabBooking';

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY cb.iCabBookingId DESC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY ru.vName ASC";
  else
  $ord = " ORDER BY ru.vName DESC";
}

if($sortby == 2){
  if($order == 0)
  $ord = " ORDER BY cb.dBooking_date ASC";
  else
  $ord = " ORDER BY cb.dBooking_date DESC";
}

if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY cb.vSourceAddresss ASC";
  else
  $ord = " ORDER BY cb.vSourceAddresss DESC";
}

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY cb.tDestAddress ASC";
  else
  $ord = " ORDER BY cb.tDestAddress DESC";
}

if($sortby == 5){
  if($order == 0)
  $ord = " ORDER BY cb.eStatus ASC";
  else
  $ord = " ORDER BY cb.eStatus DESC";
}

//End Sorting

$adm_ssql = "";
if (SITE_TYPE == 'Demo') {
    $adm_ssql = " And cb.dAddredDate > '" . WEEK_DATE . "'";
}

// Start Search Parameters
$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
$ssql = '';
if($keyword != ''){
    if($option != '') {
        if (strpos($option, 'eStatus') !== false) {
            $ssql.= " AND ".stripslashes($option)." LIKE '".$generalobjAdmin->clean($keyword)."'";
        }else {
            $ssql.= " AND ".stripslashes($option)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";
        }
    }else {
        $ssql.= " AND (CONCAT(ru.vName,' ',ru.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.tDestAddress LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vSourceAddresss	 LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%')";
    }
}
// End Search Parameters


//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(cb.iCabBookingId) as Total FROM cab_booking as cb
	 LEFT JOIN register_user as ru on ru.iUserId=cb.iUserId
	 LEFT JOIN register_driver as rd on rd.iDriverId=cb.iDriverId
	 LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId=cb.iVehicleTypeId WHERE 1=1 $ssql $adm_ssql";
	 //$ssql $adm_ssql
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];



$total_pages = ceil($total_results / $per_page); //total pages we going to have
$show_page = 1;

//-------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    } else {
        // error - show first set of results
        $start = 0;
        $end = $per_page;
    }
} else {
    // if page isn't set, show first set of results
    $start = 0;
    $end = $per_page;
}
// display pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$tpages=$total_pages;
if ($page <= 0)
    $page = 1;
//Pagination End


 $sql = "SELECT cb.*,CONCAT(ru.vName,' ',ru.vLastName) as rider,CONCAT(rd.vName,' ',rd.vLastName) as driver,vt.vVehicleType FROM cab_booking as cb
	 LEFT JOIN register_user as ru on ru.iUserId=cb.iUserId
	 LEFT JOIN register_driver as rd on rd.iDriverId=cb.iDriverId
	 LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId=cb.iVehicleTypeId WHERE 1=1$ssql $adm_ssql $ord LIMIT $start, $per_page";
	 //$ssql $adm_ssql $ord LIMIT $start, $per_page
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);

$var_filter = "";
foreach ($_REQUEST as $key=>$val) {
    if($key != "tpages" && $key != 'page')
    $var_filter.= "&$key=".stripslashes($val);
}

$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$var_filter;

?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME?> | Ride Later Bookings</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once('global_files.php');?>
    </head>
    <!-- END  HEAD-->
    
    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- Main LOading -->
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php include_once('header.php'); ?>
            <?php include_once('left_menu.php'); ?>

            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2>Ride Later Bookings</h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php include('valid_msg.php'); ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                              <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <td width="10%" class=" padding-right10"><select name="option" id="option" class="form-control">
										<option value="">All</option>
										<option value="CONCAT(ru.vName,' ',ru.vLastName)" <?php if ($option == "CONCAT(ru.vName,' ',ru.vLastName)") { echo "selected"; } ?> >Rider</option>
										<option value="cb.vSourceAddresss" <?php if ($option == 'cb.vSourceAddresss') {echo "selected"; } ?> >Pick Up Location </option>
										<?if($APP_TYPE != "UberX"){?>
										<option value="cb.tDestAddress" <?php if ($option == 'cb.tDestAddress') {echo "selected"; } ?> >Destination Location </option>
										<?php } ?>
										<option value="cb.eStatus" <?php if ($option == 'cb.eStatus') {echo "selected"; } ?> >Status</option>
                                    </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='cab_booking.php'"/>
                                    </td>
                                </tr>
                              </tbody>
                        </table>
                        
                      </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                               
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
														<th width=""><a href="javascript:void(0);" onClick="Redirect(1,<?php if($sortby == '1'){ echo $order; }else { ?>0<?php } ?>)">Rider<?php if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                        
														<th width=""><a href="javascript:void(0);" onClick="Redirect(2,<?php if($sortby == '2'){ echo $order; }else { ?>0<?php } ?>)">	Date <?php if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                        
														<th width=""><a href="javascript:void(0);" onClick="Redirect(3,<?php if($sortby == '3'){ echo $order; }else { ?>0<?php } ?>)">Pick Up location <?php if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
														
														<th width=""><a href="javascript:void(0);" onClick="Redirect(4,<?php if($sortby == '4'){ echo $order; }else { ?>0<?php } ?>)">Destination <?php if ($sortby == 4) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                        
														<th width="" align="left" style="text-align:left;">Driver</th>
														
														<th>Trip Details</th>
                                                        
														<th width="" align="left" style="text-align:left;"><a href="javascript:void(0);" onClick="Redirect(5,<?php if($sortby == '5'){ echo $order; }else { ?>0<?php } ?>)">Status <?php if ($sortby == 5) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    if(!empty($data_drv)) {
                                                    for ($i = 0; $i < count($data_drv); $i++) { 
                                                        
                                                        $default = '';
                                                        if($data_drv[$i]['eDefault']=='Yes'){
                                                                $default = 'disabled';
                                                        } ?>
                                                    <tr class="gradeA">
													  <td width="10%"><?=$generalobjAdmin->clearName($data_drv[$i]['rider']); ?></td>
													  <td width="10%" data-order="<?=$data_drv[$i]['iCabBookingId']; ?>"><?= $generalobjAdmin->DateTime($data_drv[$i]['dBooking_date']); ?> </td>
													  <td><?= $data_drv[$i]['vSourceAddresss']; ?></td>
													  <?if($APP_TYPE != "UberX"){?>
														<td><?= $data_drv[$i]['tDestAddress']; ?></td>
													  <? } ?>
														<?php if ($data_drv[$i]['eAutoAssign'] == "Yes") { ?>
															<td width="10%"><?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Auto Assigned <br />( <b><?= $generalobjAdmin->clearName($data_drv[$i]['driver']); ?> )</b><?php if(strtotime($data_drv[$i]['dBooking_date'])>strtotime(date('Y-m-d'))){ ?><a class="btn btn-info" href="add_booking.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>" data-tooltip="tooltip" title="Edit"><i class="icon-edit icon-flip-horizontal icon-white"></i></a><?php } ?><br>( Car Type : <?= $data_drv[$i]['vVehicleType']; ?>)</td>
														<?php } else if ($data_drv[$i]['eStatus'] == "Pending" && (strtotime($data_drv[$i]['dBooking_date'])>strtotime(date('Y-m-d')))) { ?>
															<td width="10%"><a class="btn btn-info" href="add_booking.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>"><i class="icon-shield icon-flip-horizontal icon-white"></i> Assign <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>
														<?php } else if($data_drv[$i]['eCancelBy'] == "Driver" && $data_drv[$i]['eStatus'] == "Cancel") { ?>
															<td width="10%"><a class="btn btn-info" href="add_booking.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>"><i class="icon-shield icon-flip-horizontal icon-white"></i> Assign <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>
														<?php } else if ($data_drv[$i]['driver'] != "" && $data_drv[$i]['driver'] != "0") { ?>
															<td width="10%"><b><?= $generalobjAdmin->clearName($data_drv[$i]['driver']); ?></b><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>) </td>
														<?php } else  { ?>
															<td width="10%">---<br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>
														<?php } ?>
													  <td width="10%"><?php if($data_drv[$i]['iTripId'] != "" && $data_drv[$i]['eStatus'] == "Completed") { ?>
														<a class="btn btn-primary" href="javascript:void(0);" onclick='javascript:window.open("invoice.php?iTripId=<?=$data_drv[$i]['iTripId']?>","_blank")';>View</a>
														  <?php }else {echo "---"; } ?>
														  
														  
														  </td>
														<td width="15%"><?php if($data_drv[$i]['eStatus'] == "Assign") {
														  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Assigned";
													   }
													   else
													   { 
															$sql="select iActive from trips where iTripId=".$data_drv[$i]['iTripId'];
															$data_stat=$obj->MySQLSelect($sql);
															// echo "<pre>";print_r($data_stat);
															if($data_stat)
															{
																for($d=0;$d<count($data_stat);$d++)
																{
																	if($data_stat[$d]['iActive'] == "Canceled")
																	{
																		echo "Canceled By ".$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];
																	}
																	else
																	{
																		echo $data_stat[$d]['iActive']; 	
																	}
																}
															}
															else
															{
																if($data_drv[$i]['eStatus'] == "Cancel")
																{
																	//echo "Canceled By ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
																	if($data_drv[$i]['eCancelBy'] == "Driver"){
																		echo "Canceled By ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
																	}else{
																		echo "Canceled By ".$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];
																	}
																}
																else
																{
																	if($data_drv[$i]['eStatus'] == 'Pending' && strtotime($data_drv[$i]['dBooking_date'])>strtotime(date('Y-m-d'))){
																		echo $data_drv[$i]['eStatus'];
																	}else {
																		echo 'Expired';
																	}
																}
															}
														}
														?>
													<?
														if ($data_drv[$i]['eStatus'] == "Cancel") {
													?>
														<br /><a href="javascript:void(0);" class="btn btn-info" data-toggle="modal" data-target="#uiModal_<?=$data_drv[$i]['iCabBookingId'];?>">Cancel Reason</a>
													<?           
														}
													?>
												  </td>
												</tr>
												<div class="modal fade" id="uiModal_<?=$data_drv[$i]['iCabBookingId'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
													  <div class="modal-content image-upload-1" style="width:400px;">
														   <div class="upload-content" style="width:350px; padding:0px;">
																<h3>Booking Cancel Reason</h3>
															<h4>Cancel By: 
																<?
																if($APP_TYPE != "UberX"){
																
																echo $data_drv[$i]['eCancelBy'];
																
																} else{
																	if($data_drv[$i]['eCancelBy'] == "Driver"){
																	echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
																	}else{
																	echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];
																	}
																} 
																?></h4>
																<h4>Cancel Reason: <?=$data_drv[$i]['vCancelReason'];?></h4>
																<form class="form-horizontal" id="frm6" method="post" enctype="multipart/form-data" action="" name="frm6">
																<input style="margin:10px 0 20px;" type="button" class="save" data-dismiss="modal" name="cancel" value="Close"></form>
														   </div>
													  </div>
                                                    <?php } }else { ?>
                                                        <tr class="gradeA">
                                                            <td colspan="7"> No Records Found.</td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </form>
										<?php include('pagination_n.php'); ?>
                                    </div>
                                </div> <!--TABLE-END-->
                            </div>
                        </div>
                    <div class="admin-notes">
                            <h4>Notes:</h4>
                            <ul>
								<li>
										Bookings module will list all Bookings on this page.
								</li>
								<li>
										Administrator can Activate / Deactivate / Delete any booking.
								</li>
								<li>
										Administrator can export data in XLS or PDF format.
								</li>
                            </ul>
                    </div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->
            
<form name="pageForm" id="pageForm" action="action/admin.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
<input type="hidden" name="iAdminId" id="iMainId01" value="" >
<input type="hidden" name="status" id="status01" value="" >
<input type="hidden" name="statusVal" id="statusVal" value="" >
<input type="hidden" name="option" value="<?php echo $option; ?>" >
<input type="hidden" name="keyword" value="<?php echo $keyword; ?>" >
<input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
<input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
<input type="hidden" name="method" id="method" value="" >
</form>
    <?php include_once('footer.php'); ?>
        <script>
            $("#setAllCheck").on('click',function(){
                if($(this).prop("checked")) {
                    jQuery("#_list_form input[type=checkbox]").each(function() {
                        if($(this).attr('disabled') != 'disabled'){
                            this.checked = 'true';
                        }
                    });
                }else {
                    jQuery("#_list_form input[type=checkbox]").each(function() {
                        this.checked = '';
                    });
                }
            });

            $("#Search").on('click', function(){
                var action = $("#_list_form").attr('action');
                var formValus = $("#frmsearch").serialize();
                window.location.href = action+"?"+formValus;
            });

            $('.entypo-export').click(function(e){
                 e.stopPropagation();
                 var $this = $(this).parent().find('div');
                 $(".openHoverAction-class div").not($this).removeClass('active');
                 $this.toggleClass('active');
            });

            $(document).on("click", function(e) {
                if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {
                  $(".show-moreOptions").removeClass("active");
                }
            });
            
        </script>
    </body>
    <!-- END BODY-->
</html>