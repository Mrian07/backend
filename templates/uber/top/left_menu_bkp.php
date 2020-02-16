<?php
$curr_url = basename($_SERVER['PHP_SELF']);
//include 'common.php' ;
$user = $_SESSION["sess_user"];
if ($user == 'driver') {
     $sql = "select * from register_driver where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_data = $obj->sql_query($sql);
     if ($db_data[0]['vImage'] == "NONE" || $db_data[0]['vImage'] == '' ) {
          $db_data[0]['img'] = "";
     } else {

       $db_data[0]['img'] = $tconfig["tsite_upload_images_driver"] . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImage'];
     }
}
if ($user == 'company') {
  $sql = "select * from company where iCompanyId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_data = $obj->sql_query($sql);

     if ($db_data[0]['vImage'] == "NONE" || $db_data[0]['vImage'] =='') {
         $db_data[0]['img'] = "";
     } else {
       $db_data[0]['img'] = $tconfig["tsite_upload_images_compnay"] . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImage'];
     }
}
if ($user == 'rider') {
     $sql = "select * from register_user where iUserId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_data = $obj->sql_query($sql);
     if ($db_data[0]['vImgName'] != "NONE") {
          $db_data[0]['img'] = $tconfig["tsite_upload_images_passenger"] . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImgName'];
     } else {
          $db_data[0]['img'] = "";
     }
}  //echo "<pre>";print_r($db_data);echo "</pre>";
?>
<span id="shadowbox" onClick="menuClose()"></span>
<nav>
  <button id="navBtnShow" onClick="menuOpen()">
  <div></div>
  <div></div>
  <div></div>
  </button>
  <ul id="listMenu">
    <span class="desktop">
        <div class="menu-logo">
		 <section id="navBtn" class="navBtnNew navOpen" onClick="menuClose()">
		  <div></div>
		  <div></div>
		  <div></div>
		</section>
		<?php
		 if(($db_data[0]['img'] == '' || $db_data[0]['img'] == 'NONE') && ($user=="")){
        ?>
		<a href="index.php" class="logo-left"><img src="assets/img/menu-logo.png" alt=""></a>
		<label><a href="sign-in" class="<?php echo strstr($_SERVER['SCRIPT_NAME'],'/sign-in') || strstr($_SERVER['SCRIPT_NAME'],'/login-new')?'active':'' ?>">
		  <i aria-hidden="true" class="fa fa-user"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_SIGN_IN_TXT'];?></a></label>
		<?php }else{?>
		<strong><a href="index.php" class="logo-left"><img src="assets/img/user-img.png" alt=""></a></strong>
        <p>Demo Name</p>
	    <label><em class="uber-img"><img src="assets/img/setting-icon.png" alt=""></em></label>  
		<?php } ?>
        </div>
        <div class="menu-left-new">
          <ul>
          <li><a href="#"><b><img alt="" src="assets/img/my-profile-icon.png"></b><span>Profile</span></a></li>
          <li><a href="#"><b><img alt="" src="assets/img/payment-icon.png"></b><span>Payment</span></a></li>
          <li><a href="#"><b><img alt="" src="assets/img/support-icon.png"></b><span>Support</span></a></li>
          <li><a href="#"><b><img alt="" src="assets/img/emergency-icon.png"></b><span>Emergency Contact</span></a></li>
          <li><a href="#"><b><img alt="" src="assets/img/about-icon.png"></b><span>About Us</span></a></li>
          <li class="logout"><a href="#"><b><img alt="" src="assets/img/sign-out.png"></b><span>Logout</span></a></li>
          </ul>
		  <div style="clear:both;"></div>
        </div>
    </span>
    <span class="mobile">
        <div class="menu-logo">
		<section id="navBtn" class="navBtnNew navOpen" onClick="menuClose()">
		  <div></div>
		  <div></div>
		  <div></div>
		</section>
		<img src="assets/img/menu-logo.png" alt=""></div>
        <!-- Top Menu Mobile -->
		<div class="menu-left-new">
        <?php
              if($user == 'driver'){
          ?>      
          <li><a href="profile" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><?=$langage_lbl['LBL_MY_PROFILE_HEADER_TXT']; ?></a></li>
          <li><a href="vehicle" class="<?=(isset($script) && $script == 'Vehicle')?'active':'';?>"><?=$langage_lbl['LBL_LEFT_MENU_VEHICLES']; ?></a></li>
          <li><a href="driver-trip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><?=$langage_lbl['LBL_LEFT_MENU_TRIPS']; ?></a></li>
          <li><a href="payment-request" class="<?=(isset($script) && $script == 'Payment Request')?'active':'';?>"><?=$langage_lbl['LBL_PAYMENT']; ?></a></li>
          <?php
              }
              else if($user == 'company'){
          ?>
              <li><a href="profile" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"> <?=$langage_lbl['LBL_MY_PROFILE_HEADER_TXT']; ?></a></li>
              <li><a href="driver" class="<?=(isset($script) && $script == 'Driver')?'active':'';?>"><?=$langage_lbl['LBL_DRIVER']; ?></a></li>
              <li><a href="vehicle" class="<?=(isset($script) && $script == 'Vehicle')?'active':'';?>"><?=$langage_lbl['LBL_LEFT_MENU_VEHICLES']; ?></a></li>
              <li><a href="company-trip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><?=$langage_lbl['LBL_LEFT_MENU_TRIPS']; ?></a></li>
          <?php
              }
              else if($user == 'rider'){
          ?>
              <li><a href="profile-rider" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><?=$langage_lbl['LBL_MY_PROFILE_HEADER_TXT']; ?></a></li>
              <li><a href="mytrip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><?=$langage_lbl['LBL_LEFT_MENU_TRIPS']; ?></a></li>
         <?php
              }
          ?>
          <!-- End Top Menu Mobile -->
        <li><a href="index.php" class="<?=(isset($script) && $script == 'Home')?'active':'';?>"><?=$langage_lbl['LBL_HOME']; ?></a></li>
        <li><a href="about-us" class="<?=(isset($script) && $script == 'About Us')?'active':'';?>"><?=$langage_lbl['LBL_ABOUT_US_TXT']; ?></a></li>
        <li><a href="help-center" class="<?=(isset($script) && $script == 'Help Center')?'active':'';?>"><?=$langage_lbl['LBL_HELP_CENTER']; ?></a></li>
        <li><a href="contact-us" class="<?=(isset($script) && $script == 'Contact Us')?'active':'';?>"><?=$langage_lbl['LBL_CONTACT_US_TXT']; ?></a></li>
        <?php
          if($user==""){
        ?>
        <li><a href="sign-in" class="<?=(isset($script) && $script == 'Login Main')?'active':'';?>"><?=$langage_lbl['LBL_LEFT_MENU_LOGIN']; ?></a></li>
        <b>
          <a href="sign-up-rider" class="<?=(isset($script) && $script == 'Rider Sign-Up')?'active':'';?>"><?=$langage_lbl['LBL_LEFTMENU_SIGN_UP_TO_RIDE']; ?></a>
          <a class="<?=(isset($script) && $script == 'Driver Sign-Up')?'active':'';?>" href="sign-up.php"><?=$langage_lbl['LBL_LEFTMENU_BECOME_A_DRIVER']; ?></a>
        </b>
        <?php
          }
          else {
        ?>
          <li><a href="logout"><?=$langage_lbl['LBL_LOGOUT']; ?></a></li>
          <div style="clear:both;"></div>
        
        <?php
            
          }
        ?>
        </div> 
    </span>
    
  </ul>
</nav>