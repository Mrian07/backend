<?php

	//print_r($_REQUEST);
	
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] == 'yes')
	{
		//$cookie_name = "edit";
		//$cookie_value = "Yes";
		//setcookie($cookie_name, $cookie_value, time() + (86400 * 30));
		$_SESSION['edita'] = 1;	
	}
	
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] == 'no'){
		//setcookie('edit', $cookie_value, time() - (86400 * 30));
		unset($_SESSION['edit']);
		$_SESSION['edita'] = "";
	}
	
	//echo "<pre>";
	//print_r($_COOKIE);
	//echo "<br>";
	//echo "<br>";
	//echo "<br>";echo "<br>";	
	//print_r($_SESSION); //die;
	//echo "</pre>";
	include_once("include/config.php");
  	include($templatePath."top/top_script.php");
?>
<?=$GOOGLE_ANALYTICS;?>
<input type="hidden" name="hdf_class" id="hdf_class" value="<?php echo $_SESSION['edita'];?>" />
<script>
	$(document).ready(function(){
		//alert('hi');
		var hdf_class=$("#hdf_class").val();
		if(hdf_class!="")
			{
			$("a").css({'border-width' : '1',
            'border-style' : '',
            'border-color' : 'red'});
				var setlink="<a href='http://192.168.1.131/cubetaxidev/admin/languages.php'>Edit</a>"
				$("a").append(setlink);
			}
	});
</script>