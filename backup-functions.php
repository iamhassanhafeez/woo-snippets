<?php 

foreach($products[0]->Colours as $colour){

	$colour_code = $colour->Code;
	$colour_name = $colour->Name;
	$var_stock = $colour->Stock;

	
	// Get the Variable product object (parent)
	$product = wc_get_product($product_id);

	
	// The variation data
	
	$variation_data =  array(
		'attributes' => array(
			'color' => $colour_name,
		),
		'sku'           => $product->get_sku(),
		'regular_price' => '22.00',
		'sale_price'    => '',
		'stock_qty'     => $var_stock,
	);

	
		
		$variation_post = array(
			'post_title'  => $product->get_name(),
			'post_name'   => 'product-'.$product_id.'-variation',
			'post_status' => 'darft',
			'post_parent' => $product_id,
			'post_type'   => 'product_variation',
			'guid'        => $product->get_permalink()
		);

		// Creating the product variation
		$variation_id = wp_insert_post( $variation_post );

		// Get an instance of the WC_Product_Variation object
		$variation = new WC_Product_Variation( $variation_id );

		// Iterating through the variations attributes
		foreach ($variation_data['attributes'] as $attribute => $term_name )
		{
			$taxonomy = 'pa_'.$attribute; // The attribute taxonomy

			//If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
			if( ! taxonomy_exists( $taxonomy ) ){
				register_taxonomy(
					$taxonomy,
				'product_variation',
					array(
						'hierarchical' => false,
						'label' => ucfirst( $attribute ),
						'query_var' => true,
						'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
					),
				);
			}

			// Check if the Term name exist and if not we create it.
			if( ! term_exists( $term_name, $taxonomy ) )
				wp_insert_term( $term_name, $taxonomy ); // Create the term

			$term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

			// Get the post Terms names from the parent variable product.
			$post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

			// Check if the post term exist and if not we set it in the parent variable product.
			if( ! in_array( $term_name, $post_term_names ) )
				wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

			// Set/save the attribute data in the product variation
			update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
		}

		## Set/save all other data

		// SKU
		if( ! empty( $variation_data['sku'] ) )
			$variation->set_sku( $variation_data['sku'] );

		// Prices
		if( empty( $variation_data['sale_price'] ) ){
			$variation->set_price( $variation_data['regular_price'] );
		} else {
			$variation->set_price( $variation_data['sale_price'] );
			$variation->set_sale_price( $variation_data['sale_price'] );
		}
		$variation->set_regular_price( $variation_data['regular_price'] );

		// Stock
		if( ! empty($variation_data['stock_qty']) ){
			$variation->set_stock_quantity( $variation_data['stock_qty'] );
			$variation->set_manage_stock(true);
			$variation->set_stock_status('');
		} else {
			$variation->set_manage_stock(false);
		}
		
		$variation->set_weight(''); // weight (reseting)

		$variation->save(); // Save the data

}		
///////////////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\







































//start fetching colours and creating variables

