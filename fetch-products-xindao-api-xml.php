<?php

//======================Fetch Product Data From API =================

add_action('wp_ajax_get_promo_xindao_products_from_api', 'get_promo_xindao_products_from_api');
function get_promo_xindao_products_from_api(){

		//fetch xml file via url
		$xml = simplexml_load_file("http://samt26.sg-host.com/wp-content/themes/bulk-branded/xindao-api/xindao-products-api.xml");
		$xml_pricing = simplexml_load_file("http://samt26.sg-host.com/wp-content/themes/bulk-branded/xindao-api/xindao-products-pricing-api.xml");

		
		//send request via admin ajax
		wp_remote_post( admin_url('admin-ajax.php?action=get_promo_xindao_products_from_api'), [
			'blocking' => false,
			'sslverify' => false,	
		] );
	
	
/*	foreach($xml_pricing->Product as $price_item){
			echo'<<>>'.	$price_item_code = (string) $price_item->ItemCode;
				//if( $sku == $price_item_code ){
					for($i=1; $i<=6; $i++){
						$Qty = 'Qty'.$i;
						$ItemPriceGross_Qty = 'ItemPriceGross_Qty'.$i;
						
			echo'<<>>'.			$item_qty = (string) $price_item->$Qty;
			echo'<<>>'.			$price_gross = (string) $price_item->$ItemPriceGross_Qty;
					//	$row = array(
					//	'min_quantity'   => $item_qty,
					//	'cost_per_unit'  => $price_gross
					//	);

					//	add_row('bulk_prices', $row, $product_id);
						
					}
			//	}
		echo'<br/>';	}
		die;*/
		//loop for testing purpose to create desired no.of posts
		$i = 1;
		
	
		foreach ($xml->Product as $product){
			
			// ftech xml strings and store in variables
			$name = (string) $product->ItemName;
			$description = (string) $product->LongDescription;
			$color = (string) $product->Color;
			$main_image = (string) $product->MainImage;
			$sku = (string) $product->ItemCode;
			$stock_qty = (string) $product->OuterCartonQty;
			
			//map fetched xml strings to wordpress products fields
			$product_title = ucwords(str_replace( '-', ' ', sanitize_title_with_dashes( $name ) ));
			$featured_image = $main_image;
			$product_sku = $sku;
			
			//insert product and get product id in return
			$product_id = wp_insert_post ([
				'post_name' => $product_title,
				'post_title' => $product_title,
				'post_content' => $description,
				'post_excerpt' => $description,
				'post_type' => 'product',
				'post_status' => 'publish',
			]);

			//fetch pricing from another source by comparing ItemCode
			foreach($xml_pricing->Product as $price_item){
				$price_item_code = (string) $price_item->ItemCode;
				if( $sku == $price_item_code ){
					for($i=1; $i<=6; $i++){
						$Qty = 'Qty'.$i;
						$ItemPriceGross_Qty = 'ItemPriceGross_Qty'.$i;
						
						$item_qty = (string) $price_item->$Qty;
						$price_gross = (string) $price_item->$ItemPriceGross_Qty;
						$row = array(
						'min_quantity'   => $item_qty,
						'cost_per_unit'  => $price_gross
						);

						add_row('bulk_prices', $row, $product_id);
						
					}
				}
			}
			//fetch price deals per product and add in repeater
			/*foreach($products->Pricing as $price){

				$row = array(
					'min_quantity'   => $price->Quantity,
					'cost_per_unit'  => $price->Price
				);

				add_row('bulk_prices', $row, $product_id);
			}*/
			//end fetch bulk price deals


			//wp_set_object_terms( $product_id, 'variable', 'product_type');
			update_post_meta( $product_id, '_sku', $product_sku);
			
			//insert featured image to parent product
			Generate_Featured_Image( $featured_image,   $product_id );
			
			$color_separated = explode(',', $color);
			//create variations
			foreach ($color_separated as $color_item){

				$colour_name = $color_item;
				
				$col_stock = $stock_qty;
				
				$col_featured = $featured_image;
				
				
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
						$var_id = bb_create_variations( $product_id, ['pa_color' => $colour_name], $col_stock);

						//insert featured image to product variation
						Generate_Featured_Image( $col_featured,   $var_id );

			}

//				if ($i++ == 15) 
	//				break;
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
	}


	//=================== Create Variation per iteration on the basis of colour ==============
	function bb_create_variations( $product_id, $values, $col_stock ){
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $product_id );
			$variation->set_attributes($values);
			$variation->set_status('publish');
			$variation->set_price('22.00');
			$variation->set_regular_price('22.00');
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