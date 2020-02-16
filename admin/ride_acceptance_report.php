<?php
include_once('../common.php');

if(!isset($generalobjAdmin)){
require_once(TPATH_CLASS."class.general_admin.php");
$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script   = "Driver Accept Report";

$sql = "select iDriverId, CONCAT(vName,' ',vLastName) AS driverName from register_driver WHERE eStatus != 'Deleted' order by vName";
$db_drivers = $obj->MySQLSelect($sql);

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY rs.iDriverRequestId DESC';

if ($sortby == 1) {
    if ($order == 0)
        $ord = " ORDER BY rd.vName ASC";
    else
        $ord = " ORDER BY rd.vName DESC";
}
//End Sorting

// Start Search Parameters
$ssql = '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
$date1=$startDate.' '."00:00:00";
$date2=$endDate.' '."23:59:59";

if ($startDate != '' && $endDate != '') {
	$ssql .= " AND rs.tDate between '$date1' and '$date2'";
}
if ($iDriverId != '') {
	$ssql .= " AND rd.iDriverId = '".$iDriverId."'";
}

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

$sql = "SELECT COUNT( DISTINCT rs.iDriverId ) AS Total FROM register_driver rd 
left join driver_request rs on rd.iDriverId=rs.iDriverId  
WHERE 1=1 $ssql GROUP by rs.iDriverId";
$totalData = $obj->MySQLSelect($sql);
$total_results = count($totalData);
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
$tpages = $total_pages;
if ($page <= 0)
    $page = 1;
//Pagination End

$sql = "SELECT rd.iDriverId , rd.vLastName ,rd.vName ,
COUNT(case when rs.eStatus = 'Accept' then 1 else NULL end) `Accept` ,
COUNT(case when rs.eStatus != '' then 1 else NULL  end) `Total Request` ,
COUNT(case when rs.eStatus  = 'Decline' then 1 else NULL end) `Decline` ,
COUNT(case when rs.eStatus  = 'Timeout' then 1 else NULL end) `Timeout` 

FROM register_driver rd 
left join driver_request rs on rd.iDriverId=rs.iDriverId  
WHERE 1=1 $ssql GROUP by rs.iDriverId $ord LIMIT $start, $per_page";
$db_res = $obj->MySQLSelect($sql);
$endRecord = count($db_res);

$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page')
        $var_filter .= "&$key=" . stripslashes($val);
}
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
//echo "<pre>"; print_r($db_log_report); exit;

$Today=Date('Y-m-d');
$tdate=date("d")-1;
$mdate=date("d");
$Yesterday = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));

$curryearFDate = date("Y-m-d",mktime(0,0,0,'1','1',date("Y")));
$curryearTDate = date("Y-m-d",mktime(0,0,0,"12","31",date("Y")));
$prevyearFDate = date("Y-m-d",mktime(0,0,0,'1','1',date("Y")-1));
$prevyearTDate = date("Y-m-d",mktime(0,0,0,"12","31",date("Y")-1));

$currmonthFDate = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$tdate,date("Y")));
$currmonthTDate = date("Y-m-d",mktime(0,0,0,date("m")+1,date("d")-$mdate,date("Y")));
$prevmonthFDate = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d")-$tdate,date("Y")));
$prevmonthTDate = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$mdate,date("Y")));

$monday = date( 'Y-m-d', strtotime( 'sunday this week -1 week' ) );
$sunday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

