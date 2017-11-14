<?php

if( !class_exists('PPM_Edit') ) {
	
	class PPM_Edit{
		
		public function __construct(){
			add_action('pe_output',array(&$this, 'output'),10,3);
		}
	
		public function output(){
			global $wpdb;
			
			$products = array();
			$products = $wpdb->get_results("SELECT * FROM (SELECT a.brand_name, b.*  FROM ".$wpdb->prefix."paura_brand AS a
											LEFT JOIN ".$wpdb->prefix."paura_product AS b
											ON a.brand_id = b.brand_id) AS c WHERE NOT(c.ID='null')");
			$count = count($products);
			
			$ld = $_POST["load"];
			if($ld=='load'){
				self::insert_brands();
				self::insert_colors();
				self::insert_sizes();
				self::insert_products();
			}

?>
			
		<div id="tab1" class="tab active">
            <h3><?php //__('Product_manage','paura_design_manage') ?>Product Manage</h3>
			<ul class="subsubsub">
				<li class="all"><a href="#" class="current"><?php //__('All','paura_design_manage') ?> All <span class="count">(<?php echo $count ?>)</span></a></li>
				<li class="btt"><form id="load" method="post"><a href="#" id="data-load" class="submit"><span class="">load</span></a><input type="hidden" name="load" value="load" /></form></li>
			</ul>

			<table class="wp-list-table widefat fixed striped posts">
				<thead>
				<tr class='set-tbl-header'>
					<td class="manage-column column-cb check-column">
							<input type="checkbox">
					</td>
					<th class="thumb-nail">Image</th>
					<th class="brand-name">Brand</th>
					<th class="product-name">Product name</th>
					<th class="cost">Cost</th>
					<th class="profit">Profit Margin</th>
					<th class="colors">Color Setting</th>
					<th class="forsome">For Someone</th>
				</tr>
				</thead>

				<tbody id="the-list" class="cls-the-list">
					<?php
						//echo self::get_products_html($products);
					?>
				</tbody>
			</table>
        </div>
			
<?php
		}
		
		//functions
		
		function insert_brands(){
			global $wpdb;
			$ss = $wpdb->get_results( 'SELECT COUNT(*) as nums FROM '.PAUTBL_BRAND );
			if($ss[0]->nums == "0"){
				$brands_array = self::get_datalist('listbrands');
				if($brands_array!=0){
					foreach($brands_array as $brands){
						$wpdb->insert(PAUTBL_BRAND, $brands, array('%s','%s'));
					}
				}
			}
		}
		function insert_colors(){
			global $wpdb;
			$ss = $wpdb->get_results( 'SELECT COUNT(*) as nums FROM '.PAUTBL_COLOR );
			if($ss[0]->nums == "0"){
				$colors_array = self::get_datalist('listcolors');
				if($colors_array!=0){
					foreach($colors_array as $colors){
						$wpdb->insert(PAUTBL_COLOR, $colors, array('%s','%s','%s','%s','%s'));
					}
				}
			}
		}
		function insert_sizes(){
			global $wpdb;
			$ss = $wpdb->get_results( 'SELECT COUNT(*) as nums FROM '.PAUTBL_SIZE );
			if($ss[0]->nums == "0"){
				$sizes_array = self::get_datalist('listsizes');
				if($sizes_array!=0){
					foreach($sizes_array as $sizes){
						
						$wpdb->insert(PAUTBL_SIZE, $sizes, array('%s','%s','%s','%s'));
					}
				}
			}
		}
		function insert_products(){
			global $wpdb;
			$ss = $wpdb->get_results( 'SELECT COUNT(*) as nums FROM '.PAUTBL_PRODUCT );
			if($ss[0]->nums == "0"){
				$products_array = self::get_datalist('listproducts');
				if($products_array!=0){
					foreach($products_array as $products){
						$brand_id = $products["brand_id"];
						$product_id = $products["product_id"];
						$product_name = $products["product_name"];
						$wprice = $products["price"];
						$cprice = $products["color_price"];
						$product_description = $products["inventory_description"];
						$color_ids = json_encode($products["colors"]);
						$data = array('brand_id'=>$brand_id,'product_id'=>$product_id,'product_name'=>$product_name,
						'product_description'=>$product_description,'white_price'=>$wprice,'color_price'=>$cprice,
						'color_ids'=>$color_ids);
						$wpdb->insert(PAUTBL_PRODUCT, $data);
					}
				}else{
					echo "error";
				}
			}
		}
		
		public function get_datalist($method){
			
			$url = "http://www.api.printaura.com/api.php";
			$postfields["key"] = "A7046bOSRJ1bWrN9Ax472M9D1Byu3Ixq";
			$postfields["hash"] = "t3p40qtcUKmXc16q7PkXQxaZG63mhOthnH2cf8Q9762uXF3gb6QoGfWYUDs697C3";
			$postfields["method"] = $method;

			// Send the query to PrintAura API using CURL

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
			$data = curl_exec($ch);
			$data_array = json_decode($data, true);
			
			if($data_array["status"]){
				return $data_array["results"];
			}else{
				return 0;
			}
			curl_close($ch);

		}

		public function get_products_html($products){
			$html = "";
			foreach($products as $product){
				if(!$product->template_name){
					$product->template_name = $product->brand_name."-".$product->product_name."-white.png";
				}
				if(!$product->price){
					$product->price = "10";
				}
				if(!$product->profit){
					$product->profit = "1";
				}
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
				$html .= self::get_cont_html($product);
			}
			
			return $html;
		}

		public function get_cont_html($product){
			global $wpdb;
			
			$id = $product->ID;
			$name = $product->product_name;
			$template_name = $product->template_name;
			$cost = $product->price;
			$profit = $product->profit;
			$cprice = $product->color_price;
			$wprice = $product->white_price;
			$cs = $product->color_ids;
			$array_some = json_decode($product->target,true);
			$array_default_color = json_decode($product->default_color,true);
			if(!is_NUll($array_some)){
				
				foreach($array_some as $some){
					switch($some){
						case "Men":
							$attr_men = "checked";
							break;
						case "Women":
							$attr_women = "checked";
							break;
						case "Kids":
							$attr_kids = "checked";
							break;
						case "Infants":
							$attr_infants = "checked";
							break;
						default :
							break;
					}
				}
			}else{
				$attr_men = "checked";
			}

			$cs_array = json_decode($cs,true);
			$color_array = array_keys($cs_array);
			
			$data_colors = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."paura_color");
		
			$html = "<tr id='$id-$name' class='$id hidden-row' style='display:none;'><td colspan=8>";
			
			$html .= "<div class='edit'>
			<div class='edit-pro'>
				<h4>Product Name: </h4>".'<input type="text" class="input-butt proname" value="'.$name.'" size="20" />'
				."<h4>Product Cost: </h4><input type='text' class='input-butt procost' value='$cost' size='5' />
				<h4>Profit Margin: </h4><input type='text' class='input-butt profit' value='$profit' size='5' />
				<h4>Template Name: </h4>".'<input type="text" class="input-butt template" value="'.$template_name.'" size="40" />'."
			</div>
			<div class='edit-for'>
				<h4>Men: </h4><input name='Men' type='checkbox' $attr_men />
				<h4>Women: </h4><input name='Women' type='checkbox' $attr_women />
				<h4>Kids: </h4><input name='Kids' type='checkbox' $attr_kids />
				<h4>Infants: </h4><input name='Infants' type='checkbox' $attr_infants />
			</div>
			<div class='edit-color'>
				<h4 style='display:inline;'>Color Price(White): </h4><input name='wprice' type='text' value='$wprice' size='10' />
				<h4 style='display:inline;'>Color Price(Colors): </h4><input name='cprice' type='text' value='$cprice' size='10' />
				<h4 style='display:inline;'>Default Color: </h4><span class='default-color-name'>$array_default_color[0]</span><div class='color-rect-default' style='background-color:$array_default_color[1];'></div>
				
				<table class='$id tbl-color'>
					<thead>
						<tr>
							<th width='5%' style='text-align: center;'>Default</th>
							<th width='15%' style='padding-left: 30px;'>Color</th>
							<th width='15%' style='padding-left: 30px;'>Name</th>
							<th width='15%' style='padding-left: 30px;'>Price</th></tr>
					</thead><tbody>";
			if(count($color_array)>0){
				foreach($color_array as $color){
					foreach($data_colors as $data_color){
						if($data_color->color_id == $color){
							if($data_color->color_mark == "light"){
								$light = "selected";
							}else{
								$dark = "selected";
							}
							if(is_null($data_color->color_price)){
								$color_price = $cprice;
							}else{
								$color_price = $data_color->color_price;
							}
							$cdiv = "<div class='color-rect' name='$data_color->color_name' style='background-color:#$data_color->color_code'></div><select class='select sel-color'><option value='Light' $light >Light</option><option value='Dark' $dark >Dark</option></select>";
							$html .= "<tr name='$data_color->color_name'><th><input type='radio' name='$id-default' class='check' /></th><td>$cdiv</td><td><input type='text' value='$data_color->color_name' size='20' /></td><td><input type='text' value='$color_price' size='10' /></td><td><input type='button' class='button' data='$data_color->color_id' value='Update'/><input type='button' class='button' data='$data_color->color_id' value='del'/></td></tr>";
							break;
						}
					}
					
				}
			}
			$html .= "</tbody></table></div></div><div class='edit-set'><h4><input type='button' class='button' value='Set' /></h4><input id='$id-hidden' type='hidden' value='$cs' size='100'/></div>";
			$html .= "</td></tr><tr></tr>";
			return $html;
		}
		
	}
	
}
new PPM_Edit();
?>