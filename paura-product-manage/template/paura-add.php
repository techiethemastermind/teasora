<?php

if( !class_exists('PPM_Add') ) {
	
	class PPM_Add{
		
		public function __construct(){
			add_action('pa_output',array(&$this,'output'),10,4);
			wp_enqueue_script('media-upload'); //Provides all the functions needed to upload, validate and give format to files.
			wp_enqueue_script('thickbox'); //Responsible for managing the modal window.
			wp_enqueue_style('thickbox'); //Provides the styles needed for this window.
			//wp_enqueue_script('jquery');
			//wp_enqueue_media();
		}
		
		public function output(){

?>

			
			<div id="tab2" class="tab">
				<h3>Design Manage
					<a href="#" id="butt-add" class="button" style="display: inline-block; top: -5px; position: relative; margin-left: 20px;">Add New</a>
				</h3>
				<div class="dv-Wraper">
					<table id="tbl-design" class="wp-list-table widefat fixed striped posts">
						<thead>
							<tr>
								<th width="150px">Design image</th>
								<th>Design name<span class="count">(<?php echo $count ?>)</span></th>
							</tr>
						</thead>
						<?php 
							self::view_design_list();
							self::add_new();
						?>
					</table>

				</div>
				<div class="dv-pro" style="margin: 25px 5px;">
					<span id="sel-brand"></span>
					<span id="sel-product"></span>
					<input type="button" id="add-product" class="button" value="add to my product" />
				</div>

			</div>
			
<?php
		}
		
		public function view_design_list(){
?>
		<tbody id="disign-list">
			<tr class="tr-disign">
				<td><span class="design-view">+</span></td>
				<td style="vertical-align: middle;">Design name</td>
			</tr>
		</tbody>
<?php
		}
		
		public function add_new(){
?>
		<tbody id="add-new" class="cls-the-list" style="display:none;">
			<tr class="dv-design-details" style="display:true;">
				<td>
					<div class="dv-design-img" >
						<span id='design-image' class="design-img">+</span>
						<h4>Design</h4>
					</div>
				</td>
				<td>
					<div class="dv-design-info" >
						<h4 style="display:inline;">Design Name: </h4><input id="design-name" type="text" class="text" size="25" placeholder="insert design name!" /><input id="image_path" type="hidden" class="text" size="25" placeholder="insert design name!" />
						<h4>Design Description</h4>
						<textarea id="design-description" rows="4" cols="50" class="text" placeholder="insert design description!" ></textarea>
					</div>
					<div class="dv-target">
						<table id="tbl-target" class="wp-list-table widefat fixed striped posts">

								<thead>
									<tr>
										<td class="manage-column column-cb check-column">
											<input class="check" type="checkbox" />
										</td>
										<th>ALL</th>
									</tr>
								</thead>
								<tbody id="target-list">
									<tr>
										<th class="manage-column column-cb check-column">
											<input id="chk-men" class="check" type="checkbox" />
										</th>
										<td>Men</td>
									</tr>
									<tr>
										<th class="manage-column column-cb check-column">
											<input id="chk-women" class="check" type="checkbox" />
										</th>
										<td>Women</td>
									</tr>
									<tr>
										<th class="manage-column column-cb check-column">
											<input id="chk-kids" class="check" type="checkbox" />
										</th>
										<td>Kids</td>
									</tr>
									<tr>
										<th class="manage-column column-cb check-column">
											<input id="chk-infants" class="check" type="checkbox" />
										</th>
										<td>Infants</td>
									</tr>
								</tbody>
							</table>
					</div>
				</td>
			</tr>
			<tr><td id="tr-men" colspan="2"></td></tr>
			<tr><td id="tr-women" colspan="2"></td></tr>
			<tr><td id="tr-kids" colspan="2"></td></tr>
			<tr><td id="tr-infants" colspan="2"></td></tr>
		</tbody>
<?php
		}
		
		public function get_pro_list($tar_pros,$tar){
			
			$html = "";
			foreach($tar_pros as $product){
				$html .= "<tr id='$product->ID' class='product-row'>
				<th class='manage-column column-cb check-column'><input type='checkbox' /></th>
				<td class='pro-template'>".'<img src="'.get_site_url().'/wp-content/uploads/paura-mockups/'.$product->template_name.'" height="60px" width="60px"></img>'."</td>
				<td class='pro-brand-name'>$product->brand_name</td>
				<td class='pro-name'>$product->product_name</td>
				<td class='pro-cost'>$product->price</td>
				<td class='pro-profit'>$product->profit</td>
				<td class='pro-colors'><a><h4>view</h4></a></td>
				<td class='pro-for'>";
				$array_some = json_decode($product->target,true);
				if(!is_null($array_some)){
					foreach($array_some as $some){
						$html .= $some.",";
					}
				}
					
				$html .= "</td></tr>";
				$html .= self::get_list_content($product,$tar);
			}
			return $html;
		}
		
		public function get_list_content($product,$tar){
			global $wpdb;
			$cs = $product->color_ids;
			$cs_array = json_decode($cs,true);
			$color_array = array_keys($cs_array);
			$data_colors = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."paura_color");
			$data_sizes = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."paura_size WHERE size_group='Adult'");
			$html = "<tr id='hid-$tar-$product->ID' class='$product->ID hidden-row' style='display:none;'><td colspan=8>";
			$html .= "<div style='display:inline-block;margin-left:30px;'>
					<h4>Product Description</h4><textarea rows='4' cols='90' >$product->product_description</textarea>
				</div>
				<div class='edit' style='display:inline-block; margin-left:20px;'><table>";
			
			if(count($color_array)>0){
				foreach($color_array as $color){
					foreach($data_colors as $data_color){
						if($data_color->color_id == $color){
							if($data_color->color_mark == "light"){
								$light = "selected";
							}else{
								$dark = "selected";
							}
							$cdiv = "<div class='color-rect' name='$data_color->color_name' style='background-color:#$data_color->color_code'></div><select class='select sel-color'><option value='Light' $light >Light</option><option value='Dark' $dark >Dark</option></select>";
							$html .= "<tr name='$data_color->color_name'><td>$cdiv</td><td>$data_color->color_name</td><td>";
							foreach($cs_array[$color] as $size_id){
								
								foreach($data_sizes as $size){
									if($size->size_id == $size_id){
										
										$html .= "<span class='wrap-size'><span class='span-size'>$size->size_name</span><span class='size-close'>X</span></span>";
									}
								}
							}
							$html .= "</td><td><input type='button' class='button' data='$data_color->color_id' value='del'/></td></tr>";
							break;
						}
					}	
				}
			}
			$html .= "</table></div>
			<h4 style='padding-left: 45px;'><input type='button' class='button' value='Save' data='$tar' /></h4><input id='$product->ID-hidden' type='text' value='$cs' size='100'/>";
			return $html;
		}
	
	}
	
}
new PPM_Add();
?>