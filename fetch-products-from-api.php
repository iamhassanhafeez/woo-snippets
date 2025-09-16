<?php 
//======================Fetch Product Data From API =================

add_action('wp_ajax_get_promo_branding_products_from_api', 'get_promo_branding_products_from_api');
function get_promo_branding_products_from_api(){
	$products = [];

	/*	$url = "https://api.impressioneurope.co.uk/v1/promo-branding";
		$args = array(
	  'headers' => array(
		'Authorization' => 'Basic ' . base64_encode( 'promo-branding' . ':' . 'XXXXXXXXXXXXXXXXXXXXXXx' )
	  )
	);*/
		//$response = wp_remote_request( $url, $args );
		$response = wp_remote_get('http://samt26.sg-host.com/wp-content/themes/bulk-branded/response.txt');

		$body = wp_remote_retrieve_body($response);

		$body = json_decode($body);

		if( ! is_array( $body ) || empty( $body )){
			return false;
		}

	wp_remote_post( admin_url('admin-ajax.php?action=get_promo_branding_products_from_api'), [
			'blocking' => false,
			'sslverify' => false,	
		] );	



			$all_products = $body;
			$i = 1;
	foreach ($all_products as $products){
			$prod_gallery =[];
			$product_title = ucwords(str_replace( '-', ' ', sanitize_title_with_dashes( $products->Title ) ));
			$description = $products->Description; 
			$featured_image = $products->Images[0]->SrcFull;
			$product_sku = $products->SKU;
		

			$product_id = wp_insert_post ([
				'post_name' => $product_title,
				'post_title' => $product_title,
				'post_content' => $description,
				'post_excerpt' => $description,
				'post_type' => 'product',
				'post_status' => 'publish',
			]);

	//fetch price deals per product and add in repeater
		foreach($products->Pricing as $price){

			$row = array(
			'min_quantity'   => $price->Quantity,
			'cost_per_unit'  => $price->Price
		);

		add_row('bulk_prices', $row, $product_id);
		}
	//end fetch bulk price deals

	//check if a category exists and assign it othewise create a new one and assign
		$category_main = $products->CategoryMain;
		$category_sub = $products->CategorySub;
	//check if parent exists and assign to the product
		$cat_main_arr = explode(',', $category_main);
	
		foreach($cat_main_arr as $cat_main){
		$cat = term_exists($cat_main, 'product_cat');
		
		if ($cat !==0 && $cat !==null)
		{
			$cat_id = $cat['term_id'];
			if( !has_term( $cat_id, 'product_cat', $product_id ) ) {
		wp_set_object_terms( $product_id, $cat_main, 'product_cat');
			}
		}
		//otherwise create new one and assign it
		else{
			$parent_cat = wp_insert_term($cat_main, 'product_cat');
			$parent_cat_id = $parent_cat['term_id'];  
			wp_set_object_terms( $product_id, $parent_cat_id, 'product_cat');
		}
			}
		
	//check if sub-cat exists
		$term_obj_list = get_the_terms( $product_id, 'product_cat' );
		$parent_term_id = $term_obj_list[0]->term_id;
		$cat_sub_arr = explode(',', $category_sub);
		
		foreach($cat_sub_arr as $cat_sub){
		$cat_s = term_exists($cat_sub, 'product_cat');
		if ($cat_s !==0 && $cat_s !==null)
		{
			$cat_s_id = $cat_s['term_id'];
			if( !has_term( $cat_s_id, 'product_cat', $product_id ) ) {
			wp_set_object_terms( $product_id, $cat_sub, 'product_cat');
			}
		}
		//otherwise create new one and assign it
		else{
			
			
			$sub_cat = wp_insert_term($cat_sub, 'product_cat', array('parent'=>$parent_term_id));
			$sub_cat_id = $sub_cat['term_id'];  
		//	wp_set_object_terms( $product_id, $sub_cat_id, 'product_cat');
		}
		}

	//wp_set_object_terms( $product_id, 'variable', 'product_type');
	update_post_meta( $product_id, '_sku', $product_sku);
	
	//insert featured image to parent product
	Generate_Featured_Image( $featured_image,   $product_id );

	//create variations
	foreach ($products->Colours as $colour){

		$colour_name = $colour->Name;
		$colour_code = $colour->Code;
		$col_stock = $colour->Stock;
		
		foreach($products->Images as $image){
			$img_colour_code = $image->ColourCode;
			if($img_colour_code == $colour_code){
				$col_featured = $image->SrcFull;
				break;
			}
			
		}
		
		
		$product = wc_get_product($product_id);

		//=============================Start Creating Atts================

		// Your product attribute settings
				$taxonomy   = 'pa_color'; // The taxonomy
				$term_name  = $colour_name; // The term
				$attributes = $product->get_attributes();
				$term_id    = get_term_by( 'name', $term_name, $taxonomy )->term_id;


				// 1) If The product attribute is set for the product
				if( array_key_exists( $taxonomy, $attributes ) ) {
					foreach( $attributes as $key => $attribute ){
						if( $key == $taxonomy ){
							$attribute->set_options( array( $term_id ) );
							$attributes[$key] = $attribute;
							break;
						}
					}
					$product->set_attributes( $attributes );
				}

				else {
					$attribute = new WC_Product_Attribute();

					$attribute->set_id( sizeof( $attributes) + 1 );
					$attribute->set_name( $taxonomy );
					$attribute->set_options( array( $term_id ) );
					$attribute->set_position( sizeof( $attributes) + 1 );
					$attribute->set_visible(true);
					$attribute->set_variation(true);
					$attributes[] = $attribute;

					$product->set_attributes( $attributes );
				}
				$product->save();
				wp_set_object_terms( $product_id, 'variable', 'product_type');

				// Append the new term in the product
				if( ! has_term( $term_name, $taxonomy, $product->get_id() ) )
				{	wp_set_object_terms($product->get_id(), $term_name, $taxonomy, true );}


		//================================End creating atts================

				$colour_name = sanitize_title($colour_name);
				// //Create variations
				$maxPrice = $products->Pricing[0]->Price;
				$var_id = bb_create_variations( $product_id, ['pa_color' => $colour_name], $col_stock, $maxPrice);

				//insert featured image to product variation
				$var_attach_id = Generate_Featured_Image( $col_featured, $var_id );
				$prod_gallery[] = $var_attach_id;
	}
				print_r (implode(',', $prod_gallery));
		
				update_post_meta($product_id,'_product_image_gallery',implode(',', $prod_gallery));
				if ($i++ == 10) 
				break;
}
	
}
//=========================== Custom Functions for API =================

	//=============== Function To Insert Parent Product Featured Image =================
	function Generate_Featured_Image( $featured_image, $product_id  ){

		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents($featured_image);
		//$filename = basename($featured_image);
		$filename = basename($image_url, '.jpg') . (string) rand(0, 5000) . '.jpg';
		if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
		else                                    $file = $upload_dir['basedir'] . '/' . $filename;
		file_put_contents($file, $image_data);

		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($filename),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $file, $product_id );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		$res1= wp_update_attachment_metadata( $attach_id, $attach_data );
		$res2= set_post_thumbnail( $product_id, $attach_id );
		return $attach_id;
	}


	//=================== Create Variation per iteration on the basis of colour ==============
	function bb_create_variations( $product_id, $values, $col_stock, $maxPrice ){
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $product_id );
			$variation->set_attributes($values);
			$variation->set_status('publish');
			$variation->set_price($maxPrice);
			$variation->set_regular_price($maxPrice);
			$variation->set_manage_stock(true);
			$variation->set_stock_quantity($col_stock);
			$variation->save();
			$product = wc_get_product($product_id);
			$product->save();
			return	$variation->get_id();

		}
//==============================END Custom Functions For API =================

//====================End fetching product data from API =============
?>
