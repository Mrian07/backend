<?
	include_once("common.php");
	include('admin/main_functions.php');
	//error_reporting(E_ALL);
	global $generalobj;
	$keywords = isset($_REQUEST['keywords'])?$_REQUEST['keywords']:"";
	
	
		$sql2 = "SELECT  COUNT(iHelpsId) AS Total FROM helps where vTitle LIKE '%".$keywords."%'";
		$per_page = $DISPLAY_RECORD_NUMBER;
		$totalData = $obj->MySQLSelect($sql2);
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
	$sql = "SELECT * FROM helps where vTitle LIKE '%".$keywords."%' LIMIT $start, $per_page";
	$db_data = $obj->MySQLSelect($sql);
	$endRecord = count($db_data);
	$newkeywords ="&keywords=".$keywords;
	$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$newkeywords;
	
			
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
   

    <div class="page-contant-inner">
     
      <!-- trips detail page -->
      <div class="static-page custom-error-page">	
		 <div class="custom-error-right-part">        
			<div class="custom-error-right-part-box">
				<?php   
					for($i=0;$i<=count($db_data);$i++){ ?>
            <ul><h4><a href="knowledgebase2_demo.php?id=<?php echo$db_data[$i]['iHelpsId']?>"><?php echo $db_data[$i]['vTitle']; ?></a></h4>		
              <li><?php echo $db_data[$i]['tDescription'];?></li>
             
            </ul>
            <?php } ?>
            <div class="pagination-kk">
            <?php include('admin/pagination_n.php'); ?>
            </div>
          </div>
        </div>
		
      </div>
     
      </div>
     <div style="clear:both;"></div>      
    </div>
  </div>
  <!-- home page end-->
  <!-- footer part -->
  <?php include_once('footer/footer_home.php');?>
  <!-- End:contact page-->
  <div style="clear:both;"></div>
</div>
<!-- footer part end -->
<!-- Footer Script -->
<?php include_once('top/footer_script.php');?>
<!-- End: Footer Script -->
</body>
<script>
	
</script>
</html>