$Pmonday = date( 'Y-m-d', strtotime('sunday this week -2 week'));
$Psunday = date( 'Y-m-d', strtotime('saturday this week -1 week'));
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title><?=$SITE_NAME?> | Ride Acceptance Report</title>
          <meta content="width=device-width, initial-scale=1.0" name="viewport" />
          <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

          <? include_once('global_files.php');?>         
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
									<h2>Ride Acceptance Report</h2>
								   
							   </div>
						</div>
						<hr />
                         <div class="table-list">
                              <div class="row">
                                   <div class="col-lg-12">
											<form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
												<div class="Posted-date mytrip-page mytrip-page-select payment-report">
													<input type="hidden" name="action" value="search" />
													<h3>Search by Date...</h3>
													<span>
													<a onClick="return todayDate('dp4','dp5');"><?=$langage_lbl['LBL_MYTRIP_Today']; ?></a>
													<a onClick="return yesterdayDate('dFDate','dTDate');"><?=$langage_lbl['LBL_MYTRIP_Yesterday']; ?></a>
													<a onClick="return currentweekDate('dFDate','dTDate');"><?=$langage_lbl['LBL_MYTRIP_Current_Week']; ?></a>
													<a onClick="return previousweekDate('dFDate','dTDate');"><?=$langage_lbl['LBL_MYTRIP_Previous_Week']; ?></a>
													<a onClick="return currentmonthDate('dFDate','dTDate');"><?=$langage_lbl['LBL_MYTRIP_Current_Month']; ?></a>
													<a onClick="return previousmonthDate('dFDate','dTDate');"><?=$langage_lbl['LBL_MYTRIP_Previous Month']; ?></a>
													<a onClick="return currentyearDate('dFDate','dTDate');"><?=$langage_lbl['LBL_MYTRIP_Current_Year']; ?></a>
													<a onClick="return previousyearDate('dFDate','dTDate');"><?=$langage_lbl['LBL_MYTRIP_Previous_Year']; ?></a>
													</span> 
													<span>
													<input type="text" id="dp4" name="startDate" placeholder="From Driver Online Date" class="form-control" value=""/>
													<input type="text" id="dp5" name="endDate" placeholder="To Driver Online Date" class="form-control" value=""/>
													<div class="col-lg-3 select001">
														<select class="form-control filter-by-text" name = 'iDriverId' data-text="Select Driver">
														   <option value="">Select Driver</option>
														   <?php foreach($db_drivers as $dbd){ ?>
														   <option value="<?php echo $dbd['iDriverId']; ?>" <?php if($iDriverId == $dbd['iDriverId']) { echo "selected"; } ?>><?php echo $generalobjAdmin->clearName($dbd['driverName']); ?></option>
														   <?php } ?>
														</select>
													</div>
													</span>
												</div>
												<div class="tripBtns001"><b>
												<input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
												<input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='ride_acceptance_report.php'"/>
												<button type="button" onClick="reportExportTypes('ride_acceptance_report')" class="export-btn001" >Export</button>
												</b>
												</div>
											</form>
                                                  <div class="table-responsive">
												  <form name="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                                       <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                            <thead>
                                                               <tr>
																<th>Driver Name</th>
																<th>Total Trip Requests</th>     
																<th>Requests Decline</th>
																<th>Requests Timeout</th>
																<th>Trips Accepted</th>
																<th>Trips Cancelled</th>
																<th>Trips Finished</th>
																<th>Acceptance Percentage</th> 
                                                               </tr>
                                                            </thead>
                                                            <tbody>
                                                           <?php  
                                                           $total_trip_req ="";
                                                           $total_trip_acce_req ="";
                                                           $total_trip_dec_req ="";
														   
                                                           for($i=0;$i<count($db_res);$i++) {

                                                            // $sql_acp = "SELECT rd.vName, rd. vLastName, rs.tDate,count(rs.iDriverId)as totalacp FROM driver_request AS rs LEFT JOIN register_driver AS rd ON rd.iDriverId = rs.iDriverId WHERE rs.eStatus ='Accept' AND rs.iDriverId ='".$db_res[$i]['iDriverId']."'";
														   //echo "<hr>";  
														    // $sql_tip="SELECT COUNT(trips.iTripId) as Finished from trips WHERE trips.iActive='Finished' and trips.iDriverId='".$db_res[$i]['iDriverId']."'";
															  $sql_acp="SELECT 
																		COUNT(case when t.eCancelled = 'Yes' then 1 else NULL end) `Cancel` ,
																		COUNT(case when t.eCancelled != '' then 1 else NULL  end) `Finish` 
																		FROM trips t  where t.iDriverId='".$db_res[$i]['iDriverId']."'";
																	
												
                                                              $db_acp = $obj->MySQLSelect($sql_acp);
															 
                                                            //  $db_tip = $obj->MySQLSelect($sql_tip);
															//echo "<pre>";print_r($db_acp);exit;
                                                              // $sql_dec = "SELECT count(iDriverId) as totaldec FROM `driver_request` WHERE eStatus  IN ('Decline', AND iDriverId ='".$db_res[$i]['iDriverId']."'";
                                                              // $db_dec= $obj->MySQLSelect($sql_dec);                                          
															
                                                             // $db_acp_val = $db_acp[0]['Accept'];
                                                             // $db_acp_val = $db_acp[0]['Accept'];
                                                            
                                                             // $db_dec_val = $db_dec[0]['Total Request'];
                                                             // $total = $db_acp_val + $db_dec_val;
                                                             // $Finished = $db_acp_val - $db_dec_val ;
															 
                                                             // $total_trip_req = $total_trip_req + $total;
                                                             // $total_trip_acce_req = $total_trip_acce_req + $db_acp_val;
                                                             // $total_trip_dec_req = $total_trip_dec_req + $db_dec_val;   
                                                             // $total_finish=$total_finish+$Finished	;														 

                                                             // $percentage = (100 * $db_acp_val)/$total;
                                                              // $aceptance_percentage = number_format($percentage, 2);        
															    
															  $Accept = $db_res[$i]['Accept'];
															  $tAccept = $tAccept + $Accept;
															  $Request = $db_res[$i]['Total Request'];
															  $tRequest =$tRequest + $Request ;
															  $Decline = $db_res[$i]['Decline'];
															  $tDecline =$tDecline + $Decline;
															  $Timeout = $db_res[$i]['Timeout'];
															  $tTimeout = $tTimeout + $Timeout ;
															  $Cancel = $db_acp[0]['Cancel'];
															  $tCancel = $tCancel + $Cancel ;
															  $Finish = $db_acp[0]['Finish'];
															  $tFinish = $tFinish + $Finish ;
															 $aceptance_percentage= (100 * ($Accept))/$Request;
															  
															  ?>
															
                                                             <tr class="gradeA">
                                                                  <td><?=$generalobjAdmin->clearName($db_res[$i]['vName'].' '.$db_res[$i]['vLastName']); ?></td>
                                                                  <td><?= $Request;?></td>
                                                                  <td><?=$Decline; ?></td>
																   <td><?=$Timeout; ?></td>
                                                                  <td><?=$Accept; ?></td>
																  
                                                                 
                                                                  <td><?=$Cancel; ?></td>
                                                                  <td><?=$Finish; ?></td>
																  
                                                                  <td><?= round($aceptance_percentage).' %'; ?></td>      
                                                             </tr>

                                                           <? 	
															 } 
														   ?>                                                              
                                                            </tbody>
                                                            <tr class="gradeA">
                                                                <td><b>TOTAL</b></td>
                                                                <td><?= $tRequest;?></td>
                                                                  <td><?=$tDecline; ?></td>
                                                                  
																  
                                                                  <td><?=$tTimeout; ?></td>
																  <td><?=$tAccept; ?></td>
                                                                  <td><?=$tCancel; ?></td>
                                                                  <td><?=$tFinish; ?></td>
                                                                <td></td>
                                                            </tr>

                                                       </table>
													   </form>
												<?php include('pagination_n.php'); ?>
											</div>
                                   </div> <!--TABLE-END-->
                              </div>
                         </div>
                    </div>
               </div>
               <!--END PAGE CONTENT -->
          </div>
          <!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="" method="post" >
