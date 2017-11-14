<?php

if( !class_exists('PPM_Manage') ) {
	
	if (!defined('PLUGIN_DIR'))
		define( 'PLUGIN_DIR', dirname(__FILE__) );
	
	class PPM_Manage{
		
		public function __construct(){
			
			include_once(PLUGIN_DIR.'/paura-edit.php');
			include_once(PLUGIN_DIR.'/paura-add.php');
			
			add_action('pm_output',array(&$this,'output'),10,1);
			
			global $wpdb;
			if($wpdb->query( $wpdb->prepare("SHOW TABLES LIKE '%s'", PAUTBL_PRODUCT) ) == 0 ){
				
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
				$sql = "CREATE TABLE `".PAUTBL_PRODUCT."paura_product` (
						 `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
							  `brand_id` varchar(50) DEFAULT NULL,
							  `product_id` varchar(50) DEFAULT NULL,
							  `product_name` varchar(255) DEFAULT NULL,
							  `product_description` text,
							  `template_url` varchar(255) DEFAULT NULL,
							  `template_name` varchar(255) DEFAULT NULL,
							  `price` float DEFAULT NULL,
							  `color_ids` text,
							  `color_price` float DEFAULT NULL,
							  `white_price` float DEFAULT NULL,
							  `size_ids` text,
							  `profit` float DEFAULT NULL,
							  `target` varchar(255) DEFAULT NULL,
							  `default_color` varchar(200) DEFAULT NULL,
							  PRIMARY KEY (`ID`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8
						";
				dbDelta($sql);
			}
			if($wpdb->query( $wpdb->prepare("SHOW TABLES LIKE '%s'", PAUTBL_BRAND) ) == 0 ){
				
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
				$sql = "CREATE TABLE ".PAUTBL_BRAND." (
						  ID BIGINT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
						  brand_id varchar(100) DEFAULT NULL,  
						  brand_name varchar(255) DEFAULT NULL,		  
						  PRIMARY KEY (ID)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8";
				dbDelta($sql);
			}
			if($wpdb->query( $wpdb->prepare("SHOW TABLES LIKE '%s'", PAUTBL_COLOR) ) == 0 ){
				
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
				$sql = "CREATE TABLE ".PAUTBL_COLOR." (
						  `ID` int(5) unsigned NOT NULL AUTO_INCREMENT,
						  `color_id` varchar(50) DEFAULT NULL,
						  `color_name` varchar(50) DEFAULT NULL,
						  `color_code` varchar(50) DEFAULT NULL,
						  `color_group` varchar(50) DEFAULT NULL,
						  `color_mark` varchar(50) DEFAULT NULL,
						  `color_price` float DEFAULT NULL,
						  PRIMARY KEY (`ID`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8";
				dbDelta($sql);
			}
			if($wpdb->query( $wpdb->prepare("SHOW TABLES LIKE '%s'", PAUTBL_SIZE) ) == 0 ){
				
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				
				$sql = "CREATE TABLE ".PAUTBL_SIZE." (
						  `ID` int(5) unsigned NOT NULL AUTO_INCREMENT,
						  `size_id` varchar(50) DEFAULT NULL,
						  `size_name` varchar(50) DEFAULT NULL,
						  `size_group` varchar(50) DEFAULT NULL,
						  `plus_size_charge` varchar(50) DEFAULT NULL,
						  PRIMARY KEY (`ID`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8";
				dbDelta($sql);
			}
		}
		
		public function output(){
		
			?>
			
				<div class="tabs">
					<ul class="tab-links">
						<li class="active"><a href="#tab1">Setting</a></li>
						<li><a href="#tab2">Design Upload</a></li>
					</ul>
					<div class="tab-content">
						<?php
							do_action('pe_output');
							do_action('pa_output');
						?>
					</div>
				</div>
						
			<?php
		}
	}
}
new PPM_Manage();
?>