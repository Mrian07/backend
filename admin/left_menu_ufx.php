<!-- MENU SECTION -->
<section class="sidebar">

    <!-- Sidebar -->
    <div id="sidebar" class="test" >
        <nav class="menu">
            <ul class="sidebar-menu">
                <!-- Main navigation -->
                
                <?php if($_SESSION['sess_iGroupId'] == '2') { ?>		
                    <?php if(RIIDE_LATER == 'YES'){ ?>
                        <li class="<?= (isset($script) && $script == 'booking') ? 'active' : ''; ?>"><a href="add_booking.php"><i class="fa fa-taxi1" style="margin:2px 0 0;"><img src="images/manual-taxi-icon.png" alt="" /></i> <span>Manual Taxi Dispatch</span> </a></li>
                    <?php } ?>
                    <?php if(RIIDE_LATER == 'YES'){ ?>
                        <li class="<?= (isset($script) && $script == 'CabBooking') ? 'active' : ''; ?>"><a href="cab_booking.php"><i aria-hidden="true" class="icon-book1" style="margin:2px 0 0;"><img src="images/ride-later-bookings.png" alt="" /></i> <span>Ride Later Bookings</span> </a></li>
                        <li class="<?= (isset($script) && $script == 'LiveMap') ? 'active' : ''; ?>"><a href="map.php"><i aria-hidden="true" class="icon-map-marker1" style="left:5px;"><img src="images/god-view-icon.png" alt="" /></i> <span>God's View</span> </a></li>
                    <?php } ?>
                    <!-- If groupId = 3 -->	
                    <?php }else if($_SESSION['sess_iGroupId'] == '3') { ?>
                
                <li class="<?= (isset($script) && $script == 'Trips') ? 'active' : ''; ?>"><a href="trip.php"><i aria-hidden="true" class="fa fa-exchange1" style="margin:2px 0 0;"><img src="images/trips-icon.png" alt="" /></i> <span>Trips</span> </a></li>
                <li class="treeview <?= (isset($script) && ($script == 'Payment Report' || $script == 'referrer' || $script == 'Wallet Report')) ? 'active' : ''; ?>"><a href="#" title="" class="expand "><i class="icon-cogs1" style="margin:3px 0 0;"><img src="images/reports-icon.png" alt="" /></i><span>Reports</span></a>
                    <ul class="treeview-menu menu_drop_down">
                        <li class=""><a href="payment_report.php"><i class="icon-money"></i> Payment Report</a></li>
                        <?php if($REFERRAL_SCHEME_ENABLE == 'Yes'){ ?>
                        <li class=""><a href="referrer.php"><i aria-hidden="true" class="fa fa-hand-peace-o"></i> Referral Report</a></li>	
                        <?php } ?>
			<?php if($WALLET_ENABLE == 'Yes'){ ?>
                        <li class=""><a href="wallet_report.php"><i aria-hidden="true" class="fa fa-google-wallet"></i> User Wallet Report</a></li> 	
                        <?php } ?>
                        <li class=""><a href="driver_pay_report.php"><i class="icon-money"></i> Driver Payment Report</a></li>
                    </ul>
                </li> 
                    
		<?php }else{ ?>	
		<li class="<?= (!isset($script) ? 'active' : ''); ?>"><a href="dashboard.php" title=""> <i class="fa fa-tachometer" aria-hidden="true"></i><span>Dashboard</span></a> </li>
                <li class="<?= (isset($script) && $script == 'Admin') ? 'active' : ''; ?>"> <a href="admin.php" title=""><i class="icon-user1">
                <img src="images/icon/admin-icon.png" alt="" /></i> <span>Admin</span> </a></li>
                <li class="<?= (isset($script) && $script == 'Company') ? 'active' : ''; ?>" id="dispatch_li"><a href="company.php"><i aria-hidden="true" class="fa fa-building-o" style="margin:2px;"></i><span>Company</span></a> </li>
                <li class="treeview <?=(isset($script) && $script == 'Driver' || $script == 'Vehicle')?'active':'';?>"><a href="#" title="" class="expand"><i class="icon-cogs1" style="margin:2px 0 0;"><img src="images/icon/driver-icon.png" alt="" /></i><span><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></span></a>
                        <ul class="treeview-menu menu_drop_down">
                            <li class=""><a href="driver.php"><i class="icon-money"></i> Driver</a></li>
                            <li class=""><a href="vehicles.php"><i aria-hidden="true" class="fa fa-taxi"></i> Vehicle</a></li>
                        </ul>
                </li>
                <li class="treeview <?=(isset($script) && $script == 'VehicleCategory' ||  $script == 'VehicleType')?'active':'';?>"><a href="#" title="" class="expand "><i class="icon-cogs1" style="margin:3px 0 0;"><img src="images/reports-icon.png" alt="" /></i><span> Washing Service</span></a>
                        <ul class="treeview-menu menu_drop_down">
                            <li class=""><a href="vehicle_category.php"><i class="icon-money"></i> Service Category </a></li>
                            <li class=""><a href="vehicle_type.php"><i aria-hidden="true" class="fa fa-taxi"></i> <?php echo $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?></a></li>
                        </ul>
                </li>
                
                <li class="<?= (isset($script) && $script == 'Rider') ? 'active' : ''; ?>"><a href="rider.php"><i class="icon-group1" style="margin:2px 0 0;"><img src="images/rider-icon.png" alt="" /></i> <span>Rider</span> </a></li>
                <li class="<?= (isset($script) && $script == 'booking') ? 'active' : ''; ?>"><a href="add_booking.php"><i class="fa fa-taxi1" style="margin:2px 0 0;"><img src="images/manual-taxi-icon.png" alt="" /></i> <span>Manual Taxi Dispatch</span> </a></li>
                <li class="<?= (isset($script) && $script == 'Trips') ? 'active' : ''; ?>"><a href="trip.php"><i aria-hidden="true" class="fa fa-exchange1" style="margin:2px 0 0;"><img src="images/trips-icon.png" alt="" /></i> <span>Trips</span> </a></li>
                <li class="<?= (isset($script) && $script == 'CabBooking') ? 'active' : ''; ?>"><a href="cab_booking.php"><i aria-hidden="true" class="icon-book1" style="margin:2px 0 0;"><img src="images/ride-later-bookings.png" alt="" /></i> <span>Ride Later Bookings</span> </a></li>
                <li class="<?= (isset($script) && $script == 'Coupon') ? 'active' : ''; ?>"><a href="coupon.php"><i aria-hidden="true" class="fa fa-product-hunt1" style="margin:2px 0 0;"><img src="images/promo-code-icon.png" alt="" /></i> <span>PromoCode</span> </a></li>
                <li class="<?= (isset($script) && $script == 'LiveMap') ? 'active' : ''; ?>"><a href="map.php"><i aria-hidden="true" class="icon-map-marker1" style="left:5px;"><img src="images/god-view-icon.png" alt="" /></i> <span>God's View</span> </a></li>
                <li class="<?= (isset($script) && $script == 'Heat Map') ? 'active' : ''; ?>"><a href="heatmap.php"><i aria-hidden="true" class="fa fa-header1" style="left:5px;"><img src="images/heat-icon.png" alt="" /></i><span>Heat View</span></a></li>
		<li class="<?= (isset($script) && $script == 'Review') ? 'active' : ''; ?>"><a href="review.php"><i class="icon-comments1" style="left:7px;"><img src="images/reviews-icon.png" alt="" /></i> <span>Reviews</span> </a></li>
                
                <li class="treeview <?= (isset($script) && ($script == 'Payment_Report' || $script == 'referrer' || $script == 'Wallet Report')) ? 'active' : ''; ?>"><a href="#" title="" class="expand "><i class="icon-cogs1" style="margin:3px 0 0;"><img src="images/reports-icon.png" alt="" /></i><span>Reports</span></a>
                    <ul class="treeview-menu menu_drop_down">
                        <li class=""><a href="payment_report.php" class="<?= (isset($script) && ($script == 'Payment_Report' )) ? 'sub_active' : ''; ?>"><i class="icon-money"></i> Payment Report</a></li>
                        <?php if($REFERRAL_SCHEME_ENABLE == 'Yes'){ ?>
                        <li class=""><a href="referrer.php" class="<?= (isset($script) && ($script == 'referrer' )) ? 'sub_active' : ''; ?>"><i aria-hidden="true" class="fa fa-hand-peace-o"></i> Referral Report</a></li>
                        <?php } ?>
						<?php if($WALLET_ENABLE == 'Yes'){ ?>
                        <li class=""><a href="wallet_report.php" class="<?= (isset($script) && ($script == 'Wallet Report' )) ? 'sub_active' : ''; ?>"><i aria-hidden="true" class="fa fa-google-wallet"></i> User Wallet Report</a></li> 	> 	
                        <?php } ?>
                        <li class=""><a href="driver_pay_report.php"><i class="icon-money"></i> Driver Payment Report</a></li>
                    </ul>
                </li>
                
                <li class="treeview <?= (isset($script) && 
				($script == 'General' || 
				$script == 'email_templates' || 
				$script == 'Document Master' || 
				$script == 'Currency' || 
				$script == 'seo_setting' || 
				$script == 'language_label' || 
				$script == 'language_label_other'
				)) ? 'active' : ''; ?>"><a href="#" title="" class="expand"><i class="icon-cogs1" style="margin:2px 0 0; left:9px;"><img src="images/settings-icon.png" alt="" /></i> <span>Settings</span> </a>
                    <ul class="treeview-menu menu_drop_down">
                          <li class=""><a href="general.php" class="<?= (isset($script) && ($script == 'General' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> General </a></li>
                        <li class=""><a href="email_template.php" class="<?= (isset($script) && ($script == 'email_templates' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> Email Templates </a></li>
						<li><a href="document_master_list.php" class="<?= (isset($script) && ($script == 'Document Master' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i>Manage Documents</a></li>	
                        <li class="treeview <?= (isset($script) && ($script == 'language_label' || $script == 'language_label_other')) ? 'active' : '';?>"><a href="#" title="" ><i class="icon-angle-right"></i> Language Label</a>
                            <ul class="treeview-menu menu_drop_down">
                                <li><a href="languages.php" class="<?= (isset($script) && ($script == 'language_label' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> General Label </a></li>
                                <li><a href="languages_admin.php" class="<?= (isset($script) && ($script == 'language_label_other' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> Ride Label </a></li>
                            </ul>
                        </li>
                        <li><a href="currency.php" class="<?= (isset($script) && ($script == 'Currency' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> Currency</a></li>
                        <li><a href="seo_setting.php" class="<?= (isset($script) && ($script == 'seo_setting' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> SEO Settings</a></li>
                    </ul>
                </li>
				
                <li class="treeview <?= (isset($script) && 
				($script == 'Make' || 
				$script == 'Model' || 
				$script == 'country' || 
				$script == 'page' || 
				$script == 'Faq' || 
				$script == 'faq_categories'||
				$script == 'home_driver' || 
				$script == 'Push Notification' || 
				$script == 'Back-up'
				)) ? 'active' : ''; ?>"><a href="#" title="" class="expand"><i class="fa fa-wrench"></i> <span>Utility</span> </a>
                    <ul class="treeview-menu menu_drop_down">
                        <li><a href="country.php" class="<?= (isset($script) && ($script == 'country' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> Country</a></li>
                        <li><a href="page.php" class="<?= (isset($script) && ($script == 'page' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> Pages</a></li>
                        <li><a href="faq.php" class="<?= (isset($script) && ($script == 'Faq' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> Faq</a></li>
                        <li><a href="faq_categories.php" class="<?= (isset($script) && ($script == 'faq_categories' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> Faq Categories</a></li>
                        <li><a href="home_driver.php" class="<?= (isset($script) && ($script == 'home_driver' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> Our Drivers</a></li>
                        <li><a href="send_notifications.php" class="<?= (isset($script) && ($script == 'Push Notification' )) ? 'sub_active' : ''; ?>"><i class="fa fa-globe"></i> Send Push-Notification</a></li>
                        <li><a href="backup.php" class="<?= (isset($script) && ($script == 'Back-up' )) ? 'sub_active' : ''; ?>"><i class="icon-angle-right"></i> DB Backup</a></li>
                    </ul>
                </li>
		<?php } ?>
                <li><a href="logout.php"><i class="icon-signin1" style="margin:2px 0 0;"><img src="images/logout-icon.png" alt="" /></i><span>Logout</span></a> </li>
            </ul>
            <!-- /main navigation -->
    </div>
    <!-- /sidebar -->
</section>
<!--END MENU SECTION -->