<input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
<input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
<input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
<input type="hidden" name="action" value="<?php echo $action; ?>" >
<input type="hidden" name="iDriverId" value="<?php echo $iDriverId; ?>" >
<input type="hidden" name="startDate" value="<?php echo $startDate; ?>" >
<input type="hidden" name="endDate" value="<?php echo $endDate; ?>" >
<input type="hidden" name="vStatus" value="<?php echo $vStatus; ?>" >
<input type="hidden" name="method" id="method" value="" >
</form>
	<? include_once('footer.php');?>
	<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
	<link rel="stylesheet" href="css/select2/select2.min.css" />
	<script src="js/plugins/select2.min.js"></script>
	<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <script>
			$('#dp4').datepicker()
            .on('changeDate', function (ev) {
                if (ev.date.valueOf() < endDate.valueOf()) {
                    $('#alert').show().find('strong').text('The start date can not be greater then the end date');
                } else {
                    $('#alert').hide();
                    startDate = new Date(ev.date);
                    $('#startDate').text($('#dp4').data('date'));
                }
                $('#dp4').datepicker('hide');
            });
			$('#dp5').datepicker()
            .on('changeDate', function (ev) {
                if (ev.date.valueOf() < startDate.valueOf()) {
                    $('#alert').show().find('strong').text('The end date can not be less then the start date');
                } else {
                    $('#alert').hide();
                    endDate = new Date(ev.date);
                    $('#endDate').text($('#dp5').data('date'));
                }
                $('#dp5').datepicker('hide');
            });
		$(document).ready(function () {
			 if('<?=$startDate?>'!=''){
				 $("#dp4").val('<?=$startDate?>');
				 $("#dp4").datepicker('update' , '<?=$startDate?>');
			 }
			 if('<?=$endDate?>'!=''){
				 $("#dp5").datepicker('update' , '<?= $endDate;?>');
				 $("#dp5").val('<?= $endDate;?>');
			 }
			 
			 $("select.filter-by-text").each(function(){
			  $(this).select2({
					placeholder: $(this).attr('data-text'),
					allowClear: true
			  }); //theme: 'classic'
			});
         });
		 
		 function setRideStatus(actionStatus) {
			 window.location.href = "trip.php?type="+actionStatus;
		 }
		 function todayDate()
		 {
			//alert('sa');
			 $("#dp4").val('<?= $Today;?>');
			 $("#dp5").val('<?= $Today;?>');
		 }
		 function resetform()
		 {
		 	//location.reload();
			document.search.reset();
			document.getElementById("iDriverId").value=" ";
		}	
		 function yesterdayDate()
		 {
			 $("#dp4").val('<?= $Yesterday;?>');
			 $("#dp4").datepicker('update' , '<?= $Yesterday;?>');
			 $("#dp5").datepicker('update' , '<?= $Yesterday;?>');
			 $("#dp4").change();
			 $("#dp5").change();
			 $("#dp5").val('<?= $Yesterday;?>');
		 }
		function currentweekDate(dt,df)
		{
		 $("#dp4").val('<?= $monday;?>');
		 $("#dp4").datepicker('update' , '<?= $monday;?>');
		 $("#dp5").datepicker('update' , '<?= $sunday;?>');
		 $("#dp5").val('<?= $sunday;?>');
		}
		function previousweekDate(dt,df)
		{
		 $("#dp4").val('<?= $Pmonday;?>');
		 $("#dp4").datepicker('update' , '<?= $Pmonday;?>');
		 $("#dp5").datepicker('update' , '<?= $Psunday;?>');
		 $("#dp5").val('<?= $Psunday;?>');
		}
		function currentmonthDate(dt,df)
		{
		 $("#dp4").val('<?= $currmonthFDate;?>');
		 $("#dp4").datepicker('update' , '<?= $currmonthFDate;?>');
		 $("#dp5").datepicker('update' , '<?= $currmonthTDate;?>');
		 $("#dp5").val('<?= $currmonthTDate;?>');
		}
		function previousmonthDate(dt,df)
		{
		 $("#dp4").val('<?= $prevmonthFDate;?>');
		 $("#dp4").datepicker('update' , '<?= $prevmonthFDate;?>');
		 $("#dp5").datepicker('update' , '<?= $prevmonthTDate;?>');
		 $("#dp5").val('<?= $prevmonthTDate;?>');
		}
		function currentyearDate(dt,df)
		{
			 $("#dp4").val('<?= $curryearFDate;?>');
			 $("#dp4").datepicker('update' , '<?= $curryearFDate;?>');
			 $("#dp5").datepicker('update' , '<?= $curryearTDate;?>');
			 $("#dp5").val('<?= $curryearTDate;?>');
		}
		function previousyearDate(dt,df)
		{
			 $("#dp4").val('<?= $prevyearFDate;?>');
			 $("#dp4").datepicker('update' , '<?= $prevyearFDate;?>');
			 $("#dp5").datepicker('update' , '<?= $prevyearTDate;?>');
			 $("#dp5").val('<?= $prevyearTDate;?>');
		}
		function checkvalid(){
			 if($("#dp5").val() < $("#dp4").val()){
				 alert("From date should be lesser than To date.")
				 return false;
			 }
		}
		
		$("#Search").on('click', function () {
			if ($("#dp5").val() < $("#dp4").val()) {
				alert("From date should be lesser than To date.")
				return false;
			} else {
				var action = $("#_list_form").attr('action');
				var formValus = $("#frmsearch").serialize();
				window.location.href = action + "?" + formValus;
			}
		});
    </script>
</body>
</html>