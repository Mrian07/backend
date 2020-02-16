<?
	include_once("common.php");
	include('admin/main_functions.php');
	//error_reporting(E_ALL);
	global $generalobj;
	$script="About Us";
	$meta = $generalobj->getStaticPage(1,$_SESSION['sess_lang']);
	 //echo "<pre>";print_r($_);exit;
	 $type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
	  if($type != ""){
			$sql = "select COUNT(iHelpscategoryId) AS Total from helps_categories where eTopic = '".$type."' and eStatus != 'Inactive'";
		 }else{
			$sql = "select COUNT(iHelpscategoryId) AS Total from helps_categories where eStatus != 'Inactive'";
		 
		 }
		 $per_page = $DISPLAY_RECORD_NUMBER;
		 $totalData = $obj->MySQLSelect($sql);
		$total_results = $totalData[0]['Total'];
		$total_pages = ceil($total_results / $per_page); //total pages we going to have
		$show_page = 1;
		
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
		
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$tpages=$total_pages;
		if ($page <= 0){
			$page = 1;
		}	

	 $ssql="";
	 if($type != ""){
		 $sql = "select * from helps_categories where eTopic = '".$type."' and eStatus != 'Inactive' LIMIT $start, $per_page";
		
		 $ssql="&type=$type";
			
	 }else{
		$sql = "select * from helps_categories where eStatus != 'Inactive' LIMIT $start, $per_page";
		$db_hepls_cat = $obj->MySQLSelect($sql);
		$endRecord = count($db_hepls_cat);
	 
	 }
	 $db_hepls_cat = $obj->MySQLSelect($sql);
		$endRecord = count($db_hepls_cat);
	 $reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$ssql;
	 
	 
	 
	 
	 
	// echo "<pre>"; print_r($db_hepls_cat); exit; 
	 
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>
<?=$meta['meta_title'];?>
</title>
<meta name="keywords" value="<?=$meta['meta_keyword'];?>"/>
<meta name="description" value="<?=$meta['meta_desc'];?>"/>
<!-- Default Top Script and css -->
<?php include_once("top/top_script.php");?>
<!-- End: Default Top Script and css-->
</head>
<body>
<div id="main-uber-page">
  <!-- Left Menu -->
  <?php include_once("top/left_menu.php");?>
  <!-- End: Left Menu-->
  <!-- home page -->
  <!-- Top Menu -->
  <?php include_once("top/header_topbar.php");?>
  <!-- End: Top Menu-->
  <!-- contact page-->
  <div class="page-contant custom-error-page">
    <div class="breadcrumbs">
      <div class="breadcrumbs-inner"> <span><a href="knowledgebase_demo.php">Home <?php echo ($type !="")? ' >'.$type:''; ?></a></span> <b>
       <b><input name="Search" type="text" placeholder="Search" onblur ="searchdata(this.value);"></b>
        </b> </div>
    </div>
    <div class="page-contant-inner">
      <h2 class="header-page trip-detail">Help</h2>
      <!-- trips detail page -->
      <div class="static-page custom-error-page">
        <div class="custom-error-left-part">
          <ul>
            <li><a href="knowledgebase_demo.php?type=Admin"><img src="assets/img/administrator-panel-icon.png" alt="">Administrator Panel</a></li>
            <li><a href="knowledgebase_demo.php?type=Front"><img src="assets/img/front-panel-icon.png" alt="">Front Panel</a></li>
            <li><a href="knowledgebase_demo.php?type=RiderApp"><img src="assets/img/rider-application-icon.png" alt="">Rider Application</a></li>
            <li><a href="knowledgebase_demo.php?type=DriverApp"><img src="assets/img/driver-application-icon.png" alt="">Driver Application</a></li>
            <li><a href="knowledgebase_demo.php?type=General"><img src="assets/img/driver-application-icon.png" alt="">General</a></li>
          </ul>
        </div>
        <div class="custom-error-right-part">
          <h3><?php echo ($type !="") ? $type.' Panel':'Administrator Panel'?></h3>
          <div class="custom-error-right-part-box">
            <?php 
	  
		for($i=0;$i<=count($db_hepls_cat);$i++){ 
			
			$iHelpscategoryId = $db_hepls_cat[$i]['iHelpscategoryId'];
			$heplscatvTitle = $db_hepls_cat[$i]['vTitle'];
			$eTopic = $db_hepls_cat[$i]['eTopic']; ?>
            <ul>
              <h4><?php echo $heplscatvTitle; ?></h4>
              <?php $sql1 = "select * from helps where iHelpscategoryId  ='".$iHelpscategoryId."' and eStatus != 'Inactive'";  
				$db_hepls = $obj->MySQLSelect($sql1);
				
				for($j=0;$j<=count($db_hepls);$j++){ ?>
              <li><a href="knowledgebase2_demo.php?id=<?php echo$db_hepls[$j]['iHelpsId']?>&prevlink=<?php echo $eTopic;?>"><?php echo $db_hepls[$j]['vTitle']; ?></a></li>
              <?php }	?>
            </ul>
            <?php } ?>
            <div class="pagination-kk">
            <?php include('admin/pagination_n.php'); ?>
            </div>
          </div>
        </div>
      </div>
      <div style="clear:both;"></div>
    </div>
    <div style="clear:both;"></div>
  </div>
  <!-- home page end-->
  <!-- footer part -->
  <?php include_once('footer/footer_home.php');?>
  <!-- End:contact page-->
</div>
<!-- footer part end -->
<!-- Footer Script -->
<?php include_once('top/footer_script.php');?>
<!-- End: Footer Script -->
</body>
</html>
<script>
	function searchdata(keywords){
		//alert(keywords);
		/* var request = $.ajax({
				type: "POST",
				url: 'change_searchdata.php',
				data: {keywords: keywords},
				success: function (dataHtml)
				{ */
					
					window.location.href = 'knowledgebase_search.php?keywords='+keywords;
					
				/* }
			}); */
		}
</script>

