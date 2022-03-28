<?php 
function getCurrentUserOrders(){
	$user_id = get_current_user_id();
	$order_args = array(
    'customer_id' => $user_id,
	//'posts_per_page' => 8
	);
	
	$orders = wc_get_orders($order_args);
	?>

	<table>
	<thead>
		<tr>
		<th class="woocommerce-orders-table__header order_name">Order Name</th>
		<th class="woocommerce-orders-table__header order_number">Order Number</th>
		<th class="woocommerce-orders-table__header order_qty">Qty</th>
		<th class="woocommerce-orders-table__header order_date">Date</th>
		<th class="woocommerce-orders-table__header order_amount">Amount</th>
		<th class="woocommerce-orders-table__header order_action">Action</th>
	  </tr>
	</thead>
	<tbody>	
	
	<?php
	foreach( $orders as $order ){
		$order_id = $order->get_id();
		?>
	  <tr class="woocommerce-orders-table__row">
		<td class="woocommerce-orders-table__cell cell_order_name">
			<?php 
		//get product(s) in the current order item
		$items = $order->get_items();
		$total_items = count($items);
		foreach ( $items as $item ) {
    	echo $product_name = $item->get_name();
	//	echo $product_id = $item->get_product_id();
		
  
	/*	////////////////////============//	
			 
    $args = array('post_type' => 'product','id' => $product_id);
    $result = new WP_Query( $args );
    if ( $result-> have_posts() ) :
    while ( $result->have_posts() ) : $result->the_post();
    comments_template(); // you can custom template by code in single-product-reviews.php
 endwhile;
endif; wp_reset_postdata();
			
			
		//========================================/////////////////////////////////	*/
			
		} 
			?>
		  </td>
		<td class="woocommerce-orders-table__cell cell_order_number">#<?php echo $order->get_order_number(); ?></td>
		<td class="woocommerce-orders-table__cell cell_order_qty"><?php echo $total_items; ?></td>
		<td class="woocommerce-orders-table__cell cell_order_date"><?php echo date_i18n( 'd/m/Y', strtotime( $post->get_date_created ) ); ?></td>
 		<td class="woocommerce-orders-table__cell cell_order_amount"><?php echo get_woocommerce_currency_symbol (); ?><?php echo $order->get_total(); ?></td>
  		<td class="woocommerce-orders-table__cell cell_order_action">
            <span class="dashicons dashicons-arrow-down-alt2 accordion"></span> 
			 <div class="panel">
				 <h3 class="your-order">Your Order</h3>
			 <div class="order-headings">
						<div class="heading-left">product</div>
						<div class="heading-right">total</div>
					</div>
                 
                <?php
                $items = $order->get_items();
                $total_items = count($items);
                foreach ( $items as $item ) {
                $product_name = $item->get_name();
                $product_id = $item->get_product_id();
				//$item_total = $item->get_item_total();
                $_product = wc_get_product( $product_id );
                $thumbnail = $_product->get_image();
                ?>   
                <div class="pannel-inner">     
                    <div class="prod-thumbnail"><?php echo '<div class="ts-product-image" style="width: 52px; height: 45px; display: inline-block; padding-right: 7px; vertical-align: middle;">'
                                . $thumbnail .
                            '</div>'; ?></div>        
                    <div class="review-form">
                        <div class="prod-title"><?php echo $item->get_quantity(); echo ' x '.$product_name; ?></div>
						<div class="form-opener">Rate this product?</div> 
                        <div class="form-panel">
						<?php echo do_shortcode('[site_reviews_form assign_to='.$product_id.']'); ?>
						</div>	
                    </div>
                    <div class="prod-total"><?php echo $order->get_formatted_line_subtotal( $item);  ?></div>
                </div>
                <?php
                }
                ?>
				<div class="total-footer">Total<br/><?php echo get_woocommerce_currency_symbol (); ?><?php echo $order->get_total($product_id); ?></div>
			</div>	
		  
		  </td>
	  </tr>	
	<?php	
		}
		?>
	</tbody>		
	</table>
<?php
}

add_shortcode('show-orders', 'getCurrentUserOrders');
//============= End Orders Custom Template ===================

//============ INVOICES CUSTOM TEMPLATE ======================

function getCurrentUserOrdersInvoices(){
	$user_id = get_current_user_id();
	$args = array(
    'customer_id' => $user_id,
	//'posts_per_page' => 8
	);
	
	$orders = wc_get_orders($args);
	?>

	<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
	<thead>
		<tr>
		<th class="woocommerce-orders-table__header order_name">Order Name</th>
		<th class="woocommerce-orders-table__header order_number">Order Number</th>
		<th class="woocommerce-orders-table__header order_qty">Qty</th>
		<th class="woocommerce-orders-table__header order_date">Date</th>
		<th class="woocommerce-orders-table__header order_amount">Amount</th>
		<th class="woocommerce-orders-table__header order_action">Download</th>
	  </tr>
	</thead>
	<tbody>	
	
	<?php
	foreach( $orders as $order ){
		$order_id = $order->get_id();
		?>
	  <tr class="woocommerce-orders-table__row">
		<td class="woocommerce-orders-table__cell cell_order_name">
			<?php 
		//get product(s) in the current order item
		$items = $order->get_items();
		$total_items = count($items);
		foreach ( $items as $item ) {
    	echo $product_name = $item->get_name();
		echo "<br/>";	
  
		} 
			?>
		  </td>
		<td class="woocommerce-orders-table__cell cell_order_number">#<?php echo $order->get_order_number(); ?></td>
		<td class="woocommerce-orders-table__cell cell_order_qty"><?php echo $total_items; ?></td>
		<td class="woocommerce-orders-table__cell cell_order_date"><?php echo date_i18n( 'd/m/Y', strtotime( $post->get_date_created ) ); ?></td>
 		<td class="woocommerce-orders-table__cell cell_order_amount"><?php echo get_woocommerce_currency_symbol (); ?><?php echo $order->get_total(); ?></td>
  		<td class="woocommerce-orders-table__cell cell_order_action"><a href="?pdfid=<?php echo $order_id; ?>" class="dashicons dashicons-arrow-down-alt2" ></a>
		  </td>
	  </tr>
	<?php	
		}
		?>
	</tbody>		
	</table>
<?php
}

add_shortcode('show-invoices', 'getCurrentUserOrdersInvoices');

