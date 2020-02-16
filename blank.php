<?
include_once('common.php');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
	
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_BLANK_PAGE']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="keywords" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	
	
	
	
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
            <div class="inner" style="min-height:600px;">
                <div class="row">
                      
						<div class="col-lg-12">
						<h2><?=$langage_lbl['LBL_BLANK']; ?></h2>
						</div>
                   
                </div>
                <hr />
				
            </div>
		</div>
       <!--END PAGE CONTENT -->
    </div>
     <!--END MAIN WRAPPER -->

	<? include_once('footer.php');?>
	
</body>
	<!-- END BODY-->    
</html>
