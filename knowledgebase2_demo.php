<?
	include_once("common.php");
	//error_reporting(E_ALL);
	global $generalobj;
	$script="About Us";
	$id = isset($_REQUEST['id'])?$_REQUEST['id']:"";
	$meta = $generalobj->getStaticPage(1,$_SESSION['sess_lang']);
	 $prevlink = isset($_REQUEST['prevlink'])?$_REQUEST['prevlink']:'';
	 $id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
	$sql1 = "select * from helps where  iHelpsId ='".$id."' and eStatus != 'Inactive'"; 
	$db_hepls = $obj->MySQLSelect($sql1);
			
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
    <div class="breadcrumbs-inner">
    <span><a href="knowledgebase_demo.php">Home ></a><?php echo $prevlink;?> Panel</span>
    <b><input name="Search" type="text" placeholder="Search" onKeyup ="searchdata(this.value,'<?php echo $id;?>');"></b>
    </div>
    </div>

    <div class="page-contant-inner">
     
      <!-- trips detail page -->
      <div class="static-page custom-error-page">
     
	  <?php 
		 if(!empty($db_hepls)){ ?>
			  <div class="custom-error-right-part" id="hlstitle">
				  <h3><?php echo $db_hepls[0]['vTitle']; ?></h3>
				  <div class="custom-error-right-part-box">
				  <p><?php echo $db_hepls[0]['tDescription']; ?></p>
						
				  </div>      
			</div>		 
		<?php  } ?>
     
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
	function searchdata(keywords,id){
		
		var request = $.ajax({
				type: "POST",
				url: 'change_searchdata.php',
				data: {keywords: keywords,id:id},
				success: function (dataHtml)
				{
					$('#hlstitle').html(dataHtml);
				}
			});
		}
</script>
</html>
