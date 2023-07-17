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
 * @version 3.8.0
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
		</thead>
		<tbody>


		     <?php
			//find out membership
  			$is_annual_or_monthly = is_user_has_annual_or_monthly_memebership();
			$testvar = 13.4;


				// error_log('dealclub membership is in cart');

					if((!array_key_exists("89338966d3810daca44fbf46e5f8f866", WC()->cart->get_cart()) && !array_key_exists("eb52463368ecd850262863fc1bc53272", WC()->cart->get_cart()) && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) && !is_user_an_active_member_wcm()) || ( $is_annual_or_monthly==174761 && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) )|| check_if_monthly_is_in_cart() ) {

						error_log('I am inside cartttt');


						//upsell annual dc
						$product = wc_get_product( 174739 );

						$product_monthly = wc_get_product( 174721 );


						?>
							<tr class="hello again">
							<td class="product-thumbnail monthly_deal">
						<?php printf( '<a href="%s">%s</a>', esc_url( get_permalink( $product->get_id() )), apply_filters( 'woocommerce_cart_item_thumbnail', $product->get_image(), "89338966d3810daca44fbf46e5f8f866" ) ); // PHPCS: XSS ok. ?>
							</td>
						<td class="product-name-mem" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">

						<?php
							global $woocommerce;

							$items                  = $woocommerce->cart->get_cart();
							$cart_total_price_final = $woocommerce->cart->total;
							$dealclub_savings       = 0;
							$cw_discount = 0;
							$cw_monthly_discount = 0;

							foreach ( $items as $item => $values ) {

								$pro_id = $values['product_id'];
								$_product = wc_get_product( $pro_id );

								if($_product->is_type( 'simple' )){

									if ( $is_annual_or_monthly == 174761 ) { //if monthly then monthlyt price

										$sale_price = get_dynamic_price( $_product->get_id() )[1];

									}
									else { //ow annual price
										$sale_price = get_dynamic_price( $_product->get_id() )[0];
									}

									$regular_price = $_product->get_sale_price();


									$sale_price_for_monthly = get_dynamic_price( $_product->get_id() )[1];
									// $sale_price = get_dynamic_price( $_product->get_id() );
									$discount = ($regular_price - $sale_price) * $values['quantity'];

									$discount_for_monthly = ($regular_price - $sale_price_for_monthly) * $values['quantity'];


								}
								//here change for save amount for variable product
								if($_product->is_type( 'variable' )){
									$var_id = $values['variation_id'];

									$dynamic_pricearr = get_all_dynamic_prices_with_id_as_key($pro_id);
									$discount = ( (str_replace( '$', '', $dynamic_pricearr[$var_id]['sale_price']  )) - (str_replace( '$', '', $dynamic_pricearr[$var_id]['dc_price']  )) ) * $values['quantity'];

								}

								$cw_discount += $discount;
								$cw_monthly_discount += $discount_for_monthly;

							}

						if($woocommerce->cart->total > 0){
							echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> Save $'. $cw_discount . ' more with DealClub!</p> %s :
							<span class = "text-dark"><del>$99.00</del> $49.00/Year</span></div></a>', esc_url( get_permalink( $product->get_id() ) ), $product->get_name() ) ;
							}
						else{
							echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> subscribe to DealClub!</p> %s :
							<span class = "text-dark">$49.00/Year</span></div></a>', esc_url( get_permalink( $product->get_id() ) ), $product->get_name() ) ;
						}
						?>

						</td>
						<td class="add-monthly-sub">
							<div class="monthly-sub-button tooltip_class">
								<a  href="<?php echo get_site_url()?>/cart/?add-to-cart=174739&utm_source=dc-page" class="offer_btn-2" > Add Item </a>

								<div class="cart_tooltip">
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
									Also, <?php echo '$'.$testvar?> will be credited as equivalent credits to your DealFuel account, in lieu of the remaining days of your monthly membership.
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
									jQuery('.tooltip_class .offer_btn-2').click(function() {
										jQuery('.cart-tooltip-text').css('display','block');
										// setTimeout(function() {
										// 	jQuery('.tooltip-text').removeClass('active');
										// 	}, 10000);
									});

									jQuery('#df_cart_close_tooltip').click(function() {
											jQuery('.cart-tooltip-text').css('display','none');
										});

								});

							</script>

					</tr>
						<!-- /****************************************/ -->
						<!-- /** Remove this comment once code is done */ -->
						<!-- /** DO not show monthly upsell is user is already a monthly memeber or if user is a non dc but monthly is added to cart */ -->
						<?php if(  !( $is_annual_or_monthly==174761 && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) ) && !check_if_monthly_is_in_cart() ): ?>

						<tr class="monthly">
								<td class="product-thumbnail monthly_deal">
								<?php printf( '<a href="%s">%s</a>', esc_url( get_permalink( $product_monthly->get_id() )), apply_filters( 'woocommerce_cart_item_thumbnail', $product_monthly->get_image(), "89338966d3810daca44fbf46e5f8f866" ) ); // PHPCS: XSS ok. ?>
									</td>
								<td class="product-name-mem" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">



						<?php



								if($woocommerce->cart->total > 0){
									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> Save $'. $cw_monthly_discount . ' more with DealClub!</p> %s :
									<span class = "text-dark"><del>$99.00</del> $49.00/Year</span></div></a>', esc_url( get_permalink( $product_monthly->get_id() ) ), $product_monthly->get_name() ) ;
									}
								else{
									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> subscribe to DealClub!</p> %s :
									<span class = "text-dark">$49.00/Year</span></div></a>', esc_url( get_permalink( $product_monthly->get_id() ) ), $product_monthly->get_name() ) ;
								}
						?>

								</td>
								<td class="add-monthly-sub">
									<div class="monthly-sub-button">
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

						<td class="product-remove">
							<?php
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
							?>
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
