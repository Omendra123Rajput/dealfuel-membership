<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="woocommerce-cart-flex">
<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product Details', 'woocommerce' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
				<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
				<th class="product-remove">&nbsp;</th>
			</tr>

			<?php
					//find out membership
  					$is_annual_or_monthly = is_user_has_annual_or_monthly_memebership();
					  ?>

			<script>
						var is_annual_or_monthly = "<?php echo $is_annual_or_monthly ?>";

							// Jquery will work even after the completion of ajax call

							if ( is_annual_or_monthly == 174761  ) {

								function changeCssofDiv() {
									jQuery(".banner").addClass("banner-annual-for-monthly");
									jQuery('.woocommerce-cart .woocommerce-cart-form .woocommerce-cart-form__contents .product-name-mem').css('top','106px');
									jQuery('.woocommerce-cart .woocommerce-cart-form .woocommerce-cart-form__contents tr .add-monthly-sub').css('margin-top','55px');
								}

								// Attach the function to .ajaxComplete()
								jQuery(document).ajaxComplete(function() {
								// Add the class after each AJAX request is complete
								changeCssofDiv();
								});

								// Initial call to add the class when the page loads
								changeCssofDiv();

							}

			</script>


		</thead>
		<tbody>
		     <?php
					//find out membership
  					$is_annual_or_monthly = is_user_has_annual_or_monthly_memebership();

					//rewardpoints

					// Get the user ID
					$user_id = get_current_user_id();

					// Get the membership object for the user

					$membership_id = get_user_membership_id($user_id);

					$membership = wc_memberships_get_user_membership($user_id,$membership_id );

					if ( $membership ) {
						// Get the membership activation date
						$activation_date = $membership->get_start_date();

						// Create DateTime objects for the activation date and current date
						$activation_date = new DateTime($activation_date);
						$current_date = new DateTime();

						// Calculate the number of days since activation
						$days_passed = $activation_date->diff($current_date)->days;

						// Extract the month of activation
						$activation_month = $activation_date->format('F'); // 'F' returns the full month name

						$no_of_day_in_the_month_of_activation = getDaysInMonth($activation_month);


						//formula for credit points to be rewared

						$monthly_member_amount = 9;

						$credit_points_to_be_rewared = $monthly_member_amount * ( $no_of_day_in_the_month_of_activation - $days_passed )/$no_of_day_in_the_month_of_activation;

						//final points to be rewarded
						$credit_points_to_be_rewared = round($credit_points_to_be_rewared,1);

					}


					/*****Calculate save discount amount*/


					global $woocommerce;

					$items                  = $woocommerce->cart->get_cart();
					$cart_total_price_final = $woocommerce->cart->total;
					$dealclub_savings       = 0;
					$cw_discount = 0;
					$cw_monthly_discount = 0;

						foreach ( $items as $item => $values ) {

						$pro_id = $values['product_id'];
						$_product = wc_get_product( $pro_id );

						if( $pro_id == 174721 ){
							continue;
						}

						if($_product->is_type( 'simple' )){

							if ( $is_annual_or_monthly == 174761 ) { //if monthly then monthly price

								$sale_price = get_dynamic_price( $_product->get_id() )[1];

							}
							else { //ow annual price
								$sale_price = get_dynamic_price( $_product->get_id() )[0];
							}

							$regular_price = $_product->get_sale_price();

							$sale_price_for_monthly = get_dynamic_price( $_product->get_id() )[1];

							$discount = ($regular_price - $sale_price) * $values['quantity'];

							$discount_for_monthly = ($regular_price - $sale_price_for_monthly) * $values['quantity'];

							if ( $is_annual_or_monthly == 174761 ) { //if user in monthly member

								// $sale_price = get_dynamic_price( $_product->get_id() )[1];
								$discount = (get_dynamic_price( $_product->get_id() )[1] - get_dynamic_price( $_product->get_id() )[0]) * $values['quantity'];

							}

							if ( check_if_monthly_is_in_cart() ) {

								$quantity = $values['quantity']; //since monthly is in cart it considered monthy as an extra so -1

								$discount = ( $regular_price - get_dynamic_price( $_product->get_id() )[0] ) * $values['quantity'];

							}


						}
						//here change for save amount for variable product
						if($_product->is_type( 'variable' )){
							$var_id = $values['variation_id'];

							$dynamic_pricearr = get_all_dynamic_prices_with_id_as_key($pro_id);

							// print_r($dynamic_pricearr);

							$discount = ( (str_replace( '$', '', $dynamic_pricearr[$var_id]['sale_price']  )) - (str_replace( '$', '', $dynamic_pricearr[$var_id]['dynamic_price_array_annual']  )) ) * $values['quantity'];

							$discount_for_monthly = ( (str_replace( '$', '', $dynamic_pricearr[$var_id]['sale_price']  )) - (str_replace( '$', '', $dynamic_pricearr[$var_id]['dynamic_price_array_monthly']  )) ) * $values['quantity'];

							if ( $is_annual_or_monthly == 174761 ) { //if user in monthly member

								$discount = ( (str_replace( '$', '', $dynamic_pricearr[$var_id]['dynamic_price_array_monthly']  )) - (str_replace( '$', '', $dynamic_pricearr[$var_id]['dynamic_price_array_annual']  )) ) * $values['quantity'];

							}

							if ( check_if_monthly_is_in_cart() ) {

								$quantity = $values['quantity']; //since monthly is in cart it considered monthy as an extra so -1

								$discount = ( (str_replace( '$', '', $dynamic_pricearr[$var_id]['sale_price']  )) - (str_replace( '$', '', $dynamic_pricearr[$var_id]['dynamic_price_array_annual']  )) ) * $values['quantity'];

							}



							}

						$cw_discount += $discount;
						$cw_monthly_discount += $discount_for_monthly;

						}

					//for annual memeber show save msg

					if( $is_annual_or_monthly==174765 ) { //if user is a monthly member or has monthly added to the cart then show annual upsell

								?>
									<!-- /Show saving msg for annual member -->
								<tr class="df-show-savings">
								<td class="savings-text" colspan="5">

									<div class="display-savings-msg">

										<div> <img class="money-bag" src="https://1461794109.nxcli.io/money-bag.png" alt="Money Bag Image"> You are saving <span class="green-text">$<?php echo $cw_discount ?></span> with DealClub! </div>

									</div>
								</td>


								</tr>

								<?php



					}

					//annual upsell

					if((!array_key_exists("89338966d3810daca44fbf46e5f8f866", WC()->cart->get_cart()) && !array_key_exists("eb52463368ecd850262863fc1bc53272", WC()->cart->get_cart()) && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) && !is_user_an_active_member_wcm()) || ( $is_annual_or_monthly==174761 && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) )|| check_if_monthly_is_in_cart() ) { //if user is a monthly member or has monthly added to the cart then show annual upsell

						//upsell annual dc
						$product = wc_get_product( 174739 );

						$product_monthly = wc_get_product( 174721 );


						?>

							<?php


								if ( $is_annual_or_monthly==174761 ) { //show saving msg for monthly member

							?>

							<tr class="df-show-savings">
								<td class="savings-text" colspan="5">

									<div class="display-savings-msg">

										<div> <img class="money-bag" src="https://1461794109.nxcli.io/money-bag.png" alt="Money Bag Image"> You are saving <span class="green-text">$<?php echo $cw_monthly_discount ?></span> with DealClub! </div>

									</div>
								</td>


							</tr>

								<script>
											//change the banner position when the user is monthly

											jQuery(document).ready(function() {

												jQuery('.banner').css('top','186px');

											});

									</script>


							<?php

							}

							?>

							<tr class="annual_upsell">

							    <!-- Add the small banner with text -->
								<div class="banner">
								<p class="recomment-text">RECOMMENDED FOR YOU</p>
								</div>


							<td class="product-thumbnail monthly_deal">


						<?php printf( '<a href="%s">%s</a>', esc_url( get_permalink( $product->get_id() )), apply_filters( 'woocommerce_cart_item_thumbnail', $product->get_image(), "89338966d3810daca44fbf46e5f8f866" ) ); // PHPCS: XSS ok. ?>
							</td>
						<td class="product-name-mem annual_upsell_product_name" colspan="4" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">


					<?php
						if($woocommerce->cart->total > 0){

								if ( $is_annual_or_monthly == 174761 ) { //if monthly then inthe annual upsell add upgrade text

									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark">Upgrade to Annual & Save <span class="green-text">$'. $cw_discount . '</span> more with DealClub!</p> %s :
									<span class = "text-dark">$49.00/Year</span></div></a>', esc_url( get_permalink( $product->get_id() ) ), 'DealClub Membership ' ) ;

								}else { //normal annual upsell text

									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> Save <span class="green-text">$'. $cw_discount . '</span> more with DealClub!</p> %s :
									<span class = "text-dark">$49.00/Year</span></div></a>', esc_url( get_permalink( $product->get_id() ) ), 'DealClub Membership ' ) ;

								}

							}
							else{
								echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> subscribe to DealClub!</p> %s :
								<span class = "text-dark">$49.00/Year</span></div></a>', esc_url( get_permalink( $product->get_id() ) ), $product->get_name() ) ;
							}

							?>

						</td>
						<td class="add-monthly-sub add_item_annual_upsell">
							<div class="monthly-sub-button tooltip_class">
								<a  href="<?php echo get_site_url()?>/cart/?add-to-cart=174739&utm_source=dc-page" class="offer_btn-2" > Add Item </a>

								<div class="cart_tooltip show_hide_tooltip">
								<div class="cart-tooltip-text">

									<div class="red_rectangle">
									<div class="df_cart_close_tooltip" id="df_cart_close_tooltip">
										<svg class="tooltip-close-btn" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
										<path d="M7.64199 12.3584L12.3587 7.64175M12.3587 12.3584L7.64199 7.64175M7.50033 18.3334H12.5003C16.667 18.3334 18.3337 16.6667 18.3337 12.5001V7.50008C18.3337 3.33341 16.667 1.66675 12.5003 1.66675H7.50033C3.33366 1.66675 1.66699 3.33341 1.66699 7.50008V12.5001C1.66699 16.6667 3.33366 18.3334 7.50033 18.3334Z" stroke="black" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</div>

									<div class="normal_message">
									Your current $9/Mo Plan will be <b>upgraded to $49/Yr Plan.</b>
									<br>
									<br>
									The exclusive benefits of the Annual Plan will be unlocked right away.
									</div>
									<div class="line"></div>
									<div class="upgrade_message">
									Also, <?php echo '$'.$credit_points_to_be_rewared?> will be credited as equivalent credits to your DealFuel account, in lieu of the remaining days of your monthly membership.
									</div>

									<div class="tooltip-pointer">

									</div>

									<div>

								</div>
								</div>

							</div>


						</td>

						<script>

							//cart tooltip

								jQuery(document).ready(function() {

									var is_annual_or_monthly = "<?php echo $is_annual_or_monthly ?>";

									//tooltip for annual upsell when user in monthly member

									if ( is_annual_or_monthly == 174761  ) {

										jQuery('.cart_tooltip').removeClass('show_hide_tooltip');

										jQuery('.tooltip_class .offer_btn-2').click(function() {
											jQuery('.cart-tooltip-text').css('display','block');
										});

										jQuery('#df_cart_close_tooltip').click(function() {
												jQuery('.cart-tooltip-text').css('display','none');
											});

									}else{

										jQuery('.cart_tooltip').css('display','none');

									}

								});

							</script>

					</tr>

						<!-- /** DO not show monthly upsell is user is already a monthly memeber or if user is a non dc but monthly is added to cart */ -->
						<!-- /** When user is Non DC or has not added any memebership to the cart then add monthly uspell as well */ -->
						<?php if(  !( $is_annual_or_monthly==174761 && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) ) && !check_if_monthly_is_in_cart() ): ?>

						<tr class="monthly">
								<td class="product-thumbnail monthly_deal">
								<?php printf( '<a href="%s">%s</a>', esc_url( get_permalink( $product_monthly->get_id() )), apply_filters( 'woocommerce_cart_item_thumbnail', $product_monthly->get_image(), "89338966d3810daca44fbf46e5f8f866" ) ); // PHPCS: XSS ok. ?>
									</td>
								<td class="product-name-mem monthly_product_name" colspan="4" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">

						<?php

								if($woocommerce->cart->total > 0){
									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> Save <span class="green-text">$'. $cw_monthly_discount . '</span> more with DealClub!</p> %s :
									<span class = "text-dark"> $9.00/Month</span></div></a>', esc_url( get_permalink( $product_monthly->get_id() ) ), 'DealClub Membership' ) ;
									}
								else{
									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> subscribe to DealClub!</p> %s :
									<span class = "text-dark">$9.00/Month</span></div></a>', esc_url( get_permalink( $product_monthly->get_id() ) ), $product_monthly->get_name() ) ;
								}
						?>

								</td>
								<td class="add-monthly-button" >
									<div class="monthly-sub-button monthly_add_item_button">
										<a  href="<?php echo get_site_url()?>/cart/?add-to-cart=174721&utm_source=dc-page" class="offer_btn-2" > Add Item </a>
									</div>
								</td>

						</tr>

						<?php endif ;?>
						<!-- /********************************************** */ -->

						<?php
			  		}


		     ?>

			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );


					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">

						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
						}
						?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Qty', 'woocommerce' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
						} else {
							$product_quantity = woocommerce_quantity_input(
								array(
									'input_name'   => "cart[{$cart_item_key}][qty]",
									'input_value'  => $cart_item['quantity'],
									'max_value'    => $_product->get_max_purchase_quantity(),
									'min_value'    => '0',
									'product_name' => $_product->get_name(),
								),
								$_product,
								false
							);
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
						?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>
						<td class="product-remove ">

							<!-- The popup container -->
							<div class="overlay" id="blur-overlay"></div>

							<div class="popup" id="floating-popup">
								<div class="popup-content">
									<span class="close-btn" id="close-popup" style="display:none">&times;</span>
									<div class="popup-mem-text">

										<span>A DealClub Membership of just $9/Month, saves <span class="green-text discount-text">$<?php echo $cw_monthly_discount ?></span> on this purchase</span>

									</div>
									<br>
									<div class="popup-extra-text">

									<span>& extra 10% on all other purchases for one month.</span>

									</div>

									<div class="popup-confirm-text">

									<span>Are you sure, you want to miss out on these huge savings???.</span>

									</div>

									<div class="popup-keep-mem-button">

									<button class="keep-mem-button">I want to save. Keep Membership</button>

									</div>

									<div class="popup-remove-mem-button">

									<button class="remove-mem-button"> <a href="%s" class="remove-product-m" aria-label="%s" data-product_id="%s" data-product_sku="%s">I hate savings! Remove Membership  </a>  </button>

									</div>

								</div>
							</div>



							<?php

								if ($product_id == 174721 ) {

									    // Define PHP variables here, will use them later in jquery
										$dynamicHref = esc_url(wc_get_cart_remove_url($cart_item_key));
										$dynamicAriaLabel = esc_html__('Remove this item', 'woocommerce');
										$dynamicProductID = esc_attr($product_id);
										$dynamicProductSKU = esc_attr($_product->get_sku());
										$home_url = get_home_url();

									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="javascript:void(0);" class="remove" aria-label="" data-product_id="" data-product_sku=""><img src="' . get_home_url(). '/wp-content/uploads/2022/12/Dustbin.svg"</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											esc_html__( 'Remove this item', 'woocommerce' ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$cart_item_key
									);



								}else{

									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><img src="' . get_home_url(). '/wp-content/uploads/2022/12/Dustbin.svg"</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											esc_html__( 'Remove this item', 'woocommerce' ),
											esc_attr( $product_id ),
											esc_attr( $_product->get_sku() )
										),
										$cart_item_key
									);

								}



							?>

								<script>

								jQuery(document).ready(function() {

								var is_annual_or_monthly = "<?php echo $is_annual_or_monthly ?>";

								var product_id = "<?php echo $product_id  ?>";

								if ( product_id == 174721) {

									jQuery(".product-remove").addClass("monthly-product");

									jQuery(".monthly-product").on("click", function () {
										jQuery("#blur-overlay").fadeIn();
										jQuery("#floating-popup").fadeIn();
									});

									jQuery("#close-popup").on("click", function () {
										jQuery("#blur-overlay").fadeOut();
										jQuery("#floating-popup").fadeOut();
									});

									//Adding the remove url to the remove the membership button

									// Your PHP variables are now accessible here
									var dynamicHref = "<?php echo $dynamicHref; ?>";
										// Use replace() with a regular expression to remove #038;
									var dynamicHref = dynamicHref.replace(/#038;/g, "");
									var dynamicAriaLabel = "<?php echo $dynamicAriaLabel; ?>";
									var dynamicProductID = "<?php echo $dynamicProductID; ?>";
									var dynamicProductSKU = "<?php echo $dynamicProductSKU; ?>";
									var homeURL = "<?php echo $home_url; ?>";

									// Select the anchor tag with class 'remove-product-m' and set its attributes dynamically
									jQuery(".remove-product-m")
										.attr("href", dynamicHref)
										.attr("aria-label", dynamicAriaLabel)
										.attr("data-product_id", dynamicProductID)
										.attr("data-product_sku", dynamicProductSKU);



								}

								});

								</script>




						</td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr>
				<td colspan="6" class="actions">

					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon">
							<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<div class="cart-collaterals">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );

	?>
</div>
</div>
<?php do_action( 'woocommerce_after_cart' ); ?>
