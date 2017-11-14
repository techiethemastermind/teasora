<?php

if( !class_exists('PPM_Ajax') ) {
	
	class PPM_Ajax{

		public function __construct(){
			add_action('wp_ajax_save_paura_product',array( &$this, 'paura_pro_save'),10,6);
			add_action('wp_ajax_update_color_set',array( &$this, 'paura_color_update'),10,7);
			add_action('wp_ajax_get_paura_product',array( &$this, 'paura_pro_get'),10,7);
			add_action('wp_ajax_save_product',array( &$this, 'product_save'),10,7);
		}
		
		public function product_save(){
			$data = $_POST("data");
			$uploadDir = 'wp-content/uploads/paura-mockups/';
			$siteurl = get_option('siteurl');
			$thumbnail = 'paura-mockups/' . $name;
			$filename = 'paura-mockups/' . $name;
			$wp_filetype = wp_check_filetype($filename, null);
			$attachment = array(
						'post_author' => 1, 
						'post_date' => current_time('mysql'),
						'post_date_gmt' => current_time('mysql'),
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => $filename,
						'comment_status' => 'closed',
						'ping_status' => 'closed',
					'post_content' => '',
					'post_status' => 'inherit',
						'post_modified' => current_time('mysql'),
						'post_modified_gmt' => current_time('mysql'),
						'post_parent' => $post_id,
						'post_type' => 'attachment',
						'guid' => $siteurl.'/'.$uploadDir.$name
			);

			$attach_id = wp_insert_attachment( $attachment, $filename, $post_id );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $thumbnail );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			// add featured image to post
			add_post_meta($post_id, '_thumbnail_id', $attach_id);
			
		}
		
		public function paura_pro_get(){
			
			$tar = $_POST["target"];
			global $wpdb;
			$tar_pros = $wpdb->get_results("SELECT * FROM (SELECT a.brand_name, b.*  FROM ".$wpdb->prefix."paura_brand AS a
				LEFT JOIN (SELECT * FROM ".$wpdb->prefix."paura_product WHERE target LIKE '%$tar%') AS b
				ON a.brand_id = b.brand_id) AS c WHERE NOT(c.ID='null')");
			$html = '<table id="tbl-pro-'.$tar.'" class="wp-list-table widefat fixed striped postst">';
			$html .= PPM_Add::get_pro_list($tar_pros,$tar);

			echo $html.'</table>';
			die;
		}
		
		public function paura_color_update(){
			
			//
			global $wpdb;
			$data = $_POST['data'];
			//var_dump($data);exit;
			$updata = array('color_name'=>$data['cname'],'color_price'=>$data['cprice'],'color_mark'=>$data['mark']);
			$rlt = $wpdb->update(PAUTBL_COLOR,$updata,array('color_id'=>$data['cid']));
			if($rlt == true){
				echo "Success!";
			}else{
				echo "Fail!";
			}
			die;
		}
		
		public function paura_pro_save(){
			
			//get posts
			$data = $_POST['data'];
			$colors = json_encode($data['colors'],JSON_FORCE_OBJECT);
			$tar = json_encode($data['target'],JSON_FORCE_OBJECT);
			$dcolor = json_encode($data['dcolor'],JSON_FORCE_OBJECT);
			//var_dump($tar);
			//insert to paura product table
			global $wpdb;
			
			$updatedata = array('product_name'=>$data['pname'],'price'=>$data['pcost'],'template_name'=>$data['tname'],
			'color_price'=>$data['cprice'],'white_price'=>$data['wprice'],'target'=>$tar,'color_ids'=>$colors,'default_color'=>$dcolor);
			
			$rlt = $wpdb->update(PAUTBL_PRODUCT, $updatedata, array('product_id'=>$data['pid']));
			
			if($rlt == true){
				echo "Success!";
			}else{
				echo "Fail!";
			}
			die;
		}
		public function add_myproduct(){
			
			$product_data = stripslashes($_POST["data"]);
			$product_array = json_decode($product_data,true);
			$product_colors = $product_data["colors"];
			$procolors_array = json_decode($product_colors,true);
			$product_sizes = $product_data("sizes");
			$prosizes_array = json_decode($product_sizes,true);
			
			//var_dump($procolors_array);
			
			//--- parent post--//
			$post = array(
			 'post_title'   => $product_array["product_name"],
			 'post_content' => "product post content goes here...",
			 'post_status'  => "publish",
			 'post_excerpt' => "product excerpt content...",
			 'post_name'    => $product_array["product_name"], //name/slug
			 'post_type'    => "product"
			);
			
			//-- insert product --//
			$new_post_id = wp_insert_post($post);
			
			//-- add tag to product --//
			wp_set_object_terms( $new_post_id, 25, 'product_tag');
			
			//-- set product values --//
			update_post_meta( $new_post_id, '_stock_status', 'instock');
			update_post_meta( $new_post_id, '_weight', "0.06" );
			update_post_meta( $new_post_id, '_sku', "tshirt".$product_array["product_name"]);
			update_post_meta( $new_post_id, '_stock', "100" );
			update_post_meta( $new_post_id, '_visibility', 'visible' );
			
			//-- insert attribute -- //
			
			$avail_colors = array(
			'white',
			'black',
			'red',
			'hess',
			);
			$avail_sizes = array(
			'white',
			'black',
			'red',
			'hess',
			);
			wp_set_object_terms($new_post_id, $avail_colors, 'Color');
			wp_set_object_terms($new_post_id, $avail_sizes, 'Size');
			$thedata = Array('Color'=>Array(
				'name'=>'Color',
				'value'=>'',
				'is_visible' => '1', 
				'is_variation' => '1',
				'is_taxonomy' => '1'
			),
			'Size'=>Array(
				'name'=>'Size',
				'value'=>'',
				'is_visible' => '1', 
				'is_variation' => '1',
				'is_taxonomy' => '1'
			));
			update_post_meta( $new_post_id,'_product_attributes',$thedata);
			
			//-- insert variations post for colors and sizes--//
			
			
			/*
			$post_id = wp_insert_post( array(
				'post_type' => 'product',
				'post_title' => 'sss',
				'post_excerpt' => 'sss',
				'post_content' => 'ssss',
				'post_status' => 'publish',
				'post_author' => 'uerr'
			));
			*/
			die;
		}
	
	}
}
new PPM_Ajax();
?>