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

							// Make JQUERY work after the ajax calls happen in the cart

							if ( is_annual_or_monthly == 1392755  ) { //if user is a new monthly mem

								function changeCssofDiv() {
									jQuery(".banner").addClass("banner-annual-for-monthly");

									if (  jQuery(window).width() >= 767 ){//if screen in large than 767
										jQuery('.woocommerce-cart .woocommerce-cart-form .woocommerce-cart-form__contents .product-name-mem').css('top','106px !important');
									}else{
										jQuery(".product-name-mem.annual_upsell_product_name").addClass("monthly-banner-carttotal-pos");
									}

									jQuery('.woocommerce-cart .woocommerce-cart-form .woocommerce-cart-form__contents tr .add-monthly-sub').css('margin-top','55px');
									jQuery('.cart_tooltip').removeClass('show_hide_tooltip');//make sure tooltip should work again after removing the annual mem from the cart

								}

								// Attach the function to .ajaxComplete()
								jQuery(document).ajaxComplete(function() {
								// Add the class after each AJAX request is complete
								changeCssofDiv();
								});

								// Initial call to add the class when the page loads
								changeCssofDiv();

							}else { //if user is not a member

								jQuery(document).ready(function() {

									//if error notice is in the dom then adjust the banner position

									if (jQuery('.woocommerce-error').length) {

										if (jQuery(window).width() <= 767) {

											jQuery('.banner').css({ 'top': '226px'});

										}else{
											jQuery('.banner').css({ 'top': '215px'});
										}

									}

								});

							}

			</script>

		</thead>
		<tbody>
		     <?php
					//find out membership
  					$is_annual_or_monthly = is_user_has_annual_or_monthly_memebership();

					/***Reward Credit Points***/

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

						$monthly_member_amount = 10; //new monthly mem is priced @ 10$

						$credit_points_to_be_rewared = $monthly_member_amount * ( $no_of_day_in_the_month_of_activation - $days_passed )/$no_of_day_in_the_month_of_activation;

						//final points to be rewarded
						$credit_points_to_be_rewared = round($credit_points_to_be_rewared,1);

					}

					/*****Calculate save discount amount******/

					global $woocommerce;

					$items                  = $woocommerce->cart->get_cart();
					$cart_total_price_final = $woocommerce->cart->total;
					$dealclub_savings       = 0;
					$cw_discount = 0;
					$cw_monthly_discount = 0;
					$home_url = get_home_url();

						foreach ( $items as $item => $values ) {

						$pro_id = $values['product_id'];
						$_product = wc_get_product( $pro_id );

						if( $pro_id == 174721 || $pro_id == 174739 || $pro_id == 1392753 ){
							continue;
						}

						if($_product->is_type( 'simple' )){

							if ( $is_annual_or_monthly == 1392755 ) { //if new monthly then monthly price

								$sale_price = get_dynamic_price( $_product->get_id() )[1];

							}
							else { //ow annual price
								$sale_price = get_dynamic_price( $_product->get_id() )[0];
							}

							$regular_price = $_product->get_sale_price();

							$sale_price_for_monthly = get_dynamic_price( $_product->get_id() )[1];

							$discount = ($regular_price - $sale_price) * $values['quantity'];

							$discount_for_monthly = ($regular_price - $sale_price_for_monthly) * $values['quantity'];

							if ( $is_annual_or_monthly == 1392755 ) { //if user is a new monthly member

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

							$discount = ( (str_replace( '$', '', $dynamic_pricearr[$var_id]['sale_price']  )) - (str_replace( '$', '', $dynamic_pricearr[$var_id]['dynamic_price_array_annual']  )) ) * $values['quantity'];

							$discount_for_monthly = ( (str_replace( '$', '', $dynamic_pricearr[$var_id]['sale_price']  )) - (str_replace( '$', '', $dynamic_pricearr[$var_id]['dynamic_price_array_monthly']  )) ) * $values['quantity'];

							if ( $is_annual_or_monthly == 1392755 ) { //if user is a new monthly member

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

					if( $is_annual_or_monthly == 174765 || $is_annual_or_monthly == 174761 ) { //if user is a monthly member or has monthly added to the cart then show annual upsell

								?>
									<!-- Show saving msg for annual member -->
								<tr class="df-show-savings">
								<td class="savings-text" colspan="5">

									<div class="display-savings-msg">

										<div> <img class="money-bag" src="<?php echo $home_url ?>/wp-content/themes/astra-child/images/money-bag.png" alt="Money Bag Image"> You saved <span class="green-text">$<?php echo $cw_discount ?></span> with DealClub! </div>

									</div>
								</td>

								</tr>

								<?php

					}

					//annual upsell

					if((!array_key_exists("89338966d3810daca44fbf46e5f8f866", WC()->cart->get_cart()) && !array_key_exists("eb52463368ecd850262863fc1bc53272", WC()->cart->get_cart()) && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) && !is_user_an_active_member_wcm()) || ( $is_annual_or_monthly==1392755 && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) )|| check_if_monthly_is_in_cart() ) { //if user is a monthly member or has monthly added to the cart then show annual upsell

						//upsell annual dc

						//product id for annual product
						$product = wc_get_product( 174739 );

						//product id for new monthly product
						$product_monthly = wc_get_product( 1392753 );


						?>

							<?php

								if ( $is_annual_or_monthly==1392755 ) { //show saving msg for monthly member

							?>

							<tr class="df-show-savings">
								<td class="savings-text" colspan="5">

									<div class="display-savings-msg">

										<div> <img class="money-bag" src="<?php echo $home_url ?>/wp-content/themes/astra-child/images/money-bag.png" alt="Money Bag Image"> You are saving <span class="green-text">$<?php echo $cw_monthly_discount ?></span> with DealClub! </div>

									</div>
								</td>

							</tr>

								<script>
											//change the banner position when the user is monthly

											jQuery(document).ready(function() {

												jQuery('.banner').addClass('main-monthly-banner');

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

								if ( $is_annual_or_monthly == 1392755 ) { //if monthly then in the annual upsell add upgrade text

									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark">Upgrade to Annual & Save <span class="green-text">$'. $cw_discount . '</span> more with DealClub!</p><span class="dc-text-mem"> %s :
									<span class = "text-dark">$49.00/Year</span></span></div></a>', esc_url( get_permalink( $product->get_id() ) ), 'DealClub Membership ' ) ;

								}else { //normal annual upsell text

									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark annual-upsell-new-text"> Save <span class="green-text">$'. $cw_discount . '</span> more with DealClub!</p><span class="dc-text-mem"> %s :
									<span class = "text-dark">$49.00/Year</span></span></div></a>', esc_url( get_permalink( $product->get_id() ) ), 'DealClub Membership ' ) ;

								}

							}
							else{
								echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark freebie-mon-text"> Subscribe to DealClub!</p><span class="dc-text-mem"> %s :
								<span class = "text-dark">$49.00/Year</span></span></div></a>', esc_url( get_permalink( $product->get_id() ) ), $product->get_name() ) ;
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
									Your current $10/Mo Plan will be <b>upgraded to $49/Yr Plan.</b>
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

									if ( is_annual_or_monthly == 1392755  ) {

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
						<?php if(  !( $is_annual_or_monthly==1392755 && !array_key_exists("0db9fb291890f0ca660b86cac47d4b08", WC()->cart->get_cart()) ) && !check_if_monthly_is_in_cart() ): ?>

						<tr class="monthly">
								<td class="product-thumbnail monthly_deal">
								<?php printf( '<a href="%s">%s</a>', esc_url( get_permalink( $product_monthly->get_id() )), apply_filters( 'woocommerce_cart_item_thumbnail', $product_monthly->get_image(), "89338966d3810daca44fbf46e5f8f866" ) ); // PHPCS: XSS ok. ?>
									</td>
								<td class="product-name-mem monthly_product_name" colspan="4" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">

						<?php

								if($woocommerce->cart->total > 0){
									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> Save <span class="green-text">$'. $cw_monthly_discount . '</span> more with DealClub!</p><span class="dc-text-mem"> %s :
									<span class = "text-dark">$10.00/Month</span></span></div></a>', esc_url( get_permalink( $product_monthly->get_id() ) ), 'DealClub Membership' ) ;
									}
								else{
									echo sprintf( '<a href="%s"><h4 class="red-star">&#9733;</h4><div><p class ="text-dark"> Subscribe to DealClub!</p><span class="dc-text-mem"> %s :
									<span class = "text-dark">$10.00/Month</span></span></div></a>', esc_url( get_permalink( $product_monthly->get_id() ) ), $product_monthly->get_name() ) ;
								}
						?>

								</td>
								<td class="add-monthly-button" >
									<div class="monthly-sub-button monthly_add_item_button">
										<a  href="<?php echo get_site_url()?>/cart/?add-to-cart=1392753&utm_source=dc-page" class="offer_btn-2" > Add Item </a>
									</div>
								</td>

						</tr>

						<?php endif ;?>

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

					if ( $product_id == 174721 || $product_id == 174739 || $product_id == 1392753  ) {

						// Variables which are required to remove the membership from the cart
						$dynamicHref = esc_url(wc_get_cart_remove_url($cart_item_key));
						$dynamicAriaLabel = esc_html__('Remove this item', 'woocommerce');
						$dynamicProductID = esc_attr($product_id);
						$dynamicProductSKU = esc_attr($_product->get_sku());
						$home_url = get_home_url();

					}

					?>
					<script>
								//get the updated number of items in the cart and the cart total every time cart is updated
								var cartItemsCount = 0;
								var cartTotalFinal = 0;

								function updateCartItemsCount(callback) {
									jQuery.get({
									url: '<?php echo admin_url('admin-ajax.php'); ?>',
									data: { action: 'get_cart_items_count' },
									success: function(response) {

										var cartItemsCount = response.item_count;
										var cartTotalFinal = parseFloat(jQuery(response.cart_total_price_final).text().replace('$', ''));
										var hasFreebieCategory = response.has_freebie_category;
										var hasSpecificProduct = response.has_specific_product;
										var hasNonFreebieCategory = response.has_non_freebie_category;

										// Invoke the callback with the updated values
        								callback(cartItemsCount, cartTotalFinal);

										//Jquery to adjust the banner alignment when ajax runs on the cart

										jQuery(document).ready(function() {

										var is_annual_or_monthly = "<?php echo $is_annual_or_monthly;  ?>";

										if ( jQuery(window).width() >= 767 && is_annual_or_monthly != 1392755 ) {//banner for normal user

											// Function to add style 'top: 86px' when AJAX call starts
											jQuery(document).ajaxStart(function() {
												if (jQuery('.woocommerce-cart .ast-container .woocommerce .woocommerce-notices-wrapper .woocommerce-error').length) {//adjust the alginment if the error notice in the cart

													jQuery('.banner').css('top', '86px');
												}

											});

											// Function to remove the 'top' style when AJAX call is completed
											jQuery(document).ajaxStop(function() {

												if (jQuery('.woocommerce-cart .ast-container .woocommerce .woocommerce-notices-wrapper .woocommerce-error').length) {//adjust the alginment if the error notice in the cart
													jQuery('.banner').delay(500).queue(function(next) {
													jQuery('.banner').css('top', '215px');
														next();
														});
												}
											});

										}else{ //banner for monthly user

											// Function to add style 'top: 140px' when AJAX call starts
											jQuery(document).ajaxStart(function() {
												jQuery('.banner.banner-annual-for-monthly').css('top', '140px');

											});

											// Function to remove the 'top' style when AJAX call is completed
											jQuery(document).ajaxStop(function() {

												jQuery('.banner.banner-annual-for-monthly').delay(300).queue(function(next) {
													jQuery('.banner.banner-annual-for-monthly').css('top', '190px');
													next();
												});
											});

										}

										});

										//hide saving for monthly and annual when only freebie is in cart

										jQuery(document).ready(function() {

										//Adding the remove url to the remove the membership button

										// Your PHP variables are now accessible here
										var dynamicHref = "<?php echo $dynamicHref; ?>".replace(/#038;/g, "");
										var dynamicAriaLabel = "<?php echo $dynamicAriaLabel; ?>";
										var dynamicProductID = "<?php echo $dynamicProductID; ?>";
										var dynamicProductSKU = "<?php echo $dynamicProductSKU; ?>";

										var $removeProductLink = jQuery(".remove-product-m");

										$removeProductLink
											.attr("href", dynamicHref)
											.attr("aria-label", dynamicAriaLabel)
											.attr("data-product_id", dynamicProductID)
											.attr("data-product_sku", dynamicProductSKU);

										var is_annual_or_monthly = "<?php echo $is_annual_or_monthly;  ?>";

										if( is_annual_or_monthly == 1392755 ) {//tooltip close when clicked on cross
											jQuery('#df_cart_close_tooltip').click(function() {
												jQuery('.cart-tooltip-text').css('display','none');
											});
										}

										if ( is_annual_or_monthly == 1392755 && cartTotalFinal == 0 ) {

											jQuery('.df-show-savings').css('display','none');
											jQuery('.freebie-mon-text').text('Upgrade to Annual Membership & Enjoy 15-100% Extra Discount.');

											if (  jQuery(window).width() >= 768 ){

												jQuery('.freebie-mon-text').css('width','80%');
												jQuery('.banner').addClass('make_banner_top_for_mon_up');

											}else{

												jQuery('.freebie-mon-text').css('width','100%');
												jQuery('.annual_upsell_product_name').attr('style', 'top: 52px !important;');
												jQuery('.banner').addClass('make_banner_top_for_mon');
												jQuery('.monthly-sub-button').attr('style', 'bottom: 3em !important;');


											}

										}else if ( (is_annual_or_monthly == 174765 && cartTotalFinal == 0) || (is_annual_or_monthly == 174761 && cartTotalFinal == 0 ) ) {//if user is annual or old monthly and cart total is 0

											if( ( cartItemsCount >=1 && !hasNonFreebieCategory) ){//if only freebie is in cart or if there is any item which is not a freebie
												jQuery('.df-show-savings').css('display','none');
											}
										}
										});


										//re-run the js for pop up and remove the membership from the cart when only membership is remaining

										function membershipRemove() {

										var product_id = "<?php echo $product_id  ?>";

										var cart_total_price_final = "<?php echo $cart_total_price_final  ?>";

										if ( ( cartItemsCount == 1 && product_id == 1392753 ) || ( cart_total_price_final == 10 ) || ( cartTotalFinal == 10 ) ) {

										jQuery('.popup-mem-text').text('A DealClub Membership of just $10/Month, will save 5%-50% on all purchases for one month.')

										jQuery('.popup-mem-text').css('padding-top','45px');
										jQuery('.popup-extra-text').css('display','none');

										if (( cartItemsCount == 1 && product_id == 1392753 ) || ( cartItemsCount >= 2 && hasSpecificProduct ) ) {

											jQuery('.annual_upsell_product_name .text-dark.annual-upsell-new-text').text('Save an EXTRA 15%-100% On All Your Purchases')
										}

										jQuery(".remove-mem-cart").on("click", function () {
												jQuery("#blur-overlay").fadeIn();
												jQuery("#floating-popup").fadeIn();
											});

											//close the popup on clicking the cross
											jQuery(".popup-close-btn").on("click", function (event) {
												event.preventDefault(); // Prevent the default anchor tag behavior
												event.stopPropagation();
												jQuery("#blur-overlay").fadeOut();
												jQuery("#floating-popup").fadeOut();
											});

										}else if ( ( cartItemsCount == 1 && product_id == 174739 ) || ( cart_total_price_final == 49 ) || ( cartTotalFinal == 49 ) ) {

										if ( ( cartItemsCount == 1 && product_id == 174739 ) || ( cartItemsCount >=1 && !hasNonFreebieCategory ) ) {//if cart has only annual added to the cart or cart has annual and freebie in cart

											jQuery('.popup-mem-text').text('A DealClub Membership of just $49/Year, will save 15%-100% on all purchases for one year.');
											jQuery('.popup-extra-text').css('display','none');
											jQuery('.popup-mem-text').css('padding-top','45px');

										}


										jQuery(".remove-mem-cart").on("click", function () {
												jQuery("#blur-overlay").fadeIn();
												jQuery("#annual-floating-popup").fadeIn();
											});

											//close the popup on clicking the cross
											jQuery(".popup-close-btn").on("click", function (event) {
												event.preventDefault(); // Prevent the default anchor tag behavior
												event.stopPropagation();
												jQuery("#blur-overlay").fadeOut();
												jQuery("#annual-floating-popup").fadeOut();
											});

										}

										if ( product_id == 1392753 || product_id == 174739) {//if added product is monthly product

										if ( product_id == 1392753 ) {

												jQuery(".remove-mem-cart").on("click", function () {
												jQuery("#blur-overlay").fadeIn();
												jQuery("#floating-popup").fadeIn();
											});

											//close the popup on clicking the cross
											jQuery(".popup-close-btn").on("click", function (event) {
												event.preventDefault(); // Prevent the default anchor tag behavior
												event.stopPropagation();
												jQuery("#blur-overlay").fadeOut();
												jQuery("#floating-popup").fadeOut();
											});

										}else{

											jQuery(".remove-mem-cart").on("click", function () {
												jQuery("#blur-overlay").fadeIn();
												jQuery("#annual-floating-popup").fadeIn();
											});

											//close the popup on clicking the cross
											jQuery(".popup-close-btn").on("click", function (event) {
												event.preventDefault(); // Prevent the default anchor tag behavior
												event.stopPropagation();
												jQuery("#blur-overlay").fadeOut();
												jQuery("#annual-floating-popup").fadeOut();
											});

										}

										}

										}

										// Attach the function to .ajaxComplete()
										jQuery(document).ajaxComplete(function() {
										// Add the class after each AJAX request is complete
										membershipRemove();
										});

										// Initial call to add the class when the page loads
										membershipRemove();

									},
									});
								}

								  // Initial call to update cart items count on page load
									updateCartItemsCount(function(initialCartItemsCount, initialCartTotalFinal) {
										 // Store the initial values in variables
    										var cartItemsCount = initialCartItemsCount;
   											var cartTotalFinal = initialCartTotalFinal;

									});

								  // Trigger the AJAX call whenever the cart is updated (e.g., when adding/removing items)
									jQuery(document.body).on('updated_cart_totals', function() {
										updateCartItemsCount(function(updatedCartItemsCount, updatedCartTotalFinal) {

										});
									});

					</script>

					<?php

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
							<!-- monthly popup  -->
							<div class="popup" id="floating-popup">
								<div class="popup-content">
								<div class="df_cart_close_popup" id="df_cart_close_popup">
										<svg class="popup-close-btn" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
										<path d="M7.64199 12.3584L12.3587 7.64175M12.3587 12.3584L7.64199 7.64175M7.50033 18.3334H12.5003C16.667 18.3334 18.3337 16.6667 18.3337 12.5001V7.50008C18.3337 3.33341 16.667 1.66675 12.5003 1.66675H7.50033C3.33366 1.66675 1.66699 3.33341 1.66699 7.50008V12.5001C1.66699 16.6667 3.33366 18.3334 7.50033 18.3334Z" stroke="black" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</div>
									<div class="popup-mem-text">

										<span>A DealClub Membership of just $10/Month, saves <span class="green-text discount-text">$<?php echo $cw_monthly_discount ?></span> on this purchase</span>

									</div>
									<br>
									<div class="popup-extra-text">

									<span>& extra 5%-50% on all other purchases for one month.</span>

									</div>

									<div class="popup-confirm-text">

									<span>Are you sure, you want to miss out on these huge savings???</span>

									</div>

									<div class="popup-keep-mem-button">

									<button class="keep-mem-button">I want to save. Keep Membership</button>

									</div>

									<div class="popup-remove-mem-button">

									<button class="remove-mem-button"> <a href="%s" class="remove-product-m" aria-label="%s" data-product_id="%s" data-product_sku="%s">I hate savings! Remove Membership  </a>  </button>

									</div>

								</div>
							</div>

							<!-- annaul popup -->

							<div class="popup annual-popup" id="annual-floating-popup">
								<div class="popup-content">
								<div class="df_cart_close_popup" id="df_cart_close_popup">
										<svg class="popup-close-btn" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
										<path d="M7.64199 12.3584L12.3587 7.64175M12.3587 12.3584L7.64199 7.64175M7.50033 18.3334H12.5003C16.667 18.3334 18.3337 16.6667 18.3337 12.5001V7.50008C18.3337 3.33341 16.667 1.66675 12.5003 1.66675H7.50033C3.33366 1.66675 1.66699 3.33341 1.66699 7.50008V12.5001C1.66699 16.6667 3.33366 18.3334 7.50033 18.3334Z" stroke="black" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
									</div>
									<div class="popup-mem-text">

										<span>A DealClub Membership of just $49/Year, saves <span class="green-text discount-text">$<?php echo $cw_discount ?></span> on this purchase</span>

									</div>
									<br>
									<div class="popup-extra-text annual-popup-extra-text">

									<span>& extra 15%-100% on all other purchases for one year.</span>

									</div>

									<div class="popup-confirm-text">

									<span>Are you sure, you want to miss out on these huge savings???</span>

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

								if ( $product_id == 1392753 || $product_id == 174739  ) {

									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="javascript:void(0);" class="remove remove-mem-cart" aria-label="" data-product_id="" data-product_sku=""><img src="' . get_home_url(). '/wp-content/uploads/2022/12/Dustbin.svg"</a>',
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

								if ( product_id == 1392753 || product_id == 174739) {//if added product is monthly product

									if ( product_id == 1392753 ) {

										jQuery(".remove-mem-cart").on("click", function () {
											jQuery("#blur-overlay").fadeIn();
											jQuery("#floating-popup").fadeIn();
										});

										//close the popup on clicking the cross
										jQuery(".popup-close-btn").on("click", function (event) {
											event.preventDefault(); // Prevent the default anchor tag behavior
											event.stopPropagation();
											jQuery("#blur-overlay").fadeOut();
											jQuery("#floating-popup").fadeOut();
										});

									}else{

										jQuery(".remove-mem-cart").on("click", function () {
											jQuery("#blur-overlay").fadeIn();
											jQuery("#annual-floating-popup").fadeIn();
										});

										//close the popup on clicking the cross
										jQuery(".popup-close-btn").on("click", function (event) {
											event.preventDefault(); // Prevent the default anchor tag behavior
											event.stopPropagation();
											jQuery("#blur-overlay").fadeOut();
											jQuery("#annual-floating-popup").fadeOut();
										});

									}

									//Adding the remove url to the remove the membership button

									// Your PHP variables are now accessible here
									var dynamicHref = "<?php echo $dynamicHref; ?>".replace(/#038;/g, "");
									var dynamicAriaLabel = "<?php echo $dynamicAriaLabel; ?>";
									var dynamicProductID = "<?php echo $dynamicProductID; ?>";
									var dynamicProductSKU = "<?php echo $dynamicProductSKU; ?>";

									var $removeProductLink = jQuery(".remove-product-m");

									$removeProductLink
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
