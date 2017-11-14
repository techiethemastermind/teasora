jQuery(document).ready(function($){
	
	var prorow_id;
	var brand_id;
	var product_id;
	var brand_name;
	var default_color_name;
	var default_color_code;
	var chk = 0;
	
	//initialize get_all_colorlist and all sizes;
	var colorlist = 0;
	var sizelist = 0;
	var product_info = 0;
	
	load_cs();

	function load_cs(){
		$.ajax({
			 type : "POST",
			 url : ajax_object.ajax_url,
			 data : {action: "load_colorlists"},
			 success: function(response) {
				colorlist = response;
			 }
		});
		$.ajax({
			 type : "POST",
			 url : ajax_object.ajax_url,
			 data : {action: "load_sizelists"},
			 success: function(response) {
				sizelist = response;
			 }
		});
	}
	
	$("#data-load").click(function(){
		$("#load").submit();
	});
	$("#butt-add").click(function(){
		if($(this).text()=="Add New"){
			$("#disign-list").attr('style','display:none;');
			$("#add-new").attr('style','display:true;');
			$(this).text("View List");
		}else{
			$("#disign-list").attr('style','display:true;');
			$("#add-new").attr('style','display:none;');
			$(this).text("Add New");
		}
		
	});
	$(".dv-design-img").click(function(){

		tb_show('Upload a Design', 'media-upload.php?type=image&amp;TB_iframe=true');
	});
	
	window.send_to_editor = function(html) {
		var image_url = $(html).attr('src');
		$("#design-image").empty();
		$("#design-image").append(html);
		$('#image_path').val(image_url);
		tb_remove(); // calls the tb_remove() of the Thickbox plugin
		//$('#submit_button').trigger('click');
	}
	$(".cls-the-list").on('click','.product-row',function(e){
		//e.preventDefault();
		prorow_id = $(this).attr('id');
		//alert($(this).css('background-color'));
		$style = 'background-color:' + $(this).css('background-color');
		if($("."+prorow_id+".hidden-row").attr("style")=="display:none;"){
			$("."+prorow_id+".hidden-row").attr('style','display:true;');
			$("."+prorow_id+".hidden-row").attr('style',$style);
			$selected = 1;
		}else{
			$("."+prorow_id+".hidden-row").attr('style','display:none;');
			$selected = 0;
		}
	});
	$(".tbl-color input[type='radio']").on('click',function(){
		$tr = $(this).parent().parent();
		default_color_name = $tr.attr('name');
		default_color_code = rgb2hex($tr.find('.color-rect').css('background-color'));
		$("."+prorow_id+".hidden-row .color-rect-default").attr('style','background-color:'+ default_color_code +';');
		$("."+prorow_id+".hidden-row .default-color-name").text(default_color_name);
		//alert(default_color_code);
	});
	$(".cls-the-list").on('click','.button',function(e){
		e.stopPropagation();
		$val = $(this).val();
		$tr = $(this).parent().parent();
		if($val == "del"){
			var color_id = $(this).attr("data");
			if(confirm("Delete " + $tr.attr('name') + "?")==true){
				$hidtext = $("#" + prorow_id + "-hidden").val();
				var jsonobj = $.parseJSON($hidtext);
				delete jsonobj[color_id];
				$("#" + prorow_id + "-hidden").val(JSON.stringify(jsonobj));
				$tr.remove();
			}else{
				return;
			}
		}else if($val == "Update"){
			
			var color_id = $(this).attr("data");
			// getting changed color name and color price
			$tds = $tr.find('td');
			$mark = $tds.eq(0).find("select").val();
			$color_name = $tds.eq(1).find("input").val();
			$color_price = $tds.eq(2).find("input").val();
			$data = {cid:color_id,cname:$color_name,cprice:$color_price,mark:$mark};
			$.ajax({
				type : "POST",
				url : ajax_object.ajax_url,
				data : {action:"update_color_set",data:$data},
				success : function(response){
					alert(response);
				}
			});

		}else if($val == "Set"){
			$product_name = $("."+prorow_id+".hidden-row .input-butt.proname").val();
			$product_cost = $("."+prorow_id+".hidden-row .input-butt.procost").val();
			$product_profit = $("."+prorow_id+".hidden-row .input-butt.profit").val();
			$product_template = $("."+prorow_id+".hidden-row .input-butt.template").val();
			
			//get checked target;
			var chkstr = [];
			$("."+prorow_id+".hidden-row .edit-for input:checked").each(function(){
				//chkstr.push({$(this).attr("name")});
				chkstr.push($(this).attr("name"));
			});
			//get default color;
			$default_color = [default_color_name,default_color_code];
			
			// color price
			$wprice = $("."+prorow_id+".hidden-row .edit-color input[name='wprice']").val();
			$cprice = $("."+prorow_id+".hidden-row .edit-color input[name='cprice']").val();
			
			$colors = $("#" + prorow_id + "-hidden").val();
			$colorjson = $.parseJSON($colors);

			$data = {pid:prorow_id,pname:$product_name,pcost:$product_cost,tname:$product_template,
					target:chkstr,dcolor:$default_color,wprice:$wprice,cprice:$cprice,colors:$colorjson};

			$.ajax({
				 type : "POST",
				 url : ajax_object.ajax_url,
				 data : {action:"save_paura_product",data:$data},
				 success: function(response) {
					alert(response);
				 }
			});
		}else if($val=="Save"){
			// get design image info:
			$design_path = $('#image_path').val();
			$design_name = $('#design-name').val();
			$design_description = $("#design-description").text();
			$product_id = prorow_id;
			$product_description = $("."+prorow_id+".hidden-row textarea").text();
			$tar = $(this).attr("data");
			$colors = $("#" + prorow_id + "-hidden").val();
			$data = {pid:$product_id,pd:$product_description,dn:$design_name,dp:$design_path,dd:$design-description,tar:$tar,cset:$colors};

			$.ajax({
				type:"POST",
				url:ajax_object.ajax_url,
				data:{action:"save_product",data:$data},
				success: function(response){
					alert(response);
				}
			});
		}
	});
	
	$("#tbl-target").on("click", "input", function(){
		$id = $(this).attr("id");
		
		if($id == "chk-men" && $(this).attr("checked")){
			$.ajax({
				 type : "POST",
				 url : ajax_object.ajax_url,
				 data : {action:"get_paura_product",target:"Men"},
				 success: function(response) {
					$("#tr-men").append(response);
				 }
			});
		}else{
			$("#tr-men").empty();
		}
		if($id == "chk-women" && $(this).attr("checked")){
			$.ajax({
				 type : "POST",
				 url : ajax_object.ajax_url,
				 data : {action:"get_paura_product",target:"Women"},
				 success: function(response) {
					$("#tr-women").append(response);
				 }
			});
		}else{
			$("#tr-women").empty();
		}
		if($id == "chk-kids" && $(this).attr("checked")){
			$.ajax({
				 type : "POST",
				 url : ajax_object.ajax_url,
				 data : {action:"get_paura_product",target:"Kids"},
				 success: function(response) {
					$("#tr-kids").append(response);
				 }
			});
		}else{
			$("#tr-kids").empty();
		}
		if($id == "chk-infants" && $(this).attr("checked")){
			$.ajax({
				 type : "POST",
				 url : ajax_object.ajax_url,
				 data : {action:"get_paura_product",target:"Infants"},
				 success: function(response) {
					$("#tr-infants").append(response);
				 }
			});
		}else{
			$("#tr-infants").empty();
		}
	});
	
	$('.tabs .tab-links a').on('click', function(e)  {
        var currentAttrValue = $(this).attr('href');
 
        // Show/Hide Tabs
        //$('.tabs ' + currentAttrValue).show().siblings().hide();
		$('.tabs ' + currentAttrValue).siblings().slideUp(400);
		$('.tabs ' + currentAttrValue).delay(400).slideDown(400);
 
        // Change/remove current tab to active
        $(this).parent('li').addClass('active').siblings().removeClass('active');
		
        e.preventDefault();
    });
	
	
	function rgb2hex(rgb){
		 rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
		 return (rgb && rgb.length === 4) ? "#" +
		  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
		  ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
		  ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
	}
	
});