foreach($products[0]->Colours as $colour){

	$colour_code = $colour->Code;
	$colour_name = $colour->Name;
	$var_stock = $colour->Stock;

	function create_product_variation( $product_id, $variation_data ){
		// Get the Variable product object (parent)
		$product = wc_get_product($product_id);

		$variation_post = array(
			'post_title'  => $product->get_name(),
			'post_name'   => 'product-'.$product_id.'-variation',
			'post_status' => 'darft',
			'post_parent' => $product_id,
			'post_type'   => 'product_variation',
			'guid'        => $product->get_permalink()
		);

		// Creating the product variation
		$variation_id = wp_insert_post( $variation_post );

		// Get an instance of the WC_Product_Variation object
		$variation = new WC_Product_Variation( $variation_id );

		// Iterating through the variations attributes
		foreach ($variation_data['attributes'] as $attribute => $term_name )
		{
			$taxonomy = 'pa_'.$attribute; // The attribute taxonomy

			//If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
			if( ! taxonomy_exists( $taxonomy ) ){
				register_taxonomy(
					$taxonomy,
				'product_variation',
					array(
						'hierarchical' => false,
						'label' => ucfirst( $attribute ),
						'query_var' => true,
						'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
					),
				);
			}

			// Check if the Term name exist and if not we create it.
			if( ! term_exists( $term_name, $taxonomy ) )
				wp_insert_term( $term_name, $taxonomy ); // Create the term

			$term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

			// Get the post Terms names from the parent variable product.
			$post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

			// Check if the post term exist and if not we set it in the parent variable product.
			if( ! in_array( $term_name, $post_term_names ) )
				wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

			// Set/save the attribute data in the product variation
			update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
		}

		## Set/save all other data

		// SKU
		if( ! empty( $variation_data['sku'] ) )
			$variation->set_sku( $variation_data['sku'] );

		// Prices
		if( empty( $variation_data['sale_price'] ) ){
			$variation->set_price( $variation_data['regular_price'] );
		} else {
			$variation->set_price( $variation_data['sale_price'] );
			$variation->set_sale_price( $variation_data['sale_price'] );
		}
		$variation->set_regular_price( $variation_data['regular_price'] );

		// Stock
		if( ! empty($variation_data['stock_qty']) ){
			$variation->set_stock_quantity( $variation_data['stock_qty'] );
			$variation->set_manage_stock(true);
			$variation->set_stock_status('');
		} else {
			$variation->set_manage_stock(false);
		}
		
		$variation->set_weight(''); // weight (reseting)

		$variation->save(); // Save the data
	}

	//$parent_id = $product_id; // get the variable product id dynamically

	// The variation data
	
	$variation_data =  array(
		'attributes' => array(
			'color' => $colour_name,
		),
		'sku'           => $product->get_sku(),
		'regular_price' => '22.00',
		'sale_price'    => '',
		'stock_qty'     => $var_stock,
	);

	// The function to be run
	create_product_variation( $product_id, $variation_data );
}
//end fetching colours and creating variations




	//insert product images gallery
if ( ! empty( $_FILES['muti_files'] )  ) {
            $files = $_FILES['muti_files'];
            foreach ($files['name'] as $key => $value){
                if ($files['name'][$key]){
                    $file = array(
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                    );
                }
                $_FILES = array("muti_files" => $file);
                $i=1;
                    foreach ($_FILES as $file => $array) {
                          if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) __return_false();
                            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
                            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
                            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
                            $attachment_id = media_handle_upload($file, $post_id);
                            $vv .= $attachment_id . ",";
                            $i++;
                    }
                    update_post_meta($post_id, '_product_image_gallery',  $vv);
            }
        }
		//end insert image gallery to single product

















		//////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//start fetching
		$term_name = ucfirst('pinkish');
		$taxonomy = 'pa_color'; // The attribute taxonomy
		
				// If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
				if( ! taxonomy_exists( $taxonomy ) ){
					register_taxonomy(
						$taxonomy,
					   'product_variation',
						array(
							'hierarchical' => false,
							'label' => ucfirst( $attribute ),
							'query_var' => true,
							'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
						),
					);
				}
		
				// Check if the Term name exist and if not we create it.
				if( ! term_exists( $term_name, $taxonomy ) )
					wp_insert_term( $term_name, $taxonomy ); // Create the term
		
				$term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug
		
				// Get the post Terms names from the parent variable product.
				$post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );
		
				// Check if the post term exist and if not we set it in the parent variable product.
				if( ! in_array( $term_name, $post_term_names ) )
				   $inserted_term = wp_set_post_terms( $product_id, $term_name, $taxonomy, true );
				if (is_wp_error($inserted_term)){
					print_r('What the heck');
				}
		
		
		
		
		
		
		
		//Create variations
		
		//start fetching colours and creating variables
		
		foreach($products[0]->Colours as $colour){
		
			$colour_code = $colour->Code;
			$colour_name = $colour->Name;
			$var_stock = $colour->Stock;
			
		
		/**
		 * [pricode_create_variations description]
		 * @param  [type] $product_id [description]
		 * @param  [type] $values     [description]
		 * @return [type]             [description]
		 */
		function pricode_create_variations( $product_id, $values ){
			$variation = new WC_Product_Variation();
			$variation->set_parent_id( $product_id );
			$variation->set_attributes($values);
			$variation->set_status('publish');
			$variation->set_sku($var_stock);
			$variation->set_price(20.00);
			$variation->set_regular_price(20.00);
			$variation->set_stock_status();
			$variation->save();
			$product = wc_get_product($product_id);
			$product->save();
		
		}
		
		//Create variations
			$new_variation = pricode_create_variations( $product_id, ['color' => $colour_name]);
		
		
		}
		
		
		
		
		//end fetching colours and creating variations	
		



?>